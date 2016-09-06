<?php

$modules = SJB_System::getModuleManager()->getModulesList();
$modules['miscellaneous']['functions']['jobg8_settings'] = array(
	'display_name'	=> 'JobG8 Plugin Settings',
	'script'		=> '../../plugins/jobg8_integration_plugin/module/miscellaneous/jobg8_settings.php',
	'type'			=> 'admin',
	'access_type'	=> array('admin'),
);
return $modules['miscellaneous'];