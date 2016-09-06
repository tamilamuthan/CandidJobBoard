<?php

$timeBegin = microtime(true);
error_reporting(-1);
ini_set('display_errors', 'off');
ini_set('precision', 14); // adequate microtime

$PATH_BASE = dirname(__FILE__);
$DEBUG     = array();

define ('PATH_TO_SYSTEM_CLASS','system/core/System.php');
define ('SJB_BASE_DIR', dirname(__FILE__ )."/");

// start of the script actions
require_once(PATH_TO_SYSTEM_CLASS);

SJB_System::loadSystemSettings('system/user-config/DefaultSettings.php');
SJB_System::loadSystemSettings('config.php');

if (is_null(SJB_System::getSystemSettings('SITE_URL'))) {
	header("Location: install.php");
	exit;
}
else {
	if (is_readable ("install.php") && SJB_System::getSystemSettings('IGNORE_INSTALLER') != 'true') {
		echo '<p>Your installation is temporarily disabled because the install.php file in the root of your'
		.' installation is still readable.<br> To proceed, please remove the file or change its mode to make'
		.' it non-readable for the Apache server process and refresh this page.</p>';
		exit;
	}
}

SJB_System::boot();
SJB_System::init();
if (SJB_Profiler::getInstance()->isProfilerEnable()) {
	SJB_Profiler::getInstance()->setStartTime($timeBegin);
}
SJB_Event::dispatch('AfterSystemBoot');

SJB_UpdateManager::updateDatabase();

// bind session clear to task scheduler event
SJB_Event::handle('task_scheduler_run', array('SJB_Session', 'clearTemporaryData'));

SJB_Request::getInstance()->execute();

SJB_HelperFunctions::debugInfoPrint();
