<?php

class SJB_Classifieds_DisplayListing extends SJB_Function
{
	public function isAccessible()
	{
		$listingTypeID = SJB_Array::get($this->params, 'listing_type_id');
		if ($listingTypeID) {
			if ($listingTypeID == 'Resume' && !SJB_Settings::getValue('public_resume_access')) {
				$this->setPermissionLabel('resume_access');
			}
			$allow = parent::isAccessible();
			$listingID = SJB_Request::getVar('listing_id', false);
			$passedParametersViaUri = SJB_Request::getVar('passed_parameters_via_uri', false);
			if (!$listingID && $passedParametersViaUri) {
				$passedParametersViaUri = SJB_UrlParamProvider::getParams();
				if (isset($passedParametersViaUri[0])) {
					$listingID = $passedParametersViaUri[0];
				}
			}
			if (SJB_UserManager::isUserLoggedIn()) {
				$currentUser = SJB_UserManager::getCurrentUser();
				if (!$allow && 'Resume' == $listingTypeID && $listingID) {
					// if view resume not allowed by ACL, check applications table
					// for current resume ID, applied for one of current user jobs
					// if present in applications - allow current user to view resume
					// check for all jobs of current user
					$cuJobs = SJB_ListingManager::getListingsByUserSID($currentUser->getSID());
					$listingSids = array();
					foreach ($cuJobs as $job) {
						$listingSids[] = $job->getSID();
						if ($listingID == $job->getSID()) {
							return true;
						}
					}
					if (!empty($listingSids)) {
						$result = SJB_DB::query('SELECT `id` FROM `applications` WHERE `resume` = ?n AND `listing_id` IN (?l) LIMIT 1', $listingID, $listingSids);
						if (!empty($result)) {
							return true;
						}
					}
				}
			}
			return $allow;
		}

		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$display_form = new SJB_Form();
		$display_form->registerTags($tp);
		$errors = array();
		$template = SJB_Request::getVar('display_template', 'display_listing.tpl');
		$listing_id = SJB_Request::getVar("listing_id");
		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$listing_id = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : null;
		}

		if (is_null($listing_id)) {
			$listing_type_id = SJB_Request::getVar('listing_type_id');
			$listing_id = SJB_ListingManager::getListingIDByListingTypeID($listing_type_id);
		}

		if (is_null($listing_id)) {
			$errors['404'] = true;
		}
		elseif (is_null($listing = SJB_ListingManager::getObjectBySID($listing_id)) || !SJB_ListingManager::isListingAccessableByUser($listing_id, SJB_UserManager::getCurrentUserSID())) {
			$errors['404'] = true;
		}
		elseif (!$listing->isActive() && $listing->getUserSID() != SJB_UserManager::getCurrentUserSID()) {
			$errors['404'] = true;
		}
		elseif ((SJB_ListingTypeManager::getListingTypeIDBySID($listing->listing_type_sid) == 'Resume' && ($template == 'display_job.tpl')) ||
				(SJB_ListingTypeManager::getListingTypeIDBySID($listing->listing_type_sid) == 'Job' && ($template == 'display_resume.tpl')) 
		) {
			SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . SJB_TemplateProcessor::listing_url($listing));
		} else {
			$listing_type_id = SJB_ListingTypeManager::getListingTypeIDBySID($listing->listing_type_sid);

			// canonical url goes here
			if ($listing_type_id == 'Job') {
				$listingUrl = SJB_TemplateProcessor::listing_url($listing);

				if (strpos(rawurldecode(SJB_Navigator::getURIThis()), $listingUrl) === false) {
					SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . $listingUrl, SJB_HelperFunctions::REDIRECT_302);
				}
			}
            
            if ($listing_type_id == 'Opportunity') {
				$listingUrl = SJB_TemplateProcessor::listing_url($listing);
				if (strpos(rawurldecode(SJB_Navigator::getURIThis()), $listingUrl) === false) {
					SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . $listingUrl, SJB_HelperFunctions::REDIRECT_302);
				}
			}


			$display_form = new SJB_Form($listing);

			$display_form->registerTags($tp);

			$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listing->getListingTypeSID());
			$form_fields = array();
			foreach ($pages as $page) {
				$form_fields = array_merge(SJB_PostingPagesManager::getAllFieldsByPageSIDForForm($page['sid']), $form_fields);
			}

			$listingOwner = SJB_UserManager::getObjectBySID($listing->user_sid);

			SJB_ListingManager::incrementViewsCounterForListing($listing_id);
			$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing);
			$filename = SJB_Request::getVar('filename', false);
			if ($filename) {
				$file = SJB_UploadFileManager::openFile($filename, $listing_id);
				$errors['NO_SUCH_FILE'] = true;
			}

			$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
			$tp->assign(
				"METADATA", array(
				"listing" => $metaDataProvider->getMetaData($listing_structure['METADATA']),
				"form_fields" => $metaDataProvider->getFormFieldsMetadata($form_fields)));

			$searchId = SJB_Request::getVar("searchId", "");
			$page = SJB_Request::getVar("page", "");
			$criteria_saver = new SJB_ListingCriteriaSaver($searchId);
			$prevNextIds = $criteria_saver->getPreviousAndNextObjectID($listing_id);
			$search_criteria_structure = $criteria_saver->createTemplateStructureForCriteria();

			$tp->assign("isApplied", SJB_Applications::isApplied($listing_id, SJB_UserManager::getCurrentUserSID()));
			$tp->assign('listing_id', $listing_id);
			$tp->assign("form_fields", $form_fields);
			$tp->assign('uri', base64_encode(SJB_Navigator::getURIThis()));
			$tp->assign('listingOwner', $listingOwner);

			// SJB-1197: ajax autoupload.
			// Fix to view file from temporary uploaded storage.
			$sessionFilesStorage = SJB_Session::getValue('tmp_uploads_storage');

			// NEED TO CHECK FOR COMPLEX PARENT AND COMPLEX STEP PARAMETERS!
			$complexParent = SJB_Request::getVar('complexParent');
			$complexStep   = SJB_Request::getVar('complexEnum');
			$fieldId       = SJB_Request::getVar('field_id');
			$isComplex     = false;
			if ($complexParent && $complexStep) {
				$fieldId   = $complexParent . ":" . $fieldId . ":" . $complexStep;
				$isComplex = true;
			}
			$tempFileValue = SJB_Array::getPath($sessionFilesStorage, "listings/{$listing_id}/{$fieldId}");

			if ($isComplex) {
			} else {
				if (!empty($tempFileValue)) {
					$fileUniqueId = isset($tempFileValue['file_id']) ? $tempFileValue['file_id'] : '';
					if (!empty($fileUniqueId)) {
						$upload_manager = new SJB_UploadFileManager();

						// file structure for file
						$fileInfo = array(
								'file_url'        => $upload_manager->getUploadedFileLink($fileUniqueId),
								'file_name'       => $upload_manager->getUploadedFileName($fileUniqueId),
								'saved_file_name' => $upload_manager->getUploadedSavedFileName($fileUniqueId),
								'file_id'         => $fileUniqueId,
						);
						$listing_structure[$fieldId] = $fileInfo;
					}
				}
			}
			// SJB-1197

			$tp->filterThenAssign("listing", $listing_structure);
			$tp->assign("prev_next_ids", $prevNextIds);
			$tp->assign("searchId", $searchId);
			$tp->assign("page", $page);
			$tp->filterThenAssign("search_criteria", $search_criteria_structure);
			$tp->filterThenAssign("search_uri", $criteria_saver->getUri());

			if ($field_id = SJB_Request::getVar('field_id')) {
				// SJB-825
				$complexEnum = SJB_Request::getVar('complexEnum', null, 'GET');
				$complexFieldID = SJB_Request::getVar('complexParent', null, 'GET');
				// SJB-825
				$tp->assign('field_id', $field_id);
			}
		}

		foreach ($errors as $k => $v) {
			switch ($k) {
				case '404':
					if (SJB_Array::get($this->params, 'listing_type_id') == 'Job') {
						$params = SJB_UrlParamProvider::getParams();
						if ($params) {
							SJB_HelperFunctions::redirect(
								SJB_HelperFunctions::getSiteUrl() . '/jobs/?keywords[all_words]=' . str_replace('-', ' ', array_pop($params)) . '&not_found=1',
								SJB_HelperFunctions::REDIRECT_302
							);
						}
					}
                    if (SJB_Array::get($this->params, 'listing_type_id') == 'Opportunity') {
						$params = SJB_UrlParamProvider::getParams();
						if ($params) {
							SJB_HelperFunctions::redirect(
								SJB_HelperFunctions::getSiteUrl() . '/opportunities/?keywords[all_words]=' . str_replace('-', ' ', array_pop($params)) . '&not_found=1',
								SJB_HelperFunctions::REDIRECT_302
							);
						}
					}
					echo SJB_System::executeFunction('miscellaneous', '404_not_found');
					return;
			}
		}
		$tp->assign('errors', $errors);

        $tp->assign('achievements', $this->getAchievements($listing->user_sid));

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
}
