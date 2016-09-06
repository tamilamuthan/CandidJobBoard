<?php

class SJB_Admin_Menu_AdminMenu extends SJB_Function
{
    public function isAccessible()
    {
        return true;
    }

    public function execute()
    {
        $GLOBALS['LEFT_ADMIN_MENU'] = [
            'Job Board' => [
                [
                    'title' => 'Job Postings',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/manage-jobs/',
                    'highlight' => [],
                ],
                [
                    'title' => "Employer Profiles",
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/manage-users/employer/',
                    'highlight' => [],
                ],
                [
                    'title' => 'Resumes',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/manage-resumes/',
                    'highlight' => [],
                ],
                [
                    'title' => "Job Seeker Profiles",
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/manage-users/jobseeker/',
                    'highlight' => [],
                ],
                [
                    'title' => 'Job Alerts',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/guest-alerts/',
                    'highlight' => [],
                ],
            ],
            'Appearance' => [
                [
                    'title' => 'Themes',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/edit-themes/',
                ],
                [
                    'title' => 'Customize Theme',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/customize-theme/',
                ],
                [
                    'title' => 'Navigation Menu',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/navigation-menu/',
                ],
                [
                    'title' => 'Templates',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/edit-templates/',
                ],
                [
                    'title' => 'Email Templates',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/edit-email-templates/',
                ],
            ],
            'Content' => [
                [
                    'title' => 'Pages',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/user-pages/',
                ],
                [
                    'title' => 'Blog',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/blog/',
                ],
            ],
            'eCommerce' => [
                [
                    'title' => 'Orders',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/manage-invoices/',
                    'highlight' =>
                        [
                            SJB_System::getSystemSettings('SITE_URL') . '/view-invoice/',
                        ]
                ],
                [
                    'title' => 'Employer Products',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/products/employer/',
                    'highlight' => [],
                ],
                [
                    'title' => 'Job Seeker Products',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/products/jobseeker/',
                    'highlight' => [],
                ],
                [
                    'title' => 'Discounts',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/promotions/',
                    'highlight' => [
                        SJB_System::getSystemSettings('SITE_URL') . '/add-promotion-code/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-promotion-code/',
                        SJB_System::getSystemSettings('SITE_URL') . '/promotions/log/',
                    ],
                ],
                [
                    'title' => 'Payment Methods',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/system/payment/gateways/',
                    'highlight' =>
                        [
                            SJB_System::getSystemSettings('SITE_URL') . '/configure-gateway/',
                        ],
                ],

            ]
        ];

        $GLOBALS['LEFT_ADMIN_MENU']['Listing Fields'] = [
            [
                'title' => 'Job Fields',
                'reference' => SJB_System::getSystemSettings('SITE_URL') . '/posting-pages/job/edit/11',
                'highlight' => [],
            ],
            [
                'title' => 'Resume Fields',
                'reference' => SJB_System::getSystemSettings('SITE_URL') . '/posting-pages/resume/edit/19',
                'highlight' => [],
            ],
            [
                'title' => 'Categories',
                'reference' => SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-field/edit-list/?field_sid=' . SJB_ListingField::CATEGORIES,
                'highlight' =>
                    [
                        SJB_System::getSystemSettings('SITE_URL') . '/add-listing-type-field/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-type-field/',
                        SJB_System::getSystemSettings('SITE_URL') . '/delete-listing-type-field/',
                        SJB_System::getSystemSettings('SITE_URL') . '/posting-pages/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-field/edit-list/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-field/edit-list-item/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-field/edit-location-fields/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-field/edit-fields/',
                    ],
            ],
            [
                'title' => 'Job Types',
                'reference' => SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-field/edit-list/?field_sid=' . SJB_ListingField::JOB_TYPE,
                'highlight' =>
                    [
                        SJB_System::getSystemSettings('SITE_URL') . '/add-listing-type-field/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-type-field/',
                        SJB_System::getSystemSettings('SITE_URL') . '/delete-listing-type-field/',
                        SJB_System::getSystemSettings('SITE_URL') . '/posting-pages/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-field/edit-list/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-field/edit-list-item/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-field/edit-location-fields/',
                        SJB_System::getSystemSettings('SITE_URL') . '/edit-listing-field/edit-fields/',
                    ],
            ]
        ];


        $GLOBALS['LEFT_ADMIN_MENU']['Settings'] =
            [
                [
                    'title' => 'System Settings',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/settings/',
                    'highlight' => [],
                ],
                [
                    'title' => 'Admin Password',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/adminpswd/',
                ],
                [
                    'title' => 'Edit Language',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/manage-phrases/',
                    'highlight' => [],
                ],
                [
                    'title' => 'Refine Search Settings',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/refine-search-settings/',
                ],
                [
                    'title' => 'Task Scheduler',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/task-scheduler-settings/',
                ],
                [
                    'title' => 'Job Backfilling',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/backfilling/',
                    'highlight' => [],
                ],
                [
                    'title' => 'RSS/XML Feeds',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/listing-feeds/',
                    'highlight' => [],
                ],
                [
                    'title' => 'Job Auto Import',
                    'reference' => SJB_System::getSystemSettings('SITE_URL') . '/show-import/',
                    'highlight' =>
                        [
                            SJB_System::getSystemSettings('SITE_URL') . '/add-import/',
                            SJB_System::getSystemSettings('SITE_URL') . '/edit-import/',
                            SJB_System::getSystemSettings('SITE_URL') . '/run-import/',
                        ],
                ],
            ];

        if (SJB_System::getSystemSettings("isSaas")) {
            foreach ($GLOBALS['LEFT_ADMIN_MENU'] as $menuKey => $menuItems) {
                foreach ($menuItems as $key => $menuItem) {
                    if (in_array($menuItem['title'], array('Templates', 'Task Scheduler', 'Admin Password'))) {
                        unset ($GLOBALS['LEFT_ADMIN_MENU'][$menuKey][$key]);
                    }
                }
            }
        }
    }
}
