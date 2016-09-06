<?php

return array
(
    'display_name' => 'Blog',
    'description' => 'Blog module',

    'startup_script'	=>	array (),

    'functions' => array
    (
        'blog' => array
        (
            'display_name'	=> 'Blog',
            'script'		=> 'blog.php',
            'type'			=> 'admin',
            'access_type'	=> array('admin', 'user'),
        ),
    )
);
