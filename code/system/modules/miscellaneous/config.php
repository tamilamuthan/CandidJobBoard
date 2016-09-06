<?php

return [
    'display_name' => 'Miscellaneous',
    'description' => 'Miscellaneous routines',

    'startup_script' => [],

    'functions' => [
        'settings' => [
            'display_name' => 'Settings',
            'script' => 'settings.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'filters' => [
            'display_name' => 'filters',
            'script' => 'filters.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'task_scheduler' => [
            'display_name' => 'Settings',
            'script' => 'task_scheduler.php',
            'raw_output' => true,
            'type' => 'user',
            'access_type' => ['user'],
        ],
        'task_scheduler_settings' => [
            'display_name' => 'Task Scheduler Settings',
            'script' => 'task_scheduler_settings.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'ajax' => [
            'display_name' => 'Ajax',
            'script' => 'ajax.php',
            'type' => 'user',
            'access_type' => ['user'],
        ],
        '404_not_found' => [
            'display_name' => '404 Page Not Found',
            'script' => '404.php',
            'type' => 'user',
            'access_type' => ['user', 'admin'],
        ],
        'adminpswd' => [
            'display_name' => 'Admin Password',
            'script' => 'adminpswd.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'contact_form' => [
            'display_name' => 'Contact Form',
            'script' => 'contact_form.php',
            'type' => 'user',
            'access_type' => ['user'],
        ],
        'plugins' => [
            'display_name' => 'Plugins',
            'script' => 'plugins.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'sitemap_generator' => [
            'display_name' => 'sitemap generator',
            'script' => 'sitemap_generator.php',
            'raw_output' => true,
            'type' => 'user',
            'access_type' => ['user'],
        ],
        'maintenance_mode' => [
            'display_name' => 'Maintenance Mode',
            'script' => 'maintenance_mode.php',
            'raw_output' => true,
            'type' => 'user',
            'access_type' => ['user'],
        ],
        'function_is_not_accessible' => [
            'display_name' => 'Function is not accessible',
            'script' => 'error.php',
            'type' => 'user',
            'access_type' => ['user'],
        ],
        'ajax_file_upload_handler' => [
            'display_name' => 'Ajax File Upload Handler',
            'script' => 'ajax_file_upload_handler.php',
            'type' => 'user',
            'access_type' => ['user', 'admin'],
            'raw_output' => true,
        ],
        'kcfinder' => [
            'display_name' => 'KCFinder',
            'script' => 'kcfinder.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'update_to_new_version' => [
            'display_name' => 'Update to new version of SJB',
            'script' => 'update_to_new_version.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'update_check' => [
            'display_name' => 'Check for SJB update',
            'script' => 'update_check.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'update_diff' => [
            'display_name' => 'Viw files difference',
            'script' => 'update_diff.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'countries' => [
            'display_name' => 'Countries',
            'script' => 'countries.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'states' => [
            'display_name' => 'States',
            'script' => 'states.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'get_states' => [
            'display_name' => 'Get States Names',
            'script' => 'get_states.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'get_user_states' => [
            'display_name' => 'Get States Names',
            'script' => 'get_states.php',
            'type' => 'user',
            'access_type' => ['user'],
            'raw_output' => true,
        ],
        'mail_check' => [
            'display_name' => 'Mail Check',
            'script' => 'mail_check.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],
        'opengraph_meta' => [
            'display_name' => 'OpenGraph Meta',
            'script' => 'opengraph_meta.php',
            'type' => 'user',
            'access_type' => ['user'],
        ],
    ],
];
