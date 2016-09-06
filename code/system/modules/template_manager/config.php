<?php

return array
(
	'display_name' => 'Template manager',
	'description' => 'Managing tamplates',
	'classes' => 'classes/',
	'functions' => array
	(
		'edit_css' => array
		(
			'edit_css'		=> 'Edit css files',
			'script'		=> 'edit_css.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'edit_templates' => array
		(
			'edit_templates'=> 'Display Modules',
			'script'		=> 'edit_templates.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'module_list' => array
		(
			'display_name'	=> 'Display Modules',
			'script'		=> 'module_list.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'template_list' => array
		(
			'display_name'	=> '',
			'script'		=> 'template_list.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'edit_template' => array
		(
			'display_name'	=> '',
			'script'		=> 'edit_template.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'theme_editor' => array
		(
			'display_name'	=> 'Theme Editor',
			'script'		=> 'edit_theme.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'customize_theme' => array
		(
			'display_name'	=> 'Customize Theme',
			'script'		=> 'customize_theme.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'navigation_menu' => array
		(
			'display_name'	=> 'Navigation Menu',
			'script'		=> 'navigation_menu.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin', 'user'),
		),
		'edit_email_templates' => array
		(
			'display_name'	=> 'Display Modules',
			'script'		=> 'edit_email_templates.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'delete_uploaded_file' => array
		(
			'display_name'	=> 'Delete Uploaded File',
			'script'		=> 'email_templates_delete_uploaded_file.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)
);
