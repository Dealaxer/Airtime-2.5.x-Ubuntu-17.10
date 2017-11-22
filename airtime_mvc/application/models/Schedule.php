<?php

class Application_Model_Schedule
{
    /**
     * Return TRUE if file is going to be played in the future.
     *
     * @param string $p_fileId
     */
    public static function IsFileScheduledInTheFuture($p_fileId)
    {
        $sql = <<<SQL
SELECT COUNT(*)
FROM cc_schedule
WHERE file_id = :file_id
  AND ends > NOW() AT TIME ZONE 'UTC'
SQL;
        $count = Application_Common_Database::prepareAndExecute( $sql, array(
            ':file_id'=>$p_fileId), 'column');
        return (is_numeric($count) && ($count != '0'));
    }

    public static function getAllFutureScheduledFiles($instanceId=null)
    {
        $sql = <<<SQL
SELECT distinct(file_id)
FROM cc_schedule
WHERE ends > now() AT TIME ZONE 'UTC'
AND file_id is not null
SQL;

        $files = Application_Common_Database::prepareAndExecute( $sql, array());

        $real_files = array();
        foreach ($files as $f) {
            $real_files[] = $f['file_id'];
        }

        return $real_files;
    }

    public static function getAllFutureScheduledWebstreams()
    {
        $sql = <<<SQL
SELECT distinct(stream_id)
FROM cc_schedule
WHERE ends > now() AT TIME ZONE 'UTC'
AND stream_id is not null
SQL;
        $streams = Application_Common_Database::prepareAndExecute( $sql, array());

        $real_streams = array();
        foreach ($streams as $s) {
            $real_streams[] = $s['stream_id'];
        }

        return $real_streams;
    }
    
    /**
     * Returns data related to the scheduled items.
     */
    public static function GetPlayOrderRange($utcTimeEnd = null, $showsToRetrieve = 5)
    {
        // Everything in this function must be done in UTC. You will get a swift kick in the pants if you mess that up.

        // when timeEnd is unspecified, return to the default behaviour - set a range of 48 hours from current time
        if (!$utcTimeEnd) {
            $end = new DateTime();
            $end->add(new DateInterval("P2D")); // Add 2 days
            $end->setTimezone(new DateTimeZone("UTC"));
            $utcTimeEnd = $end->format("Y-m-d H:i:s");
        }

        $utcNow = new DateTime("now", new DateTimeZone("UTC"));

        $shows = Application_Model_Show::getPrevCurrentNext($utcNow, $utcTimeEnd, $showsToRetrieve);
        $previousShowID = count($shows['previousShow'])>0?$shows['previousShow'][0]['instance_id']:null;
        $currentShowID = count($shows['currentShow'])>0?$shows['currentShow']['instance_id']:null;
        $nextShowID = count($shows['nextShow'])>0?$shows['nextShow'][0]['instance_id']:null;
        $results = self::GetPrevCurrentNext($previousShowID, $currentShowID, $nextShowID, $utcNow);

        $range = array(
            "station" => array (
                "env"           => APPLICATION_ENV,
                "schedulerTime" => $utcNow->format("Y-m-d H:i:s")
            ),
            //Previous, current, next songs!
            "tracks" => array(
                "previous"  => $results['previous'],
                "current"   => $results['current'],
                "next"      => $results['next']
            ),
            //Current and next shows
            "shows" => array (
                "previous"  => $shows['previousShow'],
                "current"   => $shows['currentShow'],
                "next"      => $shows['nextShow']
            )
        );

        return $range;
    }

    /**
     * Old version of the function for backwards compatibility
     * @deprecated
     */
    public static function GetPlayOrderRangeOld()
    {
        // Everything in this function must be done in UTC. You will get a swift kick in the pants if you mess that up.
    
        $utcNow = new DateTime("now", new DateTimeZone("UTC"));
    
        $shows = Application_Model_Show::getPrevCurrentNextOld($utcNow);
        $previousShowID = count($shows['previousShow'])>0?$shows['previousShow'][0]['instance_id']:null;
        $currentShowID = count($shows['currentShow'])>0?$shows['currentShow'][0]['instance_id']:null;
        $nextShowID = count($shows['nextShow'])>0?$shows['nextShow'][0]['instance_id']:null;
        $results = self::GetPrevCurrentNext($previousShowID, $currentShowID, $nextShowID, $utcNow);
    
        $range = array(
                "env" => APPLICATION_ENV,
                "schedulerTime" => $utcNow->format("Y-m-d H:i:s"),
                //Previous, current, next songs!
                "previous"=>$results['previous'] !=null?$results['previous']:(count($shows['previousShow'])>0?$shows['previousShow'][0]:null),
                "current"=>$results['current'] !=null?$results['current']:((count($shows['currentShow'])>0 && $shows['currentShow'][0]['record'] == 1)?$shows['currentShow'][0]:null),
                "next"=> $results['next'] !=null?$results['next']:(count($shows['nextShow'])>0?$shows['nextShow'][0]:null),
                //Current and next shows
                "currentShow"=>$shows['currentShow'],
                "nextShow"=>$shows['nextShow']
        );
    
        return $range;
    }

    /**
     * Queries the database for the set of schedules one hour before
     * and after the given time. If a show starts and ends within that
     * time that is considered the current show. Then the scheduled item
     * before it is the previous show, and the scheduled item after it
     * is the next show. This way the dashboard getCurrentPlaylist is
     * very fast. But if any one of the three show types are not found
     * through this mechanism a call is made to the old way of querying
     * the database to find the track info.
    **/
    public static function GetPrevCurrentNext($p_previousShowID, $p_currentShowID, $p_nextShowID, $utcNow)
    {
        $timeZone = new DateTimeZone("UTC"); //This function works entirely in UTC.
        assert(get_class($utcNow) === "DateTime");
        assert($utcNow->getTimeZone() == $timeZone);

        if ($p_previousShowID == null && $p_currentShowID == null && $p_nextShowID == null) {
            return;
        }

        $sql = "SELECT %%columns%% st.starts as starts, st.ends as ends,
            st.media_item_played as media_item_played, si.ends as show_ends
            %%tables%% WHERE ";

        $fileColumns = "ft.artist_name, ft.track_title, ";
        $fileJoin = "FROM cc_schedule st JOIN cc_files ft ON st.file_id = ft.id
            LEFT JOIN cc_show_instances si ON st.instance_id = si.id";

        $streamColumns = "ws.name AS artist_name, wm.liquidsoap_data AS track_title, ";
        $streamJoin = <<<SQL
FROM cc_schedule AS st
JOIN cc_webstream ws ON st.stream_id = ws.id
LEFT JOIN cc_show_instances AS si ON st.instance_id = si.id
LEFT JOIN cc_subjs AS sub ON sub.id = ws.creator_id
LEFT JOIN
  (SELECT *
   FROM cc_webstream_metadata
   ORDER BY start_time DESC LIMIT 1) AS wm ON st.id = wm.instance_id
SQL;

        $predicateArr = array();
        $paramMap = array();
        if (isset($p_previousShowID)) {
            $predicateArr[] = 'st.instance_id = :previousShowId';
            $paramMap[':previousShowId'] = $p_previousShowID;
        }
        if (isset($p_currentShowID)) {
            $predicateArr[] = 'st.instance_id = :currentShowId';
            $paramMap[':currentShowId'] = $p_currentShowID;
        }
        if (isset($p_nextShowID)) {
            $predicateArr[] = 'st.instance_id = :nextShowId';
            $paramMap[':nextShowId'] = $p_nextShowID;
        }

        $sql .= " (".implode(" OR ", $predicateArr).") ";
        $sql .= ' AND st.playout_status > 0 ORDER BY st.starts';

        $filesSql = str_replace("%%columns%%", $fileColumns, $sql);
        $filesSql = str_replace("%%tables%%", $fileJoin, $filesSql);

        $streamSql = str_replace("%%columns%%", $streamColumns, $sql);
        $streamSql = str_replace("%%tables%%", $streamJoin, $streamSql);

        $sql = "SELECT * FROM (($filesSql) UNION ($streamSql)) AS unioned ORDER BY starts";

        $rows = Application_Common_Database::prepareAndExecute($sql, $paramMap);
        $numberOfRows = count($rows);

        $results['previous'] = null;
        $results['current']  = null;
        $results['next']     = null;

        for ($i = 0; $i < $numberOfRows; ++$i) {

            // if the show is overbooked, then update the track end time to the end of the show time.
            if ($rows[$i]['ends'] > $rows[$i]["show_ends"]) {
                $rows[$i]['ends'] = $rows[$i]["show_ends"];
            }

            $curShowStartTime = new DateTime($rows[$i]['starts'], $timeZone);
            $curShowEndTime   = new DateTime($rows[$i]['ends'], $timeZone);

            if (($curShowStartTime <= $utcNow) && ($curShowEndTime >= $utcNow)) {
                if ($i - 1 >= 0) {
                    $results['previous'] = array("name"=>$rows[$i-1]["artist_name"]." - ".$rows[$i-1]["track_title"],
                            "starts"=>$rows[$i-1]["starts"],
                            "ends"=>$rows[$i-1]["ends"],
                            "type"=>'track');
                }
                 $results['current'] =  array("name"=>$rows[$i]["artist_name"]." - ".$rows[$i]["track_title"],
                            "starts"=>$rows[$i]["starts"],
                            "ends"=> (($rows[$i]["ends"] > $rows[$i]["show_ends"]) ? $rows[$i]["show_ends"]: $rows[$i]["ends"]),
                            "media_item_played"=>$rows[$i]["media_item_played"],
                            "record"=>0,
                            "type"=>'track');
                if (isset($rows[$i+1])) {
                    $results['next'] =  array("name"=>$rows[$i+1]["artist_name"]." - ".$rows[$i+1]["track_title"],
                            "starts"=>$rows[$i+1]["starts"],
                            "ends"=>$rows[$i+1]["ends"],
                            "type"=>'track');
                }
                break;
            }
            if ($curShowEndTime < $utcNow ) {
                $previousIndex = $i;
            }
            if ($curShowStartTime > $utcNow) {
                $results['next'] = array("name"=>$rows[$i]["artist_name"]." - ".$rows[$i]["track_title"],
                            "starts"=>$rows[$i]["starts"],
                            "ends"=>$rows[$i]["ends"],
                            "type"=>'track');
                break;
            }
        }
        //If we didn't find a a current show because the time didn't fit we may still have
        //found a previous show so use it.
        if ($results['previous'] === null && isset($previousIndex)) {
                $results['previous'] = array("name"=>$rows[$previousIndex]["artist_name"]." - ".$rows[$previousIndex]["track_title"],
                            "starts"=>$rows[$previousIndex]["starts"],
                            "ends"=>$rows[$previousIndex]["ends"]);;
        }

        return $results;
    }

    public static function GetLastScheduleItem($p_timeNow)
    {
        $sql = <<<SQL
SELECT ft.artist_name,
       ft.track_title,
       st.starts AS starts,
       st.ends AS ends
FROM cc_schedule st
LEFT JOIN cc_files ft ON st.file_id = ft.id
LEFT JOIN cc_show_instances sit ON st.instance_id = sit.id
-- this and the next line are necessary since we can overbook shows.
WHERE st.ends < TIMESTAMP :timeNow

  AND st.starts >= sit.starts
  AND st.starts < sit.ends
ORDER BY st.ends DESC LIMIT 1;
SQL;
        $row = Application_Common_Database::prepareAndExecute($sql, array(':timeNow'=>$p_timeNow));

        return $row;
    }

    public static function GetCurrentScheduleItem($p_timeNow, $p_instanceId)
    {
        /* Note that usually there will be one result returned. In some
         * rare cases two songs are returned. This happens when a track
         * that was overbooked from a previous show appears as if it
         * hasnt ended yet (track end time hasn't been reached yet). For
         * this reason,  we need to get the track that starts later, as
         * this is the *real* track that is currently playing. So this
         * is why we are ordering by track start time. */
        $sql = "SELECT *"
        ." FROM cc_schedule st"
        ." LEFT JOIN cc_files ft"
        ." ON st.file_id = ft.id"
        ." WHERE st.starts <= TIMESTAMP :timeNow1"
        ." AND st.instance_id = :instanceId"
        ." AND st.ends > TIMESTAMP :timeNow2"
        ." ORDER BY st.starts DESC"
        ." LIMIT 1";

        $row = Application_Common_Database::prepareAndExecute($sql, array(':timeNow1'=>$p_timeNow, ':instanceId'=>$p_instanceId, ':timeNow2'=>$p_timeNow,));

        return $row;
    }

    public static function GetNextScheduleItem($p_timeNow)
    {
        $sql = "SELECT"
        ." ft.artist_name, ft.track_title,"
        ." st.starts as starts, st.ends as ends"
        ." FROM cc_schedule st"
        ." LEFT JOIN cc_files ft"
        ." ON st.file_id = ft.id"
        ." LEFT JOIN cc_show_instances sit"
        ." ON st.instance_id = sit.id"
        ." WHERE st.starts > TIMESTAMP :timeNow"
        ." AND st.starts >= sit.starts" //this and the next line are necessary since we can overbook shows.
        ." AND st.starts < sit.ends"
        ." ORDER BY st.starts"
        ." LIMIT 1";

        $row = Application_Common_Database::prepareAndExecute($sql, array(':timeNow'=>$p_timeNow));

        return $row;
    }

    /*
     *
     * @param DateTime $p_startDateTime
     *
     * @param DateTime $p_endDateTime
     *
     * @return array $scheduledItems
     *
     */
    public static function GetScheduleDetailItems($p_start, $p_end, $p_shows, $p_show_instances)
    {
        $p_start_str = $p_start->format("Y-m-d H:i:s");
        $p_end_str = $p_end->format("Y-m-d H:i:s");

        //We need to search 48 hours before and after the show times so that that we
        //capture all of the show's contents.
        $p_track_start= $p_start->sub(new DateInterval("PT48H"))->format("Y-m-d H:i:s");
        $p_track_end = $p_end->add(new DateInterval("PT48H"))->format("Y-m-d H:i:s");

        $templateSql = <<<SQL
SELECT DISTINCT sched.starts AS sched_starts,
                sched.ends AS sched_ends,
                sched.id AS sched_id,
                sched.cue_in AS cue_in,
                sched.cue_out AS cue_out,
                sched.fade_in AS fade_in,
                sched.fade_out AS fade_out,
                sched.playout_status AS playout_status,
                sched.instance_id AS sched_instance_id,

                %%columns%%
                FROM (%%join%%)
SQL;

        $filesColumns = <<<SQL
                ft.track_title AS file_track_title,
                ft.artist_name AS file_artist_name,
                ft.album_title AS file_album_title,
                ft.length AS file_length,
                ft.file_exists AS file_exists,
                ft.mime AS file_mime,
                ft.soundcloud_id AS soundcloud_id
SQL;
        $filesJoin = <<<SQL
       cc_schedule AS sched
       JOIN cc_files AS ft ON (sched.file_id = ft.id
           AND ((sched.starts >= :fj_ts_1
               AND sched.starts < :fj_ts_2)
               OR (sched.ends > :fj_ts_3
               AND sched.ends <= :fj_ts_4)
               OR (sched.starts <= :fj_ts_5
               AND sched.ends >= :fj_ts_6))
        )
SQL;
        $paramMap = array(
        	":fj_ts_1" => $p_track_start,
        	":fj_ts_2" => $p_track_end,
        	":fj_ts_3" => $p_track_start,
        	":fj_ts_4" => $p_track_end,
        	":fj_ts_5" => $p_track_start,
        	":fj_ts_6" => $p_track_end,
        );

        $filesSql = str_replace("%%columns%%",
            $filesColumns,
            $templateSql);
        $filesSql= str_replace("%%join%%",
            $filesJoin,
            $filesSql);

        $streamColumns = <<<SQL
                ws.name AS file_track_title,
                sub.login AS file_artist_name,
                ws.description AS file_album_title,
                ws.length AS file_length,
                't'::BOOL AS file_exists,
                ws.mime AS file_mime,
                (SELECT NULL::integer AS soundcloud_id)
SQL;
        $streamJoin = <<<SQL
      cc_schedule AS sched
      JOIN cc_webstream AS ws ON (sched.stream_id = ws.id
          AND ((sched.starts >= :sj_ts_1
               AND sched.starts < :sj_ts_2)
               OR (sched.ends > :sj_ts_3
               AND sched.ends <= :sj_ts_4)
               OR (sched.starts <= :sj_ts_5
               AND sched.ends >= :sj_ts_6))
      )
      LEFT JOIN cc_subjs AS sub ON (ws.creator_id = sub.id)
SQL;
        $map = array(
        	":sj_ts_1" => $p_track_start,
        	":sj_ts_2" => $p_track_end,
        	":sj_ts_3" => $p_track_start,
        	":sj_ts_4" => $p_track_end,
        	":sj_ts_5" => $p_track_start,
        	":sj_ts_6" => $p_track_end,
        );
        $paramMap = $paramMap + $map;

        $streamSql = str_replace("%%columns%%",
            $streamColumns,
            $templateSql);
        $streamSql = str_replace("%%join%%",
            $streamJoin,
            $streamSql);


        $showPredicate = "";
        if (count($p_shows) > 0) {

            $params = array();
            $map = array();

            for ($i = 0, $len = count($p_shows); $i < $len; $i++) {
            	$holder = ":show_".$i;

            	$params[] = $holder;
            	$map[$holder] = $p_shows[$i];
            }

            $showPredicate = " AND show_id IN (".implode(",", $params).")";
            $paramMap = $paramMap + $map;
        } else if (count($p_show_instances) > 0) {
            $showPredicate = " AND si.id IN (".implode(",", $p_show_instances).")";
        }

        $sql = <<<SQL
SELECT showt.name AS show_name,
       showt.color AS show_color,
       showt.background_color AS show_background_color,
       showt.id AS show_id,
       showt.linked AS linked,
       si.starts AS si_starts,
       si.ends AS si_ends,
       si.time_filled AS si_time_filled,
       si.record AS si_record,
       si.rebroadcast AS si_rebroadcast,
       si.instance_id AS parent_show,
       si.id AS si_id,
       si.last_scheduled AS si_last_scheduled,
       si.file_id AS si_file_id,
       *
       FROM (($filesSql) UNION ($streamSql)) as temp
       RIGHT JOIN cc_show_instances AS si ON (si.id = sched_instance_id)
JOIN cc_show AS showt ON (showt.id = si.show_id)
WHERE si.modified_instance = FALSE
  $showPredicate
  AND ((si.starts >= :ts_1
       AND si.starts < :ts_2)
  OR (si.ends > :ts_3
      AND si.ends <= :ts_4)
  OR (si.starts <= :ts_5
      AND si.ends >= :ts_6))
ORDER BY si_starts,
         sched_starts;
SQL;

        $map = array(
        	":ts_1" => $p_start_str,
        	":ts_2" => $p_end_str,
        	":ts_3" => $p_start_str,
        	":ts_4" => $p_end_str,
        	":ts_5" => $p_start_str,
        	":ts_6" => $p_end_str,
        );
        $paramMap = $paramMap + $map;

        $rows = Application_Common_Database::prepareAndExecute(
        	$sql,
        	$paramMap,
        	Application_Common_Database::ALL
        );

        return $rows;
    }

    public static function UpdateMediaPlayedStatus($p_id)
    {
        $sql = "UPDATE cc_schedule"
                ." SET media_item_played=TRUE";
        // we need to update 'broadcasted' column as well
        // check the current switch status
        $live_dj        = Application_Model_Preference::GetSourceSwitchStatus('live_dj')        == 'on';
        $master_dj      = Application_Model_Preference::GetSourceSwitchStatus('master_dj')      == 'on';
        $scheduled_play = Application_Model_Preference::GetSourceSwitchStatus('scheduled_play') == 'on';

        if (!$live_dj && !$master_dj && $scheduled_play) {
            $sql .= ", broadcasted=1";
        }

        $sql .= " WHERE id=:pid";
        $map = array(":pid" => $p_id);

        Application_Common_Database::prepareAndExecute($sql, $map,
            Application_Common_Database::EXECUTE);
    }

    public static function UpdateBrodcastedStatus($dateTime, $value)
    {
        $now = $dateTime->format("Y-m-d H:i:s");

        $sql = <<<SQL
UPDATE cc_schedule
SET broadcasted=:broadcastedValue
WHERE starts <= :starts::TIMESTAMP
  AND ends >= :ends::TIMESTAMP
SQL;

        $retVal = Application_Common_Database::prepareAndExecute($sql, array(
            ':broadcastedValue' => $value,
            ':starts' => $now,
            ':ends' => $now), 'execute');
        return $retVal;
    }

    public static function getSchduledPlaylistCount()
    {
        $sql = "SELECT count(*) as cnt FROM cc_schedule";

        $res = Application_Common_Database::prepareAndExecute($sql, array(),
        		Application_Common_Database::COLUMN);

        return $res;
    }

    /**
     * Convert a time string in the format "YYYY-MM-DD HH:mm:SS"
     * to "YYYY-MM-DD-HH-mm-SS".
     *
     * @param  string $p_time
     * @return string
     */
    private static function AirtimeTimeToPypoTime($p_time)
    {
        $p_time = substr($p_time, 0, 19);
        $p_time = str_replace(" ", "-", $p_time);
        $p_time = str_replace(":", "-", $p_time);

        return $p_time;
    }

    /**
     * Convert a time string in the format "YYYY-MM-DD-HH-mm-SS" to
     * "YYYY-MM-DD HH:mm:SS".
     *
     * @param  string $p_time
     * @return string
     */
    private static function PypoTimeToAirtimeTime($p_time)
    {
        $t = explode("-", $p_time);

        return $t[0]."-".$t[1]."-".$t[2]." ".$t[3].":".$t[4].":00";
    }

    /**
     * Return true if the input string is in the format YYYY-MM-DD-HH-mm
     *
     * @param  string  $p_time
     * @return boolean
     */
    public static function ValidPypoTimeFormat($p_time)
    {
        $t = explode("-", $p_time);
        if (count($t) != 5) {
            return false;
        }
        foreach ($t as $part) {
            if (!is_numeric($part)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Converts a time value as a string (with format HH:MM:SS.mmmmmm) to
     * millisecs.
     *
     * @param  string $p_time
     * @return int
     */
    public static function WallTimeToMillisecs($p_time)
    {
        $t = explode(":", $p_time);
        $millisecs = 0;
        if (strpos($t[2], ".")) {
            $secParts = explode(".", $t[2]);
            $millisecs = $secParts[1];
            $millisecs = str_pad(substr($millisecs, 0, 3),3, '0');
            $millisecs = intval($millisecs);
            $seconds = intval($secParts[0]);
        } else {
            $seconds = intval($t[2]);
        }
        $ret = $millisecs + ($seconds * 1000) + ($t[1] * 60 * 1000) + ($t[0] * 60 * 60 * 1000);

        return $ret;
    }

    /**
     * Returns an array of schedule items from cc_schedule table. Tries
     * to return at least 3 items (if they are available). The parameters
     * $p_startTime and $p_endTime specify the range. Schedule items returned
     * do not have to be entirely within this range. It is enough that the end
     * or beginning of the scheduled item is in the range.
     *
     *
     * @param string $p_startTime
     *    In the format YYYY-MM-DD HH:MM:SS.nnnnnn
     * @param string $p_endTime
     *    In the format YYYY-MM-DD HH:MM:SS.nnnnnn
     * @return array
     *    Returns null if nothing found, else an array of associative
     *    arrays representing each row.
     */
    public static function getItems($p_startTime, $p_endTime)
    {
        $baseQuery = <<<SQL
SELECT st.file_id     AS file_id,
       st.id          AS id,
       st.instance_id AS instance_id,
       st.starts      AS start,
       st.ends        AS end,
       st.cue_in      AS cue_in,
       st.cue_out     AS cue_out,
       st.fade_in     AS fade_in,
       st.fade_out    AS fade_out,
       si.starts      AS show_start,
       si.ends        AS show_end,
       s.name         AS show_name,
       f.id           AS file_id,
       f.replay_gain  AS replay_gain,
       ws.id          AS stream_id,
       ws.url         AS url
FROM cc_schedule AS st
LEFT JOIN cc_show_instances AS si ON st.instance_id = si.id
LEFT JOIN cc_show           AS s  ON s.id = si.show_id
LEFT JOIN cc_files          AS f  ON st.file_id = f.id
LEFT JOIN cc_webstream      AS ws ON st.stream_id = ws.id
SQL;
        $predicates = <<<SQL
WHERE st.ends > :startTime1
  AND st.starts < :endTime
  AND st.playout_status > 0
  AND si.ends > :startTime2
  AND si.modified_instance = 'f'
ORDER BY st.starts
SQL;

        $sql = $baseQuery." ".$predicates;

        $rows = Application_Common_Database::prepareAndExecute($sql, array(
            ':startTime1' => $p_startTime,
            ':endTime'    => $p_endTime,
            ':startTime2' => $p_startTime));

        if (count($rows) < 3) {
            $dt = new DateTime("@".time());
            $dt->add(new DateInterval("PT24H"));
            $range_end = $dt->format("Y-m-d H:i:s");

            $predicates = <<<SQL
WHERE st.ends > :startTime1
  AND st.starts < :rangeEnd
  AND st.playout_status > 0
  AND si.ends > :startTime2
  AND si.modified_instance = 'f'
ORDER BY st.starts LIMIT 3
SQL;

            $sql = $baseQuery." ".$predicates." ";
            $rows = Application_Common_Database::prepareAndExecute($sql,
                array(
                    ':startTime1' => $p_startTime,
                    ':rangeEnd'   => $range_end,
                    ':startTime2' => $p_startTime));
        }

        return $rows;
    }

    /**
     * This function will ensure that an existing index in the
     * associative array is never overwritten, instead appending
     * _0, _1, _2, ... to the end of the key to make sure it is unique
     */
    private static function appendScheduleItem(&$data, $time, $item)
    {
        $key = $time;
        $i = 0;

        while (array_key_exists($key, $data["media"])) {
            $key = "{$time}_{$i}";
            $i++;
        }

        $data["media"][$key] = $item;
    }

    private static function createInputHarborKickTimes(&$data, $range_start, $range_end)
    {
        $utcTimeZone = new DateTimeZone("UTC");
        $kick_times = Application_Model_ShowInstance::GetEndTimeOfNextShowWithLiveDJ($range_start, $range_end);
        foreach ($kick_times as $kick_time_info) {
            $kick_time = $kick_time_info['ends'];
            $temp = explode('.', Application_Model_Preference::GetDefaultTransitionFade());
            // we round down transition time since PHP cannot handle millisecond. We need to
            // handle this better in the future
            $transition_time   = intval($temp[0]);
            $switchOffDataTime = new DateTime($kick_time, $utcTimeZone);
            $switch_off_time   = $switchOffDataTime->sub(new DateInterval('PT'.$transition_time.'S'));
            $switch_off_time   = $switch_off_time->format("Y-m-d H:i:s");

            $kick_start = self::AirtimeTimeToPypoTime($kick_time);
            $data["media"][$kick_start]['start']             = $kick_start;
            $data["media"][$kick_start]['end']               = $kick_start;
            $data["media"][$kick_start]['event_type']        = "kick_out";
            $data["media"][$kick_start]['type']              = "event";
            $data["media"][$kick_start]['independent_event'] = true;

            if ($kick_time !== $switch_off_time) {
                $switch_start = self::AirtimeTimeToPypoTime($switch_off_time);
                $data["media"][$switch_start]['start']             = $switch_start;
                $data["media"][$switch_start]['end']               = $switch_start;
                $data["media"][$switch_start]['event_type']        = "switch_off";
                $data["media"][$switch_start]['type']                = "event";
                $data["media"][$switch_start]['independent_event'] = true;
            }
        }
    }

    private static function createFileScheduleEvent(&$data, $item, $media_id, $uri)
    {
        $start = self::AirtimeTimeToPypoTime($item["start"]);
        $end   = self::AirtimeTimeToPypoTime($item["end"]);

        list(,,,$start_hour,,) = explode("-", $start);
        list(,,,$end_hour,,) = explode("-", $end);

        $same_hour = $start_hour == $end_hour;
        $independent_event = !$same_hour;

        $replay_gain = is_null($item["replay_gain"]) ? "0": $item["replay_gain"];
        $replay_gain += Application_Model_Preference::getReplayGainModifier();

        if ( !Application_Model_Preference::GetEnableReplayGain() ) {
            $replay_gain = 0;
        }

        $schedule_item = array(
            'id'                => $media_id,
            'type'              => 'file',
            'row_id'            => $item["id"],
            'uri'               => $uri,
            'fade_in'           => Application_Model_Schedule::WallTimeToMillisecs($item["fade_in"]),
            'fade_out'          => Application_Model_Schedule::WallTimeToMillisecs($item["fade_out"]),
            'cue_in'            => Application_Common_DateHelper::CalculateLengthInSeconds($item["cue_in"]),
            'cue_out'           => Application_Common_DateHelper::CalculateLengthInSeconds($item["cue_out"]),
            'start'             => $start,
            'end'               => $end,
            'show_name'         => $item["show_name"],
            'replay_gain'       => $replay_gain,
            'independent_event' => $independent_event,
        );

        if ($schedule_item['cue_in'] > $schedule_item['cue_out']) {
            $schedule_item['cue_in'] = $schedule_item['cue_out'];
        }
        self::appendScheduleItem($data, $start, $schedule_item);
    }

    private static function createStreamScheduleEvent(&$data, $item, $media_id, $uri)
    {
        $start = self::AirtimeTimeToPypoTime($item["start"]);
        $end   = self::AirtimeTimeToPypoTime($item["end"]);

        //create an event to start stream buffering 5 seconds ahead of the streams actual time.
        $buffer_start = new DateTime($item["start"], new DateTimeZone('UTC'));
        $buffer_start->sub(new DateInterval("PT5S"));

        $stream_buffer_start = self::AirtimeTimeToPypoTime($buffer_start->format("Y-m-d H:i:s"));

        $schedule_item = array(
            'start'             => $stream_buffer_start,
            'end'               => $stream_buffer_start,
            'uri'               => $uri,
            'row_id'            => $item["id"],
            'type'              => 'stream_buffer_start',
            'independent_event' => true
        );

        self::appendScheduleItem($data, $start, $schedule_item);

        $schedule_item = array(
            'id'                => $media_id,
            'type'              => 'stream_output_start',
            'row_id'            => $item["id"],
            'uri'               => $uri,
            'start'             => $start,
            'end'               => $end,
            'show_name'         => $item["show_name"],
            'independent_event' => true
        );
        self::appendScheduleItem($data, $start, $schedule_item);

        //since a stream never ends we have to insert an additional "kick stream" event. The "start"
        //time of this event is the "end" time of the stream minus 1 second.
        $dt = new DateTime($item["end"], new DateTimeZone('UTC'));
        $dt->sub(new DateInterval("PT1S"));

        $stream_end = self::AirtimeTimeToPypoTime($dt->format("Y-m-d H:i:s"));

        $schedule_item = array(
            'start'             => $stream_end,
            'end'               => $stream_end,
            'uri'               => $uri,
            'type'              => 'stream_buffer_end',
            'row_id'            => $item["id"],
            'independent_event' => true
        );
        self::appendScheduleItem($data, $stream_end, $schedule_item);

        $schedule_item = array(
            'start'             => $stream_end,
            'end'               => $stream_end,
            'uri'               => $uri,
            'type'              => 'stream_output_end',
            'independent_event' => true
        );
        self::appendScheduleItem($data, $stream_end, $schedule_item);
    }

    private static function getRangeStartAndEnd($p_fromDateTime, $p_toDateTime)
    {
        $CC_CONFIG = Config::getConfig();

        $utcTimeZone = new DateTimeZone('UTC');
        
        /* if $p_fromDateTime and $p_toDateTime function parameters are null,
            then set range * from "now" to "now + 24 hours". */
        if (is_null($p_fromDateTime)) {
            $t1 = new DateTime("@".time(), $utcTimeZone);
            $range_start = $t1->format("Y-m-d H:i:s");
        } else {
            $range_start = Application_Model_Schedule::PypoTimeToAirtimeTime($p_fromDateTime);
        }
        if (is_null($p_fromDateTime)) {
            $t2 = new DateTime("@".time(), $utcTimeZone);

            $cache_ahead_hours = $CC_CONFIG["cache_ahead_hours"];

            if (is_numeric($cache_ahead_hours)) {
                //make sure we are not dealing with a float
                $cache_ahead_hours = intval($cache_ahead_hours);
            } else {
                $cache_ahead_hours = 1;
            }

            $t2->add(new DateInterval("PT".$cache_ahead_hours."H"));
            $range_end = $t2->format("Y-m-d H:i:s");
        } else {
            $range_end = Application_Model_Schedule::PypoTimeToAirtimeTime($p_toDateTime);
        }

        return array($range_start, $range_end);
    }


    private static function createScheduledEvents(&$data, $range_start, $range_end)
    {
        $utcTimeZone = new DateTimeZone("UTC");
        $items = self::getItems($range_start, $range_end);

        foreach ($items as $item) {
            $showEndDateTime = new DateTime($item["show_end"], $utcTimeZone);

            $trackStartDateTime = new DateTime($item["start"], $utcTimeZone);
            $trackEndDateTime = new DateTime($item["end"], $utcTimeZone);

            if ($trackStartDateTime->getTimestamp() > $showEndDateTime->getTimestamp()) {
                //do not send any tracks that start past their show's end time
                continue;
            }

            if ($trackEndDateTime->getTimestamp() > $showEndDateTime->getTimestamp()) {
                $di = $trackStartDateTime->diff($showEndDateTime);

                $item["cue_out"] = $di->format("%H:%i:%s").".000";
                $item["end"] = $showEndDateTime->format("Y-m-d H:i:s");
            }

            if (!is_null($item['file_id'])) {
                //row is from "file"
                $media_id = $item['file_id'];
                $storedFile = Application_Model_StoredFile::RecallById($media_id);
                $uri = $storedFile->getFilePath();
                self::createFileScheduleEvent($data, $item, $media_id, $uri);
            } 
            elseif (!is_null($item['stream_id'])) {
                //row is type "webstream"
                $media_id = $item['stream_id'];
                $uri = $item['url'];
                self::createStreamScheduleEvent($data, $item, $media_id, $uri);
            } 
            else {
                throw new Exception("Unknown schedule type: ".print_r($item, true));
            }

        }
    }

    /* Check if two events are less than or equal to 1 second apart
     */
    public static function areEventsLinked($event1, $event2) {
        $dt1 = DateTime::createFromFormat("Y-m-d-H-i-s", $event1['start']);
        $dt2 = DateTime::createFromFormat("Y-m-d-H-i-s", $event2['start']);

        $seconds = $dt2->getTimestamp() - $dt1->getTimestamp();
        return $seconds <= 1;
    }

    /**
     * Streams are a 4 stage process.
     * 1) start buffering stream 5 seconds ahead of its start time
     * 2) at the start time tell liquidsoap to switch to this source
     * 3) at the end time, tell liquidsoap to stop reading this stream
     * 4) at the end time, tell liquidsoap to switch away from input.http source.
     *
     * When we have two streams back-to-back, some of these steps are unnecessary
     * for the second stream. Instead of sending commands 1,2,3,4,1,2,3,4 we should
     * send 1,2,1,2,3,4 - We don't need to tell liquidsoap to stop reading (#3), because #1
     * of the next stream implies this when we pass in a new url. We also don't need #4.
     *
     * There's a special case here is well. When the back-to-back streams are the same, we
     * can collapse the instructions 1,2,(3,4,1,2),3,4 to 1,2,3,4. We basically cut out the
     * middle part. This function handles this.
     */
    private static function foldData(&$data)
    {
        $previous_key = null;
        $previous_val = null;
        $previous_previous_key = null;
        $previous_previous_val = null;
        $previous_previous_previous_key = null;
        $previous_previous_previous_val = null;
        foreach ($data as $k => $v) {

            if ($v["type"] == "stream_output_start"
                && !is_null($previous_previous_val)
                && $previous_previous_val["type"] == "stream_output_end"
                && self::areEventsLinked($previous_previous_val, $v)) {

                unset($data[$previous_previous_previous_key]);
                unset($data[$previous_previous_key]);
                unset($data[$previous_key]);
                if ($previous_previous_val['uri'] == $v['uri']) {
                    unset($data[$k]);
                }
            }

            $previous_previous_previous_key = $previous_previous_key;
            $previous_previous_previous_val = $previous_previous_val;
            $previous_previous_key = $previous_key;
            $previous_previous_val = $previous_val;
            $previous_key = $k;
            $previous_val = $v;
        }
    }

    public static function getSchedule($p_fromDateTime = null, $p_toDateTime = null)
    {
        //generate repeating shows if we are fetching the schedule
        //for days beyond the shows_populated_until value in cc_pref
        $needScheduleUntil = $p_toDateTime;
        if (is_null($needScheduleUntil)) {
            $needScheduleUntil = new DateTime("now", new DateTimeZone("UTC"));
            $needScheduleUntil->add(new DateInterval("P1D"));
        }
        Application_Model_Show::createAndFillShowInstancesPastPopulatedUntilDate($needScheduleUntil);
        
        list($range_start, $range_end) = self::getRangeStartAndEnd($p_fromDateTime, $p_toDateTime);

        $data = array();
        $data["media"] = array();

        //Harbor kick times *MUST* be ahead of schedule events, so that pypo
        //executes them first.
        self::createInputHarborKickTimes($data, $range_start, $range_end);
        self::createScheduledEvents($data, $range_start, $range_end);

        //self::foldData($data["media"]);
        return $data;
    }

    public static function deleteAll()
    {
        $sql = "TRUNCATE TABLE cc_schedule";
        Application_Common_Database::prepareAndExecute($sql, array(),
            Application_Common_Database::EXECUTE);
    }

    public static function deleteWithFileId($fileId)
    {
        $sql = "DELETE FROM cc_schedule WHERE file_id=:file_id";
        Application_Common_Database::prepareAndExecute($sql, array(':file_id'=>$fileId), 'execute');
    }

    public static function checkOverlappingShows($show_start, $show_end,
        $update=false, $instanceId=null, $showId=null)
    {
        //if the show instance does not exist or was deleted, return false
        if (!is_null($showId)) {
            $ccShowInstance = CcShowInstancesQuery::create()
                ->filterByDbShowId($showId)
                ->filterByDbStarts($show_start->format("Y-m-d H:i:s"))
                ->findOne();
        } elseif (!is_null($instanceId)) {
            $ccShowInstance = CcShowInstancesQuery::create()
                ->filterByDbId($instanceId)
                ->findOne();
        }
        if ($update && ($ccShowInstance && $ccShowInstance->getDbModifiedInstance() == true)) {
            return false;
        }

        $overlapping = false;

        $params = array(
            ':show_end1'  => $show_end->format('Y-m-d H:i:s'),
            ':show_end2'  => $show_end->format('Y-m-d H:i:s'),
            ':show_end3'  => $show_end->format('Y-m-d H:i:s')
        );


        /* If a show is being edited, exclude it from the query
         * In both cases (new and edit) we only grab shows that
         * are scheduled 2 days prior
         */
        if ($update) {
            $sql = <<<SQL
SELECT id,
       starts,
       ends
FROM cc_show_instances
WHERE (ends <= :show_end1
       OR starts <= :show_end2)
  AND date(starts) >= (date(:show_end3) - INTERVAL '2 days')
  AND modified_instance = FALSE
SQL;
            if (is_null($showId)) {
                $sql .= <<<SQL
  AND id != :instanceId
ORDER BY ends
SQL;
                $params[':instanceId'] = $instanceId;
            } else {
                $sql .= <<<SQL
  AND show_id != :showId
ORDER BY ends
SQL;
                $params[':showId'] = $showId;
            }
            $rows = Application_Common_Database::prepareAndExecute($sql, $params, 'all');
        } else {
            $sql = <<<SQL
SELECT id,
       starts,
       ends
FROM cc_show_instances
WHERE (ends <= :show_end1
       OR starts <= :show_end2)
  AND date(starts) >= (date(:show_end3) - INTERVAL '2 days')
  AND modified_instance = FALSE
ORDER BY ends
SQL;

            $rows = Application_Common_Database::prepareAndExecute($sql, array(
                ':show_end1' => $show_end->format('Y-m-d H:i:s'),
                ':show_end2' => $show_end->format('Y-m-d H:i:s'),
                ':show_end3' => $show_end->format('Y-m-d H:i:s')), 'all');
        }

        foreach ($rows as $row) {
            $start = new DateTime($row["starts"], new DateTimeZone('UTC'));
            $end   = new DateTime($row["ends"], new DateTimeZone('UTC'));

            if ($show_start->getTimestamp() < $end->getTimestamp() &&
                $show_end->getTimestamp() > $start->getTimestamp()) {
                $overlapping = true;
                break;
            }
        }

        return $overlapping;
    }

    public static function GetType($p_scheduleId){
        $scheduledItem = CcScheduleQuery::create()->findPK($p_scheduleId);
        if ($scheduledItem->getDbFileId() == null) {
            return 'webstream';
        } else {
            return 'file';
        }
    }

    public static function GetFileId($p_scheduleId)
    {
        $scheduledItem = CcScheduleQuery::create()->findPK($p_scheduleId);

        return $scheduledItem->getDbFileId();
    }

    public static function GetStreamId($p_scheduleId)
    {
        $scheduledItem = CcScheduleQuery::create()->findPK($p_scheduleId);

        return $scheduledItem->getDbStreamId();
    }
}
