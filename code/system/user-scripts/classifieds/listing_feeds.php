<?php

class SJB_Classifieds_ListingFeeds extends SJB_Function
{
    public function execute()
    {
        $feed = false;
        $params = SJB_UrlParamProvider::getParams();
        if ($params) {
            $feed = SJB_DB::query('SELECT * FROM listing_feeds WHERE `id` = ?s', basename($params[0], '.xml'));
        }
        // todo: feedId deprecated since 5.0.3
        if (!$feed && SJB_Request::getInt('feedId')) {
            $feed = SJB_DB::query('SELECT * FROM listing_feeds WHERE `sid` = ?n', SJB_Request::getInt('feedId', ''));
        }
        if (!$feed) {
            echo SJB_System::executeFunction('miscellaneous', '404_not_found');
            return;
        }
        $feed = array_shift($feed);
        $template = $feed['template'];
        $mime = $feed['mime_type'];

        $limits = SJB_Admin_Classifieds_ListingFeeds::getLimits();
        $limit = SJB_Request::getInt('limit', $limits['default']);
        if (!in_array($limit, $limits['values'])) {
            $limit = $limits['default'];
        }

        $criteria = [
            'listing_type' => [
                'equal' => 'Job'
            ],
            'active' => [
                'equal' => 1
            ],
            'default_sorting_field' => 'activation_date',
            'default_sorting_order' => 'DESC',
            'default_listings_per_page' => $limit
        ];

        if (SJB_Request::getVar('featured')) {
            $criteria['featured']['equal'] = 1;
        }
        if (SJB_Request::getVar('keywords')) {
            $criteria['keywords']['all_words'] = SJB_Request::getVar('keywords');
        }
        if (SJB_Request::getVar('location')) {
            $criteria['GooglePlace']['location']['value'] = SJB_Request::getVar('location');
            $radiuses = SJB_LocationManager::getRadiuses();
            $radius = SJB_Request::getInt('radius');
            if (empty($radius) || !in_array($radius, $radiuses['values'])) {
                $radius = $radiuses['default'];
            }
            $criteria['GooglePlace']['location']['radius'] = $radius;
        }
        if (SJB_Request::getInt('job_type')) {
            $criteria['EmploymentType']['multi_like'] = [SJB_Request::getInt('job_type')];
        }
        if (SJB_Request::getVar('products')) {
            $criteria['product_info']['like'] = [];
            foreach (explode(',', SJB_Request::getVar('products')) as $item) {
                $item = intval($item);
                if ($item) {
                    $criteria['product_info']['like'][] = sprintf('product_sid";s:%s:"%s"', strlen((string) $item), $item);
                }
            }
        }
        if (SJB_Request::getVar('categories')) {
            $categories = explode(',', SJB_Request::getVar('categories'));
            $criteria['JobCategory']['multi_like'] = [];
            foreach ($categories as $category) {
                $category = intval($category);
                if ($category) {
                    $criteria['JobCategory']['multi_like'][] = $category;
                }
            }
        }
        if (SJB_Request::getVar('exclude_imported') || $feed['id'] == 'indeed') {
            $criteria['data_source']['is_null'] = '1';
            $jobg8 = SJB_UserManager::getUserInfoByUserName('jobg8');
            if ($jobg8) {
                $contract = SJB_ContractManager::getAllContractsInfoByUserSID($jobg8['sid'])[0];
                $contract = unserialize($contract['serialized_extra_info']);
                $criteria['product_info']['not_like'] = sprintf('product_sid";s:%s:"%s"', strlen((string) $contract['product_sid']), $contract['product_sid']);
            }
        }
        if ($feed['id'] == 'indeed') {
            // exclude everything that goes away
            $criteria['ApplicationSettings']['not_like'] = 'http';
        }

        $searchResultsTP = new SJB_SearchResultsTP($criteria, 'Job');
        $tp = $searchResultsTP->getChargedTemplateProcessor();

        for ($i = 0; $i < ob_get_level(); $i++) {
            ob_end_clean();
        }
        header('Content-Type: ' . $mime);
        $tp->assign('feed', $feed);
        $tp->assign('lastBuildDate', date('D, d M Y H:i:s'));
        $tp->display($template);
        exit();
    }
}
