<?php

return [
    'sl-96-themes', function() {
        $success = true;
        $success &= SJB_DB::query('update `settings` set `name` = concat(`name`, "_Bootstrap") where `name` like "%theme_%"');
        SJB_DB::query('update `settings` set `name` = replace(`name`, "_Bootstrap_Bootstrap", "_Bootstrap") where `name` like "%_Bootstrap_Bootstrap%"');
        ThemeManager::reset();
        SJB_Settings::loadSettings();
        $settings = ThemeManager::getThemeSettings('Bootstrap');
        $success = copy(SJB_BASE_DIR . 'templates/Bootstrap/main/images/' . $settings['logo'], SJB_BASE_DIR . 'templates/Bootstrap/assets/images/' . $settings['logo']) && $success;
        $success = copy(SJB_BASE_DIR . 'templates/Bootstrap/new/img/' . $settings['favicon'], SJB_BASE_DIR . 'templates/Bootstrap/assets/images/' . $settings['favicon']) && $success;
        $success = copy(SJB_BASE_DIR . 'templates/Bootstrap/new/' . $settings['main_banner'], SJB_BASE_DIR . 'templates/Bootstrap/assets/images/' . $settings['main_banner']) && $success;
        $success = copy(SJB_BASE_DIR . 'templates/Bootstrap/new/img/' . $settings['secondary_banner'], SJB_BASE_DIR . 'templates/Bootstrap/assets/images/' . $settings['secondary_banner']) && $success;
        ThemeManager::compileStyles();
        return $success;
    },
    'google-place-full-text', function() {
        return SJB_DB::query('ALTER TABLE `listings` ADD FULLTEXT INDEX GooglePlaceF(`GooglePlace`);') &&
            SJB_DB::query('ALTER TABLE `users` ADD FULLTEXT INDEX GooglePlaceF(`GooglePlace`);');
    },
    'patch-apply_click', function() {
        return SJB_DB::query('alter table `listings` drop COLUMN `apply_click`') &&
            SJB_DB::query('alter table `listings` drop COLUMN `PostedWithin`');
    },
    'youtube-facebook-phrases', function() {
        $phrases = [
            [
                'phrase' => '<b>e.g.</b> https://youtu.be/XXXXXXXXXXX',
                'translations' => [
                    'en' => '<b>e.g.</b> https://youtu.be/XXXXXXXXXXX',
                    'de' => '',
                    'es' => '',
                    'fr' => '',
                    'ru' => '<b>например:</b> https://youtu.be/XXXXXXXXXXX'
                ]
            ],
            [
                'phrase' => 'View details and Apply',
                'translations' => [
                    'en' => 'View details and Apply',
                    'de' => '',
                    'es' => '',
                    'fr' => '',
                    'ru' => 'Посмотреть детали и подать заявку'
                ]
            ],
            [
                'phrase' => 'Next',
                'translations' => [
                    'en' => 'Next',
                    'de' => '',
                    'es' => '',
                    'fr' => '',
                    'ru' => 'Следующая'
                ]
            ],
            [
                'phrase' => 'Previous',
                'translations' => [
                    'en' => 'Previous',
                    'de' => '',
                    'es' => '',
                    'fr' => '',
                    'ru' => 'Предыдущая'
                ]
            ]
        ];
        foreach ($phrases as $phrase) {
            foreach ($phrase['translations'] as $lang => $translation) {
                $langFile = SJB_BASE_DIR . "languages/{$lang}.pages.xml";
                if (file_exists($langFile)) {
                    file_put_contents($langFile, str_replace(
                        '<page key="Frontend">',
                        sprintf('<page key="Frontend">
                                  <string key="%s">
                                    <tr lang="%s"> %s </tr>
                                  </string>', XML_Util::replaceEntities($phrase['phrase']), $lang, XML_Util::replaceEntities($translation)),
                        file_get_contents($langFile))
                    );
                }
            }
        }
        return true;
    },
    'sl-76-more-results', function() {
        $pages = SJB_DB::query('select `uri`, `parameters` from `pages` where uri in (?l)', [
            '/jobs/', '/resumes/', '/company/'
        ]);
        $success = true;
        foreach ($pages as $page) {
            $params = unserialize($page['parameters']);
            if (!$params) {
                return false;
            }
            $params['default_listings_per_page'] = '20';
            $success &= SJB_DB::query('update `pages` set `parameters` = ?s where `uri` = ?s', serialize($params), $page['uri']);
        }
        return $success;
    },
    'locations-service', function() {
        $files = scandir(SJB_BASE_DIR . 'system/cache');
        foreach ($files as $file) {
            if (strpos($file, 'sess_') !== false && $file != 'sess_' . SJB_Session::getSessionId()) {
                @unlink(SJB_BASE_DIR . 'system/cache/' . $file);
            }
        }
        foreach ($_SESSION as $key => $value) {
            if (stripos($key, 'goole_location_') !== false) {
                unset($_SESSION[$key]);
            }
        }
        return true;
    },
    'remove-add-listing-type', function() {
        return SJB_DB::query('delete from `pages` where `uri` in ("/add-listing-type/", "/delete-listing-type/", "/listing-types/", "/edit-listing-field/edit-fields/edit-list/", "/edit-listing-field/edit-fields/edit-list-item/", "/add-listing-field/")');
    },
    'SL-49', function() {
        $files = [
            'templates/Bootstrap/field_types/display/file.tpl',
            'templates/Bootstrap/field_types/display/picture.tpl',
            'templates/Flow/field_types/display/file.tpl',
            'templates/Flow/field_types/display/picture.tpl',
            'templates/Simplicity/field_types/display/file.tpl',
            'templates/Simplicity/field_types/display/picture.tpl',
        ];
        foreach ($files as $file) {
            @unlink(SJB_BASE_DIR . $file);
        }
        return true;
    },
    'SL-122', function() {
        return SJB_DB::query('UPDATE `user_profile_fields` SET `width` = "250", `height` = "250", `second_width` = "150" WHERE `id` = "Logo" AND `width` = "180" AND `height` = "190"');
    },
    'SL-100', function() {
        $queries = [
            'ALTER TABLE `listing_feeds` DROP `type`',
            'ALTER TABLE `listing_feeds` DROP `count`',
            'ALTER TABLE `listing_feeds` DROP INDEX name',
            'ALTER TABLE `listing_feeds` ADD `id` VARCHAR(255) NOT NULL',
            'ALTER TABLE `listing_feeds` ADD `order` INT NOT NULL DEFAULT 0',
            'ALTER TABLE `listing_feeds` ADD INDEX(`id`)',
            'update listing_feeds set `name` = "Latest Jobs (RSS)", `description` = "", `id` = "rss", `order` = 1 where sid = 3',
            "update listing_feeds set `name` = 'Indeed', `description` = 'Using this feed you can submit your jobs to Indeed.com.<br/> Use this link <a target=\"_blank\" href=\"http://www.indeed.com/hire?indpubnum=6053709130975284\">http://www.indeed.com/hire?indpubnum=6053709130975284</a> for instruction on how to get started.', `id` = 'indeed', `order` = 2 where sid = 2",
            "update listing_feeds set `name` = 'SimplyHired', `description` = 'Using this feed you can submit your jobs to SimplyHired.com.<br/> Use this link <a href=\"http://www.simplyhired.com/a/add-jobs/feed\" target=\"_blank\">http://www.simplyhired.com/a/add-jobs/feed</a> for instruction on how to get started.', `id` = 'simplyhired', `order` = 3 where sid = 1",
            'INSERT INTO `listing_feeds` (`sid`, `name`, `template`, `description`, `mime_type`, `id`, `order`) VALUES (NULL, \'Trovit\', \'feed_trovit.tpl\', \'Using this feed you can submit your jobs to job.trovit.com.<br/> Use this link <a href="http://about.trovit.com/your-ads-on-trovit/us/" target="_blank">http://about.trovit.com/your-ads-on-trovit/us/</a> for instruction on how to get started.\', \'text/xml\', \'trovit\', \'4\')',
            'update listing_feeds set mime_type = concat(mime_type, "; charset=utf-8") where mime_type not like "%utf-8%"',
            'INSERT INTO `pages` (`uri`, `pass_parameters_via_uri`, `module`, `function`, `template`, `title`, `access_type`, `parameters`, `keywords`, `description`, `ID`, `content`) VALUES (\'/feeds/\', \'1\', \'classifieds\', \'listing_feeds\', \'empty.tpl\', \'Listing Feeds\', \'user\', \'a:0:{}\', \'\', \'\', NULL, NULL)',
        ];
        $success = true;
        foreach ($queries as $query) {
            $success &= SJB_DB::queryExec($query);
        }

        $params = SJB_DB::queryValue('select `parameters` from `pages` where `uri` = "/rss/"');
        $params = unserialize($params);
        $params['template'] = 'feed_rss.tpl';
        $success &= SJB_DB::queryExec('update `pages` set `parameters` = ?s where `uri` = "/rss/"', serialize($params));

        $files = [
            'templates/_system/classifieds/feed_indeed_test.tpl',
            'templates/Bootstrap/classifieds/feed_error.tpl',
            'templates/Bootstrap/classifieds/feed_rss.tpl',
            'templates/Bootstrap/classifieds/rss2.tpl',
            'templates/Bootstrap/classifieds/feed_simplyhired.tpl',
            'templates/Bootstrap/classifieds/feed_indeed.tpl',
            'templates/Bootstrap/classifieds/feed_indeed_test.tpl',
            'templates/Flow/classifieds/feed_error.tpl',
            'templates/Flow/classifieds/feed_rss.tpl',
            'templates/Flow/classifieds/rss2.tpl',
            'templates/Flow/classifieds/feed_simplyhired.tpl',
            'templates/Flow/classifieds/feed_indeed.tpl',
            'templates/Flow/classifieds/feed_indeed_test.tpl',
            'templates/Simplicity/classifieds/feed_error.tpl',
            'templates/Simplicity/classifieds/feed_rss.tpl',
            'templates/Simplicity/classifieds/rss2.tpl',
            'templates/Simplicity/classifieds/feed_simplyhired.tpl',
            'templates/Simplicity/classifieds/feed_indeed.tpl',
            'templates/Simplicity/classifieds/feed_indeed_test.tpl',
        ];
        foreach ($files as $file) {
            @unlink(SJB_BASE_DIR . $file);
        }

        return $success;
    },
    'SL-120', function() {
        return SJB_DB::queryExec('delete from `pages` where `uri` = "/category/"');
    },
    'SL-129', function() {
        $dirs = [
            SJB_BASE_DIR . 'system/ext/vendor/guzzlehttp',
            SJB_BASE_DIR . 'system/ext/vendor/mailgun',
            SJB_BASE_DIR . 'system/ext/vendor/php-http',
            SJB_BASE_DIR . 'system/ext/vendor/psr/http-message',
        ];
        foreach ($dirs as $dir) {
            exec('rm -Rf ' . $dir);
        }
        return true;
    },
    'SL-119', function() {
        if (!SJB_Settings::getValue('date_format')) {
            $formats = SJB_DateFormatter::getFormats();
            SJB_Settings::saveSetting('date_format', current($formats));
        }
        SJB_DB::query('update `email_templates` set `text` = replace(`text`, "|date_format", "|date")');
        return true;
    },
    'SL-138', function() {
        SJB_DB::query('delete from `settings` where `name` like "%simplyhired%"');
        exec(sprintf('rm -Rf ' . SJB_BASE_DIR . 'system/plugins/simply_hired'));
        return true;
    },
    '5.0.3', function() {
        ThemeManager::compileStyles();
        $files = [
            'templates/Bootstrap/field_types/display/file.tpl',
            'templates/Bootstrap/field_types/display/picture.tpl',
            'templates/Bootstrap/field_types/input/complex.tpl',
            'templates/Bootstrap/field_types/input/date.tpl',
            'templates/Bootstrap/field_types/input/expiration_date.tpl',
            'templates/Bootstrap/field_types/search/geo.distance.tpl',
            'templates/Bootstrap/payment/invoice_payment_page.tpl',
            'templates/Flow/field_types/display/file.tpl',
            'templates/Flow/field_types/display/picture.tpl',
            'templates/Flow/field_types/input/complex.tpl',
            'templates/Flow/field_types/input/date.tpl',
            'templates/Flow/field_types/input/expiration_date.tpl',
            'templates/Flow/field_types/search/geo.distance.tpl',
            'templates/Flow/payment/invoice_payment_page.tpl',
            'templates/Simplicity/field_types/display/file.tpl',
            'templates/Simplicity/field_types/display/picture.tpl',
            'templates/Simplicity/field_types/input/complex.tpl',
            'templates/Simplicity/field_types/input/date.tpl',
            'templates/Simplicity/field_types/input/expiration_date.tpl',
            'templates/Simplicity/field_types/search/geo.distance.tpl',
            'templates/Simplicity/payment/invoice_payment_page.tpl',
            'templates/_system/field_types/search/geo.distance.tpl',
            'templates/_system/field_types/search/geo.tpl',
            'templates/_system/field_types/search/pictures.tpl',
        ];
        foreach ($files as $file) {
            @unlink(SJB_BASE_DIR . $file);
        }
        return true;
    },
];