<?php

class Application_Form_AddShowWhen extends Zend_Form_SubForm
{

    public function init()
    {
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/add-show-when.phtml'))
        ));

        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        $dateValidator = Application_Form_Helper_ValidationTypes::overrrideDateValidator("YYYY-MM-DD");
        $regexValidator = Application_Form_Helper_ValidationTypes::overrideRegexValidator(
            "/^[0-2]?[0-9]:[0-5][0-9]$/",
            _("'%value%' does not fit the time format 'HH:mm'"));

        // Add start date element
        $startDate = new Zend_Form_Element_Text('add_show_start_date');
        $startDate->class = 'input_text';
        $startDate->setRequired(true)
                    ->setLabel(_('Date/Time Start:'))
                    ->setValue(date("Y-m-d"))
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        $notEmptyValidator,
                        $dateValidator))
                    ->setDecorators(array('ViewHelper'));
        $startDate->setAttrib('alt', 'date');
        $this->addElement($startDate);

        // Add start time element
        $startTime = new Zend_Form_Element_Text('add_show_start_time');
        $startTime->class = 'input_text';
        $startTime->setRequired(true)
                    ->setValue('00:00')
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        $notEmptyValidator,
                        $regexValidator
                        ))->setDecorators(array('ViewHelper'));
        $startTime->setAttrib('alt', 'time');
        $this->addElement($startTime);

        // Add end date element
        $endDate = new Zend_Form_Element_Text('add_show_end_date_no_repeat');
        $endDate->class = 'input_text';
        $endDate->setRequired(true)
                    ->setLabel(_('Date/Time End:'))
                    ->setValue(date("Y-m-d"))
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        $notEmptyValidator,
                        $dateValidator))
                    ->setDecorators(array('ViewHelper'));
        $endDate->setAttrib('alt', 'date');
        $this->addElement($endDate);

        // Add end time element
        $endTime = new Zend_Form_Element_Text('add_show_end_time');
        $endTime->class = 'input_text';
        $endTime->setRequired(true)
                    ->setValue('01:00')
                    ->setFilters(array('StringTrim'))
                    ->setValidators(array(
                        $notEmptyValidator,
                        $regexValidator))
                    ->setDecorators(array('ViewHelper'));
        $endTime->setAttrib('alt', 'time');
        $this->addElement($endTime);

        // Add duration element
        $this->addElement('text', 'add_show_duration', array(
            'label'      => _('Duration:'),
            'class'      => 'input_text',
            'value'      => '01h 00m',
            'readonly'   => true,
            'decorators'  => array('ViewHelper')
        ));

        $timezone = new Zend_Form_Element_Select('add_show_timezone');
        $timezone->setRequired(true)
                 ->setLabel(_("Timezone:"))
                 ->setMultiOptions(Application_Common_Timezone::getTimezones())
                 ->setValue(Application_Model_Preference::GetUserTimezone())
                 ->setAttrib('class', 'input_select add_show_input_select')
                 ->setDecorators(array('ViewHelper'));
        $this->addElement($timezone);

        // Add repeats element
        $this->addElement('checkbox', 'add_show_repeats', array(
            'label'      => _('Repeats?'),
            'required'   => false,
            'decorators'  => array('ViewHelper')
        ));

    }

    public function isWhenFormValid($formData, $validateStartDate, $originalStartDate,
        $update, $instanceId) {
        if (parent::isValid($formData)) {
            return self::checkReliantFields($formData, $validateStartDate,
                $originalStartDate, $update, $instanceId);
        } else {
            return false;
        }
    }

    public function checkReliantFields($formData, $validateStartDate, $originalStartDate=null, $update=false, $instanceId=null)
    {
        $valid = true;

        $start_time = $formData['add_show_start_date']." ".$formData['add_show_start_time'];
        $end_time = $formData['add_show_end_date_no_repeat']." ".$formData['add_show_end_time'];

        //have to use the timezone the user has entered in the form to check past/present
        $showTimezone = new DateTimeZone($formData["add_show_timezone"]);
        $nowDateTime = new DateTime("now", $showTimezone);
        $showStartDateTime = new DateTime($start_time, $showTimezone);
        $showEndDateTime = new DateTime($end_time, $showTimezone);
        
        if ($validateStartDate) {
            if ($showStartDateTime < $nowDateTime) {
                $this->getElement('add_show_start_time')->setErrors(array(_('Cannot create show in the past')));
                $valid = false;
            }
            // if edit action, check if original show start time is in the past. CC-3864
            if ($originalStartDate) {
                if ($originalStartDate < $nowDateTime) {
                    $this->getElement('add_show_start_time')->setValue($originalStartDate->format("H:i"));
                    $this->getElement('add_show_start_date')->setValue($originalStartDate->format("Y-m-d"));
                    $this->getElement('add_show_start_time')->setErrors(array(_('Cannot modify start date/time of the show that is already started')));
                    $this->disableStartDateAndTime();
                    $valid = false;
                }
            }
        }
        
        // if end time is in the past, return error
        if ($showEndDateTime < $nowDateTime) {
            $this->getElement('add_show_end_time')->setErrors(array(_('End date/time cannot be in the past')));
            $valid = false;
        }
        
        //validate duration.
        $duration = $showStartDateTime->diff($showEndDateTime);
        
        if ($showStartDateTime > $showEndDateTime) {
        	$this->getElement('add_show_duration')->setErrors(array(_('Cannot have duration < 0m')));
        	$valid = false;
        }
        else if ($showStartDateTime == $showEndDateTime) {
        	$this->getElement('add_show_duration')->setErrors(array(_('Cannot have duration 00h 00m')));
        	$valid = false;
        }
        else if (intval($duration->format('%d')) > 0 && 
        		(intval($duration->format('%h')) > 0
        		 || intval($duration->format('%i')) > 0
        		 || intval($duration->format('%s')) > 0)) {
        	$this->getElement('add_show_duration')->setErrors(array(_('Cannot have duration greater than 24h')));
        	$valid = false;
        }
        

        /* We need to know the show duration broken down into hours and minutes
         * They are used for checking overlapping shows and for validating
         * rebroadcast instances
         */
        $hours = $duration->format("%h");
        $minutes = $duration->format("%i");

        /* Check if show is overlapping
         * We will only do this check if the show is valid
         * upto this point
         */
        if ($valid) {
        	//we need to know the start day of the week in show's local timezome
        	$startDow = $showStartDateTime->format("w");
        	
            $utc = new DateTimeZone('UTC');
            $showStartDateTime->setTimezone($utc);
            $showEndDateTime->setTimezone($utc);

            if ($formData["add_show_repeats"]) {

                //get repeating show end date
                if ($formData["add_show_no_end"]) {
                    $date = Application_Model_Preference::GetShowsPopulatedUntil();

                    if (is_null($date)) {
                        $populateUntilDateTime = new DateTime("now", $utc);
                        Application_Model_Preference::SetShowsPopulatedUntil($populateUntilDateTime);
                    } else {
                        $populateUntilDateTime = clone $date;
                    }

                } elseif (!$formData["add_show_no_end"]) {
                    $popUntil = $formData["add_show_end_date"]." ".$formData["add_show_end_time"];
                    $populateUntilDateTime = new DateTime($popUntil, $showTimezone);
                    $populateUntilDateTime->setTimezone($utc);
                }

                //get repeat interval
                if ($formData["add_show_repeat_type"] == 0) {
                    $interval = 'P7D';
                } elseif ($formData["add_show_repeat_type"] == 1) {
                    $interval = 'P14D';
                } elseif ($formData["add_show_repeat_type"] == 4) {
                    $interval = 'P21D';
                } elseif ($formData["add_show_repeat_type"] == 5) {
                    $interval = 'P28D';
                } elseif ($formData["add_show_repeat_type"] == 2 && $formData["add_show_monthly_repeat_type"] == 2) {
                    $interval = 'P1M';
                } elseif ($formData["add_show_repeat_type"] == 2 && $formData["add_show_monthly_repeat_type"] == 3) {
                    list($weekNumberOfMonth, $dayOfWeek) =
                        Application_Service_ShowService::getMonthlyWeeklyRepeatInterval(
                            new DateTime($start_time, $showTimezone));
                }

                /* Check first show
                 * Continue if the first show does not overlap
                 */
                if ($update) {
                    $overlapping = Application_Model_Schedule::checkOverlappingShows(
                                    $showStartDateTime, $showEndDateTime, $update, null, $formData["add_show_id"]);
                } else {
                    $overlapping = Application_Model_Schedule::checkOverlappingShows(
                                    $showStartDateTime, $showEndDateTime);
                }

                /* Check if repeats overlap with previously scheduled shows
                 * Do this for each show day
                 */
                if (!$overlapping) {

                    if (!isset($formData['add_show_day_check'])) {
                        return false;
                    }

                    foreach ($formData["add_show_day_check"] as $day) {
                        $repeatShowStart = clone $showStartDateTime;
                        $repeatShowEnd = clone $showEndDateTime;
                        $daysAdd=0;
                        if ($startDow !== $day) {
                            if ($startDow > $day)
                                $daysAdd = 6 - $startDow + 1 + $day;
                            else
                                $daysAdd = $day - $startDow;

                            /* In case we are crossing daylights saving time we need
                             * to convert show start and show end to local time before
                             * adding the interval for the next repeating show
                             */
                            $repeatShowStart->setTimezone($showTimezone);
                            $repeatShowEnd->setTimezone($showTimezone);
                            $repeatShowStart->add(new DateInterval("P".$daysAdd."D"));
                            $repeatShowEnd->add(new DateInterval("P".$daysAdd."D"));
                            //set back to UTC
                            $repeatShowStart->setTimezone($utc);
                            $repeatShowEnd->setTimezone($utc);
                        }
                        /* Here we are checking each repeating show by
                         * the show day.
                         * (i.e: every wednesday, then every thursday, etc.)
                         */
                        while ($repeatShowStart->getTimestamp() < $populateUntilDateTime->getTimestamp()) {
                            if ($formData['add_show_id'] == -1) {
                                //this is a new show
                                $overlapping = Application_Model_Schedule::checkOverlappingShows(
                                    $repeatShowStart, $repeatShowEnd);
                                
                                /* If the repeating show is rebroadcasted we need to check
                                 * the rebroadcast dates relative to the repeating show
                                 */
                                if (!$overlapping && $formData['add_show_rebroadcast']) {
                                    $overlapping = self::checkRebroadcastDates(
                                        $repeatShowStart, $formData, $hours, $minutes);
                                }
                            } else {
                                $overlapping = Application_Model_Schedule::checkOverlappingShows(
                                    $repeatShowStart, $repeatShowEnd, $update, null, $formData["add_show_id"]);
                                    
                                if (!$overlapping && $formData['add_show_rebroadcast']) {
                                    $overlapping = self::checkRebroadcastDates(
                                        $repeatShowStart, $formData, $hours, $minutes, true);
                                }
                            }
                            
                            if ($overlapping) {
                                $valid = false;
                                $this->getElement('add_show_duration')->setErrors(array(_('Cannot schedule overlapping shows')));
                                break 1;
                            } else {
                                if ($formData["add_show_repeat_type"] == 2 && $formData["add_show_monthly_repeat_type"] == 3) {
                                    $monthlyWeeklyStart = new DateTime($repeatShowStart->format("Y-m"),
                                        new DateTimeZone("UTC"));
                                    $monthlyWeeklyStart->add(new DateInterval("P1M"));
                                    $repeatShowStart = clone Application_Service_ShowService::getNextMonthlyWeeklyRepeatDate(
                                        $monthlyWeeklyStart,
                                        $formData["add_show_timezone"],
                                        $formData['add_show_start_time'],
                                        $weekNumberOfMonth,
                                        $dayOfWeek);
                                    $repeatShowEnd = clone $repeatShowStart;
                                    $repeatShowEnd->add(new DateInterval("PT".$hours."H".$minutes."M"));
                                } else {
                                    $repeatShowStart->setTimezone($showTimezone);
                                    $repeatShowEnd->setTimezone($showTimezone);
                                    $repeatShowStart->add(new DateInterval($interval));
                                    $repeatShowEnd->add(new DateInterval($interval));
                                    $repeatShowStart->setTimezone($utc);
                                    $repeatShowEnd->setTimezone($utc);
                                }
                            }
                        }
                    }
                } else {
                    $valid = false;
                    $this->getElement('add_show_duration')->setErrors(array(_('Cannot schedule overlapping shows')));
                }
            } elseif ($formData["add_show_rebroadcast"]) {
                /* Check first show
                 * Continue if the first show does not overlap
                 */
                $overlapping = Application_Model_Schedule::checkOverlappingShows($showStartDateTime, $showEndDateTime, $update, $instanceId);

                if (!$overlapping) {
                    $durationToAdd = "PT".$hours."H".$minutes."M";
                    for ($i = 1; $i <= 10; $i++) {
                        
                        if (empty($formData["add_show_rebroadcast_date_absolute_".$i])) break;
                        
                        $abs_rebroadcast_start = $formData["add_show_rebroadcast_date_absolute_".$i]." ".
                                                 $formData["add_show_rebroadcast_time_absolute_".$i];
                        $rebroadcastShowStart = new DateTime($abs_rebroadcast_start);
                        $rebroadcastShowStart->setTimezone(new DateTimeZone('UTC'));
                        $rebroadcastShowEnd = clone $rebroadcastShowStart;
                        $rebroadcastShowEnd->add(new DateInterval($durationToAdd));
                        $overlapping = Application_Model_Schedule::checkOverlappingShows($rebroadcastShowStart,
                            $rebroadcastShowEnd, $update, null, $formData["add_show_id"]);
                        if ($overlapping) {
                            $valid = false;
                            $this->getElement('add_show_duration')->setErrors(array(_('Cannot schedule overlapping shows')));
                            break;
                        }
                    }
                } else {
                    $valid = false;
                    $this->getElement('add_show_duration')->setErrors(array(_('Cannot schedule overlapping shows')));
                }
            } else {
              $overlapping = Application_Model_Schedule::checkOverlappingShows($showStartDateTime, $showEndDateTime, $update, $instanceId);
                if ($overlapping) {
                    $this->getElement('add_show_duration')->setErrors(array(_('Cannot schedule overlapping shows')));
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    public function checkRebroadcastDates($repeatShowStart, $formData, $hours, $minutes, $showEdit=false) {
        $overlapping = false;
        for ($i = 1; $i <= 10; $i++) {
            if (empty($formData["add_show_rebroadcast_date_".$i])) break;
            $rebroadcastShowStart = clone $repeatShowStart;
            /* formData is in local time so we need to set the
             * show start back to local time
             */
            $rebroadcastShowStart->setTimezone(new DateTimeZone(
                $formData["add_show_timezone"]));
            $rebroadcastWhenDays = explode(" ", $formData["add_show_rebroadcast_date_".$i]);
            $rebroadcastWhenTime = explode(":", $formData["add_show_rebroadcast_time_".$i]);
            $rebroadcastShowStart->add(new DateInterval("P".$rebroadcastWhenDays[0]."D"));
            $rebroadcastShowStart->setTime($rebroadcastWhenTime[0], $rebroadcastWhenTime[1]);
            $rebroadcastShowStart->setTimezone(new DateTimeZone('UTC'));
            
            $rebroadcastShowEnd = clone $rebroadcastShowStart;
            $rebroadcastShowEnd->add(new DateInterval("PT".$hours."H".$minutes."M"));
            
            if ($showEdit) {
                $overlapping = Application_Model_Schedule::checkOverlappingShows(
                    $rebroadcastShowStart, $rebroadcastShowEnd, true, null, $formData['add_show_id']);
            } else {
                $overlapping = Application_Model_Schedule::checkOverlappingShows(
                    $rebroadcastShowStart, $rebroadcastShowEnd);
            }
            
            if ($overlapping) break;
        }
        
        return $overlapping;
    }
    
    public function disable()
    {
        $elements = $this->getElements();
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('disabled','disabled');
            }
        }
    }

    public function disableRepeatCheckbox()
    {
        $element = $this->getElement('add_show_repeats');
        if ($element->getType() != 'Zend_Form_Element_Hidden') {
            $element->setAttrib('disabled','disabled');
        }
    }

    public function disableStartDateAndTime()
    {
        $elements = array($this->getElement('add_show_start_date'), $this->getElement('add_show_start_time'));
        foreach ($elements as $element) {
            if ($element->getType() != 'Zend_Form_Element_Hidden') {
                $element->setAttrib('disabled','disabled');
            }
        }
    }
}
