<?php

class SJB_Classifieds_Browse extends SJB_Function
{
	var $parameters = [];

	public function isAccessible()
	{
		$browseUrl = SJB_Request::getVar('browseUrl', false);
		if ($browseUrl) {
			$parameters = SJB_BrowseDBManager::getBrowseParametersByUri($browseUrl);
			$this->parameters = array_merge($_REQUEST, unserialize($parameters));
		} else {
			$this->parameters = $_REQUEST;
		}

		$listingTypeId = SJB_Request::getVar('listing_type_id', '');
		$browseManager = SJB_ObjectMother::createBrowseManager($listingTypeId, $this->parameters);
		$params = $browseManager->getParams();
		if (empty($params))
			return true;
		return parent::isAccessible();
	}

	public function execute()
	{
		if (empty($this->parameters['passed_parameters_via_uri'])) {
			$this->parameters['passed_parameters_via_uri'] = '';
		}
		$this->parameters['passed_parameters_via_uri'] = str_replace('jobs-in-', '', $this->parameters['passed_parameters_via_uri']);
		$this->parameters['passed_parameters_via_uri'] = str_replace('-', ' ', $this->parameters['passed_parameters_via_uri']);
		$uri = str_replace('jobs-in-', '', $_SERVER['REQUEST_URI']);
		$uri = parse_url(str_replace('-', ' ', $uri));
		if (!preg_match("/\\/$/", $uri['path'])) {
			$uri = parse_url($_SERVER['REQUEST_URI']);
			$query = isset($uri['query']) ? '?' . $uri['query'] : '';
			SJB_HelperFunctions::redirect($uri['path'] . '/' . $query);
		} else {
			$uri = SJB_Request::getVar('browseUrl', $this->getUri());
		}
		$listingTypeId = SJB_Request::getVar('listing_type_id', '');
		$browseManager = SJB_ObjectMother::createBrowseManager($listingTypeId, $this->parameters);
		$browseItems = [];
		if ($browseManager->canBrowse()) {
			$browseItems = $browseManager->getItemsFromDB($uri, true);
		}
		$tp = $this->getTemplateProcessor($browseManager, $listingTypeId);

		// if we did't find anything then try to math browse and url (for cities with dashes)
		if (!$browseManager->canBrowse() && !$tp->getVariable('listings')->value) {
			$browseItems = $browseManager->getItemsFromDB($uri, false);
			$rawUrl = rawurldecode($_REQUEST['passed_parameters_via_uri']);
			foreach (array_keys($browseItems) as $item) {
				$url = SJB_TemplateProcessor::pretty_url($item, false);
				if (strpos($rawUrl, $url) !== false) {
					$this->parameters['passed_parameters_via_uri'] = strtolower($url);
					$browseManager = SJB_ObjectMother::createBrowseManager($listingTypeId, $this->parameters);
					$tp = $this->getTemplateProcessor($browseManager, $listingTypeId);
					break;
				}
			}
		}

		ksort($browseItems, SORT_FLAG_CASE | SORT_NATURAL);
		$tp->assign('browseItems', $browseItems);
		$tp->assign('recordsNumToDisplay', SJB_Array::get($this->params, 'recordsNumToDisplay', 21));
		$tp->assign('user_page_uri', $uri);
		$tp->assign('sitePageUri', SJB_HelperFunctions::getSiteUrl() . $this->getUri());
		$tp->assign('browse_level', $browseManager->getLevel() + 1);
		$tp->assign('browse_navigation_elements', $browseManager->getNavigationElements($uri));
		$tp->assign('browse_request_data', $browseManager->getRequestDataForSearchResults());
		$tp->display(SJB_Request::getVar('browse_template', 'browse_items_and_results.tpl'));
	}

	protected function getUri()
	{
		$globalTemplateVariables = SJB_System::getGlobalTemplateVariables();
		$uri = $globalTemplateVariables['GLOBALS']['user_page_uri'];
		return preg_match("/\\/$/", $uri) ? $uri : $uri . '/';
	}

	/**
	 * @param SJB_BrowseManager $browseManager
	 * @param $listingTypeId
	 * @return SJB_TemplateProcessor
	 */
	protected function getTemplateProcessor($browseManager, $listingTypeId)
	{
		if ($browseManager->canBrowse()) {
			$browsing_meta_data = $browseManager->getBrowsingMetaData();
			$tp = SJB_System::getTemplateProcessor();
			$tp->assign('METADATA', $browsing_meta_data);
		} else {
			$requestData = $browseManager->getRequestDataForSearchResults();
			$requestData['default_listings_per_page'] = SJB_Array::get($this->params, 'recordsNumToDisplay', 21);
			$requestData['default_sorting_field'] = SJB_Request::getVar('sorting_field', 'activation_date');
			$requestData['default_sorting_order'] = SJB_Request::getVar('sorting_order', 'DESC');
			if (isset($_REQUEST['restore']))
				$requestData['restore'] = 1;
			else
				$requestData['action'] = 'search';
			if (isset($_REQUEST['searchId']))
				$requestData['searchId'] = SJB_Request::getVar('searchId');
			$requestData['listings_per_page'] = SJB_Request::getVar('listings_per_page', $requestData['default_listings_per_page']);
			$requestData['page'] = SJB_Request::getVar('page', null);

			$searchResultsTP = new SJB_SearchResultsTP($requestData, $listingTypeId, false);
			$searchResultsTP->usePriority(true);
			$tp = $searchResultsTP->getChargedTemplateProcessor();
			$tp->assign('errors', $searchResultsTP->pluginErrors);
			$tp->assign('listing_type', $listingTypeId);
		}
		$tp->assign('columns', SJB_Request::getVar('columns', 1));
		return $tp;
	}
}
