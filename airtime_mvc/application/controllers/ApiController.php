<?php

class ApiController extends Zend_Controller_Action
{

    public function init()
    {
        $ignoreAuth = array("live-info", 
            "live-info-v2", 
            "week-info", 
            "station-metadata", 
            "station-logo",
            "show-history-feed", 
            "item-history-feed",
            "shows",
            "show-tracks",
            "show-schedules"
        );

        $params = $this->getRequest()->getParams();
        if (!in_array($params['action'], $ignoreAuth)) {
            $this->checkAuth();
        }
        /* Initialize action controller here */
        $context = $this->_helper->getHelper('contextSwitch');
        $context->addActionContext('version'                       , 'json')
                ->addActionContext('recorded-shows'                , 'json')
                ->addActionContext('calendar-init'                 , 'json')
                ->addActionContext('upload-file'                   , 'json')
                ->addActionContext('upload-recorded'               , 'json')
                ->addActionContext('media-monitor-setup'           , 'json')
                ->addActionContext('media-item-status'             , 'json')
                ->addActionContext('reload-metadata'               , 'json')
                ->addActionContext('list-all-files'                , 'json')
                ->addActionContext('list-all-watched-dirs'         , 'json')
                ->addActionContext('add-watched-dir'               , 'json')
                ->addActionContext('remove-watched-dir'            , 'json')
                ->addActionContext('set-storage-dir'               , 'json')
                ->addActionContext('get-stream-setting'            , 'json')
                ->addActionContext('status'                        , 'json')
                ->addActionContext('register-component'            , 'json')
                ->addActionContext('update-liquidsoap-status'      , 'json')
                ->addActionContext('live-chat'                     , 'json')
                ->addActionContext('update-file-system-mount'      , 'json')
                ->addActionContext('handle-watched-dir-missing'    , 'json')
                ->addActionContext('rabbitmq-do-push'              , 'json')
                ->addActionContext('check-live-stream-auth'        , 'json')
                ->addActionContext('update-source-status'          , 'json')
                ->addActionContext('get-bootstrap-info'            , 'json')
                ->addActionContext('get-files-without-replay-gain' , 'json')
                ->addActionContext('get-files-without-silan-value' , 'json')
                ->addActionContext('reload-metadata-group'         , 'json')
                ->addActionContext('notify-webstream-data'         , 'json')
                ->addActionContext('get-stream-parameters'         , 'json')
                ->addActionContext('push-stream-stats'             , 'json')
                ->addActionContext('update-stream-setting-table'   , 'json')
                ->addActionContext('update-replay-gain-value'      , 'json')
                ->addActionContext('update-cue-values-by-silan'    , 'json')
                ->initContext();
    }

    public function checkAuth()
    {
        $CC_CONFIG = Config::getConfig();
        $api_key = $this->_getParam('api_key');

        if (!in_array($api_key, $CC_CONFIG["apiKey"]) &&
            is_null(Zend_Auth::getInstance()->getStorage()->read())) {
            header('HTTP/1.0 401 Unauthorized');
            print _('You are not allowed to access this resource.');
            exit;
        }
    }

    public function versionAction()
    {
        $this->_helper->json->sendJson( array(
            "airtime_version" => Application_Model_Preference::GetAirtimeVersion(),
            "api_version" => AIRTIME_API_VERSION));
    }

    /**
     * Allows remote client to download requested media file.
     *
     * @return void
     *
     */
    public function getMediaAction()
    {
        $fileId = $this->_getParam("file");

        $media = Application_Model_StoredFile::RecallById($fileId);
        if ($media != null) {

            $filepath = $media->getFilePath();
            // Make sure we don't have some wrong result beecause of caching
            clearstatcache();
            if (is_file($filepath)) {
                $full_path = $media->getPropelOrm()->getDbFilepath();

                $file_base_name = strrchr($full_path, '/');
                /* If $full_path does not contain a '/', strrchr will return false,
                 * in which case we can use $full_path as the base name.
                 */
                if (!$file_base_name) {
                    $file_base_name = $full_path;
                } else {
                    $file_base_name = substr($file_base_name, 1);
                }

                //Download user left clicks a track and selects Download.
                if ("true" == $this->_getParam('download')) {
                    //path_info breaks up a file path into seperate pieces of informaiton.
                    //We just want the basename which is the file name with the path
                    //information stripped away. We are using Content-Disposition to specify
                    //to the browser what name the file should be saved as.
                    header('Content-Disposition: attachment; filename="'.$file_base_name.'"');
                } else {
                    //user clicks play button for track and downloads it.
                    header('Content-Disposition: inline; filename="'.$file_base_name.'"');
                }

                $this->smartReadFile($filepath, $media->getPropelOrm()->getDbMime());
                exit;
            } else {
                header ("HTTP/1.1 404 Not Found");
            }
        }

        $this->_helper->json->sendJson(array());
    }

    /**
    * Reads the requested portion of a file and sends its contents to the client with the appropriate headers.
    *
    * This HTTP_RANGE compatible read file function is necessary for allowing streaming media to be skipped around in.
    *
    * @param string $location
    * @param string $mimeType
    * @return void
    *
    * @link https://groups.google.com/d/msg/jplayer/nSM2UmnSKKA/Hu76jDZS4xcJ
    * @link http://php.net/manual/en/function.readfile.php#86244
    */
    public function smartReadFile($location, $mimeType = 'audio/mp3')
    {
        $size= filesize($location);
        $time= date('r', filemtime($location));

        $fm = @fopen($location, 'rb');
        if (!$fm) {
            header ("HTTP/1.1 505 Internal server error");

            return;
        }

        $begin = 0;
        $end   = $size - 1;

        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }
        }

        if (isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP/1.1 206 Partial Content');
        } else {
            header('HTTP/1.1 200 OK');
        }
        header("Content-Type: $mimeType");
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length:' . (($end - $begin) + 1));
        if (isset($_SERVER['HTTP_RANGE'])) {
            header("Content-Range: bytes $begin-$end/$size");
        }
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: $time");

        //We can have multiple levels of output buffering. Need to
        //keep looping until all have been disabled!!!
        //http://www.php.net/manual/en/function.ob-end-flush.php
        while (@ob_end_flush());

        $cur = $begin;
        fseek($fm, $begin, 0);

        while (!feof($fm) && $cur <= $end && (connection_status() == 0)) {
            echo  fread($fm, min(1024 * 16, ($end - $cur) + 1));
            $cur += 1024 * 16;
        }
    }

    //Used by the SaaS monitoring
    public function onAirLightAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $result = array();
        $result["on_air_light"] = false;
        $result["on_air_light_expected_status"] = false;
        $result["station_down"] = false;

        $range = Application_Model_Schedule::GetPlayOrderRange();

        $isItemCurrentlyScheduled = !is_null($range["current"]) && count($range["currentShow"]) > 0 ? true : false;

        $isCurrentItemPlaying = $range["current"]["media_item_played"] ? true : false;

        if ($isItemCurrentlyScheduled ||
            Application_Model_Preference::GetSourceSwitchStatus("live_dj") == "on" ||
            Application_Model_Preference::GetSourceSwitchStatus("master_dj") == "on")
        {
            $result["on_air_light_expected_status"] = true;
        }

        if (($isItemCurrentlyScheduled && $isCurrentItemPlaying) ||
            Application_Model_Preference::GetSourceSwitchStatus("live_dj") == "on" ||
            Application_Model_Preference::GetSourceSwitchStatus("master_dj") == "on")
        {
            $result["on_air_light"] = true;
        }

        if ($result["on_air_light_expected_status"] != $result["on_air_light"]) {
            $result["station_down"] = true;
        }

        echo isset($_GET['callback']) ? $_GET['callback'].'('.json_encode($result).')' : json_encode($result);
    }

    /**
     * Retrieve the currently playing show as well as upcoming shows.
     * Number of shows returned and the time interval in which to
     * get the next shows can be configured as GET parameters.
     *
     * TODO: in the future, make interval length a parameter instead of hardcode to 48
     *
     * Possible parameters:
     * type - Can have values of "endofday" or "interval". If set to "endofday",
     *        the function will retrieve shows from now to end of day.
     *        If set to "interval", shows in the next 48 hours will be retrived.
     *        Default is "interval".
     * limit - How many shows to retrieve
     *         Default is "5".
     */
    public function liveInfoAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
    
            $request = $this->getRequest();
    
            $utcTimeNow = gmdate("Y-m-d H:i:s");
            $utcTimeEnd = "";   // if empty, getNextShows will use interval instead of end of day
    
            // default to the station timezone
            $timezone = Application_Model_Preference::GetDefaultTimezone();
            $userDefinedTimezone = strtolower($request->getParam('timezone'));
            $upcase = false; // only upcase the timezone abbreviations
            $this->checkTimezone($userDefinedTimezone, $timezone, $upcase);
    
            $type = $request->getParam('type');
            $limit = $request->getParam('limit');
            if ($limit == "" || !is_numeric($limit)) {
                $limit = "5";
            }
            /* This is some *extremely* lazy programming that needs to be fixed. For some reason
             * we are using two entirely different codepaths for very similar functionality (type = endofday
             * vs type = interval). Needs to be fixed for 2.3 - MK */
            if ($type == "endofday") {
    
                // make getNextShows use end of day
                $end = Application_Common_DateHelper::getTodayStationEndDateTime();
                $end->setTimezone(new DateTimeZone("UTC"));
                $utcTimeEnd = $end->format("Y-m-d H:i:s");
                
                $result = array(
                    "env" => APPLICATION_ENV,
                    "schedulerTime" => $utcTimeNow,
                    "currentShow" => Application_Model_Show::getCurrentShow($utcTimeNow),
                    "nextShow" => Application_Model_Show::getNextShows($utcTimeNow, $limit, $utcTimeEnd)
                );
            } else {
                $result = Application_Model_Schedule::GetPlayOrderRangeOld($limit);
            }
    
            // XSS exploit prevention
            $this->convertSpecialChars($result, array("name", "url"));
            // apply user-defined timezone, or default to station
            Application_Common_DateHelper::convertTimestampsToTimezone(
                $result['currentShow'],
                array("starts", "ends", "start_timestamp","end_timestamp"),
                $timezone
            );
            Application_Common_DateHelper::convertTimestampsToTimezone(
                $result['nextShow'],
                array("starts", "ends", "start_timestamp","end_timestamp"),
                $timezone
            );
            
            //Convert the UTC scheduler time ("now") to the user-defined timezone.
            $result["schedulerTime"] = Application_Common_DateHelper::UTCStringToTimezoneString($result["schedulerTime"], $timezone);
            $result["timezone"] = $upcase ? strtoupper($timezone) : $timezone;
            $result["timezoneOffset"] = Application_Common_DateHelper::getTimezoneOffset($timezone);
    
            // used by caller to determine if the airtime they are running or widgets in use is out of date.
            $result['AIRTIME_API_VERSION'] = AIRTIME_API_VERSION;
            header("Content-Type: application/json");
    
            if (version_compare(phpversion(), '5.4.0', '<')) {
                $js = json_encode($result);
            } else {
                $js = json_encode($result, JSON_PRETTY_PRINT);
            }
            // If a callback is not given, then just provide the raw JSON.
            echo isset($_GET['callback']) ? $_GET['callback'].'('.$js.')' : $js;
        } else {
            header('HTTP/1.0 401 Unauthorized');
            print _('You are not allowed to access this resource. ');
            exit;
        }
    }
    
    /**
     * Retrieve the currently playing show as well as upcoming shows.
     * Number of shows returned and the time interval in which to
     * get the next shows can be configured as GET parameters.
     *
     * Possible parameters:
     * days - How many days to retrieve.
     *        Default is 2 (today + tomorrow).
     * shows - How many shows to retrieve
     *         Default is 5.
     * timezone - The timezone to send the times in
     *            Defaults to the station timezone
     */
    public function liveInfoV2Action()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
    
            $request = $this->getRequest();
    
            $utcTimeNow = gmdate("Y-m-d H:i:s");
            $utcTimeEnd = "";   // if empty, getNextShows will use interval instead of end of day
    
            // default to the station timezone
            $timezone = Application_Model_Preference::GetDefaultTimezone();
            $userDefinedTimezone = strtolower($request->getParam('timezone'));
            $upcase = false; // only upcase the timezone abbreviations
            $this->checkTimezone($userDefinedTimezone, $timezone, $upcase);
    
            $daysToRetrieve = $request->getParam('days');
            $showsToRetrieve = $request->getParam('shows');
            if ($daysToRetrieve == "" || !is_numeric($daysToRetrieve)) {
                $daysToRetrieve = "2";
            }
            if ($showsToRetrieve == "" || !is_numeric($showsToRetrieve)) {
                $showsToRetrieve = "5";
            }
    
            // set the end time to the day's start n days from now.
            // days=1 will return shows until the end of the current day,
            // days=2 will return shows until the end of tomorrow, etc.
            $end = Application_Common_DateHelper::getEndDateTime($timezone, $daysToRetrieve);
            $end->setTimezone(new DateTimeZone("UTC"));
            $utcTimeEnd = $end->format("Y-m-d H:i:s");
    
            $result = Application_Model_Schedule::GetPlayOrderRange($utcTimeEnd, $showsToRetrieve);
    
            // XSS exploit prevention
            $this->convertSpecialChars($result, array("name", "url"));
            // apply user-defined timezone, or default to station
            $this->applyLiveTimezoneAdjustments($result, $timezone, $upcase);
    
            // used by caller to determine if the airtime they are running or widgets in use is out of date.
            $result["station"]["AIRTIME_API_VERSION"] = AIRTIME_API_VERSION;
            header("Content-Type: application/json");
    
            if (version_compare(phpversion(), '5.4.0', '<')) {
                $js = json_encode($result);
            } else {
                $js = json_encode($result, JSON_PRETTY_PRINT);
            }
            // If a callback is not given, then just provide the raw JSON.
            echo isset($_GET['callback']) ? $_GET['callback'].'('.$js.')' : $js;
        } else {
            header('HTTP/1.0 401 Unauthorized');
            print _('You are not allowed to access this resource. ');
            exit;
        }
    }
    
    /**
     * Check that the value for the timezone the user gave is valid.
     * If it is, override the default (station) timezone.
     * If it's an abbreviation (pst, edt) we upcase the output.
     *
     * @param string    $userDefinedTimezone    the requested timezone value
     * @param string    $timezone               the default timezone
     * @param boolean   $upcase                 whether the timezone output should be upcased
     */
    private function checkTimezone($userDefinedTimezone, &$timezone, &$upcase)
    {
        $delimiter = "/";
        // if the user passes in a timezone in standard form ("Continent/City")
        // we need to fix the downcased string by upcasing each word delimited by a /
        if (strpos($userDefinedTimezone, $delimiter) !== false) {
            $userDefinedTimezone = implode($delimiter, array_map('ucfirst', explode($delimiter, $userDefinedTimezone)));
        }
        // if the timezone defined by the user exists, use that
        if (array_key_exists($userDefinedTimezone, timezone_abbreviations_list())) {
            $timezone = $userDefinedTimezone;
            $upcase = true;
        } else if (in_array($userDefinedTimezone, timezone_identifiers_list())) {
            $timezone = $userDefinedTimezone;
        }
    }
    
    /**
     * If the user passed in a timezone parameter, adjust timezone-dependent
     * variables in the result to reflect the given timezone.
     *
     * @param object    $result     reference to the object to send back to the user
     * @param string    $timezone   the user's timezone parameter value
     * @param boolean   $upcase     whether the timezone output should be upcased
     */
    private function applyLiveTimezoneAdjustments(&$result, $timezone, $upcase)
    {
        Application_Common_DateHelper::convertTimestampsToTimezone(
        $result,
        array("starts", "ends", "start_timestamp","end_timestamp"),
        $timezone
        );
    
        //Convert the UTC scheduler time ("now") to the user-defined timezone.
        $result["station"]["schedulerTime"] = Application_Common_DateHelper::UTCStringToTimezoneString($result["station"]["schedulerTime"], $timezone);
        $result["station"]["timezone"] = $upcase ? strtoupper($timezone) : $timezone;
    }

    public function weekInfoAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            //weekStart is in station time.
            $weekStartDateTime = Application_Common_DateHelper::getWeekStartDateTime();
            
            $dow = array("monday", "tuesday", "wednesday", "thursday", "friday",
                        "saturday", "sunday", "nextmonday", "nexttuesday", "nextwednesday",
                        "nextthursday", "nextfriday", "nextsaturday", "nextsunday");

            $result = array();
            
            // default to the station timezone
            $timezone = Application_Model_Preference::GetDefaultTimezone();
            $userDefinedTimezone = strtolower($this->getRequest()->getParam("timezone"));
            // if the timezone defined by the user exists, use that
            if (array_key_exists($userDefinedTimezone, timezone_abbreviations_list())) {
                $timezone = $userDefinedTimezone;
            }            
            $utcTimezone = new DateTimeZone("UTC");
            
            $weekStartDateTime->setTimezone($utcTimezone);
            $utcDayStart = $weekStartDateTime->format("Y-m-d H:i:s");
            for ($i = 0; $i < 14; $i++) {
                
                //have to be in station timezone when adding 1 day for daylight savings.
                $weekStartDateTime->setTimezone(new DateTimeZone($timezone));
                $weekStartDateTime->add(new DateInterval('P1D'));
                
                //convert back to UTC to get the actual timestamp used for search.
                $weekStartDateTime->setTimezone($utcTimezone);
                
                $utcDayEnd = $weekStartDateTime->format("Y-m-d H:i:s");
                $shows = Application_Model_Show::getNextShows($utcDayStart, "ALL", $utcDayEnd);
                $utcDayStart = $utcDayEnd;
                
                // convert to user-defined timezone, or default to station
                Application_Common_DateHelper::convertTimestampsToTimezone(
                    $shows,
                    array("starts", "ends", "start_timestamp","end_timestamp"),
                    $timezone
                );

                $result[$dow[$i]] = $shows;
            }

            // XSS exploit prevention
            $this->convertSpecialChars($result, array("name", "url"));
            
            //used by caller to determine if the airtime they are running or widgets in use is out of date.
            $result['AIRTIME_API_VERSION'] = AIRTIME_API_VERSION;
            header("Content-type: text/javascript");
            
            $js = json_encode($result, JSON_PRETTY_PRINT);
            // If a callback is not given, then just provide the raw JSON.
            echo isset($_GET['callback']) ? $_GET['callback'].'('.$js.')' : $js;
        } else {
            header('HTTP/1.0 401 Unauthorized');
            print _('You are not allowed to access this resource. ');
            exit;
        }
    }

    public function scheduleAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        header("Content-Type: application/json");

        $data = Application_Model_Schedule::getSchedule();

        echo json_encode($data, JSON_FORCE_OBJECT);
    }

    public function notifyMediaItemStartPlayAction()
    {
        $media_id = $this->_getParam("media_id");
        Logging::debug("Received notification of new media item start: $media_id");
        Application_Model_Schedule::UpdateMediaPlayedStatus($media_id);

        try {
            $historyService = new Application_Service_HistoryService();
            $historyService->insertPlayedItem($media_id);

            //set a 'last played' timestamp for media item
            //needed for smart blocks
            $mediaType = Application_Model_Schedule::GetType($media_id);
            if ($mediaType == 'file') {
                $file_id = Application_Model_Schedule::GetFileId($media_id);
                if (!is_null($file_id)) {
                    //we are dealing with a file not a stream
                    $file = Application_Model_StoredFile::RecallById($file_id);
                    $now = new DateTime("now", new DateTimeZone("UTC"));
                    $file->setLastPlayedTime($now);
                }
            } else {
                // webstream
                $stream_id = Application_Model_Schedule::GetStreamId($media_id);
                if (!is_null($stream_id)) {
                    $webStream = new Application_Model_Webstream($stream_id);
                    $now = new DateTime("now", new DateTimeZone("UTC"));
                    $webStream->setLastPlayed($now);
                }
            }
        } catch (Exception $e) {
            Logging::info($e);
        }

        $this->_helper->json->sendJson(array("status"=>1, "message"=>""));
    }
    
    /**
     * Go through a given array and sanitize any potentially exploitable fields
     * by passing them through htmlspecialchars
     *
     * @param unknown $arr  the array to sanitize
     * @param unknown $keys indexes of values to be sanitized
     */
    private function convertSpecialChars(&$arr, $keys)
    {
        foreach ($arr as &$a) {
            if (is_array($a)) {
                foreach ($keys as &$key) {
                    if (array_key_exists($key, $a)) {
                        $a[$key] = htmlspecialchars($a[$key]);
                    }
                }
                $this->convertSpecialChars($a, $keys);
            }
        }
    }
    
    /**
     * API endpoint to provide station metadata
     */
    public function stationMetadataAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
    
            $CC_CONFIG = Config::getConfig();
            $baseDir = Application_Common_OsPath::formatDirectoryWithDirectorySeparators($CC_CONFIG['baseDir']);
            $path = 'http://'.$_SERVER['HTTP_HOST'].$baseDir."api/station-logo";
    
            $result["name"] = Application_Model_Preference::GetStationName();
            $result["logo"] = $path;
            $result["description"] = Application_Model_Preference::GetStationDescription();
            $result["timezone"] = Application_Model_Preference::GetDefaultTimezone();
            $result["locale"] = Application_Model_Preference::GetDefaultLocale();
    
            // used by caller to determine if the airtime they are running or widgets in use is out of date.
            $result['AIRTIME_API_VERSION'] = AIRTIME_API_VERSION;
            header("Content-type: text/javascript");
    
            $js = json_encode($result, JSON_PRETTY_PRINT);
            // If a callback is not given, then just provide the raw JSON.
            echo isset($_GET['callback']) ? $_GET['callback'].'('.$js.')' : $js;
        } else {
            header('HTTP/1.0 401 Unauthorized');
            print _('You are not allowed to access this resource. ');
            exit;
        }
    }
    
    /**
     * API endpoint to display the current station logo
     */
    public function stationLogoAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
    
            $logo = Application_Model_Preference::GetStationLogo();
            // if there's no logo, just die - redirects to a 404
            if (!$logo || $logo === '') {
                return;
            }
    
            // we're passing this as an image instead of using it in a data uri, so decode it
            $blob = base64_decode($logo);
    
            // use finfo to get the mimetype from the decoded blob
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $blob, FILEINFO_MIME_TYPE);
            finfo_close($f);
    
            header("Content-type: " . $mime_type);
            echo $blob;
        } else {
            header('HTTP/1.0 401 Unauthorized');
            print _('You are not allowed to access this resource. ');
            exit;
        }
    }

    public function recordedShowsAction()
    {
        $utcTimezone = new DateTimeZone("UTC");
        $nowDateTime = new DateTime("now", $utcTimezone);
        $endDateTime = clone $nowDateTime;
        $endDateTime = $endDateTime->add(new DateInterval("PT2H"));

        $this->view->shows =
            Application_Model_Show::getShows(
                $nowDateTime,
                $endDateTime,
                $onlyRecord = true);

        $this->view->is_recording = false;
        $this->view->server_timezone = Application_Model_Preference::GetDefaultTimezone();

        $rows = Application_Model_Show::getCurrentShow();
        
        if (count($rows) > 0) {
            $this->view->is_recording = ($rows[0]['record'] == 1);
        }
    }

    public function uploadFileAction()
    {
        $upload_dir = ini_get("upload_tmp_dir");
        $tempFilePath = Application_Model_StoredFile::uploadFile($upload_dir);
        $tempFileName = basename($tempFilePath);

        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        $result = Application_Model_StoredFile::copyFileToStor($upload_dir, $fileName, $tempFileName);

        if (!is_null($result)) {
            $this->_helper->json->sendJson(
                array("jsonrpc" => "2.0", "error" => array("code" => $result['code'], "message" => $result['message']))
            );
        }
    }

    public function uploadRecordedAction()
    {
        $show_instance_id           = $this->_getParam('showinstanceid');
        $file_id                    = $this->_getParam('fileid');
        $this->view->fileid         = $file_id;
        $this->view->showinstanceid = $show_instance_id;
        $this->uploadRecordedActionParam($show_instance_id, $file_id);
    }

    // The paramterized version of the uploadRecordedAction controller.
    // We want this controller's action to be invokable from other
    // controllers instead being of only through http
    public function uploadRecordedActionParam($show_instance_id, $file_id)
    {
        $showCanceled = false;
        $file = Application_Model_StoredFile::RecallById($file_id);
        //$show_instance  = $this->_getParam('show_instance');

        try {
            $show_inst = new Application_Model_ShowInstance($show_instance_id);
            $show_inst->setRecordedFile($file_id);
        } 
        catch (Exception $e) {
            //we've reached here probably because the show was
            //cancelled, and therefore the show instance does not exist
            //anymore (ShowInstance constructor threw this error). We've
            //done all we can do (upload the file and put it in the
            //library), now lets just return.
            $showCanceled = true;
        }

        // TODO : the following is inefficient because it calls save on both
        // fields
        $file->setMetadataValue('MDATA_KEY_CREATOR', "Airtime Show Recorder");
        $file->setMetadataValue('MDATA_KEY_TRACKNUMBER', $show_instance_id);

        if (!$showCanceled && Application_Model_Preference::GetAutoUploadRecordedShowToSoundcloud()) {
            $id = $file->getId();
            Application_Model_Soundcloud::uploadSoundcloud($id);
        }
    }

    public function mediaMonitorSetupAction()
    {
        $this->view->stor = Application_Model_MusicDir::getStorDir()->getDirectory();

        $watchedDirs = Application_Model_MusicDir::getWatchedDirs();
        $watchedDirsPath = array();
        foreach ($watchedDirs as $wd) {
            $watchedDirsPath[] = $wd->getDirectory();
        }
        $this->view->watched_dirs = $watchedDirsPath;
    }

    public function dispatchMetadata($md, $mode)
    {
        $return_hash = array();
        Application_Model_Preference::SetImportTimestamp();

        $con = Propel::getConnection(CcFilesPeer::DATABASE_NAME);
        $con->beginTransaction();
        try {
            // create also modifies the file if it exists
            if ($mode == "create") {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                $filepath = Application_Common_OsPath::normpath($filepath);
                $file = Application_Model_StoredFile::RecallByFilepath($filepath, $con);
                if (is_null($file)) {
                    $file = Application_Model_StoredFile::Insert($md, $con);
                } else {
                    // If the file already exists we will update and make sure that
                    // it's marked as 'exists'.
                    $file->setFileExistsFlag(true);
                    $file->setFileHiddenFlag(false);
                    $file->setMetadata($md);
                }
                if ($md['is_record'] != 0) {
                    $this->uploadRecordedActionParam($md['MDATA_KEY_TRACKNUMBER'], $file->getId());
                }
                
            } elseif ($mode == "modify") {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                $file = Application_Model_StoredFile::RecallByFilepath($filepath, $con);

                //File is not in database anymore.
                if (is_null($file)) {
                    $return_hash['error'] = sprintf(_("File does not exist in %s"), PRODUCT_NAME);
                }
                //Updating a metadata change.
                else {
                    //CC-5207 - restart media-monitor causes it to reevaluate all
                    //files in watched directories, and reset their cue-in/cue-out
                    //values. Since media-monitor has nothing to do with cue points
                    //let's unset it here. Note that on mode == "create", we still
                    //want media-monitor sending info about cue_out which by default
                    //will be equal to length of track until silan can take over.
                    unset($md['MDATA_KEY_CUE_IN']);
                    unset($md['MDATA_KEY_CUE_OUT']);

                    $file->setMetadata($md);
                }
            } elseif ($mode == "moved") {
                $file = Application_Model_StoredFile::RecallByFilepath(
                    $md['MDATA_KEY_ORIGINAL_PATH'], $con);

                if (is_null($file)) {
                    $return_hash['error'] = sprintf(_('File does not exist in %s'), PRODUCT_NAME);
                } else {
                    $filepath = $md['MDATA_KEY_FILEPATH'];
                    //$filepath = str_replace("\\", "", $filepath);
                    $file->setFilePath($filepath);
                }
            } elseif ($mode == "delete") {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                $filepath = str_replace("\\", "", $filepath);
                $file = Application_Model_StoredFile::RecallByFilepath($filepath, $con);

                if (is_null($file)) {
                    $return_hash['error'] = sprintf(_('File does not exist in %s'), PRODUCT_NAME);
                    Logging::warn("Attempt to delete file that doesn't exist.
                        Path: '$filepath'");
                } else {
                    $file->deleteByMediaMonitor();
                }
            } elseif ($mode == "delete_dir") {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                //$filepath = str_replace("\\", "", $filepath);
                $files = Application_Model_StoredFile::RecallByPartialFilepath($filepath, $con);

                foreach ($files as $file) {
                    $file->deleteByMediaMonitor();
                }
                $return_hash['success'] = 1;
            }

            if (!isset($return_hash['error'])) {
                $return_hash['fileid'] = is_null($file) ? '-1' : $file->getId();
            }
            $con->commit();
        } catch (Exception $e) {
            Logging::warn("rolling back");
            Logging::warn($e->getMessage());
            $con->rollback();
            $return_hash['error'] = $e->getMessage();
        }
        return $return_hash;
    }

    public function reloadMetadataGroupAction()
    {
        // extract all file metadata params from the request.
        // The value is a json encoded hash that has all the information related to this action
        // The key(mdXXX) does not have any meaning as of yet but it could potentially correspond
        // to some unique id.
        $request     = $this->getRequest();
        $responses   = array();
        $params      = $request->getParams();
        $valid_modes = array('delete_dir', 'delete', 'moved', 'modify', 'create');
        foreach ($params as $k => $raw_json) {
            // Valid requests must start with mdXXX where XXX represents at
            // least 1 digit
            if ( !preg_match('/^md\d+$/', $k) ) { continue; }
            $info_json = json_decode($raw_json, $assoc = true);

            // Log invalid requests
            if ( !array_key_exists('mode', $info_json) ) {
                Logging::info("Received bad request(key=$k), no 'mode' parameter. Bad request is:");
                Logging::info( $info_json );
                array_push( $responses, array(
                    'error' => _("Bad request. no 'mode' parameter passed."),
                    'key' => $k));
                continue;
            } elseif ( !in_array($info_json['mode'], $valid_modes) ) {
                // A request still has a chance of being invalid even if it
                // exists but it's validated by $valid_modes array
                $mode = $info_json['mode'];
                Logging::info("Received bad request(key=$k). 'mode' parameter was invalid with value: '$mode'. Request:");
                Logging::info( $info_json );
                array_push( $responses, array(
                    'error' => _("Bad request. 'mode' parameter is invalid"),
                    'key' => $k,
                    'mode' => $mode ) );
                continue;
            }
            // Removing 'mode' key from $info_json might not be necessary...
            $mode = $info_json['mode'];
            unset( $info_json['mode'] );
            try {
                $response = $this->dispatchMetadata($info_json, $mode);
            } catch (Exception $e) {
                Logging::warn($e->getMessage());
                Logging::warn(gettype($e));
            } 
            // We tack on the 'key' back to every request in case the would like to associate
            // his requests with particular responses
            $response['key'] = $k;
            array_push($responses, $response);
        }
        $this->_helper->json->sendJson($responses);
    }

    public function listAllFilesAction()
    {
        $request = $this->getRequest();
        $dir_id = $request->getParam('dir_id');
        $all    = $request->getParam('all');

        $this->view->files =
            Application_Model_StoredFile::listAllFiles($dir_id, $all);
    }

    public function listAllWatchedDirsAction()
    {
        $result = array();

        $arrWatchedDirs = Application_Model_MusicDir::getWatchedDirs();
        $storDir = Application_Model_MusicDir::getStorDir();

        $result[$storDir->getId()] = $storDir->getDirectory();

        foreach ($arrWatchedDirs as $watchedDir) {
            $result[$watchedDir->getId()] = $watchedDir->getDirectory();
        }

        $this->view->dirs = $result;
    }

    public function addWatchedDirAction()
    {
        $request = $this->getRequest();
        $path = base64_decode($request->getParam('path'));

        $this->view->msg = Application_Model_MusicDir::addWatchedDir($path);
    }

    public function removeWatchedDirAction()
    {
        $request = $this->getRequest();
        $path = base64_decode($request->getParam('path'));

        $this->view->msg = Application_Model_MusicDir::removeWatchedDir($path);
    }

    public function setStorageDirAction()
    {
        $request = $this->getRequest();
        $path = base64_decode($request->getParam('path'));

        $this->view->msg = Application_Model_MusicDir::setStorDir($path);
    }

    public function getStreamSettingAction()
    {
        $info = Application_Model_StreamSetting::getStreamSetting();
        $this->view->msg = $info;
    }

    public function statusAction()
    {
        $request = $this->getRequest();
        $getDiskInfo = $request->getParam('diskinfo') == "true";

        $status = array(
            "platform"=>Application_Model_Systemstatus::GetPlatformInfo(),
            "airtime_version"=>Application_Model_Preference::GetAirtimeVersion(),
            "services"=>array(
                "pypo"=>Application_Model_Systemstatus::GetPypoStatus(),
                "liquidsoap"=>Application_Model_Systemstatus::GetLiquidsoapStatus(),
                "media_monitor"=>Application_Model_Systemstatus::GetMediaMonitorStatus()
            )
        );

        if ($getDiskInfo) {
            $status["partitions"] = Application_Model_Systemstatus::GetDiskInfo();
        }

        $this->view->status = $status;
    }

    public function registerComponentAction()
    {
        $request = $this->getRequest();

        $component = $request->getParam('component');
        $remoteAddr = Application_Model_ServiceRegister::GetRemoteIpAddr();
        Logging::info("Registered Component: ".$component."@".$remoteAddr);

        Application_Model_ServiceRegister::Register($component, $remoteAddr);
    }

    public function updateLiquidsoapStatusAction()
    {
        $request = $this->getRequest();

        $msg = $request->getParam('msg_post');
        $stream_id = $request->getParam('stream_id');
        $boot_time = $request->getParam('boot_time');

        Application_Model_StreamSetting::setLiquidsoapError($stream_id, $msg, $boot_time);
    }

    public function updateSourceStatusAction()
    {
        $request = $this->getRequest();

        $sourcename = $request->getParam('sourcename');
        $status = $request->getParam('status');

        // on source disconnection sent msg to pypo to turn off the switch
        // Added AutoTransition option
        if ($status == "false" && Application_Model_Preference::GetAutoTransition()) {
            $data = array("sourcename"=>$sourcename, "status"=>"off");
            Application_Model_RabbitMq::SendMessageToPypo("switch_source", $data);
            Application_Model_Preference::SetSourceSwitchStatus($sourcename, "off");
            Application_Model_LiveLog::SetEndTime($sourcename == 'scheduled_play'?'S':'L',
                                                  new DateTime("now", new DateTimeZone('UTC')));
        } elseif ($status == "true" && Application_Model_Preference::GetAutoSwitch()) {
            $data = array("sourcename"=>$sourcename, "status"=>"on");
            Application_Model_RabbitMq::SendMessageToPypo("switch_source", $data);
            Application_Model_Preference::SetSourceSwitchStatus($sourcename, "on");
            Application_Model_LiveLog::SetNewLogTime($sourcename == 'scheduled_play'?'S':'L',
                                                  new DateTime("now", new DateTimeZone('UTC')));
        }
        Application_Model_Preference::SetSourceStatus($sourcename, $status);
    }

    // handles addition/deletion of mount point which watched dirs reside
    public function updateFileSystemMountAction()
    {
        $request = $this->getRequest();

        $params = $request->getParams();
        $added_list = empty($params['added_dir'])?array():explode(',', $params['added_dir']);
        $removed_list = empty($params['removed_dir'])?array():explode(',', $params['removed_dir']);

        // get all watched dirs
        $watched_dirs = Application_Model_MusicDir::getWatchedDirs(null, null);

        foreach ($added_list as $ad) {
            $ad .= '/';
            foreach ($watched_dirs as $dir) {
                $dirPath = $dir->getDirectory();

                // if mount path itself was watched
                if ($dirPath == $ad) {
                    Application_Model_MusicDir::addWatchedDir($dirPath, false);
                } elseif (substr($dirPath, 0, strlen($ad)) === $ad && $dir->getExistsFlag() == false) {
                    // if dir contains any dir in removed_list( if watched dir resides on new mounted path )
                    Application_Model_MusicDir::addWatchedDir($dirPath, false);
                } elseif (substr($ad, 0, strlen($dirPath)) === $dirPath) {
                    // is new mount point within the watched dir?
                    // pyinotify doesn't notify anyhing in this case, so we add this mount point as
                    // watched dir
                    // bypass nested loop check
                    Application_Model_MusicDir::addWatchedDir($ad, false, true);
                }
            }
        }

        foreach ($removed_list as $rd) {
            $rd .= '/';
            foreach ($watched_dirs as $dir) {
                $dirPath = $dir->getDirectory();
                // if dir contains any dir in removed_list( if watched dir resides on new mounted path )
                if (substr($dirPath, 0, strlen($rd)) === $rd && $dir->getExistsFlag() == true) {
                    Application_Model_MusicDir::removeWatchedDir($dirPath, false);
                } elseif (substr($rd, 0, strlen($dirPath)) === $dirPath) {
                    // is new mount point within the watched dir?
                    // pyinotify doesn't notify anyhing in this case, so we walk through all files within
                    // this watched dir in DB and mark them deleted.
                    // In case of h) of use cases, due to pyinotify behaviour of noticing mounted dir, we need to
                    // compare agaisnt all files in cc_files table

                    $watchDir = Application_Model_MusicDir::getDirByPath($rd);
                    // get all the files that is under $dirPath
                    $files = Application_Model_StoredFile::listAllFiles(
                        $dir->getId(),$all=false);
                    foreach ($files as $f) {
                        // if the file is from this mount
                        if (substr($f->getFilePath(), 0, strlen($rd)) === $rd) {
                            $f->delete();
                        }
                    }
                    if ($watchDir) {
                        Application_Model_MusicDir::removeWatchedDir($rd, false);
                    }
                }
            }
        }
    }

    // handles case where watched dir is missing
    public function handleWatchedDirMissingAction()
    {
        $request = $this->getRequest();

        $dir = base64_decode($request->getParam('dir'));
        Application_Model_MusicDir::removeWatchedDir($dir, false);
    }

    /* This action is for use by our dev scripts, that make
     * a change to the database and we want rabbitmq to send
     * out a message to pypo that a potential change has been made. */
    public function rabbitmqDoPushAction()
    {
        Logging::info("Notifying RabbitMQ to send message to pypo");

        Application_Model_RabbitMq::SendMessageToPypo("reset_liquidsoap_bootstrap", array());
        Application_Model_RabbitMq::PushSchedule();
    }

    public function getBootstrapInfoAction()
    {
        $live_dj = Application_Model_Preference::GetSourceSwitchStatus('live_dj');
        $master_dj = Application_Model_Preference::GetSourceSwitchStatus('master_dj');
        $scheduled_play = Application_Model_Preference::GetSourceSwitchStatus('scheduled_play');

        $res = array("live_dj"=>$live_dj, "master_dj"=>$master_dj, "scheduled_play"=>$scheduled_play);
        $this->view->switch_status = $res;
        $this->view->station_name = Application_Model_Preference::GetStationName();
        $this->view->stream_label = Application_Model_Preference::GetStreamLabelFormat();
        $this->view->transition_fade = Application_Model_Preference::GetDefaultTransitionFade();
    }

    /* This is used but Liquidsoap to check authentication of live streams*/
    public function checkLiveStreamAuthAction()
    {
        $request = $this->getRequest();

        $username = $request->getParam('username');
        $password = $request->getParam('password');
        $djtype = $request->getParam('djtype');

        if ($djtype == 'master') {
            //check against master
            if ($username == Application_Model_Preference::GetLiveStreamMasterUsername()
                    && $password == Application_Model_Preference::GetLiveStreamMasterPassword()) {
                $this->view->msg = true;
            } else {
                $this->view->msg = false;
            }
        } elseif ($djtype == "dj") {
            //check against show dj auth
            $showInfo = Application_Model_Show::getCurrentShow();
            // there is current playing show
            if (isset($showInfo[0]['id'])) {
                $current_show_id = $showInfo[0]['id'];
                $CcShow = CcShowQuery::create()->findPK($current_show_id);

                // get custom pass info from the show
                $custom_user = $CcShow->getDbLiveStreamUser();
                $custom_pass = $CcShow->getDbLiveStreamPass();

                // get hosts ids
                $show = new Application_Model_Show($current_show_id);
                $hosts_ids = $show->getHostsIds();

                // check against hosts auth
                if ($CcShow->getDbLiveStreamUsingAirtimeAuth()) {
                    foreach ($hosts_ids as $host) {
                        $h = new Application_Model_User($host['subjs_id']);
                        if ($username == $h->getLogin() && md5($password) == $h->getPassword()) {
                            $this->view->msg = true;

                            return;
                        }
                    }
                }
                // check against custom auth
                if ($CcShow->getDbLiveStreamUsingCustomAuth()) {
                    if ($username == $custom_user && $password == $custom_pass) {
                        $this->view->msg = true;
                    } else {
                        $this->view->msg = false;
                    }
                } else {
                    $this->view->msg = false;
                }
            } else {
                // no show is currently playing
                $this->view->msg = false;
            }
        }
    }

    /* This action is for use by our dev scripts, that make
     * a change to the database and we want rabbitmq to send
     * out a message to pypo that a potential change has been made. */
    public function getFilesWithoutReplayGainAction()
    {
        $dir_id = $this->_getParam('dir_id');

        //connect to db and get get sql
        $rows = Application_Model_StoredFile::listAllFiles2($dir_id, 100);

        $this->_helper->json->sendJson($rows);
    }
    
    public function getFilesWithoutSilanValueAction()
    {
        //connect to db and get get sql
        $rows = Application_Model_StoredFile::getAllFilesWithoutSilan();
    
        $this->_helper->json->sendJson($rows);
    }

    public function updateReplayGainValueAction()
    {
        $request = $this->getRequest();
        $data = json_decode($request->getParam('data'));

        foreach ($data as $pair) {
            list($id, $gain) = $pair;
            // TODO : move this code into model -- RG
            $file = Application_Model_StoredFile::RecallById($p_id = $id)->getPropelOrm();
            $file->setDbReplayGain($gain);
            $file->save();
        }

        $this->_helper->json->sendJson(array());
    }
    
    public function updateCueValuesBySilanAction()
    {
        $request = $this->getRequest();
        $data = json_decode($request->getParam('data'), $assoc = true);

        foreach ($data as $pair) {
            list($id, $info) = $pair;
            // TODO : move this code into model -- RG
            $file = Application_Model_StoredFile::RecallById($p_id = $id)->getPropelOrm();

            //What we are doing here is setting a more accurate length that was
            //calculated with silan by actually scanning the entire file. This
            //process takes a really long time, and so we only do it in the background
            //after the file has already been imported -MK
            try {
                $length = $file->getDbLength();
                if (isset($info['length'])) {
                    $length = $info['length'];
                    //length decimal number in seconds. Need to convert it to format
                    //HH:mm:ss to get around silly PHP limitations.
                    $length = Application_Common_DateHelper::secondsToPlaylistTime($length);
                    $file->setDbLength($length);
                }

                $cuein = isset($info['cuein']) ? $info['cuein'] : 0;
                $cueout = isset($info['cueout']) ? $info['cueout'] : $length;

                $file->setDbCuein($cuein);
                $file->setDbCueout($cueout);
                $file->setDbSilanCheck(true);
                $file->save();
            } catch (Exception $e) {
                Logging::info("Failed to update silan values for ".$file->getDbTrackTitle());
                Logging::info("File length analyzed by Silan is: ".$length);
                //set silan_check to true so we don't attempt to re-anaylze again
                $file->setDbSilanCheck(true);
                $file->save();
            }
        }

        $this->_helper->json->sendJson(array());
    }

    public function notifyWebstreamDataAction()
    {
        $request = $this->getRequest();
        $data = $request->getParam("data");
        $media_id = intval($request->getParam("media_id"));
        $data_arr = json_decode($data);
        
        //$media_id is -1 sometimes when a stream has stopped playing
        if (!is_null($media_id) && $media_id > 0) {

            if (isset($data_arr->title)) {
                
                $data_title = substr($data_arr->title, 0, 1024);

                $previous_metadata = CcWebstreamMetadataQuery::create()
                    ->orderByDbStartTime('desc')
                    ->filterByDbInstanceId($media_id)
                    ->findOne();

                $do_insert = true;
                if ($previous_metadata) {
                    if ($previous_metadata->getDbLiquidsoapData() == $data_title) {
                        Logging::debug("Duplicate found: ". $data_title);
                        $do_insert = false;
                    }
                }

                if ($do_insert) {
                    
                    $startDT = new DateTime("now", new DateTimeZone("UTC"));
                    
                    $webstream_metadata = new CcWebstreamMetadata();
                    $webstream_metadata->setDbInstanceId($media_id);
                    $webstream_metadata->setDbStartTime($startDT);
                    $webstream_metadata->setDbLiquidsoapData($data_title);
                    $webstream_metadata->save();
                    
                    $historyService = new Application_Service_HistoryService();
                    $historyService->insertWebstreamMetadata($media_id, $startDT, $data_arr);
                }
            }
        } 

        $this->view->response = $data;
        $this->view->media_id = $media_id;
    }

    public function getStreamParametersAction() {
        $streams = array("s1", "s2", "s3");
        $stream_params = array();
        foreach ($streams as $s) {
            $stream_params[$s] =
                Application_Model_StreamSetting::getStreamDataNormalized($s);
        }
        $this->view->stream_params = $stream_params;
    }

    public function pushStreamStatsAction() {
        $request = $this->getRequest();
        $data = json_decode($request->getParam("data"), true);

        Application_Model_ListenerStat::insertDataPoints($data);
        $this->view->data = $data;
    }
    
    public function updateStreamSettingTableAction() {
        $request = $this->getRequest();
        $data = json_decode($request->getParam("data"), true);
        
        foreach ($data as $k=>$v) {
            Application_Model_StreamSetting::SetListenerStatError($k, $v);
        }
    }

    /**
     * display played items for a given time range and show instance_id
     *
     * @return json array
     */
    public function itemHistoryFeedAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $instance = $request->getParam("instance_id", null);

            list($startsDT, $endsDT) = Application_Common_HTTPHelper::getStartEndFromRequest($request);

            $historyService = new Application_Service_HistoryService();
            $results = $historyService->getPlayedItemData($startsDT, $endsDT, $params, $instance);

            $this->_helper->json->sendJson($results['history']);
    }
        catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    /**
     * display show schedules for a given time range and show instance_id
     *
     * @return json array
     */
    public function showHistoryFeedAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $userId = $request->getParam("user_id", null);
 
            list($startsDT, $endsDT) = Application_Common_HTTPHelper::getStartEndFromRequest($request);
            
            $historyService = new Application_Service_HistoryService();
            $shows = $historyService->getShowList($startsDT, $endsDT, $userId);

            $this->_helper->json->sendJson($shows);
        }
        catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }

    /**
     * display show info (without schedule) for given show_id
     *
     * @return json array
     */
    public function showsAction()
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $showId = $request->getParam("show_id", null);
            $results = array();
 
            if (empty($showId)) {            
                $shows = CcShowQuery::create()->find();
                foreach($shows as $show) {
                    $results[] = $show->getShowInfo();
                }
            } else {
                $show = CcShowQuery::create()->findPK($showId);
                $results[] = $show->getShowInfo();
            }

            $this->_helper->json->sendJson($results);
        }
        catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }
    }
   
    /**
     * display show schedule for given show_id
     *
     * @return json array
     */
    public function showSchedulesAction() 
    {
        try {
            $request = $this->getRequest();
            $params = $request->getParams();
            $showId = $request->getParam("show_id", null);
 
            list($startsDT, $endsDT) = Application_Common_HTTPHelper::getStartEndFromRequest($request);

            if ((!isset($showId)) || (!is_numeric($showId))) {
            //if (!isset($showId)) {
                $this->_helper->json->sendJson(
                    array("jsonrpc" => "2.0", "error" => array("code" => 400, "message" => "missing invalid type for required show_id parameter. use type int.".$showId))
                );
            }
            
            $shows = Application_Model_Show::getShows($startsDT, $endsDT, FALSE, $showId);

            // is this a valid show?
            if (empty($shows)) {
                $this->_helper->json->sendJson(
                    array("jsonrpc" => "2.0", "error" => array("code" => 204, "message" => "no content for requested show_id"))
                );
            }

            $this->_helper->json->sendJson($shows);
        }
        catch (Exception $e) {
            Logging::info($e);
            Logging::info($e->getMessage());
        }

    }
    
    /**
     * displays track listing for given instance_id
     *
     * @return json array
     */
    public function showTracksAction()
    {
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $prefTimezone = Application_Model_Preference::GetTimezone();

        $instanceId = $this->_getParam('instance_id');

        if ((!isset($instanceId)) || (!is_numeric($instanceId))) {
            $this->_helper->json->sendJson(
                array("jsonrpc" => "2.0", "error" => array("code" => 400, "message" => "missing invalid type for required instance_id parameter. use type int"))
            );
        }

        $showInstance = new Application_Model_ShowInstance($instanceId);
        $showInstanceContent = $showInstance->getShowListContent($prefTimezone);
        
        // is this a valid show instance with content?
        if (empty($showInstanceContent)) {
            $this->_helper->json->sendJson(
                array("jsonrpc" => "2.0", "error" => array("code" => 204, "message" => "no content for requested instance_id"))
            );
        }

        $result = array();
        $position = 0;
        foreach ($showInstanceContent as $track) {

            $elementMap = array(
                'title' => isset($track['track_title']) ? $track['track_title'] : "",
                'artist' => isset($track['creator']) ? $track['creator'] : "",
                'position' => $position,
                'id' => ++$position,
                'mime' => isset($track['mime'])?$track['mime']:"",
                'starts' => isset($track['starts']) ? $track['starts'] : "",
                'length' => isset($track['length']) ? $track['length'] : "",
                'file_id' => ($track['type'] == 0) ? $track['item_id'] :  $track['filepath']
            );

            $result[] = $elementMap;
        }

        $this->_helper->json($result);

    }
    
}
