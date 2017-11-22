<?php

class Application_Form_Preferences extends Zend_Form
{

    public function init()
    {
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'form/preferences.phtml'))
        ));

        $general_pref = new Application_Form_GeneralPreferences();

        $this->addElement('hash', 'csrf', array(
           'salt' => 'unique',
           'decorators' => array(
                'ViewHelper'
            )
        ));

        $this->addSubForm($general_pref, 'preferences_general');

            $email_pref = new Application_Form_EmailServerPreferences();
            $this->addSubForm($email_pref, 'preferences_email_server');

        $soundcloud_pref = new Application_Form_SoundcloudPreferences();
        $this->addSubForm($soundcloud_pref, 'preferences_soundcloud');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Save'));
        //$submit->removeDecorator('Label');
        $submit->setAttribs(array('class'=>'btn right-floated'));
        $submit->removeDecorator('DtDdWrapper');

        $this->addElement($submit);
    }
}
