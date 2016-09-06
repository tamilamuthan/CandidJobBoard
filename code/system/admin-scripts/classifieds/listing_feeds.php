<?php

class SJB_Admin_Classifieds_ListingFeeds extends SJB_Function
{
    public static function getLimits()
    {
        return [
            'values' => [10, 50, 100, 200, 400],
            'default' => 50,
        ];
    }

    public function execute()
    {
        $tp = SJB_System::getTemplateProcessor();
        switch (SJB_Request::getVar('action', '')) {
            case 'edit':
                $feed = SJB_Request::getVar('id');
                $feed = SJB_DB::query('SELECT f.* FROM `listing_feeds` as f WHERE `sid` = ?n', $feed);
                $feed = array_pop($feed);

                $tp->assign('categories', SJB_ListingFieldManager::getFieldInfoBySID(SJB_ListingField::CATEGORIES)['list_values']);
                $tp->assign('job_types', SJB_ListingFieldManager::getFieldInfoBySID(SJB_ListingField::JOB_TYPE)['list_values']);
                $tp->assign('products', SJB_ProductsManager::getUserGroupProducts(SJB_UserGroup::EMPLOYER));
                $tp->assign('radius', SJB_LocationManager::getRadiuses());
                $tp->assign('limits', self::getLimits());
                $tp->assign('feed', $feed);
                $tp->display('edit_listing_feed.tpl');
                break;
            default:
                $feeds = SJB_DB::query('SELECT f.* FROM `listing_feeds` as f ORDER BY f.`order`');
                $tp->assign('feeds', $feeds);
                $tp->display('listing_feeds.tpl');
        }
    }
}
