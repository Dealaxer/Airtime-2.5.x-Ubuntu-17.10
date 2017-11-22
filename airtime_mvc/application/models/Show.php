<?php

class Application_Model_Show
{
    private $_showId;

    public function __construct($showId=NULL)
    {
        $this->_showId = $showId;
    }

    public function getName()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);

        return $show->getDbName();
    }

    public function setName($name)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbName($name);
        Application_Model_RabbitMq::PushSchedule();
    }

    public function setAirtimeAuthFlag($flag)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbLiveStreamUsingAirtimeAuth($flag);
        $show->save();
    }

    public function setCustomAuthFlag($flag)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbLiveStreamUsingCustomAuth($flag);
        $show->save();
    }

    public function setCustomUsername($username)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbLiveStreamUser($username);
        $show->save();
    }

    public function setCustomPassword($password)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbLiveStreamPass($password);
        $show->save();
    }

    public function getDescription()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);

        return $show->getDbDescription();
    }

    public function setDescription($description)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbDescription($description);
    }

    public function getColor()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);

        return $show->getDbColor();
    }

    public function setColor($color)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbColor($color);
    }

    public function getUrl()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);

        return $show->getDbUrl();
    }

    /*TODO : This method is not actually used anywhere as far as I can tell. We
        can safely remove it and probably many other superfluous methods.
        --RG*/

    public function setUrl($p_url)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbUrl($p_url);
    }

    public function getGenre()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);

        return $show->getDbGenre();
    }

    public function setGenre($p_genre)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbGenre($p_genre);
    }

    public function getBackgroundColor()
    {
        $show = CcShowQuery::create()->findPK($this->_showId);

        return $show->getDbBackgroundColor();
    }

    public function setBackgroundColor($backgroundColor)
    {
        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->setDbBackgroundColor($backgroundColor);
    }

    public function getId()
    {
        return $this->_showId;
    }

    public function getHosts()
    {
        $sql = <<<SQL
SELECT first_name,
       last_name
FROM cc_show_hosts
LEFT JOIN cc_subjs ON cc_show_hosts.subjs_id = cc_subjs.id
WHERE show_id = :show_id
SQL;

        $hosts = Application_Common_Database::prepareAndExecute( $sql,
            array( ':show_id' => $this->getId() ), 'all');

        $res = array();
        foreach ($hosts as $host) {
            $res[] = $host['first_name']." ".$host['last_name'];
        }
        return $res;
    }

    public function getHostsIds()
    {
        $sql = <<<SQL
SELECT subjs_id
FROM cc_show_hosts
WHERE show_id = :show_id
SQL;

        $hosts = Application_Common_Database::prepareAndExecute(
            $sql, array( ':show_id' => $this->getId() ), 'all');

        return $hosts;
    }

    /**
     * remove everything about this show.
     */
    public function delete()
    {
        //usually we hide the show-instance, but in this case we are deleting the show template
        //so delete all show-instances as well.
        CcShowInstancesQuery::create()->filterByDbOriginalShow($this->_showId)->delete();

        $show = CcShowQuery::create()->findPK($this->_showId);
        $show->delete();
    }

    public function resizeShow($deltaDay, $deltaMin, $instanceId)
    {
        $con = Propel::getConnection();

        if ($deltaDay > 0) {
            return _("Shows can have a max length of 24 hours.");
        }

        $utcTimezone = new DateTimeZone("UTC");
        $nowDateTime = new DateTime("now", $utcTimezone);

        //keep track of cc_show_day entries we need to update
        $showDayIds = array();

        /*
         * If the resized show is an edited instance of a repeating show we
         * need to treat it as a separate show and not resize the other instances
         * 
         * Also, if the resized show has edited instances, we need to exclude
         * those from the resize
         */
        $ccShow = CcShowQuery::create()->findPk($this->_showId);
        if ($ccShow->isRepeating()) {

            //convert instance to local timezone
            $ccShowInstance = CcShowInstancesQuery::create()->findPk($instanceId);
            $startsDT = $ccShowInstance->getDbStarts(null);
            $timezone = $ccShow->getFirstCcShowDay()->getDbTimezone();
            $startsDT->setTimezone(new DateTimeZone($timezone));

            /* Get cc_show_day for the current instance. If we don't find one
             * we know it is a repeat interval of one of cc_show_days first
             * show and we can assume we aren't resizing a modified instance
             */
            $ccShowDay = CcShowDaysQuery::create()
                ->filterByDbFirstShow($startsDT->format("Y-m-d"))
                ->filterByDbStartTime($startsDT->format("H:i:s"))
                ->filterByDbShowId($this->_showId)
                ->findOne();

            /* Check if this cc_show_day rule is non-repeating. If it is, then
             * we know this instance was edited out of the repeating sequence
             */
            if (!$ccShowDay || $ccShowDay->getDbRepeatType() != -1) {
                $ccShowDays = $ccShow->getRepeatingCcShowDays();
                foreach ($ccShowDays as $day) {
                    array_push($showDayIds, $day->getDbId());
                }

                $excludeIds = $ccShow->getEditedRepeatingInstanceIds();

                //exlcude edited instances from resize
                $showInstances = CcShowInstancesQuery::create()
                    ->filterByDbShowId($this->_showId)
                    ->filterByDbModifiedInstance(false)
                    ->filterByDbId($excludeIds, criteria::NOT_IN)
                    ->find();
            } elseif ($ccShowDay->getDbRepeatType() == -1) {
                array_push($showDayIds, $ccShowDay->getDbId());

                //treat edited instance as separate show for resize
                $showInstances = CcShowInstancesQuery::create()
                    ->filterByDbId($instanceId)
                    ->find();
            }
        } else {
            $ccShowDays = $ccShow->getCcShowDayss();
            foreach ($ccShowDays as $day) {
                array_push($showDayIds, $day->getDbId());
            }

            $showInstances = CcShowInstancesQuery::create()
                ->filterByDbShowId($this->_showId)
                ->find($con);
        }

        /* Check two things:
           1. If the show being resized and any of its repeats end in the past
           2. If the show being resized and any of its repeats overlap
              with other scheduled shows */

        //keep track of instance ids for update show instances start/end times
        $instanceIds = array();
        $displayTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        
        //check if new show time overlaps with any other shows
        foreach ($showInstances as $si) {
            array_push($instanceIds, $si->getDbId());

            $startsDateTime = $si->getDbStarts(null);
            $endsDateTime   = $si->getDbEnds(null);

            /* The user is moving the show on the calendar from the perspective
                of local time.  * incase a show is moved across a time change
                border offsets should be added to the local * timestamp and
                then converted back to UTC to avoid show time changes */
            $startsDateTime->setTimezone($displayTimezone);
            $endsDateTime->setTimezone($displayTimezone);

            //$newStartsDateTime = Application_Model_ShowInstance::addDeltas($startsDateTime, $deltaDay, $deltaMin);
            $newEndsDateTime   = Application_Model_ShowInstance::addDeltas($endsDateTime, $deltaDay, $deltaMin);

            if ($newEndsDateTime->getTimestamp() < $nowDateTime->getTimestamp()) {
                return _("End date/time cannot be in the past");
            }

            //convert our new starts/ends to UTC.
            //$newStartsDateTime->setTimezone($utc);
            $newEndsDateTime->setTimezone($utcTimezone);

            $overlapping = Application_Model_Schedule::checkOverlappingShows(
                $startsDateTime, $newEndsDateTime, true, $si->getDbId());

            if ($overlapping) {
                return _("Cannot schedule overlapping shows.\nNote: Resizing a repeating show ".
                       "affects all of its repeats.");
            }
        }

        $hours = $deltaMin/60;
        $hours = ($hours > 0) ? floor($hours) : ceil($hours);
        $mins  = abs($deltaMin % 60);

        $sql_gen = "UPDATE cc_show_instances ".
            "SET ends = (ends + :deltaDay1::INTERVAL + :interval1::INTERVAL) ".
            "WHERE (id IN (".implode($instanceIds, ",").") ".
            "AND ends > :current_timestamp1) ".
            "AND ((ends + :deltaDay2::INTERVAL + :interval2::INTERVAL - starts) <= interval '24:00')";

        Application_Common_Database::prepareAndExecute($sql_gen,
            array(
                ':deltaDay1'          => "$deltaDay days",
                ':interval1'          => "$hours:$mins",
                ':current_timestamp1' =>  $nowDateTime->format("Y-m-d H:i:s"),
                ':deltaDay2'          => "$deltaDay days",
                ':interval2'          => "$hours:$mins"
            ), "execute");

        $sql_gen = "UPDATE cc_show_days ".
            "SET duration = (CAST(duration AS interval) + :deltaDay3::INTERVAL + :interval3::INTERVAL) ".
            "WHERE id IN (".implode($showDayIds, ",").") ".
            "AND ((CAST(duration AS interval) + :deltaDay4::INTERVAL + :interval4::INTERVAL) <= interval '24:00')";

        Application_Common_Database::prepareAndExecute($sql_gen,
            array(
                ':deltaDay3'          => "$deltaDay days",
                ':interval3'          => "$hours:$mins",
                ':deltaDay4'          => "$deltaDay days",
                ':interval4'          => "$hours:$mins"
            ), "execute");

        $con = Propel::getConnection(CcSchedulePeer::DATABASE_NAME);
        $con->beginTransaction();

        try {
            //update the status flag in cc_schedule.

            /* Since we didn't use a propel object when updating
             * cc_show_instances table we need to clear the instances
             * so the correct information is retrieved from the db
             */
            CcShowInstancesPeer::clearInstancePool();

            $instances = CcShowInstancesQuery::create()
                ->filterByDbEnds($nowDateTime->format("Y-m-d H:i:s"), Criteria::GREATER_THAN)
                ->filterByDbId($instanceIds, Criteria::IN)
                ->find($con);

            foreach ($instances as $instance) {
                $instance->updateScheduleStatus($con);
            }

            $con->commit();
        } catch (Exception $e) {
            $con->rollback();
            Logging::info("Couldn't update schedule status.");
            Logging::info($e->getMessage());
        }

        Application_Model_RabbitMq::PushSchedule();
    }

    public function cancelShow($day_timestamp)
    {
        $timeinfo = explode(" ", $day_timestamp);

        CcShowDaysQuery::create()
            ->filterByDbShowId($this->_showId)
            ->update(array('DbLastShow' => $timeinfo[0]));

        $sql = <<<SQL
SELECT id from cc_show_instances
WHERE starts >= :dayTimestamp::TIMESTAMP
  AND show_id = :showId
SQL;
    
        $rows = Application_Common_Database::prepareAndExecute( $sql, array(
            ':dayTimestamp' => $day_timestamp,
            ':showId'       => $this->getId()), 'all');

        foreach ($rows as $row) {
            try {
                $showInstance = new Application_Model_ShowInstance($row["id"]);
                $showInstance->delete($rabbitmqPush = false);
            } catch (Exception $e) {
                Logging::info($e->getMessage());
            }
        }

        Application_Model_RabbitMq::PushSchedule();
    }

    /**
     * Check whether the current show originated
     * from a recording.
     *
     * @return boolean
     *      true if originated from recording, otherwise false.
     */
    public function isRecorded()
    {
        $showInstancesRow = CcShowInstancesQuery::create()
            ->filterByDbShowId($this->getId())
            ->filterByDbRecord(1)
            ->filterByDbModifiedInstance(false)
            ->findOne();

        return !is_null($showInstancesRow);
    }

    /**
     * Check whether the current show has rebroadcasts of a recorded
     * show. Should be used in conjunction with isRecorded().
     *
     * @return boolean
     *      true if show has rebroadcasts, otherwise false.
     */
    public function isRebroadcast()
    {
         $showInstancesRow = CcShowInstancesQuery::create()
        ->filterByDbShowId($this->_showId)
        ->filterByDbRebroadcast(1)
        ->filterByDbModifiedInstance(false)
        ->findOne();

        return !is_null($showInstancesRow);
    }

    /**
     * Get start time and relative start date for a recorded
     * shows rebroadcasts. For example start date format would be
     * "x days" and time would HH:MM:SS
     *
     * @return array
     *      array of associate arrays containing "day_offset" and "start_time"
     */
    public function getRebroadcastsRelative()
    {
        $sql = <<<SQL
SELECT day_offset,
       start_time
FROM cc_show_rebroadcast
WHERE show_id = :showId
ORDER BY day_offset
SQL;
        return Application_Common_Database::prepareAndExecute( $sql,
            array( ':showId' => $this->getId() ), 'all' );
    }

    /**
     * Check whether the current show is set to repeat
     * repeating shows.
     *
     * @return boolean
     *      true if repeating shows, otherwise false.
     */
    public function isRepeating()
    {
        $showDaysRow = CcShowDaysQuery::create()
            ->filterByDbShowId($this->_showId)
            ->findOne();

        if (!is_null($showDaysRow)) {
            return ($showDaysRow->getDbRepeatType() != -1);
        } else {
            return false;
        }
    }

    /**
     * Get the repeat type of the show. Show can have repeat type of 
     * "weekly", "every 2 weeks", "monthly", "monthly on the same weekday",
     * "every 3 weeks" and "every 4 weeks". These values are represented 
     * by 0, 1, 2, 3, 4 and 5, respectively.
     *
     * @return int
     *      Return the integer corresponding to the repeat type.
     */
    public function getRepeatType()
    {
        $showDaysRow = CcShowDaysQuery::create()
            ->filterByDbShowId($this->_showId)
            ->findOne();

        if (!is_null($showDaysRow))
            return $showDaysRow->getDbRepeatType();
        else
            return -1;
    }

    /**
     * Get the end date for a repeating show in the format yyyy-mm-dd
     *
     * @return string
     *      Return the end date for the repeating show or the empty
     *      string if there is no end.
     */
    public function getRepeatingEndDate()
    {
        $sql = <<<SQL
SELECT last_show
FROM cc_show_days
WHERE show_id = :showId
ORDER BY last_show DESC
SQL;

        $query = Application_Common_Database::prepareAndExecute( $sql,
            array( 'showId' => $this->getId() ), 'column' );

        /* TODO: Why return empty string instead of false? very confusing --RG
         */
        return ($query !== false) ? $query : "";
    }

    /**
     * Deletes all future instances of the current show object
     * from the show_instances table. This function is used when
     * a show is being edited - in some cases, when a show is edited
     * we just destroy all future show instances, and let another function
     * regenerate them later on. Note that this isn't always the most
     * desirable thing to do. Deleting a show instance and regenerating
     * it cause any scheduled playlists within those show instances to
     * be gone for good.
     */
    public function deleteAllInstances()
    {
        $sql = <<<SQL
DELETE
FROM cc_show_instances
WHERE starts > :timestamp::TIMESTAMP
  AND show_id = :showId
SQL;
        Application_Common_Database::prepareAndExecute( $sql,
            array( ':timestamp' => gmdate("Y-m-d H:i:s"),
                   ':showId'    => $this->getId()), 'execute');
    }

    /**
     * Deletes all future rebroadcast instances of the current
     * show object from the show_instances table.
     */
    public function deleteAllRebroadcasts()
    {
        $sql = <<<SQL
DELETE
FROM cc_show_instances
WHERE starts > :timestamp::TIMESTAMP
  AND show_id :showId
  AND rebroadcast 1
SQL;
        Application_Common_Database::prepareAndExecute( $sql,
            array( ':showId' => $this->getId(),
                   ':timestamp' => gmdate("Y-m-d H:i:s")), 'execute');
    }

    /**
     * Get the start date of the current show in UTC timezone.
     *
     * @return string
     *      The start date in the format YYYY-MM-DD or empty string in case
     *      start date could not be found
     */
    public function getStartDateAndTime()
    {
        $con = Propel::getConnection();

        $showId = $this->getId();
        $stmt = $con->prepare(
            "SELECT first_show, start_time, timezone FROM cc_show_days"
            ." WHERE show_id = :showId"
            ." ORDER BY first_show"
            ." LIMIT 1");

        $stmt->bindParam(':showId', $showId);
        $stmt->execute();

        if (!$stmt) {
            return "";
        }

        $rows = $stmt->fetchAll();
        $row = $rows[0];

        $dt = new DateTime($row["first_show"]." ".$row["start_time"], new DateTimeZone($row["timezone"]));
        $dt->setTimezone(new DateTimeZone("UTC"));

        return $dt->format("Y-m-d H:i");
    }

    /**
     * Get the start date of the current show in UTC timezone.
     *
     * @return string
     *      The start date in the format YYYY-MM-DD
     */
    public function getStartDate()
    {
        list($date,) = explode(" ", $this->getStartDateAndTime());

        return $date;
    }

    /**
     * Get the start time of the current show in UTC timezone.
     *
     * @return string
     *      The start time in the format HH:MM
     */

    public function getStartTime()
    {
        list(,$time) = explode(" ", $this->getStartDateAndTime());

        return $time;
    }

    /**
     * Get the end date of the current show.
     * Note that this is not the end date of repeated show
     *
     * @return string
     *      The end date in the format YYYY-MM-DD
     */
    public function getEndDate()
    {
        $startDate     = $this->getStartDate();
        $startTime     = $this->getStartTime();
        $duration      = $this->getDuration();

        $startDateTime = new DateTime($startDate.' '.$startTime);
        $duration      = explode(":", $duration);

        $endDate = $startDateTime->add(new DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));

        return $endDate->format('Y-m-d');
    }

    /**
     * Get the end time of the current show.
     *
     * @return string
     *      The start time in the format HH:MM:SS
     */
    public function getEndTime()
    {
        $startDate = $this->getStartDate();
        $startTime = $this->getStartTime();
        $duration = $this->getDuration();

        $startDateTime = new DateTime($startDate.' '.$startTime);
        $duration = explode(":", $duration);

        $endDate = $startDateTime->add(new DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));

        return $endDate->format('H:i:s');
    }

    /**
     * Indicate whether the starting point of the show is in the
     * past.
     *
     * @return boolean
     *      true if the StartDate is in the past, false otherwise
     */
    public function isStartDateTimeInPast()
    {
        return (gmdate("Y-m-d H:i:s") > ($this->getStartDate()." ".$this->getStartTime()));
    }

    /**
     * Get the ID's of future instance of the current show.
     *
     * @return array
     *      A simple array containing all ID's of show instance
     *  scheduled in the future.
     */
    public function getAllFutureInstanceIds()
    {
        $sql = <<<SQL
SELECT id
FROM cc_show_instances
WHERE show_id = :showId
  AND starts > :timestamp::TIMESTAMP
  AND modified_instance != TRUE
SQL;
        $rows = Application_Common_Database::prepareAndExecute($sql,
            array( ':showId'    => $this->getId(),
                   ':timestamp' => gmdate("Y-m-d H:i:s")), "all");

        $res = array();
        foreach ($rows as $r) {
            $res[] = $r['id'];
        }
        return $res;
    }

    /* Called when a show's duration is changed (edited).
     *
     * @param array $p_data
     *      array containing the POST data about the show from the
     *      browser.
     *
     */
    private function updateDurationTime($p_data)
    {
        //need to update cc_show_instances, cc_show_days
        $con = Propel::getConnection();
        $timestamp = gmdate("Y-m-d H:i:s");

        $stmt =  $con->prepare("UPDATE cc_show_days "
                 ."SET duration = :add_show_duration "
                 ."WHERE show_id = :add_show_id" );
        $stmt->execute( array(
            ':add_show_duration' => $p_data['add_show_duration'],
            ':add_show_id' => $p_data['add_show_id']
        ));


        $sql = <<<SQL
UPDATE cc_show_instances
SET ends = starts + :add_show_duration::INTERVAL
WHERE show_id = :show_id
  AND ends > :timestamp::TIMESTAMP
SQL;
        
        Application_Common_Database::prepareAndExecute( $sql, array(
            ':add_show_duration' => $p_data['add_show_duration'],
            ':show_id' => $p_data['add_show_id'],
            ':timestamp' => $timestamp), "execute");
    }

    public function getDuration($format=false)
    {
        $showDay = CcShowDaysQuery::create()->filterByDbShowId($this->getId())->findOne();
        if (!$format) {
            return $showDay->getDbDuration();
        } else {
            $info = explode(':',$showDay->getDbDuration());

            return str_pad(intval($info[0]),2,'0',STR_PAD_LEFT).'h '.str_pad(intval($info[1]),2,'0',STR_PAD_LEFT).'m';
        }
    }

    public function getShowDays()
    {
        $showDays = CcShowDaysQuery::create()->filterByDbShowId(
            $this->getId())->find();
        $res = array();
        foreach ($showDays as $showDay) {
            $res[] = $showDay->getDbDay();
        }
        return $res;
    }

    /* Only used for shows that aren't repeating.
     *
     * @return Boolean: true if show has an instance, otherwise false. */
    public function hasInstance()
    {
        return (!is_null($this->getInstance()));
    }

    /* Only used for shows that aren't repeating.
     *
     * @return CcShowInstancesQuery: An propel object representing a
     *      row in the cc_show_instances table. */
    public function getInstance()
    {
        $showInstance = CcShowInstancesQuery::create()
            ->filterByDbShowId($this->getId())
            ->findOne();

        return $showInstance;
    }

    /**
     *  returns info about live stream override info
     */
    public function getLiveStreamInfo()
    {
        $info = array();
        if ($this->getId() == null) {
            return $info;
        } else {
            $ccShow = CcShowQuery::create()->findPK($this->_showId);
            $info['custom_username'] = $ccShow->getDbLiveStreamUser();
            $info['cb_airtime_auth'] = $ccShow->getDbLiveStreamUsingAirtimeAuth();
            $info['cb_custom_auth']  = $ccShow->getDbLiveStreamUsingCustomAuth();
            $info['custom_username'] = $ccShow->getDbLiveStreamUser();
            $info['custom_password'] = $ccShow->getDbLiveStreamPass();
            return $info;
        }
    }

    /* Only used for shows that are repeating. Note that this will return
     * true even for dates that only have a "modified" show instance (does not
     * check if the "modified_instance" column is set to true). This is intended
     * behaviour.
     *
     * @param $p_dateTime: Date for which we are checking if instance
     * exists.
     *
     * @return Boolean: true if show has an instance on $p_dateTime,
     *      otherwise false. */
    public function hasInstanceOnDate($p_dateTime)
    {
        return (!is_null($this->getInstanceOnDate($p_dateTime)));
    }

    /* Only used for shows that are repeating. Note that this will return
     * shows that have been "modified" (does not check if the "modified_instance"
     * column is set to true). This is intended behaviour.
     *
     * @param $p_dateTime: Date for which we are getting an instance.
     *
     * @return CcShowInstancesQuery: An propel object representing a
     *      row in the cc_show_instances table. */
    public function getInstanceOnDate($p_dateTime)
    {
        $timestamp = $p_dateTime->format("Y-m-d H:i:s");
        $sql = <<<SQL
SELECT id
FROM cc_show_instances
WHERE date(starts) = date(:timestamp::TIMESTAMP)
  AND show_id = :showId
  AND rebroadcast = 0;
SQL;
        try {
            $row = Application_Common_Database::prepareAndExecute( $sql,
                array( ':showId' => $this->getId(),
                       ':timestamp' => $timestamp ), 'column');
            return CcShowInstancesQuery::create()
                ->findPk($row);
        } catch (Exception $e) {
            return null;
        }
        
    }
    
    /**
     * 
     * Creates show instances that are scheduled after the shows_populated_until
     * value in cc_pref. If the show instances are linked their show content will
     * get filled.
     * 
     * This can occur when a user is viewing the next day/week/month in the
     * calendar or when pypo is requesting the schedule after the shows_populated_until
     * date and time.
     */
    public static function createAndFillShowInstancesPastPopulatedUntilDate($needScheduleUntil)
    {
        //UTC DateTime object
        $showsPopUntil = Application_Model_Preference::GetShowsPopulatedUntil();
        //if application is requesting shows past our previous populated until date, generate shows up until this point.
        if (is_null($showsPopUntil) || $showsPopUntil->getTimestamp() < $needScheduleUntil->getTimestamp()) {
            $service_show = new Application_Service_ShowService();
            $ccShow = $service_show->delegateInstanceCreation(null, $needScheduleUntil, true);
            Application_Model_Preference::SetShowsPopulatedUntil($needScheduleUntil);
        }
    }

    /**
     * Get all the show instances in the given time range (inclusive).
     *
     * @param DateTime $start_timestamp
     *      In UTC time.
     * @param DateTime $end_timestamp
     *      In UTC time.
     * @param  unknown_type $excludeInstance
     * @param  boolean      $onlyRecord
     * @param  int          $showId
     *      limits the results to instances of a given showId only
     * @return array
     */
    public static function getShows($start_timestamp, $end_timestamp, $onlyRecord=FALSE, $showId=null)
    {
        self::createAndFillShowInstancesPastPopulatedUntilDate($end_timestamp);

        $sql = <<<SQL
SELECT si1.starts            AS starts,
       si1.ends              AS ends,
       si1.record            AS record,
       si1.rebroadcast       AS rebroadcast,
       si2.starts            AS parent_starts,
       si1.instance_id       AS record_id,
       si1.show_id           AS show_id,
       show.name             AS name,
       show.color            AS color,
       show.background_color AS background_color,
       show.linked           AS linked,
       si1.file_id           AS file_id,
       si1.id                AS instance_id,
       si1.created           AS created,
       si1.last_scheduled    AS last_scheduled,
       si1.time_filled       AS time_filled,
       f.soundcloud_id
FROM cc_show_instances      AS si1
LEFT JOIN cc_show_instances AS si2  ON si1.instance_id = si2.id
LEFT JOIN cc_show           AS show ON show.id         = si1.show_id
LEFT JOIN cc_files          AS f    ON f.id            = si1.file_id
WHERE si1.modified_instance = FALSE
SQL;
        //only want shows that are starting at the time or later.
        $start_string = $start_timestamp->format("Y-m-d H:i:s");
        $end_string = $end_timestamp->format("Y-m-d H:i:s");

        $params = array();

        if ($showId) {
            $sql .= " AND (si1.show_id = :show_id)";
            $params[':show_id'] = $showId; 
        }

        if ($onlyRecord) {
            $sql .= " AND (si1.starts >= :start::TIMESTAMP AND si1.starts < :end::TIMESTAMP)";
            $sql .= " AND (si1.record = 1)";

            $params[':start'] = $start_string;
            $params[':end'] = $end_string;
            return Application_Common_Database::prepareAndExecute( $sql, $params, 'all');

        } else {
            $sql .= " ". <<<SQL
AND ((si1.starts >= :start1::TIMESTAMP AND si1.starts < :end1::TIMESTAMP)
     OR (si1.ends > :start2::TIMESTAMP AND si1.ends <= :end2::TIMESTAMP)
     OR (si1.starts <= :start3::TIMESTAMP AND si1.ends >= :end3::TIMESTAMP))
ORDER BY si1.starts
SQL;
            $params = array_merge($params, array(
                    'start1' => $start_string,
                    'start2' => $start_string,
                    'start3' => $start_string,
                    'end1'   => $end_string,
                    'end2'   => $end_string,
                    'end3'   => $end_string
                )
            );
            return Application_Common_Database::prepareAndExecute( $sql, $params, 'all');
        }
    }

    private static function setNextPop($next_date, $show_id, $day)
    {
        $nextInfo = explode(" ", $next_date);

        $repeatInfo = CcShowDaysQuery::create()
            ->filterByDbShowId($show_id)
            ->filterByDbDay($day)
            ->findOne();

        $repeatInfo->setDbNextPopDate($nextInfo[0])
            ->save();
    }

    /**
     *
     * @param DateTime $start
     *          -in UTC time
     * @param DateTime $end
     *          -in UTC time
     * @param boolean $editable
     */
    public static function &getFullCalendarEvents($p_start, $p_end, $p_editable=false)
    {
        $events   = array();
        $interval = $p_start->diff($p_end);
        $days     = $interval->format('%a');
        $shows    = Application_Model_Show::getShows($p_start, $p_end);
        $content_count = Application_Model_ShowInstance::getContentCount(
            $p_start, $p_end);
        $isFull = Application_Model_ShowInstance::getIsFull($p_start, $p_end);

        $displayTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        $utcTimezone = new DateTimeZone("UTC");
        $now = new DateTime("now", $utcTimezone);

        foreach ($shows as &$show) {
            $options = array();

            //only bother calculating percent for week or day view.
            if (intval($days) <= 7) {
                $options["percent"] = Application_Model_Show::getPercentScheduled($show["starts"], $show["ends"], $show["time_filled"]);
            }

            if (isset($show["parent_starts"])) {
                $parentStartsDT = new DateTime($show["parent_starts"], $utcTimezone);
            }

            $startsDT = DateTime::createFromFormat("Y-m-d G:i:s",
                $show["starts"], $utcTimezone);
            $endsDT   = DateTime::createFromFormat("Y-m-d G:i:s",
                $show["ends"], $utcTimezone);

            if( $p_editable ) {
                if ($show["record"] && $now > $startsDT) {
                    $options["editable"] = false;
                } elseif ($show["rebroadcast"] &&
                    $now > $parentStartsDT) {
                    $options["editable"] = false;
                } elseif ($now < $endsDT) {
                    $options["editable"] = true;
                }
            }

            $startsDT->setTimezone($displayTimezone);
            $endsDT->setTimezone($displayTimezone);

            $options["show_empty"] = (array_key_exists($show['instance_id'],
                $content_count)) ? 0 : 1;

            if (array_key_exists($show['instance_id'], $isFull)) {
                $options["show_partial_filled"] = !$isFull[$show['instance_id']];
            } else {
                $options["show_partial_filled"] = true;
            }

            $event = array();

            $event["id"]            = intval($show["instance_id"]);
            $event["title"]         = $show["name"];
            $event["start"]         = $startsDT->format("Y-m-d H:i:s");
            $event["end"]           = $endsDT->format("Y-m-d H:i:s");
            $event["allDay"]        = false;
            $event["showId"]        = intval($show["show_id"]);
            $event["linked"]        = intval($show["linked"]);
            $event["record"]        = intval($show["record"]);
            $event["rebroadcast"]   = intval($show["rebroadcast"]);
            $event["soundcloud_id"] = is_null($show["soundcloud_id"])
                ? -1 : $show["soundcloud_id"];
            
            //for putting the now playing icon on the show.
            if ($now > $startsDT && $now < $endsDT) {
                $event["nowPlaying"] = true;
            }
            else {
                $event["nowPlaying"] = false;
            }

            //event colouring
            if ($show["color"] != "") {
                $event["textColor"] = "#".$show["color"];
            }

            if ($show["background_color"] != "") {
                $event["color"] = "#".$show["background_color"];
            }

            foreach ($options as $key => $value) {
                $event[$key] = $value;
            }

            $events[] = $event;
        }
        return $events;
    }

    /**
     * Calculates the percentage of a show scheduled given the start and end times in date/time format
     * and the time_filled as the total time the schow is scheduled for in time format.
     * 
     * TODO when using propel properly this should be a method on the propel show instance model.
     **/
    private static function getPercentScheduled($p_starts, $p_ends, $p_time_filled)
    {
        $utcTimezone = new DatetimeZone("UTC");
        $startDt = new DateTime($p_starts, $utcTimezone);
        $endDt = new DateTime($p_ends, $utcTimezone);
        $durationSeconds = intval($endDt->format("U")) - intval($startDt->format("U"));
        $time_filled = Application_Common_DateHelper::playlistTimeToSeconds($p_time_filled);
        if ($durationSeconds != 0) { //Prevent division by zero if the show duration somehow becomes zero.
            $percent = ceil(( $time_filled / $durationSeconds) * 100);
        } else {
            $percent = 0;
        }
        return $percent;
    }

    /**
     * Given time $timeNow, returns the show being played right now.
     * Times are all in UTC time.
     *
     * @param  String $timeNow - current time (in UTC)
     * @return array  - show being played right now
     */
    public static function getCurrentShow($timeNow=null)
    {
        $con = Propel::getConnection();
        if ($timeNow == null) {
            $timeNow = gmdate("Y-m-d H:i:s");
        }
        //TODO, returning starts + ends twice (once with an alias). Unify this after the 2.0 release. --Martin
        $sql = <<<SQL
SELECT si.starts AS start_timestamp,
       si.ends AS end_timestamp,
       s.name,
       s.id,
       si.id AS instance_id,
       si.record,
       s.url,
       starts,
       ends
FROM cc_show_instances si
     LEFT JOIN cc_show s
     ON si.show_id = s.id
WHERE si.show_id = s.id
  AND si.starts <= :timeNow1::timestamp
  AND si.ends > :timeNow2::timestamp
  AND modified_instance != TRUE
SQL;

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':timeNow1', $timeNow);
        $stmt->bindParam(':timeNow2', $timeNow);

        if ($stmt->execute()) {
            $rows = $stmt->fetchAll();
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        return $rows;
    }

    /**
     * Gets the current show, previous and next with an n-day window from
     * the given timeNow, so timeNow-2days and timeNow+$daysToRetrieve days.
     * 
     * @param $utcNow A DateTime object containing the current time in UTC.
     * @return An array containing the previous show, 
     *         current show, and next show.
     */
    public static function getPrevCurrentNext($utcNow, $utcEndStr, $showsToRetrieve)
    {
        $timeZone = new DateTimeZone("UTC"); //This function works entirely in UTC.
        assert(get_class($utcNow) === "DateTime");
        assert($utcNow->getTimeZone() == $timeZone);
        
        $CC_CONFIG = Config::getConfig();
        $con = Propel::getConnection();

        // This will fetch the currently playing show first, then any 
        // upcoming shows within our interval, and finally move on to 
        // previous shows in the past 2 days.
        $sql = <<<SQL
SELECT s.name,
       s.description,
       s.genre,
       s.id,
       si.id AS instance_id,
       si.record,
       s.url,
       s.image_path,
       starts,
       ends
FROM cc_show_instances si
     LEFT JOIN cc_show s
     ON si.show_id = s.id
WHERE si.show_id = s.id
  AND si.starts >= :timeNow::timestamp - INTERVAL '2 days'
  AND si.starts < :timeEnd::timestamp
  AND modified_instance != TRUE
ORDER BY 
  CASE 
    WHEN si.ends > :timeNow::timestamp
      AND  si.starts < :timeNow::timestamp THEN 1
    WHEN si.starts > :timeNow::timestamp THEN 2
    ELSE 3
  END
LIMIT :lim
SQL;

        $stmt = $con->prepare($sql);
        
        $utcNowStr = $utcNow->format("Y-m-d H:i:s");
        $stmt->bindValue(':timeNow', $utcNowStr);
        $stmt->bindValue(':timeEnd', $utcEndStr);
        $stmt->bindValue(':lim', $showsToRetrieve);
        
        if ($stmt->execute()) {
            // use PDO::FETCH_ASSOC to only get the associative values
            // note that fetchAll() defaults to PDO::FETCH_BOTH, which we don't want
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }

        $numberOfRows = count($rows);

        $results['previousShow'] = array();
        $results['currentShow']  = null;
        $results['nextShow']     = array();

        for ($i = 0; $i < $numberOfRows; ++$i) {
            // all shows start/end times are stored in the database as UTC.
            $showStartTime = new DateTime($rows[$i]['starts'], $timeZone);
            $showEndTime   = new DateTime($rows[$i]['ends'], $timeZone);
            
            // Find the show that is within the current time.
            if (($showStartTime <= $utcNow) && ($showEndTime > $utcNow)) {   
                $results['currentShow'] = $rows[$i];
            } else if ($showEndTime < $utcNow ) {
                array_push($results['previousShow'], $rows[$i]);
            } else if ($showStartTime > $utcNow) {
                array_push($results['nextShow'], $rows[$i]);
            }
        }

        return $results;
    }
    
    /**
     * Gets the current show, previous and next with an 2day window from
     * the given timeNow, so timeNow-2days and timeNow+2days.
     *
     * @param $utcNow A DateTime object containing the current time in UTC.
     * @return An array (with stupid sub-arrays) containing the previous show id,
     *         current show id, and next show id.
     * @deprecated
     */
    public static function getPrevCurrentNextOld($utcNow)
    {
        $timeZone = new DateTimeZone("UTC"); //This function works entirely in UTC.
        assert(get_class($utcNow) === "DateTime");
        assert($utcNow->getTimeZone() == $timeZone);
    
        $CC_CONFIG = Config::getConfig();
        $con = Propel::getConnection();
    
        //TODO, returning starts + ends twice (once with an alias). Unify this after the 2.0 release. --Martin
        $sql = <<<SQL
SELECT si.starts AS start_timestamp,
       si.ends AS end_timestamp,
       s.name,
       s.description,
       s.id,
       si.id AS instance_id,
       si.record,
       s.url,
       s.image_path,
       starts,
       ends
FROM cc_show_instances si
     LEFT JOIN cc_show s
     ON si.show_id = s.id
WHERE si.show_id = s.id
  AND si.starts > :timeNow1::timestamp - INTERVAL '2 days'
  AND si.ends < :timeNow2::timestamp + INTERVAL '2 days'
  AND modified_instance != TRUE
ORDER BY si.starts
SQL;
    
        $stmt = $con->prepare($sql);
    
        $utcNowStr = $utcNow->format("Y-m-d H:i:s");
        $stmt->bindValue(':timeNow1', $utcNowStr);
        $stmt->bindValue(':timeNow2', $utcNowStr);
    
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $msg = implode(',', $stmt->errorInfo());
            throw new Exception("Error: $msg");
        }
    
        $numberOfRows = count($rows);
    
        $results['previousShow'] = array();
        $results['currentShow']  = array();
        $results['nextShow']     = array();
    
        for ($i = 0; $i < $numberOfRows; ++$i) {
            //All shows start/end times are stored in the database as UTC.
            $showStartTime = new DateTime($rows[$i]['starts'], $timeZone);
            $showEndTime   = new DateTime($rows[$i]['ends'], $timeZone);
    
            //Find the show that is within the current time.
            if (($showStartTime <= $utcNow) && ($showEndTime > $utcNow))
            {
                if ($i-1 >= 0) {
                    $results['previousShow'][0] = array(
                            "id"                      => $rows[$i-1]['id'],
                            "instance_id"             => $rows[$i-1]['instance_id'],
                            "name"                    => $rows[$i-1]['name'],
                            "description"            => $rows[$i-1]['description'],
                            "url"                     => $rows[$i-1]['url'],
                            "start_timestamp"         => $rows[$i-1]['start_timestamp'],
                            "end_timestamp"           => $rows[$i-1]['end_timestamp'],
                            "starts"                  => $rows[$i-1]['starts'],
                            "ends"                    => $rows[$i-1]['ends'],
                            "record"                  => $rows[$i-1]['record'],
                            "image_path"              => $rows[$i-1]['image_path'],
                            "type"                   => "show");
                }
    
                $results['currentShow'][0] =  $rows[$i];
    
                if (isset($rows[$i+1])) {
                    $results['nextShow'][0] =  array(
                            "id"                     => $rows[$i+1]['id'],
                            "instance_id"             => $rows[$i+1]['instance_id'],
                            "name"                    => $rows[$i+1]['name'],
                            "description"             => $rows[$i+1]['description'],
                            "url"                     => $rows[$i+1]['url'],
                            "start_timestamp"         => $rows[$i+1]['start_timestamp'],
                            "end_timestamp"           => $rows[$i+1]['end_timestamp'],
                            "starts"                  => $rows[$i+1]['starts'],
                            "ends"                    => $rows[$i+1]['ends'],
                            "record"                  => $rows[$i+1]['record'],
                            "image_path"              => $rows[$i+1]['image_path'],
                            "type"                    => "show");
                }
                break;
            }
            //Previous is any row that ends after time now capture it in case we need it later.
            if ($showEndTime < $utcNow ) {
                $previousShowIndex = $i;
            }
            //if we hit this we know we've gone to far and can stop looping.
            if ($showStartTime > $utcNow) {
                $results['nextShow'][0] = array(
                        "id"                      => $rows[$i]['id'],
                        "instance_id"             => $rows[$i]['instance_id'],
                        "name"                    => $rows[$i]['name'],
                        "description"             => $rows[$i]['description'],
                        "url"                     => $rows[$i]['url'],
                        "start_timestamp"         => $rows[$i]['start_timestamp'],
                        "end_timestamp"           => $rows[$i]['end_timestamp'],
                        "starts"                  => $rows[$i]['starts'],
                        "ends"                    => $rows[$i]['ends'],
                        "record"                 => $rows[$i]['record'],
                        "image_path"             => $rows[$i]['image_path'],
                        "type"                   => "show");
                break;
            }
        }
        //If we didn't find a a current show because the time didn't fit we may still have
        //found a previous show so use it.
        if (count($results['previousShow']) == 0 && isset($previousShowIndex)) {
            $results['previousShow'][0] = array(
                    "id"                      => $rows[$previousShowIndex]['id'],
                    "instance_id"             => $rows[$previousShowIndex]['instance_id'],
                    "name"                    => $rows[$previousShowIndex]['name'],
                    "description"             => $rows[$previousShowIndex]['description'],
                    "start_timestamp"         => $rows[$previousShowIndex]['start_timestamp'],
                    "end_timestamp"           => $rows[$previousShowIndex]['end_timestamp'],
                    "starts"                  => $rows[$previousShowIndex]['starts'],
                    "ends"                    => $rows[$previousShowIndex]['ends'],
                    "record"                  => $rows[$previousShowIndex]['record'],
                    "type"                    => "show");
        }
    
        return $results;
    }

    /**
     * Given a start time $timeStart and end time $timeEnd, returns the next $limit
     * number of shows within the time interval
     * If $timeEnd not given, shows within next 48 hours from $timeStart are returned
     * If $limit not given, all shows within the intervals are returned
     * Times are all in UTC time.
     *
     * @param  String $timeStart - interval start time (in UTC)
     * @param  int    $limit     - number of shows to return
     * @param  String $timeEnd   - interval end time (in UTC)
     * @return array  - the next $limit number of shows within the time interval
     */
    public static function getNextShows($timeStart, $limit = "ALL", $timeEnd = "")
    {
        // defaults to retrieving shows from next 2 days if no end time has
        // been specified
        if ($timeEnd == "") {
            $timeEnd = "'$timeStart' + INTERVAL '2 days'";
        }

        //TODO, returning starts + ends twice (once with an alias). Unify this after the 2.0 release. --Martin
        $sql = <<<SQL
SELECT si.starts AS start_timestamp,
       si.ends AS end_timestamp,
       s.name,
       s.id,
       si.id AS instance_id,
       si.record,
       s.url,
       starts,
       ends
FROM cc_show_instances si
     LEFT JOIN cc_show s
     ON si.show_id = s.id
WHERE si.show_id = s.id
  AND si.starts >= :timeStart::timestamp
  AND si.starts < :timeEnd::timestamp
  AND modified_instance != TRUE
ORDER BY si.starts
SQL;

        //PDO won't accept "ALL" as a limit value (complains it is not an
        //integer, and so we must completely remove the limit clause if we
        //want to show all results - MK
        if ($limit != "ALL") {
            $sql .= PHP_EOL."LIMIT :lim";
            $params =  array(
            ':timeStart' => $timeStart,
            ':timeEnd'   => $timeEnd,
            ':lim'       => $limit);
        } else {
            $params = array(
            ':timeStart' => $timeStart,
            ':timeEnd'   => $timeEnd);
        }

        return Application_Common_Database::prepareAndExecute( $sql, $params, 'all');
    }

    public static function getMaxLengths()
    {
        $sql = <<<SQL
SELECT column_name, character_maximum_length FROM information_schema.columns
WHERE table_name = 'cc_show' AND character_maximum_length > 0
SQL;
        $result     = Application_Common_Database::prepareAndExecute($sql);
        $assocArray = array();
        foreach ($result as $row) {
            $assocArray[$row['column_name']] = $row['character_maximum_length'];
        }
        return $assocArray;
    }

    public static function getStartEndCurrentMonthView() {
        $first_day_of_calendar_month_view = mktime(0, 0, 0, date("n"), 1);
        $weekStart = Application_Model_Preference::GetWeekStartDay();
        while (date('w', $first_day_of_calendar_month_view) != $weekStart) {
            $first_day_of_calendar_month_view -= 60*60*24;
        }
        $last_day_of_calendar_view = $first_day_of_calendar_month_view + 3600*24*42;

        $start = new DateTime("@".$first_day_of_calendar_month_view);
        $end = new DateTime("@".$last_day_of_calendar_view);

        return array($start, $end);
    }

    public static function getStartEndCurrentWeekView() {
        $first_day_of_calendar_week_view = mktime(0, 0, 0, date("n"), date("j"));
        $weekStart = Application_Model_Preference::GetWeekStartDay();
        while (date('w', $first_day_of_calendar_week_view) != $weekStart) {
            $first_day_of_calendar_week_view -= 60*60*24;
        }
        $last_day_of_calendar_view = $first_day_of_calendar_week_view + 3600*24*7;

        $start = new DateTime("@".$first_day_of_calendar_week_view);
        $end = new DateTime("@".$last_day_of_calendar_view);

        return array($start, $end);
    }

    public static function getStartEndCurrentDayView() {
        $today = mktime(0, 0, 0, date("n"), date("j"));
        $tomorrow = $today + 3600*24;

        $start = new DateTime("@".$today);
        $end = new DateTime("@".$tomorrow);

        return array($start, $end);
    }

}
