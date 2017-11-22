<?php

class Application_Form_ShowBuilder extends Zend_Form_SubForm
{

    public function init()
    {
        $user = Application_Model_User::getCurrentUser();

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/showbuilder.phtml'))
        ));

        // Add start date element
        $startDate = new Zend_Form_Element_Text('sb_date_start');
        $startDate->class = 'input_text';
        $startDate->setRequired(true)
                  ->setLabel(_('Date Start:'))
                  ->setValue(date("Y-m-d"))
                  ->setFilters(array('StringTrim'))
                  ->setValidators(array(
                      'NotEmpty',
                      array('date', false, array('YYYY-MM-DD'))))
                  ->setDecorators(array('ViewHelper'));
        $startDate->setAttrib('alt', 'date');
        $this->addElement($startDate);

        // Add start time element
        $startTime = new Zend_Form_Element_Text('sb_time_start');
        $startTime->class = 'input_text';
        $startTime->setRequired(true)
                  ->setValue('00:00')
                  ->setFilters(array('StringTrim'))
                  ->setValidators(array(
                      'NotEmpty',
                      array('date', false, array('HH:mm')),
                      array('regex', false, array('/^[0-2]?[0-9]:[0-5][0-9]$/', 'messages' => _('Invalid character entered')))))
                  ->setDecorators(array('ViewHelper'));
        $startTime->setAttrib('alt', 'time');
        $this->addElement($startTime);

        // Add end date element
        $endDate = new Zend_Form_Element_Text('sb_date_end');
        $endDate->class = 'input_text';
        $endDate->setRequired(true)
                ->setLabel(_('Date End:'))
                ->setValue(date("Y-m-d"))
                ->setFilters(array('StringTrim'))
                ->setValidators(array(
                    'NotEmpty',
                    array('date', false, array('YYYY-MM-DD'))))
                ->setDecorators(array('ViewHelper'));
        $endDate->setAttrib('alt', 'date');
        $this->addElement($endDate);

        // Add end time element
        $endTime = new Zend_Form_Element_Text('sb_time_end');
        $endTime->class = 'input_text';
        $endTime->setRequired(true)
                ->setValue('01:00')
                ->setFilters(array('StringTrim'))
                ->setValidators(array(
                    'NotEmpty',
                    array('date', false, array('HH:mm')),
                    array('regex', false, array('/^[0-2]?[0-9]:[0-5][0-9]$/', 'messages' => _('Invalid character entered')))))
                ->setDecorators(array('ViewHelper'));
        $endTime->setAttrib('alt', 'time');
        $this->addElement($endTime);

        // add a select to choose a show.
        $showSelect = new Zend_Form_Element_Select("sb_show_filter");
        $showSelect->setLabel(_("Show:"));
        $showSelect->setMultiOptions($this->getShowNames());
        $showSelect->setValue(null);
        $showSelect->setDecorators(array('ViewHelper'));
        $this->addElement($showSelect);

        if ($user->getType() === 'H') {
            $myShows = new Zend_Form_Element_Checkbox('sb_my_shows');
            $myShows->setLabel(_('All My Shows:'))
                    ->setDecorators(array('ViewHelper'));
            $this->addElement($myShows);
        }
    }

    private function getShowNames()
    {
        $showNames = array("0" => "-------------------------");

        $shows = CcShowQuery::create()
            ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
            ->orderByDbName()
            ->find();

        foreach ($shows as $show) {

            $showNames[$show->getDbId()] = $show->getDbName();
        }

        return $showNames;
    }

}
