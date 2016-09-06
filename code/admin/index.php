<?php

$timeBegin = microtime(true);
error_reporting(-1);
ini_set('display_errors', 'off');
ini_set('precision', 14); // adequate microtime

define ('PATH_TO_SYSTEM_CLASS','../system/core/System.php');
$DEBUG = array();
$PATH_BASE = str_replace('/admin', '', dirname(__FILE__));

require_once(PATH_TO_SYSTEM_CLASS);
define ('SJB_BASE_DIR', realpath(dirname(__FILE__ ) . "/..") . '/');
SJB_System::loadSystemSettings ('../system/admin-config/DefaultSettings.php');
SJB_System::loadSystemSettings ('../config.php');

$GLOBALS['system_settings']['USER_SITE_URL'] = $GLOBALS['system_settings']['SITE_URL'];
$GLOBALS['system_settings']['SITE_URL'] = $GLOBALS['system_settings']['ADMIN_SITE_URL'];

// load installed SJB version info
SJB_System::setGlobalTemplateVariable('version', SJB_System::getSystemSettings('version'));

SJB_System::boot();
SJB_System::init();
if (SJB_Profiler::getInstance()->isProfilerEnable()) {
	SJB_Profiler::getInstance()->setStartTime($timeBegin);
}

SJB_Request::getInstance()->execute();

SJB_HelperFunctions::debugInfoPrint();
