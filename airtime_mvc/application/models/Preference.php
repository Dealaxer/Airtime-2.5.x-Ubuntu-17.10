<?php

require_once 'Cache.php';

class Application_Model_Preference
{
    
    private static function getUserId()
    {
        //pass in true so the check is made with the autoloader
        //we need this check because saas calls this function from outside Zend
        if (!class_exists("Zend_Auth", true) || !Zend_Auth::getInstance()->hasIdentity()) {
            $userId = null;
        } else {
            $auth = Zend_Auth::getInstance();
            $userId = $auth->getIdentity()->id;
        }
        
        return $userId;
    }
    
    /**
     *
     * @param boolean $isUserValue is true when we are setting a value for the current user
     */
    private static function setValue($key, $value, $isUserValue = false)
    {
        $cache = new Cache();
        
        try {
            
            $con = Propel::getConnection(CcPrefPeer::DATABASE_NAME);
            $con->beginTransaction();

            $userId = self::getUserId();
            
            if ($isUserValue && is_null($userId))
                throw new Exception("User id can't be null for a user preference {$key}.");
            
            //Check if key already exists
            $sql = "SELECT COUNT(*) FROM cc_pref"
                ." WHERE keystr = :key";
            
            $paramMap = array();
            $paramMap[':key'] = $key;
            
            //For user specific preference, check if id matches as well
            if ($isUserValue) {
                $sql .= " AND subjid = :id";
                $paramMap[':id'] = $userId;
            } 

            $result = Application_Common_Database::prepareAndExecute($sql, 
                    $paramMap, 
                    Application_Common_Database::COLUMN,
                    PDO::FETCH_ASSOC, 
                    $con);

            $paramMap = array();
            if ($result > 1) {
                //this case should not happen.
                throw new Exception("Invalid number of results returned. Should be ".
                    "0 or 1, but is '$result' instead");
            } else if ($result == 1) {
                
                // result found
                if (!$isUserValue) {
                    // system pref
                    $sql = "UPDATE cc_pref"
                        ." SET subjid = NULL, valstr = :value"
                        ." WHERE keystr = :key";
                } else {
                    // user pref
                    $sql = "UPDATE cc_pref"
                        . " SET valstr = :value"
                        . " WHERE keystr = :key AND subjid = :id";
                   
                    $paramMap[':id'] = $userId;
                }
            } else {
                
                // result not found
                if (!$isUserValue) {
                    // system pref
                    $sql = "INSERT INTO cc_pref (keystr, valstr)"
                        ." VALUES (:key, :value)";
                } else {
                    // user pref
                    $sql = "INSERT INTO cc_pref (subjid, keystr, valstr)"
                        ." VALUES (:id, :key, :value)";
                   
                    $paramMap[':id'] = $userId;
                }
            }
            $paramMap[':key'] = $key;
            $paramMap[':value'] = $value;

            Application_Common_Database::prepareAndExecute($sql, 
                    $paramMap, 
                    'execute', 
                    PDO::FETCH_ASSOC, 
                    $con);

            $con->commit();
        } catch (Exception $e) {
            $con->rollback();
            header('HTTP/1.0 503 Service Unavailable');
            Logging::info("Database error: ".$e->getMessage());
            exit;
        }

        $cache->store($key, $value, $isUserValue, $userId);
    }

    private static function getValue($key, $isUserValue = false)
    {
        $cache = new Cache();
        
        try {
            
            $userId = self::getUserId();
            
            if ($isUserValue && is_null($userId))
                throw new Exception("User id can't be null for a user preference.");

            // If the value is already cached, return it
            $res = $cache->fetch($key, $isUserValue, $userId);
            if ($res !== false) return $res;
           
            //Check if key already exists
            $sql = "SELECT COUNT(*) FROM cc_pref"
            ." WHERE keystr = :key";
            
            $paramMap = array();
            $paramMap[':key'] = $key;
            
            //For user specific preference, check if id matches as well
            if ($isUserValue) {
                $sql .= " AND subjid = :id";
                $paramMap[':id'] = $userId;
            }
            
            $result = Application_Common_Database::prepareAndExecute($sql, $paramMap, Application_Common_Database::COLUMN);
            
            //return an empty string if the result doesn't exist.
            if ($result == 0) {
                $res = "";
            } else {
                $sql = "SELECT valstr FROM cc_pref"
                ." WHERE keystr = :key";
                
                $paramMap = array();
                $paramMap[':key'] = $key;

                //For user specific preference, check if id matches as well
                if ($isUserValue) {
                    $sql .= " AND subjid = :id";
                    $paramMap[':id'] = $userId;
                }
                
                $result = Application_Common_Database::prepareAndExecute($sql, $paramMap, Application_Common_Database::COLUMN);

                $res = ($result !== false) ? $result : "";
            }
            
            $cache->store($key, $res, $isUserValue, $userId);
            return $res;
        } 
        catch (Exception $e) {
            header('HTTP/1.0 503 Service Unavailable');
            Logging::info("Could not connect to database: ".$e->getMessage());
            exit;
        }
    }

    public static function GetHeadTitle()
    {
        $title = self::getValue("station_name");
        if (strlen($title) > 0)
            $title .= " - ";

        return $title."Airtime";
    }

    public static function SetHeadTitle($title, $view=null)
    {
        self::setValue("station_name", $title);

        // in case this is called from airtime-saas script
        if ($view !== null) {
            //set session variable to new station name so that html title is updated.
            //should probably do this in a view helper to keep this controller as minimal as possible.
            $view->headTitle()->exchangeArray(array()); //clear headTitle ArrayObject
            $view->headTitle(self::GetHeadTitle());
        }

        $eventType = "update_station_name";
        $md = array("station_name"=>$title);

        Application_Model_RabbitMq::SendMessageToPypo($eventType, $md);
    }

    /**
     * Set the furthest date that a never-ending show
     * should be populated until.
     *
     * @param DateTime $dateTime
     *        A row from cc_show_days table
     */
    public static function SetShowsPopulatedUntil($dateTime)
    {
        $dateTime->setTimezone(new DateTimeZone("UTC"));
        self::setValue("shows_populated_until", $dateTime->format("Y-m-d H:i:s"));
    }

    /**
     * Get the furthest date that a never-ending show
     * should be populated until.
     *
     * Returns null if the value hasn't been set, otherwise returns
     * a DateTime object representing the date.
     *
     * @return DateTime (in UTC Timezone)
     */
    public static function GetShowsPopulatedUntil()
    {
        $date = self::getValue("shows_populated_until");

        if ($date == "") {
            return null;
        } else {
            return new DateTime($date, new DateTimeZone("UTC"));
        }
    }
    
    public static function SetDefaultCrossfadeDuration($duration)
    {
        self::setValue("default_crossfade_duration", $duration);
    }
    
    public static function GetDefaultCrossfadeDuration()
    {
        $duration = self::getValue("default_crossfade_duration");
    
        if ($duration === "") {
            // the default value of the fade is 00.5
            return "0";
        }
    
        return $duration;
    }
    
    public static function SetDefaultFadeIn($fade)
    {
        self::setValue("default_fade_in", $fade);
    }
    
    public static function GetDefaultFadeIn()
    {
        $fade = self::getValue("default_fade_in");
    
        if ($fade === "") {
            // the default value of the fade is 00.5
            return "0.5";
        }
    
        return $fade;
    }
    
    public static function SetDefaultFadeOut($fade)
    {
        self::setValue("default_fade_out", $fade);
    }
    
    public static function GetDefaultFadeOut()
    {
        $fade = self::getValue("default_fade_out");
    
        if ($fade === "") {
            // the default value of the fade is 0.5
            return "0.5";
        }
    
        return $fade;
    }

    public static function SetDefaultFade($fade)
    {
        self::setValue("default_fade", $fade);
    }

    public static function SetDefaultTransitionFade($fade)
    {
        self::setValue("default_transition_fade", $fade);

        $eventType = "update_transition_fade";
        $md = array("transition_fade"=>$fade);
        Application_Model_RabbitMq::SendMessageToPypo($eventType, $md);
    }

    public static function GetDefaultTransitionFade()
    {
        $transition_fade = self::getValue("default_transition_fade");
        return ($transition_fade == "") ? "0.000" : $transition_fade;
    }

    public static function SetStreamLabelFormat($type)
    {
        self::setValue("stream_label_format", $type);

        $eventType = "update_stream_format";
        $md = array("stream_format"=>$type);

        Application_Model_RabbitMq::SendMessageToPypo($eventType, $md);
    }

    public static function GetStreamLabelFormat()
    {
        return self::getValue("stream_label_format");
    }

    public static function GetStationName()
    {
        return self::getValue("station_name");
    }

    public static function SetAutoUploadRecordedShowToSoundcloud($upload)
    {
        self::setValue("soundcloud_auto_upload_recorded_show", $upload);
    }

    public static function GetAutoUploadRecordedShowToSoundcloud()
    {
        return self::getValue("soundcloud_auto_upload_recorded_show");
    }

    public static function SetSoundCloudUser($user)
    {
        self::setValue("soundcloud_user", $user);
    }

    public static function GetSoundCloudUser()
    {
        return self::getValue("soundcloud_user");
    }

    public static function SetSoundCloudPassword($password)
    {
        if (strlen($password) > 0)
            self::setValue("soundcloud_password", $password);
    }

    public static function GetSoundCloudPassword()
    {
        return self::getValue("soundcloud_password");
    }

    public static function SetSoundCloudTags($tags)
    {
        self::setValue("soundcloud_tags", $tags);
    }

    public static function GetSoundCloudTags()
    {
        return self::getValue("soundcloud_tags");
    }

    public static function SetSoundCloudGenre($genre)
    {
        self::setValue("soundcloud_genre", $genre);
    }

    public static function GetSoundCloudGenre()
    {
        return self::getValue("soundcloud_genre");
    }

    public static function SetSoundCloudTrackType($track_type)
    {
        self::setValue("soundcloud_tracktype", $track_type);
    }

    public static function GetSoundCloudTrackType()
    {
        return self::getValue("soundcloud_tracktype");
    }

    public static function SetSoundCloudLicense($license)
    {
        self::setValue("soundcloud_license", $license);
    }

    public static function GetSoundCloudLicense()
    {
        return self::getValue("soundcloud_license");
    }

    public static function SetAllow3rdPartyApi($bool)
    {
        self::setValue("third_party_api", $bool);
    }

    public static function GetAllow3rdPartyApi()
    {
        $val = self::getValue("third_party_api");
        return (strlen($val) == 0 ) ? "0" : $val;
    }

    public static function SetPhone($phone)
    {
        self::setValue("phone", $phone);
    }

    public static function GetPhone()
    {
        return self::getValue("phone");
    }

    public static function SetEmail($email)
    {
        self::setValue("email", $email);
    }

    public static function GetEmail()
    {
        return self::getValue("email");
    }

    public static function SetStationWebSite($site)
    {
        self::setValue("station_website", $site);
    }

    public static function GetStationWebSite()
    {
        return self::getValue("station_website");
    }

    public static function SetSupportFeedback($feedback)
    {
        self::setValue("support_feedback", $feedback);
    }

    public static function GetSupportFeedback()
    {
        return self::getValue("support_feedback");
    }

    public static function SetPublicise($publicise)
    {
        self::setValue("publicise", $publicise);
    }

    public static function GetPublicise()
    {
        return self::getValue("publicise");
    }

    public static function SetRegistered($registered)
    {
        self::setValue("registered", $registered);
    }

    public static function GetRegistered()
    {
        return self::getValue("registered");
    }

    public static function SetStationCountry($country)
    {
        self::setValue("country", $country);
    }

    public static function GetStationCountry()
    {
        return self::getValue("country");
    }

    public static function SetStationCity($city)
    {
        self::setValue("city", $city);
    }

    public static function GetStationCity()
    {
        return self::getValue("city");
    }

    public static function SetStationDescription($description)
    {
        self::setValue("description", $description);
    }

    public static function GetStationDescription()
    {
        return self::getValue("description");
    }

    // Sets station default timezone (from preferences)
    public static function SetDefaultTimezone($timezone)
    {
        self::setValue("timezone", $timezone);
    }

    // Returns station default timezone (from preferences)
    public static function GetDefaultTimezone()
    {
        $stationTimezone = self::getValue("timezone");
        if (is_null($stationTimezone) || $stationTimezone == "") {
            $stationTimezone = "UTC";
        }
        return $stationTimezone;
    }

    public static function SetUserTimezone($timezone = null)
    {
        // When a new user is created they will get the default timezone
        // setting which the admin sets on preferences page
        if (is_null($timezone))
            $timezone = self::GetDefaultTimezone();
        self::setValue("user_timezone", $timezone, true);
    }

    public static function GetUserTimezone()
    {
        $timezone = self::getValue("user_timezone", true); 
        if (!$timezone) {
            return self::GetDefaultTimezone();
        } else {
            return $timezone;
        }
    }

    // Always attempts to returns the current user's personal timezone setting
    public static function GetTimezone()
    {
        $userId = self::getUserId();
        
        if (!is_null($userId)) {
            return self::GetUserTimezone();
        } else {
            return self::GetDefaultTimezone();
        }
    }

    // This is the language setting on preferences page
    public static function SetDefaultLocale($locale)
    {
        self::setValue("locale", $locale);
    }

    public static function GetDefaultLocale()
    {
        return self::getValue("locale");
    }

    public static function GetUserLocale()
    {
        $locale = self::getValue("user_locale", true);
        if (!$locale) {
            return self::GetDefaultLocale();
        } 
        else {
            return $locale;
        }
    }

    public static function SetUserLocale($locale = null)
    {
        // When a new user is created they will get the default locale
        // setting which the admin sets on preferences page
        if (is_null($locale))
            $locale = self::GetDefaultLocale();
        self::setValue("user_locale", $locale, true);
    }

    public static function GetLocale()
    {
        $userId = self::getUserId();
        
        if (!is_null($userId)) {
            return self::GetUserLocale();
        } else {
            return self::GetDefaultLocale();
        }
    }

    public static function SetStationLogo($imagePath)
    {
        if (empty($imagePath)) {
            Logging::info("Removed station logo");
        }
        $image = @file_get_contents($imagePath);
        $image = base64_encode($image);
        self::setValue("logoImage", $image);
    }

    public static function GetStationLogo()
    {
        return self::getValue("logoImage");
    }
    
    public static function SetUniqueId($id)
    {
        self::setValue("uniqueId", $id);
    }

    public static function GetUniqueId()
    {
        return self::getValue("uniqueId");
    }

    public static function GetCountryList()
    {
        $sql = "SELECT * FROM cc_country";
        
        $res = Application_Common_Database::prepareAndExecute($sql, array());

        $out = array();
        $out[""] = _("Select Country");
        foreach ($res as $r) {
            $out[$r["isocode"]] = $r["name"];
        }

        return $out;
    }

    public static function GetSystemInfo($returnArray=false, $p_testing=false)
    {
        exec('/usr/bin/airtime-check-system --no-color', $output);
        $output = preg_replace('/\s+/', ' ', $output);

        $systemInfoArray = array();
        foreach ($output as $key => &$out) {
            $info = explode('=', $out);
            if (isset($info[1])) {
                $key = str_replace(' ', '_', trim($info[0]));
                $key = strtoupper($key);
                if ($key == 'WEB_SERVER' || $key == 'CPU' || $key == 'OS'  || $key == 'TOTAL_RAM' ||
                          $key == 'FREE_RAM' || $key == 'AIRTIME_VERSION' || $key == 'KERNAL_VERSION'  ||
                          $key == 'MACHINE_ARCHITECTURE' || $key == 'TOTAL_MEMORY_MBYTES' || $key == 'TOTAL_SWAP_MBYTES' ||
                          $key == 'PLAYOUT_ENGINE_CPU_PERC') {
                    if ($key == 'AIRTIME_VERSION') {
                        // remove hash tag on the version string
                        $version = explode('+', $info[1]);
                        $systemInfoArray[$key] = $version[0];
                    } else {
                        $systemInfoArray[$key] = $info[1];
                    }
                }
            }
        }

        $outputArray = array();

        $outputArray['LIVE_DURATION'] = Application_Model_LiveLog::GetLiveShowDuration($p_testing);
        $outputArray['SCHEDULED_DURATION'] = Application_Model_LiveLog::GetScheduledDuration($p_testing);
        $outputArray['SOUNDCLOUD_ENABLED'] = self::GetUploadToSoundcloudOption();
        if ($outputArray['SOUNDCLOUD_ENABLED']) {
            $outputArray['NUM_SOUNDCLOUD_TRACKS_UPLOADED'] = Application_Model_StoredFile::getSoundCloudUploads();
        } else {
            $outputArray['NUM_SOUNDCLOUD_TRACKS_UPLOADED'] = NULL;
        }

        $outputArray['STATION_NAME'] = self::GetStationName();
        $outputArray['PHONE'] = self::GetPhone();
        $outputArray['EMAIL'] = self::GetEmail();
        $outputArray['STATION_WEB_SITE'] = self::GetStationWebSite();
        $outputArray['STATION_COUNTRY'] = self::GetStationCountry();
        $outputArray['STATION_CITY'] = self::GetStationCity();
        $outputArray['STATION_DESCRIPTION'] = self::GetStationDescription();

        // get web server info
        if (isset($systemInfoArray["AIRTIME_VERSION_URL"])) {
           $url = $systemInfoArray["AIRTIME_VERSION_URL"];
           $index = strpos($url,'/api/');
           $url = substr($url, 0, $index);
           
           $headerInfo = get_headers(trim($url),1);
           $outputArray['WEB_SERVER'] = $headerInfo['Server'][0];
        }

        $outputArray['NUM_OF_USERS'] = Application_Model_User::getUserCount();
        $outputArray['NUM_OF_SONGS'] = Application_Model_StoredFile::getFileCount();
        $outputArray['NUM_OF_PLAYLISTS'] = Application_Model_Playlist::getPlaylistCount();
        $outputArray['NUM_OF_SCHEDULED_PLAYLISTS'] = Application_Model_Schedule::getSchduledPlaylistCount();
        $outputArray['NUM_OF_PAST_SHOWS'] = Application_Model_ShowInstance::GetShowInstanceCount(gmdate("Y-m-d H:i:s"));
        $outputArray['UNIQUE_ID'] = self::GetUniqueId();
        $outputArray['SAAS'] = self::GetPlanLevel();
        if ($outputArray['SAAS'] != 'disabled') {
            $outputArray['TRIAL_END_DATE'] = self::GetTrialEndingDate();
        } else {
            $outputArray['TRIAL_END_DATE'] = NULL;
        }
        $outputArray['INSTALL_METHOD'] = self::GetInstallMethod();
        $outputArray['NUM_OF_STREAMS'] = self::GetNumOfStreams();
        $outputArray['STREAM_INFO'] = Application_Model_StreamSetting::getStreamInfoForDataCollection();

        $outputArray = array_merge($systemInfoArray, $outputArray);

        $outputString = "\n";
        foreach ($outputArray as $key => $out) {
            if ($key == 'TRIAL_END_DATE' && ($out != '' || $out != 'NULL')) {
                continue;
            }
            if ($key == "STREAM_INFO") {
                $outputString .= $key." :\n";
                foreach ($out as $s_info) {
                    foreach ($s_info as $k => $v) {
                        $outputString .= "\t".strtoupper($k)." : ".$v."\n";
                    }
                }
            } elseif ($key == "SOUNDCLOUD_ENABLED") {
                if ($out) {
                    $outputString .= $key." : TRUE\n";
                } elseif (!$out) {
                    $outputString .= $key." : FALSE\n";
                }
            } elseif ($key == "SAAS") {
                if (strcmp($out, 'disabled')!=0) {
                    $outputString .= $key.' : '.$out."\n";
                }
            } else {
                $outputString .= $key.' : '.$out."\n";
            }
        }
        if ($returnArray) {
            $outputArray['PROMOTE'] = self::GetPublicise();
            $outputArray['LOGOIMG'] = self::GetStationLogo();

            return $outputArray;
        } else {
            return $outputString;
        }
    }

    public static function GetInstallMethod()
    {
        $easy_install = file_exists('/usr/bin/airtime-easy-setup');
        $debian_install = file_exists('/var/lib/dpkg/info/airtime.config');
        if ($debian_install) {
            if ($easy_install) {
                return "easy_install";
            } else {
                return "debian_install";
            }
        } else {
            return "manual_install";
        }
    }

    public static function SetRemindMeDate($p_never = false)
    {
        if ($p_never) {
            self::setValue("remindme", -1);
        } else {
            $weekAfter = mktime(0, 0, 0, gmdate("m"), gmdate("d")+7, gmdate("Y"));
            self::setValue("remindme", $weekAfter);
        }
    }

    public static function GetRemindMeDate()
    {
        return self::getValue("remindme");
    }

    public static function SetImportTimestamp()
    {
        $now = time();
        if (self::GetImportTimestamp()+5 < $now) {
            self::setValue("import_timestamp", $now);
        }
    }

    public static function GetImportTimestamp()
    {
        return self::getValue("import_timestamp");
    }

    public static function GetStreamType()
    {
        $st = self::getValue("stream_type");

        return explode(',', $st);
    }

    public static function GetStreamBitrate()
    {
        $sb = self::getValue("stream_bitrate");

        return explode(',', $sb);
    }

    public static function SetPrivacyPolicyCheck($flag)
    {
        self::setValue("privacy_policy", $flag);
    }

    public static function GetPrivacyPolicyCheck()
    {
        return self::getValue("privacy_policy");
    }

    public static function SetNumOfStreams($num)
    {
        self::setValue("num_of_streams", intval($num));
    }

    public static function GetNumOfStreams()
    {
        return self::getValue("num_of_streams");
    }

    public static function SetMaxBitrate($bitrate)
    {
        self::setValue("max_bitrate", intval($bitrate));
    }

    public static function GetMaxBitrate()
    {
        return self::getValue("max_bitrate");
    }

    public static function SetPlanLevel($plan)
    {
        self::setValue("plan_level", $plan);
    }

    public static function GetPlanLevel()
    {
        $plan = self::getValue("plan_level");
        if (trim($plan) == '') {
            $plan = 'disabled';
        }

        return $plan;
    }

    public static function SetTrialEndingDate($date)
    {
        self::setValue("trial_end_date", $date);
    }

    public static function GetTrialEndingDate()
    {
        return self::getValue("trial_end_date");
    }

    public static function SetEnableStreamConf($bool)
    {
        self::setValue("enable_stream_conf", $bool);
    }

    public static function GetEnableStreamConf()
    {
        if (self::getValue("enable_stream_conf") == Null) {
            return "true";
        }

        return self::getValue("enable_stream_conf");
    }

    public static function GetSchemaVersion()
    {
        $schemaVersion = self::getValue("schema_version");

        //Pre-2.5.2 releases all used this ambiguous "system_version" key to represent both the code and schema versions...
        if (empty($schemaVersion)) {
            $schemaVersion = self::getValue("system_version");
        }

        return $schemaVersion;
    }

    public static function SetSchemaVersion($version)
    {
        self::setValue("schema_version", $version);
    }

    public static function GetAirtimeVersion()
    {
        if (defined('APPLICATION_ENV') && APPLICATION_ENV == "development" && function_exists('exec')) {
            $version = exec("git rev-parse --short HEAD 2>/dev/null", $out, $return_code);
            if ($return_code == 0) {
                return self::getValue("system_version")."+".$version.":".time();
            }
        }

        return self::getValue("system_version");
    }

    public static function GetLatestVersion()
    {
        $latest = self::getValue("latest_version");
        if ($latest == null || strlen($latest) == 0) {
            return self::GetAirtimeVersion();
        } else {
            return $latest;
        }
    }

    public static function SetLatestVersion($version)
    {
        $pattern = "/^[0-9]+\.[0-9]+\.[0-9]+/";
        if (preg_match($pattern, $version)) {
            self::setValue("latest_version", $version);
        }
    }

    public static function GetLatestLink()
    {
        $link = self::getValue("latest_link");
        if ($link == null || strlen($link) == 0) {
            return 'http://airtime.sourcefabric.org';
        } else {
            return $link;
        }
    }

    public static function SetLatestLink($link)
    {
        $pattern = "#^(http|https|ftp)://" .
                    "([a-zA-Z0-9]+\.)*[a-zA-Z0-9]+" .
                    "(/[a-zA-Z0-9\-\.\_\~\:\?\#\[\]\@\!\$\&\'\(\)\*\+\,\;\=]+)*/?$#";
        if (preg_match($pattern, $link)) {
            self::setValue("latest_link", $link);
        }
    }

    public static function SetUploadToSoundcloudOption($upload)
    {
        self::setValue("soundcloud_upload_option", $upload);
    }

    public static function GetUploadToSoundcloudOption()
    {
        return self::getValue("soundcloud_upload_option");
    }

    public static function SetSoundCloudDownloadbleOption($upload)
    {
        self::setValue("soundcloud_downloadable", $upload);
    }

    public static function GetSoundCloudDownloadbleOption()
    {
        return self::getValue("soundcloud_downloadable");
    }

    public static function SetWeekStartDay($day)
    {
        self::setValue("week_start_day", $day);
    }

    public static function GetWeekStartDay()
    {
        $val = self::getValue("week_start_day");
        return (strlen($val) == 0) ? "0" : $val;
    }

    /**
    * Stores the last timestamp of user updating stream setting
    */
    public static function SetStreamUpdateTimestamp()
    {
        $now = time();
        self::setValue("stream_update_timestamp", $now);
    }

    /**
     * Gets the last timestamp of user updating stream setting
     */
    public static function GetStreamUpdateTimestemp()
    {
        $update_time = self::getValue("stream_update_timestamp");
        return ($update_time == null) ? 0 : $update_time;
    }

    public static function GetClientId()
    {
        return self::getValue("client_id");
    }

    public static function SetClientId($id)
    {
        if (is_numeric($id)) {
            self::setValue("client_id", $id);
        } else {
            Logging::warn("Attempting to set client_id to invalid value: $id");
        }
    }

    /* User specific preferences start */

    /**
     * Sets the time scale preference (agendaDay/agendaWeek/month) in Calendar.
     *
     * @param $timeScale    new time scale
     */
    public static function SetCalendarTimeScale($timeScale)
    {
        self::setValue("calendar_time_scale", $timeScale, true /* user specific */);
    }

    /**
     * Retrieves the time scale preference for the current user.
     * Defaults to month if no entry exists
     */
    public static function GetCalendarTimeScale()
    {
        $val = self::getValue("calendar_time_scale", true /* user specific */);
        if (strlen($val) == 0) {
            $val = "month";
        }

        return $val;
    }

    /**
     * Sets the number of entries to show preference in library under Playlist Builder.
     *
     * @param $numEntries    new number of entries to show
     */
    public static function SetLibraryNumEntries($numEntries)
    {
        self::setValue("library_num_entries", $numEntries, true /* user specific */);
    }

    /**
     * Retrieves the number of entries to show preference in library under Playlist Builder.
     * Defaults to 10 if no entry exists
     */
    public static function GetLibraryNumEntries()
    {
        $val = self::getValue("library_num_entries", true /* user specific */);
        if (strlen($val) == 0) {
            $val = "10";
        }

        return $val;
    }

    /**
     * Sets the time interval preference in Calendar.
     *
     * @param $timeInterval        new time interval
     */
    public static function SetCalendarTimeInterval($timeInterval)
    {
        self::setValue("calendar_time_interval", $timeInterval, true /* user specific */);
    }

    /**
     * Retrieves the time interval preference for the current user.
     * Defaults to 30 min if no entry exists
     */
    public static function GetCalendarTimeInterval()
    {
        $val = self::getValue("calendar_time_interval", true /* user specific */);
        return (strlen($val) == 0) ? "30" : $val;
    }

    public static function SetDiskQuota($value)
    {
        self::setValue("disk_quota", $value, false);
    }

    public static function GetDiskQuota()
    {
        $val = self::getValue("disk_quota");
        return (strlen($val) == 0) ? 0 : $val;
    }

    public static function SetLiveStreamMasterUsername($value)
    {
        self::setValue("live_stream_master_username", $value, false);
    }

    public static function GetLiveStreamMasterUsername()
    {
        return self::getValue("live_stream_master_username");
    }

    public static function SetLiveStreamMasterPassword($value)
    {
        self::setValue("live_stream_master_password", $value, false);
    }

    public static function GetLiveStreamMasterPassword()
    {
        return self::getValue("live_stream_master_password");
    }

    public static function SetSourceStatus($sourcename, $status)
    {
        self::setValue($sourcename, $status, false);
    }

    public static function GetSourceStatus($sourcename)
    {
        $value = self::getValue($sourcename);
        return !($value == null || $value == "false");
    }

    public static function SetSourceSwitchStatus($sourcename, $status)
    {
        self::setValue($sourcename."_switch", $status, false);
    }

    public static function GetSourceSwitchStatus($sourcename)
    {
        $value = self::getValue($sourcename."_switch");
        return ($value == null || $value == "off") ? 'off' : 'on';
    }

    public static function SetMasterDJSourceConnectionURL($value)
    {
        self::setValue("master_dj_source_connection_url", $value, false);
    }

    public static function GetMasterDJSourceConnectionURL()
    {
        return self::getValue("master_dj_source_connection_url");
    }

    public static function SetLiveDJSourceConnectionURL($value)
    {
        self::setValue("live_dj_source_connection_url", $value, false);
    }

    public static function GetLiveDJSourceConnectionURL()
    {
        return self::getValue("live_dj_source_connection_url");
    }

    /* Source Connection URL override status starts */
    public static function GetLiveDjConnectionUrlOverride()
    {
        return self::getValue("live_dj_connection_url_override");
    }

    public static function SetLiveDjConnectionUrlOverride($value)
    {
        self::setValue("live_dj_connection_url_override", $value, false);
    }

    public static function GetMasterDjConnectionUrlOverride()
    {
        return self::getValue("master_dj_connection_url_override");
    }

    public static function SetMasterDjConnectionUrlOverride($value)
    {
        self::setValue("master_dj_connection_url_override", $value, false);
    }
    /* Source Connection URL override status ends */

    public static function SetAutoTransition($value)
    {
        self::setValue("auto_transition", $value, false);
    }

    public static function GetAutoTransition()
    {
        return self::getValue("auto_transition");
    }

    public static function SetAutoSwitch($value)
    {
        self::setValue("auto_switch", $value, false);
    }

    public static function GetAutoSwitch()
    {
        return self::getValue("auto_switch");
    }

    public static function SetEnableSystemEmail($upload)
    {
        self::setValue("enable_system_email", $upload);
    }

    public static function GetEnableSystemEmail()
    {
        $v =  self::getValue("enable_system_email");
        return ($v === "") ?  0 : $v;
    }

    public static function SetSystemEmail($value)
    {
        self::setValue("system_email", $value, false);
    }

    public static function GetSystemEmail()
    {
        return self::getValue("system_email");
    }

    public static function SetMailServerConfigured($value)
    {
        self::setValue("mail_server_configured", $value, false);
    }

    public static function GetMailServerConfigured()
    {
        return self::getValue("mail_server_configured");
    }

    public static function SetMailServer($value)
    {
        self::setValue("mail_server", $value, false);
    }

    public static function GetMailServer()
    {
        return self::getValue("mail_server");
    }

    public static function SetMailServerEmailAddress($value)
    {
        self::setValue("mail_server_email_address", $value, false);
    }

    public static function GetMailServerEmailAddress()
    {
        return self::getValue("mail_server_email_address");
    }

    public static function SetMailServerPassword($value)
    {
        self::setValue("mail_server_password", $value, false);
    }

    public static function GetMailServerPassword()
    {
        return self::getValue("mail_server_password");
    }

    public static function SetMailServerPort($value)
    {
        self::setValue("mail_server_port", $value, false);
    }

    public static function GetMailServerPort()
    {
        return self::getValue("mail_server_port");
    }

    public static function SetMailServerRequiresAuth($value)
    {
        self::setValue("mail_server_requires_auth", $value, false);
    }

    public static function GetMailServerRequiresAuth()
    {
        return self::getValue("mail_server_requires_auth");
    }
    /* User specific preferences end */

    public static function ShouldShowPopUp()
    {
        $today = mktime(0, 0, 0, gmdate("m"), gmdate("d"), gmdate("Y"));
        $remindDate = Application_Model_Preference::GetRemindMeDate();
        $retVal = false;
        
        if (is_null($remindDate) || ($remindDate != -1 && $today >= $remindDate)) {
            $retVal = true;
        }
        
        return $retVal;
    }



    public static function getOrderingMap($pref_param)
    {
        $v = self::getValue($pref_param, true);

        $id = function ($x) { return $x; };

        if ($v === '') {
            return $id;
        }

        $ds = unserialize($v);
        
        
        if (is_null($ds) || !is_array($ds)) {
            return $id;
        }
        
        if (!array_key_exists('ColReorder', $ds)) {
            return $id;
        }

        return function ($x) use ($ds) {
            if (array_key_exists($x, $ds['ColReorder'])) {
                return $ds['ColReorder'][$x];
            } else {
                /*For now we just have this hack for debugging. We should not
                    rely on this behaviour in case of failure*/
                Logging::warn("Index $x does not exist preferences");
                Logging::warn("Defaulting to identity and printing preferences");
                Logging::warn($ds);
                return $x;
            }
        };
    }

    public static function getCurrentLibraryTableColumnMap()
    {
        return self::getOrderingMap("library_datatable");
    }

    public static function setCurrentLibraryTableSetting($settings)
    {
        $data = serialize($settings);
        self::setValue("library_datatable", $data, true);
    }

    public static function getCurrentLibraryTableSetting()
    {
        $data = self::getValue("library_datatable", true);
        return ($data != "") ? unserialize($data) : null;
    }


    public static function setTimelineDatatableSetting($settings)
    {
        $data = serialize($settings);
        self::setValue("timeline_datatable", $data, true);
    }

    public static function getTimelineDatatableSetting()
    {
        $data = self::getValue("timeline_datatable", true);
        return ($data != "") ? unserialize($data) : null;
    }


    public static function setNowPlayingScreenSettings($settings)
    {
        $data = serialize($settings);
        self::setValue("nowplaying_screen", $data, true);
    }

    public static function getNowPlayingScreenSettings()
    {
        $data = self::getValue("nowplaying_screen", true);
        return ($data != "") ? unserialize($data) : null;
    }

    public static function setLibraryScreenSettings($settings)
    {
        $data = serialize($settings);
        self::setValue("library_screen", $data, true);
    }

    public static function getLibraryScreenSettings()
    {
        $data = self::getValue("library_screen", true);
        return ($data != "") ? unserialize($data) : null;
    }

    public static function SetEnableReplayGain($value) {
        self::setValue("enable_replay_gain", $value, false);
    }
    
    public static function GetEnableReplayGain() {
        return self::getValue("enable_replay_gain", false);
    }
    
    public static function getReplayGainModifier() {
        $rg_modifier = self::getValue("replay_gain_modifier");
        
        if ($rg_modifier === "")
            return "0";
        
        return $rg_modifier;
    }
    
    public static function setReplayGainModifier($rg_modifier)
    {
        self::setValue("replay_gain_modifier", $rg_modifier, true);
    }
    
    public static function SetHistoryItemTemplate($value) {
        self::setValue("history_item_template", $value);
    }
    
    public static function GetHistoryItemTemplate() {
        return self::getValue("history_item_template");
    }
    
    public static function SetHistoryFileTemplate($value) {
        self::setValue("history_file_template", $value);
    }
    
    public static function GetHistoryFileTemplate() {
        return self::getValue("history_file_template");
    }
}
