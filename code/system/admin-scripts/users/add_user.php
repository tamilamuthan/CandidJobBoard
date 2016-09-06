<?php

class SJB_Admin_Users_AddUser extends SJB_Function
{
	public function isAccessible()
	{
		$passedParametersViaUri = SJB_UrlParamProvider::getParams();
		$userGroupID = $passedParametersViaUri ? array_shift($passedParametersViaUri) : false;
		$this->setPermissionLabel('manage_' . mb_strtolower($userGroupID, 'UTF-8'));
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$passedParametersViaUri = SJB_UrlParamProvider::getParams();
		$userGroupID = $passedParametersViaUri ? array_shift($passedParametersViaUri) : false;

		if (!$userGroupID) {
			$userGroupsInfo = SJB_UserGroupManager::getAllUserGroupsInfo();
			$tp->assign('user_groups_info', $userGroupsInfo);
			$tp->display('add_user_choose_user_group.tpl');
		} else {
			$userGroupSID = SJB_UserGroupManager::getUserGroupSIDByID($userGroupID);
			$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userGroupSID);
			$user = SJB_ObjectMother::createUser($_REQUEST, $userGroupSID);
			$user->deleteProperty('active');
			if ($user->getUserGroupSID() == SJB_UserGroup::JOBSEEKER) {
				$user->deleteProperty('featured');
			}
			$registration_form = SJB_ObjectMother::createForm($user);
			$registration_form->registerTags($tp);
			$form_submitted = SJB_Request::getVar('action', '') == 'add';
			$errors = array();

			if ($form_submitted && $registration_form->isDataValid($errors)) {
				SJB_UserManager::saveUser($user);

				// >>> SJB-1197
				// needs to check session for ajax-uploaded files, and set it to user profile
				$tmpUploadsStorage = SJB_Session::getValue('tmp_uploads_storage');
				$formToken         = SJB_Request::getVar('form_token');
				if (!empty($formToken)) {
					$tmpUploadedFields = SJB_Array::getPath($tmpUploadsStorage, $formToken);

					if (!is_null($tmpUploadsStorage) && is_array($tmpUploadedFields)) {
						// prepare user profile fields array
						$userProfileFieldsInfo = SJB_UserProfileFieldManager::getAllFieldsInfo();
						$userProfileFields     = array();
						foreach ($userProfileFieldsInfo as $field) {
							$userProfileFields[$field['id']] = $field;
						}

						// look for temporary values
						foreach ($tmpUploadedFields as $fieldId => $fieldInfo) {
							// check field ID for valid ID in user profile fields
							if (!array_key_exists($fieldId, $userProfileFields) || empty($fieldInfo)) {
								continue;
							}

							$fieldType         = $userProfileFields[$fieldId]['type'];
							$profilePropertyId = $fieldId . '_' . $user->getSID();
							$uploadFileSID = SJB_DB::queryValue("SELECT `sid` FROM `uploaded_files` WHERE `id` = ?s", $fieldInfo['file_id']);
							if ($uploadFileSID) {
								SJB_DB::query("DELETE FROM `uploaded_files` WHERE `id` = ?s", $profilePropertyId);
							}
							switch ( strtolower($fieldType)) {
								case 'file':
									// change temporary file ID
									SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` = ?s", $profilePropertyId, $fieldInfo['file_id']);

									// set value of user property to new uploaded file
									$user->setPropertyValue($fieldId, $profilePropertyId);
									break;

								case 'logo':
									// change temporary file ID and thumb ID
									SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` = ?s", $profilePropertyId, $fieldInfo['file_id']);
									SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` = ?s", $profilePropertyId . '_thumb', $fieldInfo['file_id'] . '_thumb');

									// set value of user property to new uploaded file
									$user->setPropertyValue($fieldId, $profilePropertyId);
									break;

								default:
									break;
							}
							$tmpUploadsStorage = SJB_Array::unsetValueByPath($tmpUploadsStorage, "{$formToken}/{$fieldId}");
						}

						// and save user with new fields data
						SJB_UserManager::saveUser($user);
						// clean temporary storage
						$tmpUploadsStorage = SJB_Array::unsetValueByPath($tmpUploadsStorage, "{$formToken}");
						// CLEAR TEMPORARY SESSION STORAGE
						SJB_Session::setValue('tmp_uploads_storage', $tmpUploadsStorage);
					}
				}
				// <<< SJB-1197

				SJB_UserManager::activateUserByUserName($user->getUserName());
				$defaultProduct = SJB_UserGroupManager::getDefaultProduct($userGroupSID);
				$availableProductIDs = SJB_ProductsManager::getProductsIDsByUserGroupSID($userGroupSID);
				if ($defaultProduct && in_array($defaultProduct, $availableProductIDs)) {
					$contract = new SJB_Contract(array('product_sid' => $defaultProduct));
					$contract->setUserSID($user->getSID());
					$contract->saveInDB();
				}
				SJB_Notifications::sendUserWelcomeLetter($user->getSID());
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-users/" . mb_strtolower($userGroupInfo['id'], 'utf8') . '/?restore=1');
			}
			else {
				$registration_form = SJB_ObjectMother::createForm($user);
				$registration_form->registerTags($tp);
				$tp->assign("errors", $errors);
				$tp->assign("user_group", $userGroupInfo);
				$tp->assign("form_fields", $registration_form->getFormFieldsInfo());
				$tp->display("add_user.tpl");
			}
		}
	}
}
