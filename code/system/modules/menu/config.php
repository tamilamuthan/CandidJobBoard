<?php

return array
(
	'display_name' => 'Menu',
	'description' => 'Top menu, My Account, User Menu',
	'classes' => 'classes/',
	'startup_script'	=>	array (
		'admin'	=> 'admin_menu',
		),
	'functions' => array
	(
		'admin_menu' => array
		(
			'display_name'	=> 'Admin Menu',
			'script'		=> 'startup_admin.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'raw_output'	=> true,
		),
		'show_left_menu' => array
		(
			'display_name'	=> 'Left admin menu',
			'script'		=> 'menu_block.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'user_menu' => array
		(
			'display_name'	=> 'User Menu',
			'script'		=> 'user_menu.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'footer_menu' => array
		(
			'display_name'	=> 'Footer',
			'script'		=> 'footer_menu.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
	),
);
