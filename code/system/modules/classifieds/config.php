<?php

return [

    'display_name' => 'Classifieds engine',
    'description' => 'Classifieds engine',
    'classes' => 'classes/',

    'functions' => [

        'listing_fields' => [
            'display_name' => 'Listing Fields',
            'script' => 'listing_fields.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_listing_field' => [
            'display_name' => 'Edit Listing Field',
            'script' => 'edit_listing_field.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'delete_listing_field' => [
            'display_name' => 'Delete Listing Field',
            'script' => 'delete_listing_field.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_listing_type' => [
            'display_name' => 'Edit Listing Type',
            'script' => 'edit_listing_type.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'add_listing_type_field' => [
            'display_name' => 'Add Listing Type Field',
            'script' => 'add_listing_type_field.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_listing_type_field' => [
            'display_name' => 'Edit Listing Type Field',
            'script' => 'edit_listing_type_field.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'delete_listing_type_field' => [
            'display_name' => 'Delete Listing Type Field',
            'script' => 'delete_listing_type_field.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'add_listing' => [
            'display_name' => 'Add Listing',
            'script' => 'add_listing.php',
            'type' => 'user',
            'access_type' => ['admin', 'user'],
            'params' => ['input_template']
        ],

        'display_listing' => [
            'display_name' => 'Display Listing',
            'script' => 'display_listing.php',
            'type' => 'user',
            'access_type' => ['admin', 'user'],
            'params' => ['display_template', 'listing_type_id']
        ],

        'search_form' => [
            'display_name' => 'Search Form',
            'script' => 'search_form.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => ['listing_type_id', 'form_template'],
        ],

        'search_results' => [
            'display_name' => 'Search Form',
            'script' => 'search_results.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => [
                'default_sorting_field',
                'default_sorting_order',
                'default_listings_per_page',
                'results_template',
                'listing_type_id'
            ],
        ],

        'pay_for_listing' => [
            'display_name' => 'Pay For Listing',
            'script' => 'pay_for_listing.php',
            'type' => 'user',
            'access_type' => ['user'],
        ],

        'manage_listings' => [
            'display_name' => 'Manage Listings',
            'script' => 'manage_listings.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'listing_actions' => [
            'display_name' => '',
            'script' => 'listing_actions.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_listing' => [
            'display_name' => 'Edit Listing',
            'script' => 'edit_listing.php',
            'type' => 'user',
            'access_type' => ['admin', 'user'],
            'params' => ['edit_template']
        ],

        'add_listing_step' => [
            'display_name' => 'Add Listing',
            'script' => 'add_listing_step.php',
            'type' => 'user',
            'access_type' => ['admin', 'user'],
            'params' => ['edit_template']
        ],

        'my_listings' => [
            'display_name' => 'My Listings',
            'script' => 'my_listings.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => ['listing_type_id'],
        ],

        'edit_list' => [
            'display_name' => 'Edit List',
            'script' => 'edit_list.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_complex_fields' => [
            'display_name' => 'Edit Fields',
            'script' => 'edit_complex_fields.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'edit_list_item' => [
            'display_name' => 'Edit List Item',
            'script' => 'edit_list_item.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'manage_listing' => [
            'display_name' => 'Manage Listing',
            'script' => 'manage_listing.php',
            'type' => 'user',
            'access_type' => ['admin', 'user'],
        ],

        'move_listing_type_field' => [
            'display_name' => '',
            'script' => 'move_listing_type_field.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'apply_now' => [
            'display_name' => 'Apply Now',
            'script' => 'apply_now.php',
            'type' => 'user',
            'access_type' => ['user'],
        ],

        'apply_now_opportunity' => [
            'display_name' => 'Apply Now',
            'script' => 'apply_now_opportunity.php',
            'type' => 'user',
            'access_type' => ['user'],
        ],

        'delete_uploaded_file' => [
            'display_name' => 'Delete Uploaded File',
            'script' => 'delete_uploaded_file.php',
            'type' => 'user',
            'access_type' => ['user', 'admin'],
        ],

        'featured_listings' => [
            'display_name' => 'Featured Listings',
            'script' => 'featured_listings.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => ['items_count', 'listing_type', 'template'],
        ],

        'latest_listings' => [
            'display_name' => 'Latest Listings',
            'script' => 'latest_listings.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => ['items_count', 'listing_type', 'template', 'mime_type'],
        ],

        'import_listings' => [
            'display_name' => 'Import Listings',
            'script' => 'import_listings.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'export_listings' => [
            'display_name' => 'Export Listings',
            'script' => 'export_listings.php',
            'type' => 'admin',
            'access_type' => ['admin'],
            'raw_output' => false,
        ],

        'browse' => [
            'display_name' => 'Browse',
            'script' => 'browse.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => [
                'level1Field',
                'browse_template',
                'listing_type_id',
                'columns',
                'recordsNumToDisplay',
                'parent'
            ],
        ],

        'display_my_listing' => [
            'display_name' => 'Display My Listing',
            'script' => 'display_my_listing.php',
            'type' => 'user',
            'access_type' => ['admin', 'user'],
            'params' => ['display_template', 'listing_type_id']
        ],

        'listing_feeds' => [
            'display_name' => 'Listing Feeds',
            'script' => 'listing_feeds.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => ['count_listings'],
        ],

        'browseCompany' => [
            'display_name' => 'companies',
            'script' => 'browseCompany.php',
            'type' => 'user',
            'access_type' => ['user'],
            'params' => ['display_template', 'listing_type_id']
        ],

        'import_users' => [
            'display_name' => 'Import Users',
            'script' => 'import_users.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'refine_search' => [
            'display_name' => 'refine search settings',
            'script' => 'refine_search.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'count_listings' => [
            'display_name' => 'count_listings',
            'script' => 'count_listings.php',
            'type' => 'user',
            'access_type' => ['user'],
        ],

        'posting_pages' => [
            'display_name' => 'posting_pages',
            'script' => 'posting_pages.php',
            'type' => 'admin',
            'access_type' => ['admin'],
        ],

        'listing_preview' => [
            'display_name' => 'listing_preview',
            'script' => 'listing_preview.php',
            'type' => 'user',
            'access_type' => ['user'],
        ],
    ],
];
