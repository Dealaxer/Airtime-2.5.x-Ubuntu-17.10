<?php

class Application_Validate_NotDemoValidate extends Zend_Validate_Abstract
{
    const NOTDEMO = 'notdemo';

    protected $_messageTemplates = array(
        self::NOTDEMO => "Cannot be changed in demo mode"
    );

    public function isValid($value)
    {
    	$this->_setValue($value);
        
        $CC_CONFIG = Config::getConfig();
        if (isset($CC_CONFIG['demo']) && $CC_CONFIG['demo'] == 1) {
        	$this->_error(self::NOTDEMO);
        	return false;	
        } else {
        	return true;
        }
    }
}

