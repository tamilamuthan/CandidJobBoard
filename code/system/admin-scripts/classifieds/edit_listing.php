<?php

class SJB_Admin_Classifieds_EditListing extends SJB_Function
{
	public function isAccessible()
	{
		$listingId = SJB_Request::getVar('listing_id', null);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
		$listingTypeId = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
		$listingType = !in_array($listingTypeId, array('Resume', 'Job')) ? "{$listingTypeId}_listings" : $listingTypeId . 's';
		$this->setPermissionLabel(
			array(
				'manage_' . strtolower($listingType),
			)
		);
		return parent::isAccessible();
	}

	public function execute()
	{
		$listing_id = SJB_Request::getVar('listing_id', null);
		$listing_info = SJB_ListingManager::getListingInfoBySID($listing_id);
		$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listing_info['listing_type_sid']);

		if (!is_null($listing_info)) {
			$filename = SJB_Request::getVar('filename', false);
			if ($filename) {
				$file = SJB_UploadFileManager::openFile($filename, $listing_id);
				$errors['NO_SUCH_FILE'] = true;
			}

			$listing_info = array_merge($listing_info, $_REQUEST);

			$listing = new SJB_Listing($listing_info, $listing_info['listing_type_sid']);
			$listing->setSID($listing_id);

			$listing_edit_form = new SJB_Form($listing);

			$form_is_submitted = SJB_Request::getVar('action');

			$errors = array();

			if ($form_is_submitted && $listing_edit_form->isDataValid($errors)) {
				$listingSid = $listing->getID();
				SJB_BrowseDBManager::deleteListings($listingSid);
				SJB_ListingManager::saveListing($listing);
				SJB_BrowseDBManager::addListings($listingSid);
				
				$formToken           = SJB_Request::getVar('form_token');
				$sessionFilesStorage = SJB_Session::getValue('tmp_uploads_storage');
				$uploadedFields      = SJB_Array::getPath($sessionFilesStorage, $formToken);
				if (!empty($uploadedFields)) {
					foreach ($uploadedFields as $fieldId => $fieldValue) {
						// get field of listing
						$isComplex = false;
						if (strpos($fieldId, ':') !== false) {
							$isComplex = true;
						}

						$tmpUploadedFileId = $fieldValue['file_id'];
						// rename it to real listing field value
						$newFileId = $fieldId . "_" . $listing->getSID();
						$uploadFileSID = SJB_DB::queryValue("SELECT `sid` FROM `uploaded_files` WHERE `id` = ?s", $tmpUploadedFileId);
						if ($uploadFileSID) {
							SJB_DB::query("DELETE FROM `uploaded_files` WHERE `id` = ?s", $newFileId);
						}
						SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` =?s", $newFileId, $tmpUploadedFileId);

						if ($isComplex) {
							list($parentField, $subField, $complexStep) = explode(':', $fieldId);
							$parentProp  = $listing->getProperty($parentField);
							$parentValue = $parentProp->getValue();

							// look for complex property with current $fieldID and set it to new value of property
							if (!empty($parentValue)) {
								foreach ($parentValue as $id => $value) {
									if ($id == $subField) {
										$parentValue[$id][$complexStep] = $newFileId;
									}
								}
								$listing->setPropertyValue($parentField, $parentValue);
							}
						} else {
							$listing->setPropertyValue($fieldId, $newFileId);
						}

						// unset value from session temporary storage
						$sessionFilesStorage = SJB_Array::unsetValueByPath($sessionFilesStorage, "{$formToken}/{$fieldId}");
					}

					//and remove token key from temporary storage
					$sessionFilesStorage = SJB_Array::unsetValueByPath($sessionFilesStorage, "{$formToken}");
					SJB_Session::setValue('tmp_uploads_storage', $sessionFilesStorage);

					SJB_ListingManager::saveListing($listing);
				}



				SJB_Event::dispatch('listingEdited', $listingSid);

				if (SJB_Request::isAjax()) {
					echo '<p class="green">Listing Saved</p>';
					exit;
				}

				if ($form_is_submitted == 'save_info') {
					$listingTypeId = SJB_ListingTypeManager::getListingTypeIDBySID($listing_info['listing_type_sid']);
					$listingType = $listingTypeId !='Job' && $listingTypeId !='Resume' ? $listingTypeId . '-listings' : $listingTypeId . 's';
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . "/manage-" . strtolower($listingType) . "/?restore=1");
				}
				$listing_info = SJB_ListingManager::getListingInfoBySID($listing_id);
				$listing = new SJB_Listing($listing_info, $listing_info['listing_type_sid']);
				$listing->setSID($listing_id);
				$listing_edit_form = new SJB_Form($listing);
			}

			$form_fields = $listing_edit_form->getFormFieldsInfo();
			$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listing->getListingTypeSID());
			$realFormFields = array();
			foreach ($pages as $page) {
				$listingFields = SJB_PostingPagesManager::getAllFieldsByPageSIDForForm($page['sid']);
				foreach ($listingFields as $fieldID => $listingField) {
					if (isset($form_fields[$fieldID])) {
						$realFormFields[$fieldID] = $form_fields[$fieldID];
					}
				}
			}
			$adminFields = array();
			foreach ($form_fields as $fieldName => $field) {
				if (!isset($realFormFields[$fieldName])) {
					$adminFields[$fieldName] = $field;
				}
			}
			$realFormFields = array_merge($adminFields, $realFormFields);
			
			$tp = SJB_System::getTemplateProcessor();

			$listing_edit_form->registerTags($tp);
			$extraInfo = $listing_info['product_info'];
			if ($extraInfo) {
				$extraInfo = unserialize($extraInfo);
				$tp->assign("listing_duration", $extraInfo['listing_duration']);
			}
			$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing);
			if (!isset($listing_structure['access_type']))
				$listing_structure['access_type'] = 'everyone';
			$tp->assign('form_fields', $realFormFields);
			$tp->assign('listing', $listing_structure);
			$tp->assign('errors', $errors);
			$tp->assign('listingType', SJB_ListingTypeManager::createTemplateStructure($listingTypeInfo));
			$tp->assign('expired', SJB_ListingManager::getIfListingHasExpiredBySID($listing->getSID()));
			SJB_System::setGlobalTemplateVariable('wikiExtraParam', $listingTypeInfo['id']);

			$tp->display('edit_listing.tpl');
		}
	}
}
