<?php

class SJB_Classifieds_SearchResults extends SJB_Function
{
	public function isAccessible()
	{
		if (SJB_Array::get($this->params, 'listing_type_id') == 'Resume' && !SJB_Settings::getValue('public_resume_access')) {
			$this->setPermissionLabel('resume_access');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$this->redirectToListingByKeywords();
		// SEO friendly URL for company profile
		$m = array();
		$isCompanyProfilePage = false;
		if (preg_match('#/company/([0-9]+)/.*#', SJB_Navigator::getURI(), $m) ||
		    preg_match('#/investor/([0-9]+)/.*#', SJB_Navigator::getURI(), $m)
            ) {
			$isCompanyProfilePage = true;
			$params = SJB_FixedUrlParamProvider::getParams($_REQUEST);
			if (!empty($params)) {
				$aliasUsername = SJB_UserManager::getUserNameByUserSID($m[1]);
				if (!empty($aliasUsername)) {
					$_REQUEST['username']['equal'] = $aliasUsername;
				}
			}
		}
		if (!empty($_REQUEST['username']['equal']) && is_int($_REQUEST['username']['equal'])) {
			$aliasUsername = SJB_UserManager::getUserNameByUserSID(intval($_REQUEST['username']['equal']));
			if (!empty($aliasUsername))
				$_REQUEST['username']['equal'] = $aliasUsername;
		}

		$listingTypeId = SJB_Request::getVar('listing_type_id', 0);
		if (!$listingTypeId) {
			$listingTypeId = isset($_REQUEST['listing_type']['equal']) ? $_REQUEST['listing_type']['equal'] : SJB_Session::getValue('listing_type_id');
		}
		if ($listingTypeId) {
			$_REQUEST['listing_type']['equal'] = $listingTypeId;
		}
		$action = SJB_Request::getVar('action', 'search');

		//XSS defense
		$searchId = SJB_Request::getVar('searchId', false);
		if ($searchId && !is_numeric($searchId)) {
			$_REQUEST['searchId'] = false;
		}
		$request = $_REQUEST;
		if (SJB_System::getSettingByName('turn_on_refine_search_' . $listingTypeId)) {
			switch ($action) {
				case 'refine':
					$searchID = SJB_Request::getVar('searchId', false);
					unset($request['searchId']);
					$criteria_saver = new SJB_ListingCriteriaSaver($searchID);
					$request = SJB_RefineSearch::mergeCriteria($criteria_saver->getCriteria(), $request);
					break;
				case 'undo':
					$param = SJB_Request::getVar('param', false);
					$field_type = SJB_Request::getVar('type', false);
					$value = SJB_Request::getVar('value', false);
					if ($param && $field_type && $value) {
						$searchID = SJB_Request::getVar('searchId', false);
						unset($request['searchId']);
						$criteria_saver = new SJB_ListingCriteriaSaver($searchID);
						$criteria = $criteria_saver->criteria;
						if (isset($criteria[$param][$field_type])) {
							if (is_array($criteria[$param][$field_type])) {
								foreach ($criteria[$param][$field_type] as $key => $val) {
									if ($val == $value)
										unset($criteria[$param][$field_type][$key]);
								}
							}
							else {
								unset($criteria[$param]);
							}
						}
						$criteria['default_sorting_field'] = $request['default_sorting_field'];
						$criteria['default_sorting_order'] = $request['default_sorting_order'];
						$criteria['default_listings_per_page'] = $request['default_listings_per_page'];
						$criteria['results_template'] = $request['results_template'];

						$request = array_merge($criteria, $request);
					}
					break;
			}
		}

		$searchResultsTP = new SJB_SearchResultsTP($request, $listingTypeId, false, true);
		$searchResultsTP->usePriority(true);
		$template = SJB_Request::getVar("results_template", "search_results.tpl");

		$tp = $searchResultsTP->getChargedTemplateProcessor();
		$userForm = null;
		if ($isCompanyProfilePage) {
			$user = SJB_UserManager::getObjectBySID(intval($m[1]));
			$userForm = new SJB_Form($user);
			$userForm->registerTags($tp);
		}

		$errors = array();
		if (!empty($searchResultsTP->pluginErrors)) {
			foreach ($searchResultsTP->pluginErrors as $err)
				$errors[] = $err;
		}

		$tp->assign('errors', $errors);

		$tp->assign('is_company_profile_page', $isCompanyProfilePage);
		$tp->assign("listing_type_id", $listingTypeId);
		if ($userForm) {
			$tp->assign('form_fields', $userForm->getFormFieldsInfo());
		}

		$tp->display($template);
	}

    public function getAchievements($userSID)
    {
        $achievements = array();
            $achievements = SJB_AchievementManager::getAllAchievementsInfoByUserSID($userSID);
            foreach ($achievements as $key => $achievementInfo) {
                 $achievements[$key] = $achievementInfo;
                 $achievements[$key]['badge'] =  SJB_BadgesManager::getBadgeInfoBySID($achievementInfo['badge_sid']);
        }
        return $achievements;
    }

	private function redirectToListingByKeywords()
	{
		$arrayKeywords = SJB_Request::getVar('keywords');
		if (empty ($arrayKeywords))
			return;
		$keywords = array_pop($arrayKeywords);
		if (empty ($keywords))
			return;
		$id_listing = intval($keywords);
		if ($id_listing == 0)
			return;
		$listing_info = SJB_ListingDBManager::getListingInfoBySID($id_listing);
		if (empty ($listing_info))
			return;

		$type = SJB_ListingTypeDBManager::getListingTypeInfoBySID($listing_info['listing_type_sid']);
		$listing_type = $type['id'];
		$arrayType = SJB_Request::getVar('listing_type');

		if (empty ($arrayType))
			return;
		$expected_type = array_pop($arrayType);

		if ($expected_type != $listing_type)
			return;

		SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_TemplateProcessor::listing_url($listing_info));
	}
}
