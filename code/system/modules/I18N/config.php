<?php


return array
(
	'display_name' => 'I18N',
	'description' => '',
	'startup_script' =>	array (),
	'functions' => array
	(
		'manage_languages' => array
		(
			'display_name'	=> 'Languages',
			'script'		=> 'languages.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		), 

		'manage_phrases' => array
		(
			'display_name'	=> 'Manage Phrases',
			'script'		=> 'manage_phrases.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'edit_phrase' => array
		(
			'display_name'	=> 'Edit Phrase',
			'script'		=> 'update_phrase.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)
);
