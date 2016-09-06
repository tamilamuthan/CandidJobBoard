<?php

return array
(
	'display_name' => 'Applications',
	'description' => 'Job applications view',
	'classes' => 'classes/',
	'functions' => array
	(
		'view' => array
		(
			'display_name'	=> 'View applications',
			'script'		=> 'view.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		/**
		 * TODO: возможно, что это нигде не используется. проверить и удалить, если не нужно
		 */
		'edit' => array
		(
			'display_name'	=> 'Edit application',
			'script'		=> 'edit.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
	),
);
