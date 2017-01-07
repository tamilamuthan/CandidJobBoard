<?php

class SJB_Classifieds_ApplyNow extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('apply_for_a_job');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		$field_errors = array();
		$tp = SJB_System::getTemplateProcessor();
		$loggedIn = SJB_UserManager::isUserLoggedIn();
		$current_user_sid = SJB_UserManager::getCurrentUserSID();

		$controller      = new SJB_SendListingInfoController($_REQUEST);
		$isDataSubmitted = false;

		$jobInfo = SJB_ListingManager::getListingInfoBySID($controller->getListingID());
		if ($controller->isListingSpecified()) {
			if ($controller->isDataSubmitted()) {
				// получим уникальный id для файла в uploaded_files

				$file_id_current = 'application_' . md5(microtime());
				$upload_manager = new SJB_UploadFileManager();
				$upload_manager->setFileGroup('files');
				$upload_manager->setUploadedFileID($file_id_current);
				$file_name = $upload_manager->uploadFile('file_tmp');
				$id_file = $upload_manager->fileId;

				$post = $controller->getData();
				$listingId = 0;
				if (isset($post['submitted_data']['id_resume']))
					$listingId = $post['submitted_data']['id_resume'];

				$mimeType = isset($_FILES['file_tmp']['type']) ? $_FILES['file_tmp']['type'] : '';

				if (isset($_FILES['file_tmp']['error'])) {
					switch ($_FILES['file_tmp']['error']) {
						case UPLOAD_ERR_INI_SIZE:
							$errors['FILE_SIZE'] = 'File size shouldn\'t be larger than 5 MB.';
					}
				}

				if (empty($errors) && isset($_FILES['file_tmp']['size']) && $file_name != '' && $_FILES['file_tmp']['size'] == 0) {
					$errors['FILE_IS_EMPTY'] = 'The uploaded file should not be blank';
				}

				if (!empty($_FILES['file_tmp']['name'])){
					$fileFormats = explode(',',SJB_System::getSettingByName('file_valid_types'));
					$fileInfo = pathinfo($_FILES['file_tmp']['name']);
					if (!isset($fileInfo['extension']) || !in_array(strtolower($fileInfo['extension']), $fileFormats)) {
						$errors['NOT_SUPPORTED_FILE_FORMAT'] = strtolower($fileInfo['extension']) . ' ' . SJB_I18N::getInstance()->gettext(null, 'File format is not supported');
					}
				}

				if ($file_name == '' && $listingId == 0) {
					$canAppplyWithoutResume = false;
					SJB_Event::dispatch('CanApplyWithoutResume', $canAppplyWithoutResume);
					if (!$canAppplyWithoutResume) {
						$errors['APPLY_INPUT_ERROR'] = 'Please select file or resume';
					}
				} else if (!empty($current_user_sid) && SJB_Applications::isApplied($post['submitted_data']['listing_id'], $current_user_sid)) {
					$errors['APPLY_APPLIED_ERROR'] = 'You already applied to this job';
				} else if (empty($current_user_sid) && SJB_Applications::isAppliedGuest($post['submitted_data']['listing_id'], trim($post['submitted_data']['email']))) {
					$errors['APPLY_APPLIED_ERROR'] = 'You already applied to this job';
				}

				$res = false;
				$listing_info = '';
                
                $score = 0;
                if (!empty($jobInfo['screening_questionnaire'])) {
                        $questions = new SJB_Questions($_REQUEST, $jobInfo['screening_questionnaire']);
                        $add_form = new SJB_Form($questions);
                        $add_form->registerTags($tp);
                        $add_form->isDataValid($field_errors);
                        $tp->assign('field_errors', $field_errors);
                        if (!$field_errors) {
                            $result = array();
                            $properties = $questions->getProperties();
                            $countAnswers = 0;
                            foreach ($properties as $key => $val) {
                                if ($val->type->property_info['type'] == 'boolean') {
                                    switch ($val->value) {
                                        case 0:
                                            $val->value = 'No';
                                            break;
                                        case 1:
                                            $val->value = 'Yes';
                                            break;
                                    }
                                }
                                $result[$val->caption] = $val->value;
                                if (isset($val->type->property_info['list_values'])) {
                                    foreach ($val->type->property_info['list_values'] as $list_values) {
                                        if (is_array($val->value)) {
                                            foreach ($val->value as $value) {
                                                if ($value == $list_values['id'] && $list_values['score'] != 'no') {
                                                    $score += $list_values['score'];
                                                    $countAnswers++;
                                                }
                                            }
                                        }
                                        else {
                                            if ($val->value == $list_values['id'] && $list_values['score'] != 'no') {
                                                $score += $list_values['score'];
                                                $countAnswers++;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($countAnswers === 0)
                                $score = 0.00;
                            else
                                $score = round($score / $countAnswers, 2);
                            $post['submitted_data']['questionnaire'] = serialize($result);
                        }
                }


				if (count($errors) == 0 && count($field_errors) == 0) {
					$res = SJB_Applications::create(
						$post['submitted_data']['listing_id'],
						$current_user_sid,
						(isset($post['submitted_data']['id_resume'])) ? $post['submitted_data']['id_resume'] : '',
						$post['submitted_data']['comments'],
						$file_name,
						$mimeType,
						$id_file,
						$_POST,
                        $post['submitted_data']['questionnaire'],
                        $score
					);
					if (isset($post['submitted_data']['id_resume']) && $post['submitted_data']['id_resume'] != 0) {
						$listing_info = SJB_ListingManager::getListingInfoBySID($post['submitted_data']['id_resume']);
						$emp_sid = SJB_ListingManager::getUserSIDByListingSID($post['submitted_data']['listing_id']);
						$accessible = SJB_ListingManager::isListingAccessableByUser($post['submitted_data']['id_resume'], $emp_sid);
						if (!$accessible)
							SJB_ListingManager::setListingAccessibleToUser($post['submitted_data']['id_resume'], $emp_sid);
					}
					if (!empty($file_name))
						$file_name = 'files/files/'. $file_name;
                    
                    if (!empty($jobInfo['screening_questionnaire'])) {
                            $questionnaire = SJB_ScreeningQuestionnaires::getInfoBySID($jobInfo['screening_questionnaire']);
                            if ($questionnaire) {
                                $passing_score = 0;
                                switch ($questionnaire['passing_score']) {
                                    case 'acceptable':
                                        $passing_score = 1;
                                        break;
                                    case 'good':
                                        $passing_score = 2;
                                        break;
                                    case 'very_good':
                                        $passing_score = 3;
                                        break;
                                    case 'excellent':
                                        $passing_score = 4;
                                        break;
                                }
                            }
                            if ($score >= $passing_score && $questionnaire['send_auto_reply_more'] ==   1) {
                                if (!empty($questionnaire['email_text_more'])) {
                                    SJB_Notifications::userAutoReply($jobInfo, $current_user_sid,       $questionnaire['email_text_more'], $notRegisterUserData);
                                }
                            }
                            elseif ($score < $passing_score && $questionnaire['send_auto_reply_less']   == 1) {
                                if (!empty($questionnaire['email_text_less'])) {
                                    SJB_Notifications::userAutoReply($jobInfo, $current_user_sid,       $questionnaire['email_text_less'], $notRegisterUserData);
                                }
                            }
                    }
                    
					SJB_Notifications::sendApplyNow($post, $file_name, $listing_info, $_POST);
				}

				if ($res === false) {
					$errors['APPLY_ERROR'] = 'Cannot apply';
				}

				$isDataSubmitted = true;
			}

            if (!empty($jobInfo['screening_questionnaire'])) {
                $questions = new SJB_Questions($_REQUEST, $jobInfo['screening_questionnaire']);
                $add_form = new SJB_Form($questions);
                $add_form->registerTags($tp);
                $form_fields = $add_form->getFormFieldsInfo();
                $tp->assign('form_fields', $form_fields);
                $tp->assign('questionsObject', $questions);
            }

            
			if ($loggedIn) {
				$resumes = array();
				foreach (SJB_ListingDBManager::getActiveListingsSIDByUserSID($current_user_sid) as $key => $resume) {
					$listing = SJB_ListingManager::createTemplateStructureForListing(SJB_ListingManager::getObjectBySID($resume));
					if ($listing['type']['id'] == 'Resume') {
						$resumes[] = $listing;
					}
				}
				$tp->assign('resumes', $resumes);
			}
			$tp->assign('listing', $jobInfo);
		} else {
			echo SJB_System::executeFunction('miscellaneous', '404_not_found');
			return;
		}

		$tp->assign('request', $_REQUEST);
		$tp->assign('errors', $errors);
		$tp->assign('listing_id', $controller->getListingID());
		$tp->assign('is_data_submitted', $isDataSubmitted);
		$tp->display('apply_now.tpl');
	}
}
