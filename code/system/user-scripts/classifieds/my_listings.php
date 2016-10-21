<?php

class SJB_Classifieds_MyListings extends SJB_Function
{
	protected $listingTypeID = null;
	protected $listingTypeSID = null;
	protected $requestCriteria = array();

	public function execute()
	{
		if (!function_exists('_filter_data')) {
			function _filter_data(&$array, $key, $pattern)
			{
				if (isset($array[$key])) {
					if (!preg_match($pattern, $array[$key]))
						unset($array[$key]);
				}
			}
		}

		_filter_data($_REQUEST, 'sorting_field', "/^[_\w\d]+$/");
		_filter_data($_REQUEST, 'sorting_order', "/(^DESC$)|(^ASC$)/i");
		_filter_data($_REQUEST, 'default_sorting_field', "/^[_\w\d]+$/");
		_filter_data($_REQUEST, 'default_sorting_order', "/(^DESC$)|(^ASC$)/i");

		$tp = SJB_System::getTemplateProcessor();
		if (!SJB_UserManager::isUserLoggedIn()) {
			$errors['NOT_LOGGED_IN'] = true;
			$tp->assign("ERRORS", $errors);
			$tp->display("error.tpl");
			return;
		}

		$this->defineRequestedListingTypeID();
		$currentUser = SJB_UserManager::getCurrentUser();
                
		if (!$this->listingTypeID) {
                        $page = 'resume';
                        $eSid = SJB_UserGroupManager::getUserGroupSIDByID('Entrepreneur');
                        $iSid = SJB_UserGroupManager::getUserGroupSIDByID('Investor');
                        switch ($currentUser->getUserGroupSID()) {
                            case $eSid: $page = 'idea'; break;
                            case $iSid: $page = 'opportunity'; break;
                            case SJB_UserGroup::EMPLOYER: $page = 'job'; break;
                            case SJB_UserGroup::JOBSEEKER: $page = 'resume'; break;
                        }
			SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . '/my-listings/' . $page . '/');
		
                        
                }
                
		$this->listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($this->listingTypeID);

		$this->requestCriteria = array(
			'user_sid' 		=> array('equal' => $currentUser->getSID()),
			'listing_type_sid' 	=> array('equal' => $this->listingTypeSID)
		);

		$acl = SJB_Acl::getInstance();

		SJB_ListingManager::deletePreviewListingsByUserSID($currentUser->getSID());

		$searcher = new SJB_ListingSearcher();

		// to save criteria in the session different from search_results
		$criteriaSaver = new SJB_ListingCriteriaSaver('MyListings');

		if (isset($_REQUEST['restore'])) {
			$_REQUEST = array_merge($_REQUEST, $criteriaSaver->getCriteria());
		}

		if (isset($_REQUEST['listings'])) {
			$listingsSIDs = $_REQUEST['listings'];
			$userListingsSIDs = array();
			$listings = SJB_ListingManager::getListingsByUserSID($currentUser->getSID());
			foreach ($listings as $listing) {
				$userListingsSIDs[] = $listing->sid;
			}
			$userListingsSIDs = array_flip($userListingsSIDs);
			$listingsSIDs = array_intersect_key($listingsSIDs, $userListingsSIDs);
		}

		if (!empty($listingsSIDs)) {
			if (isset($_REQUEST['action_deactivate'])) {
				$this->executeAction($listingsSIDs, 'deactivate');
			}
			elseif (isset($_REQUEST['action_activate'])) {
				$redirectToShoppingCard = false;
				$activatedListings = array();
				foreach ($listingsSIDs as $listingSID => $value) {
					$listingInfo = SJB_ListingManager::getListingInfoBySID($listingSID);
					if ($listingInfo['active']) {
						continue;
					}
					else if ($listingInfo['checkouted'] == 0) {
						$redirectToShoppingCard = true;
					}
					else if (SJB_ListingManager::activateListingBySID($listingSID, false)) {
						$activatedListings[] = $listingSID;
					}
				}
				SJB_BrowseDBManager::addListings($activatedListings);
				if ($redirectToShoppingCard) {
					$shoppingUrl = SJB_System::getSystemSettings('SITE_URL') . '/shopping-cart/';
					SJB_HelperFunctions::redirect($shoppingUrl);
				}
			} else if (isset($_REQUEST['action_delete'])) {
				$this->executeAction($listingsSIDs, 'delete');
			}
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/my-listings/" . mb_strtolower($this->listingTypeID) . '/');
		}

		$listing = new SJB_Listing(array(), $this->listingTypeSID);
		$idAliasInfo = $listing->addIDProperty();
		$listing->addActivationDateProperty();
		$listing->addKeywordsProperty();
		$listing->addActiveProperty();
		$listing->addNumberOfViewsProperty();
		if (!$listing->getProperty('expiration_date')) {
			$listing->addExpirationDateProperty();
		}
		$listingTypeIdAliasInfo = $listing->addListingTypeIDProperty();

		$sortingFields = array();
		$innerJoin = array();
		$sortingField = SJB_Request::getVar("sorting_field", null);
		$sortingOrder = SJB_Request::getVar("sorting_order", null);
		if (isset($sortingField, $sortingOrder)) {
			$orderInfo = array(
				'sorting_field' => $sortingField,
				'sorting_order' => $sortingOrder
			);
		} else {
			$orderInfo = $criteriaSaver->getOrderInfo();
			$sortingField = isset($orderInfo['sorting_field']) ? $orderInfo['sorting_field'] :null;
		}

		if ($orderInfo['sorting_field'] == 'applications') {
			$innerJoin['applications'] = array(
				'count'       => 'count(`applications`.id) as appCount',
				'join'        => 'LEFT JOIN',
				'join_field'  => 'listing_id',
				'join_field2' => 'sid',
				'main_table'  => 'listings',
			);
			$sortingFields['appCount'] = $orderInfo['sorting_order'];
			$searcher->setGroupByField(array('listings' => 'sid'));
		}
		else if ($orderInfo['sorting_field'] == 'id') {
			$sortingFields['sid'] = $orderInfo['sorting_order'];
		} else {
			$property = $listing->getProperty($sortingField);
			if (!empty($property) && $property->isSystem()) {
				$sortingFields[$orderInfo['sorting_field']] = $orderInfo['sorting_order'];
			} else {
				$sortingFields['activation_date'] = 'DESC';
			}
		}

		$this->requestCriteria['sorting_field'] = $orderInfo['sorting_field'];
		$this->requestCriteria['sorting_order'] = $orderInfo['sorting_order'];

		$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData(array_merge($_REQUEST, $this->requestCriteria), $listing);
		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias($idAliasInfo);
		$aliases->addAlias($listingTypeIdAliasInfo);
		$foundListingsSIDs = $searcher->getObjectsSIDsByCriteria($criteria, $aliases, $sortingFields, $innerJoin);

		// получим информацию о имеющихся листингах
		$listingsInfo = array();
		$contractInfo['extra_info']['listing_amount'] = 0;

		if ($acl->isAllowed('post_' . $this->listingTypeID)) {
			$permissionParam = $acl->getPermissionParams('post_' . $this->listingTypeID);
			if (empty($permissionParam)) {
				$contractInfo['extra_info']['listing_amount'] = 'unlimited';
			} else {
				$contractInfo['extra_info']['listing_amount'] = $permissionParam;
			}
		}
		$contractsSIDs = $currentUser->getContractID();
		$listingsInfo['listingsNum'] = SJB_ContractManager::getListingsNumberByContractSIDsListingType($contractsSIDs, $this->listingTypeID);
		$listingsInfo['listingsMax'] = $contractInfo['extra_info']['listing_amount'];
		if ($listingsInfo['listingsMax'] === 'unlimited') {
			$listingsInfo['listingsLeft'] = 'unlimited';
		} else {
			$listingsInfo['listingsLeft'] = $listingsInfo['listingsMax'] - $listingsInfo['listingsNum'];
			$listingsInfo['listingsLeft'] = $listingsInfo['listingsLeft'] < 0 ? 0 : $listingsInfo['listingsLeft'];
		}

		$tp->assign('listingTypeID', $this->listingTypeID);
		$tp->assign('listingTypeName', SJB_ListingTypeManager::getListingTypeNameBySID($this->listingTypeSID));
		$tp->assign('listingsInfo', $listingsInfo);

		$page = SJB_Request::getVar('page', 1);
		$criteriaSaver->setSessionForListingsPerPage(10);
		$criteriaSaver->setSessionForCurrentPage($page);
		$criteriaSaver->setSessionForCriteria($_REQUEST);
		$criteriaSaver->setSessionForOrderInfo($orderInfo);
		$criteriaSaver->setSessionForObjectSIDs($foundListingsSIDs);

		// get Applications
		$appsGroups = SJB_Applications::getAppGroupsByEmployer($currentUser->getSID());
		$apps = array();
		foreach ($appsGroups as $group) {
			$apps[$group['listing_id']] = $group['count'];
		}

		$searchCriteriaStructure = $criteriaSaver->createTemplateStructureForCriteria();
		$listingSearchStructure = $criteriaSaver->createTemplateStructureForSearch();

		/**************** P A G I N G *****************/
		if ($listingSearchStructure['current_page'] > $listingSearchStructure['pages_number']) {
			$listingSearchStructure['current_page'] = $listingSearchStructure['pages_number'];
		}
		if ($listingSearchStructure['current_page'] < 1) {
			$listingSearchStructure['current_page'] = 1;
		}

		$sortedFoundListingsSIDsByPages = array_chunk($foundListingsSIDs, $listingSearchStructure['listings_per_page'], true);

		/************* S T R U C T U R E **************/
		$listingsStructure = array();
		$listingStructureMetaData = array();

		if (isset($sortedFoundListingsSIDsByPages[$listingSearchStructure['current_page'] - 1])) {
			foreach ($sortedFoundListingsSIDsByPages[$listingSearchStructure['current_page'] - 1] as $sid) {
				$listing = SJB_ListingManager::getObjectBySID($sid);
				$listingStructure = SJB_ListingManager::createTemplateStructureForListing($listing);
				$listingsStructure[$listing->getID()] = $listingStructure;

				if (isset($listingStructure['METADATA'])) {
					$listingStructureMetaData = array_merge($listingStructureMetaData, $listingStructure['METADATA']);
				}
			}
		}

		/*************** D I S P L A Y ****************/
		$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
		$metadata = array();
		$metadata['listing'] = $metaDataProvider->getMetaData($listingStructureMetaData);

		$tp->assign('METADATA', $metadata);
		$tp->assign('sorting_field', $listingSearchStructure['sorting_field']);
		$tp->assign('sorting_order', $listingSearchStructure['sorting_order']);
		$tp->assign('property', $this->getSortableProperties());
		$tp->assign('listing_search', $listingSearchStructure);
//		$tp->assign('search_criteria', $searchCriteriaStructure);
		$tp->assign('listings', $listingsStructure);
		$tp->assign('apps', $apps);

		$contractsInfo = SJB_ContractManager::getAllContractsInfoByUserSID($currentUser->getSID());

		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		foreach ($contractsInfo as $key => $contractInfo) {
			$contractInfo['extra_info'] = unserialize($contractInfo['serialized_extra_info']);
			$contractInfo['listingAmount'] = array();
			foreach ($listingTypes as $listingType) {
				$listingTypeID = $listingType['id'];
				if ($this->acl->isAllowed('post_' . $listingTypeID, $contractInfo['id'], 'contract')) {
					$contractInfo['listingAmount'][$listingTypeID]['name'] = $listingType['name'];
					$permissionParam = $this->acl->getPermissionParams('post_' . $listingTypeID, $contractInfo['id'], 'contract');
					$contractInfo['listingAmount'][$listingTypeID]['numPostings'] = $contractInfo['number_of_postings'];
					if (empty($permissionParam)) {
						$contractInfo['listingAmount'][$listingTypeID]['count'] = 'unlimited';
						$contractInfo['listingAmount'][$listingTypeID]['listingsLeft'] = 'unlimited';
					}
					else {
						$contractInfo['listingAmount'][$listingTypeID]['count'] = $permissionParam;
						$contractInfo['listingAmount'][$listingTypeID]['listingsLeft'] = max($contractInfo['listingAmount'][$listingTypeID]['count'] - $contractInfo['listingAmount'][$listingTypeID]['numPostings'], 0);
					}
				}
			}

			$contractsInfo[$key] = $contractInfo;
			$contractsInfo[$key]['product_info'] = SJB_ProductsManager::getProductInfoBySID($contractInfo['extra_info']['product_sid']);
		}
		$tp->assign('my_products', $contractsInfo);

		$tp->display('my_listings.tpl');
	}

	/**
	 * @param array  $listingsIds Used listing sids
	 * @param string $action      Actions performed with the listings(delete, deactivate, activate)
	 */
	private function executeAction(array $listingsIds, $action)
	{
		if (empty($listingsIds)) {
			return;
		}
		
		$processListingsIds = array();
		foreach ($listingsIds as $key => $value) {
			$processListingsIds[] = $key;
		}
		
		switch ($action) {
			case 'delete':
				SJB_ListingManager::deleteListingBySID($processListingsIds);
				return;
			case 'deactivate':
				SJB_ListingManager::deactivateListingBySID($processListingsIds);
				return;
		}
	}

	protected function defineRequestedListingTypeID()
	{
		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$params = SJB_FixedUrlParamProvider::getParams($_REQUEST);
			if ($params) {
				$this->listingTypeID = array_pop($params);
			}
		} else {
			$this->listingTypeID = isset($_REQUEST['listing_type_id']) ? $_REQUEST['listing_type_id'] : null;
		}
	}

	/**
	 * Returns sortable properties by listing
	 * @return array
	 */
	private function getSortableProperties()
	{
		$emptyListing = new SJB_Listing(array(), $this->listingTypeSID);
		$emptyListing->addIDProperty();
		$emptyListing->addListingTypeIDProperty();
		$emptyListing->addActivationDateProperty();
		$emptyListing->addNumberOfViewsProperty();
		$emptyListing->addApplicationsProperty();
		$emptyListing->addActiveProperty();
		$emptyListing->addExpirationDateProperty(null);

		$sortableProperties = array();
		$propertyList = $emptyListing->getPropertyList();

		foreach ($propertyList as $property) {
			$sortableProperties[$property]['is_sortable'] = true;
		}
		return $sortableProperties;
	}
}
