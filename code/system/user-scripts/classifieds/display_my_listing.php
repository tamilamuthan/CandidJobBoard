<?php

class SJB_Classifieds_DisplayMyListing extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$display_form = new SJB_Form();
		$display_form->registerTags($tp);

		$errors = array();
		$criteria_saver = new SJB_ListingCriteriaSaver ('MyListings');

		$listingSID = SJB_Request::getVar("listing_id");
		if (isset ($_REQUEST ['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$listingSID = isset ($passed_parameters_via_uri [0]) ? $passed_parameters_via_uri [0] : null;
		}

		$template = SJB_Request::getVar('display_template', 'display_listing.tpl');

		if (is_null($listingSID)) {
			$errors ['404'] = true;
		} elseif (is_null($listing = SJB_ListingManager::getObjectBySID($listingSID))) {
			$errors ['404'] = true;
		} elseif (!$listing->isActive() && $listing->getUserSID() != SJB_UserManager::getCurrentUserSID()) {
			$errors ['404'] = true;
		} else {

			$display_form = new SJB_Form ($listing);
			$display_form->registerTags($tp);

			$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listing->getListingTypeSID());
			$form_fields = array();
			foreach ($pages as $page) {
				$form_fields = array_merge(SJB_PostingPagesManager::getAllFieldsByPageSIDForForm($page['sid']), $form_fields);
			}

			$listingOwner = SJB_UserManager::getObjectBySID($listing->user_sid);

			// listing preview @author still
			$listingTypeSID = $listing->getListingTypeSID();
			$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSID);
			if (SJB_Request::getInstance()->page_config->uri == '/' . strtolower($listingTypeID) . '-preview/') {
				if (!empty($_SERVER['HTTP_REFERER']) && (stristr($_SERVER['HTTP_REFERER'], 'edit-' . $listingTypeID))) {
						$tp->assign('referer', $_SERVER['HTTP_REFERER']);
				} else {
					$lastPage = SJB_PostingPagesManager::getPagesByListingTypeSID($listingTypeSID);
					$lastPage = array_pop($lastPage);
					$tp->assign('referer', SJB_System::getSystemSettings('SITE_URL') . '/add-listing/'
							. $listingTypeID . '/'
							. $lastPage['page_id'] . '/' . $listing->getSID());
				}
				$tp->assign('checkouted', SJB_ListingManager::isListingCheckOuted($listing->getSID()));
				$tp->assign('contract_id', $listing->contractID);
			}

			$listingStructure = SJB_ListingManager::createTemplateStructureForListing($listing);
			$filename = SJB_Request::getVar('filename', false);
			if ($filename) {
				SJB_UploadFileManager::openFile($filename, $listingSID);
				$errors ['NO_SUCH_FILE'] = true;
			}
			$prev_and_next_listing_id = $criteria_saver->getPreviousAndNextObjectID($listingSID);
			$metaDataProvider =  SJB_ObjectMother::getMetaDataProvider();
			$tp->assign('METADATA', array('listing' => $metaDataProvider->getMetaData($listingStructure ['METADATA']), 'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields)));

			$tp->assign('listing_id', $listingSID);
			$tp->assign('form_fields', $form_fields);
			$tp->filterThenAssign("listing", $listingStructure);
			$tp->assign('prev_next_ids', $prev_and_next_listing_id);
			$tp->assign('preview_listing_sid', SJB_Request::getVar('preview_listing_sid'));
			$tp->assign('listingOwner', $listingOwner);
		}

		foreach ($errors as $k => $v) {
			switch ($k) {
				case '404':
					echo SJB_System::executeFunction('miscellaneous', '404_not_found');
					return;
			}
		}

		$search_criteria_structure = $criteria_saver->createTemplateStructureForCriteria();

		$tp->filterThenAssign('search_criteria', $search_criteria_structure);
		$tp->assign('errors', $errors);
		$tp->assign('myListing', true);
		$tp->display($template);
	}
}
