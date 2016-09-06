<?php

return array
(
	'display_name' => 'Social',
	'description' => 'Social Plugins',

	'startup_script'	=>	array (),

	'functions' => array
	(
		'social_login' => array
		(
			'display_name'	=> 'Social Login Forms',
			'script'		=> 'social_login.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'registration_social' => array
		(
			'display_name'	=> 'Show register block',
			'script'		=> 'registration_social.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'link_with_linkedin' => array
		(
			'display_name'	=> 'Link Profile With Linkedin',
			'script'		=> 'link_with_linkedin.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'social_plugins' => array
		(
			'display_name'	=> 'List Of available Social Plugins',
			'script'		=> 'social_plugins.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
	),
);
