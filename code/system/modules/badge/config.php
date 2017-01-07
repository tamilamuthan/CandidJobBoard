<?php
return array
(
	'display_name' => 'Badge',
	'description' => 'Handles badges routines',

	'startup_script'	=>	array (),

	'functions' => array
	(
		'badges' => array(
								'display_name'	=> 'Badges',
								'script'		=> 'badges.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'add_badge' => array(
								'display_name'	=> 'Add Badge',
								'script'		=> 'add_badge.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'edit_badge' => array(
								'display_name'	=> 'Edit Badge',
								'script'		=> 'edit_badge.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'user_badges' => array(
								'display_name'	=> 'Badges',
								'script'		=> 'badges.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								'params'		=> array ('action', 'userGroupID')
								),
		'user_badge' => array(
								'display_name'	=> 'User Badge',
								'script'		=> 'user_badge.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								'params'		=> array ('action')
								),
	),
);
