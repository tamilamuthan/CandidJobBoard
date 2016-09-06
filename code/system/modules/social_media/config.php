<?php

return array
(
	'display_name' => 'Social Media',
	'description'  => 'Social Media',

	'startup_script' => array (),

	'functions' => array
	(
		'social_media' => array
		(
			'display_name' => 'Social Media',
			'script'       => 'social_media.php',
			'type'         => 'admin',
			'access_type'  => array('admin'),
		),
	),
);
