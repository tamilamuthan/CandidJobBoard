<?php

class SJB_Admin_Classifieds_ExportListings extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('export_listings');
		return parent::isAccessible();
	}

	public function execute()
	{
		ini_set('max_execution_time', 0);
		$tp = SJB_System::getTemplateProcessor();
		
		$listingTypeId = SJB_Request::getVar('listing_type_id', 0);
		if (!$listingTypeId) {
			$listingTypeId = SJB_Request::getVar('listing_type', 0);
			if ($listingTypeId) {
				$listingTypeId = $listingTypeId['equal'];
			}
		}
		
		$exportProperties = SJB_Request::getVar('export_properties', array());
		
		$listing  = SJB_ExportController::createListing($listingTypeId);
		$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($_REQUEST, $listing);
		$searchFormBuilder = new SJB_SearchFormBuilder($listing);
		$searchFormBuilder->registerTags($tp);
		$searchFormBuilder->setCriteria($criteria);
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (empty($exportProperties)) {
				SJB_FlashMessages::getInstance()->addWarning('EMPTY_EXPORT_PROPERTIES');
			} else {
				$searcher         = new SJB_ListingSearcher();
				$searchAliases    = SJB_ExportController::getSearchPropertyAliases();
				$foundListingsSid = $searcher->getObjectsSIDsByCriteria($criteria, $searchAliases);
				
				if (empty($foundListingsSid)) {
					SJB_FlashMessages::getInstance()->addWarning('EMPTY_EXPORT_DATA');
				} else {
					$result = SJB_ExportController::createExportDirectories();
					if ($result === true) {
						$exportProperties['extUserID'] = 1;
						if (!empty($exportProperties['GooglePlace'])) {
							$exportProperties['Location'] = 1;
						}
						$exportAliases = SJB_ExportController::getExportPropertyAliases();
						$exportData    = SJB_ExportController::getExportData($foundListingsSid, $exportProperties, $exportAliases);
						
						$fileName = mb_strtolower($listingTypeId) . 's.xls';
						SJB_HelperFunctions::makeXLSExportFile($exportData, $fileName, 'Listings');
						if (!file_exists(SJB_System::getSystemSettings('EXPORT_FILES_DIRECTORY') . "/{$fileName}")) {
							SJB_FlashMessages::getInstance()->addWarning('CANT_CREATE_EXPORT_FILES');
						} else {
							SJB_ExportController::sendExportFile($fileName);
						}
					}
				}
			}
		}
		$properties = SJB_ListingManager::getAllListingPropertiesID($listingTypeId);
		foreach ($properties['common'] as $key => $property) {
			if ($property['id'] == 'Location') {
				unset($properties['common'][$key]);
			}
		}
		$tp->assign('properties', $properties);
		$tp->assign('selected_listing_type_id', $listingTypeId);
		$tp->display('export_listings.tpl');
	}
}
