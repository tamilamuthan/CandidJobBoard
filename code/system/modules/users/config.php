<?php

return [
    'display_name' => 'User Management',
    'description' => '',
    'classes' => 'classes/',
    'startup_script' => [
        'user' => 'init_current_user_structure',
    ],
    'functions' => [
        'acl' => [
            'display_name' => 'Permissions',
            'script' => 'acl.php',
            'type' => 'admin',
            'access_type' => ['admin'],
            'params' => ['type', 'role'],
        ],

        'registration' => [
            'display_name' => 'Show register block',
            'script' => 'registration.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => ['user_group_id'],
        ],

        'login_as_user' => [
            'display_name' => 'Login as user',
            'script' => 'login_as_user.php',
            'type' => 'user',
            'access_type' => ['admin'],
            'params' => [],
        ],

        'user_groups' => [
            'display_name' => 'Show Users Groups',
            'script' => 'user_groups.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'add_user_group' => [
            'display_name' => 'Add a New User Group',
            'script' => 'add_user_group.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_user_group' => [
            'display_name' => 'Edit User Group',
            'script' => 'edit_user_group.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'delete_user_group' => [
            'display_name' => 'Delete User Group',
            'script' => 'delete_user_group.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_user_profile' => [
            'display_name' => 'Edit User Profile Fields',
            'script' => 'edit_user_profile_fields.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_user_profile_field' => [
            'display_name' => 'Edit User Profile Field',
            'script' => 'edit_user_profile_field.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_list' => [
            'display_name' => 'Edit List',
            'script' => 'edit_list.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_list_item' => [
            'display_name' => 'Edit List Item',
            'script' => 'edit_list_item.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'add_user_profile_field' => [
            'display_name' => 'Add User Profile Field',
            'script' => 'add_user_profile_field.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'delete_user_profile_field' => [
            'display_name' => 'Delete User Profile Field',
            'script' => 'delete_user_profile_field.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'users' => [
            'display_name' => 'Show Users',
            'script' => 'users.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_user' => [
            'display_name' => 'Edit User',
            'script' => 'edit_user.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'login' => [
            'display_name' => 'Login',
            'script' => 'login.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => ['template'],
        ],

        'logout' => [
            'display_name' => 'Login',
            'script' => 'logout.php',
            'type' => 'user',
            'access_type' => ['user'],
        ],

        'password_recovery' => [
            'display_name' => 'Password Recovery',
            'script' => 'password_recovery.php',
            'type' => 'user',
            'access_type' => ['user'],
            'raw_output' => false,
        ],

        'change_password' => [
            'display_name' => 'Change Password',
            'script' => 'change_password.php',
            'type' => 'user',
            'access_type' => ['user'],
            'raw_output' => false,
        ],

        'edit_profile' => [
            'display_name' => 'Edit Profile',
            'script' => 'edit_profile.php',
            'type' => 'user',
            'access_type' => ['user'],
            'raw_output' => false,
        ],

        'delete_uploaded_file' => [
            'display_name' => 'Delete Uploaded File',
            'script' => 'delete_uploaded_file.php',
            'type' => 'user',
            'access_type' => ['user', 'admin'],

        ],

        'init_current_user_structure' => [
            'display_name' => 'Init Current User Structure',
            'script' => 'init_current_user_structure.php',
            'type' => 'user',
            'access_type' => ['user', 'admin'],

        ],

        'add_user' => [
            'display_name' => 'Add User',
            'script' => 'add_user.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'featured_profiles' => [
            'display_name' => 'Featured Companies',
            'script' => 'featured_profiles.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => ['template', 'items_count'],
        ],

        'export_users' => [
            'display_name' => 'Export Users',
            'script' => 'export_users.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'choose_user' => [
            'display_name' => 'Choose User',
            'script' => 'choose_user.php',
            'type' => 'admin',
            'access_type' => ['admin'],
            'raw_output' => true,
        ],
    ]
];
