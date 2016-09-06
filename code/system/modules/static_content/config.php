<?php

return array
(
	'display_name' => 'Static Content',
	'description' => 'Static Content',
	'classes' => 'classes/',
	'functions' => array
	(
		'show_static_content' => array
		(
			'display_name'	=> 'Show Static Content',
			'script'		=> 'show_static_content.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params'		=> array('pageid'),	
		),
	)
);

