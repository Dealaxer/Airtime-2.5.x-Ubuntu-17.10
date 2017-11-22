<?php

//  Only enable cookie secure if we are supporting https.
//  Ideally, this would always be on and we would force https,
//  but the default installation configs are likely to be installed by
//  amature users on the setup that does not have https.  Forcing
//  cookie_secure on non https would result in confusing login problems.
if(!empty($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', '1');
}
ini_set('session.cookie_httponly', '1');

error_reporting(E_ALL|E_STRICT);

function exception_error_handler($errno, $errstr, $errfile, $errline) {
    //Check if the statement that threw this error wanted its errors to be
    //suppressed. If so then return without with throwing exception.
    if (0 === error_reporting()) return;
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    return false;
}

set_error_handler("exception_error_handler");

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('VERBOSE_STACK_TRACE')
    || define('VERBOSE_STACK_TRACE', (getenv('VERBOSE_STACK_TRACE') ? getenv('VERBOSE_STACK_TRACE') : true));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(LIB_PATH)
)));

set_include_path(APPLICATION_PATH . 'common' . PATH_SEPARATOR . get_include_path());

//Propel classes.
set_include_path(APPLICATION_PATH . 'models' . PATH_SEPARATOR . get_include_path());

//Controller plugins.
set_include_path(APPLICATION_PATH . 'controllers/plugins' . PATH_SEPARATOR . get_include_path());

//Zend framework
if (file_exists('/usr/share/php/libzend-framework-php')) {
    set_include_path('/usr/share/php/libzend-framework-php' . PATH_SEPARATOR . get_include_path());
}

/** Zend_Application */
require_once 'Zend/Application.php';
$application = new Zend_Application(
        APPLICATION_ENV,
        CONFIG_PATH . 'application.ini'
);

require_once(APPLICATION_PATH . "logging/Logging.php");
Logging::setLogPath('/var/log/airtime/zendphp.log');

// Create application, bootstrap, and run
try {
    $sapi_type = php_sapi_name();
    if (substr($sapi_type, 0, 3) == 'cli') {
        set_include_path(APPLICATION_PATH . PATH_SEPARATOR . get_include_path());
        require_once("Bootstrap.php");
    } else {
        $application->bootstrap()->run();
    }
} catch (Exception $e) {
    echo $e->getMessage();
    echo "<pre>";
    echo $e->getTraceAsString();
    echo "</pre>";
    Logging::info($e->getMessage());
    if (VERBOSE_STACK_TRACE) {
        Logging::info($e->getTraceAsString());
    } else {
        Logging::info($e->getTrace());
    }
}

