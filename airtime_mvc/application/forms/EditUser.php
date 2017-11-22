<?php
require_once( __DIR__ . '/../validate/NotDemoValidate.php');

class Application_Form_EditUser extends Zend_Form
{

    public function init()
    {
        /*
        $this->addElementPrefixPath('Application_Validate',
                                    '../application/validate',
                                    'validate');
                                    * */

        $currentUser = Application_Model_User::getCurrentUser();
        $currentUserId = $currentUser->getId();
        $userData = Application_Model_User::GetUserData($currentUserId);
        $notEmptyValidator = Application_Form_Helper_ValidationTypes::overrideNotEmptyValidator();
        $emailValidator = Application_Form_Helper_ValidationTypes::overrideEmailAddressValidator();
        $notDemoValidator = new Application_Validate_NotDemoValidate();
        
        $this->setDecorators(array(
                array('ViewScript', array('viewScript' => 'form/edit-user.phtml', "currentUser" => $currentUser->getLogin()))));
        $this->setAttrib('id', 'current-user-form');

        $this->addElement('hash', 'csrf', array(
           'salt' => 'unique'
        ));
        
        $hidden = new Zend_Form_Element_Hidden('cu_user_id');
        $hidden->setDecorators(array('ViewHelper'));
        $hidden->setValue($userData["id"]);
        $this->addElement($hidden);

        $login = new Zend_Form_Element_Text('cu_login');
        $login->setLabel(_('Username:'));
        $login->setValue($userData["login"]);
        $login->setAttrib('class', 'input_text');
        $login->setAttrib('readonly', 'readonly');
        $login->setRequired(true);
        $login->addValidator($notEmptyValidator);
        $login->addFilter('StringTrim');
        $login->setDecorators(array('viewHelper'));
        $this->addElement($login);

        $password = new Zend_Form_Element_Password('cu_password');
        $password->setLabel(_('Password:'));
        $password->setAttrib('class', 'input_text');
        $password->setRequired(true);
        $password->addFilter('StringTrim');
        $password->addValidator($notEmptyValidator);
        $password->setDecorators(array('viewHelper'));
        $this->addElement($password);

        $passwordVerify = new Zend_Form_Element_Password('cu_passwordVerify');
        $passwordVerify->setLabel(_('Verify Password:'));
        $passwordVerify->setAttrib('class', 'input_text');
        $passwordVerify->setRequired(true);
        $passwordVerify->addFilter('StringTrim');
        $passwordVerify->addValidator($notEmptyValidator);
        $passwordVerify->addValidator($notDemoValidator);
        $passwordVerify->setDecorators(array('viewHelper'));
        $this->addElement($passwordVerify);

        $firstName = new Zend_Form_Element_Text('cu_first_name');
        $firstName->setLabel(_('Firstname:'));
        $firstName->setValue($userData["first_name"]);
        $firstName->setAttrib('class', 'input_text');
        $firstName->addFilter('StringTrim');
        $firstName->setDecorators(array('viewHelper'));
        $this->addElement($firstName);

        $lastName = new Zend_Form_Element_Text('cu_last_name');
        $lastName->setLabel(_('Lastname:'));
        $lastName->setValue($userData["last_name"]);
        $lastName->setAttrib('class', 'input_text');
        $lastName->addFilter('StringTrim');
        $lastName->setDecorators(array('viewHelper'));
        $this->addElement($lastName);

        $email = new Zend_Form_Element_Text('cu_email');
        $email->setLabel(_('Email:'));
        $email->setValue($userData["email"]);
        $email->setAttrib('class', 'input_text');
        $email->addFilter('StringTrim');
        $email->setRequired(true);
        $email->addValidator($notEmptyValidator);
        $email->addValidator($emailValidator);
        $email->setDecorators(array('viewHelper'));
        $this->addElement($email);

        $cellPhone = new Zend_Form_Element_Text('cu_cell_phone');
        $cellPhone->setLabel(_('Mobile Phone:'));
        $cellPhone->setValue($userData["cell_phone"]);
        $cellPhone->setAttrib('class', 'input_text');
        $cellPhone->addFilter('StringTrim');
        $cellPhone->setDecorators(array('viewHelper'));
        $this->addElement($cellPhone);

        $skype = new Zend_Form_Element_Text('cu_skype');
        $skype->setLabel(_('Skype:'));
        $skype->setValue($userData["skype_contact"]);
        $skype->setAttrib('class', 'input_text');
        $skype->addFilter('StringTrim');
        $skype->setDecorators(array('viewHelper'));
        $this->addElement($skype);

        $jabber = new Zend_Form_Element_Text('cu_jabber');
        $jabber->setLabel(_('Jabber:'));
        $jabber->setValue($userData["jabber_contact"]);
        $jabber->setAttrib('class', 'input_text');
        $jabber->addFilter('StringTrim');
        $jabber->addValidator($emailValidator);
        $jabber->setDecorators(array('viewHelper'));
        $this->addElement($jabber);

        $locale = new Zend_Form_Element_Select("cu_locale");
        $locale->setLabel(_("Language:"));
        $locale->setMultiOptions(Application_Model_Locale::getLocales());
        $locale->setValue(Application_Model_Preference::GetUserLocale($currentUserId));
        $locale->setDecorators(array('ViewHelper'));
        $this->addElement($locale);
        
        $timezone = new Zend_Form_Element_Select("cu_timezone");
        $timezone->setLabel(_("Interface Timezone:"));
        $timezone->setMultiOptions(Application_Common_Timezone::getTimezones());
        $timezone->setValue(Application_Model_Preference::GetUserTimezone($currentUserId));
        $timezone->setDecorators(array('ViewHelper'));
        $this->addElement($timezone);
    }

    public function validateLogin($p_login, $p_userId) {
        $count = CcSubjsQuery::create()
            ->filterByDbLogin($p_login)
            ->filterByDbId($p_userId, Criteria::NOT_EQUAL)
            ->count();

        if ($count != 0) {
            $this->getElement('cu_login')->setErrors(array(_("Login name is not unique.")));
            return false;
        } else {
            return true;
        }
    }

    // We need to add the password identical validator here in case
    // Zend version is less than 1.10.5
    public function isValid($data) {
        $passwordIdenticalValidator = Application_Form_Helper_ValidationTypes::overridePasswordIdenticalValidator(
            $data['cu_password']);
        $this->getElement('cu_passwordVerify')->addValidator($passwordIdenticalValidator);
        return parent::isValid($data);
    }
}
