<?php

class SJB_Miscellaneous_Ajax extends SJB_Function
{
	public function execute()
	{
		$action = SJB_Request::getVar('action', null);

		// set for debug call via GET request
		if (empty($action)) {
			$action = SJB_Request::getVar('action', '');
		}

		switch ($action) {
			case 'request_for_listings':
				SJB_AjaxRequests::requestToListingsProviders();
				break;

			case 'get_refine_search_block':
				SJB_AjaxRequests::getRefineSearchBlock();
				break;

			default :
				exit();
		}
		exit();
	}
}

class SJB_AjaxRequests
{
	/**
	 * Method gets request for listings to listings providers and dispatch individual events for providers plugins.
	 * After dispatch will be created usual listings structure and than will be returned as JSON, marked to jQuery callback.
	 *
	 * @static
	 * @return mixed
	 */
	public static function requestToListingsProviders()
	{
		// get list of listing providers
		$listingProviders = array();
		SJB_Event::dispatch('registerListingProviders', $listingProviders, true);

		$_REQUEST['listing_type']['equal'] =
		$listing_type_id = 'Job';

		$searchResultsTP = new SJB_SearchResultsTP($_REQUEST, $listing_type_id);
		// manually create listing_search_structure (in main search for listings this called in getChargedTemplateProcessor)
		// This need to properly work of listings providers per page search
		$searchResultsTP->listing_search_structure = $searchResultsTP->criteria_saver->createTemplateStructureForSearch();

		// dispatch event to given listings providerName
		$listingsStructure = array();
		foreach ($listingProviders as $providerName) {
			try {
				SJB_Event::dispatch($providerName . 'BeforeGenerateListingStructure', $searchResultsTP, true);
			} catch(Exception $e) {
				error_log($e->getMessage() . ' ' . $e->getTraceAsString());
			}
			// fill listings structure with provider listings
			SJB_Event::dispatch($providerName . 'AfterGenerateListingStructure', $listingsStructure, true);
		}

		$tp = $searchResultsTP->getChargedTemplateProcessorForListingStructure($listingsStructure);

		$tp->display('../classifieds/search_results_jobs_listings.tpl');
	}

	public static function getRefineSearchBlock()
	{
		$tp = SJB_System::getTemplateProcessor();
		$listingTypeId = SJB_Request::getVar('listing_type');
		if (!isset($listingTypeId['equal'])) {
			$_REQUEST['listing_type']['equal'] = SJB_Session::getValue('listing_type_id');
		}

		$searchResultsTP = new SJB_SearchResultsTP($_REQUEST, $listingTypeId['equal']);
		$searchCriteria = $searchResultsTP->getCriteriaSaver()->getCriteria();
		if (SJB_Request::getVar('showRefineFields', false)) {
			$refineFields = SJB_RefineSearch::getRefineFieldsByCriteria($searchResultsTP, $searchCriteria);
			$tp->assign('refineFields', $refineFields);
		}
		$currentSearch = SJB_RefineSearch::getCurrentSearchByCriteria($searchCriteria);
		$tp->assign('currentSearch', $currentSearch);
		$tp->assign('searchId', SJB_Request::getVar('searchId'));
		$tp->display('../classifieds/search_results_refine_block.tpl');
	}

}