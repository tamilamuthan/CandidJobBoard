<?php

return array
(
	'display_name' => 'Site Pages',
	'description' => 'Managing site pages',
	'functions' => array
	(
		'edit_user_pages' => array
		(
			'display_name'	=> 'Editing site pages',
			'script'		=> 'edit_user_pages.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)
);
