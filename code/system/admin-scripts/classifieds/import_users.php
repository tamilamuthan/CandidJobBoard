<?php

class SJB_Admin_Classifieds_ImportUsers extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('import_users');
		return parent::isAccessible();
	}

	public function execute()
	{
		ini_set('max_execution_time', 0);
		$template_processor = SJB_System::getTemplateProcessor();
		$errors = array();

		$encodingFromCharset = SJB_Request::getVar('encodingFromCharset', 'UTF-8');
		$file_info = isset($_FILES['import_file']) ? $_FILES['import_file'] : null;
		if (!empty($file_info)) {
			$extension = $_REQUEST['file_type'];
			if (!SJB_ImportFile::isValidFileExtensionByFormat($extension, $file_info)) {
				$errors['DO_NOT_MATCH_SELECTED_FILE_FORMAT'] = true;
			}
		}

		$user_group_id = SJB_Request::getVar('user_group_id', null);
		$user_group_sid = SJB_UserGroupManager::getUserGroupSIDByID($user_group_id);
		$template_processor->assign('userGroup', SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid));

		if (empty($file_info) || SJB_UploadFileManager::getErrorId('import_file') || !empty($errors)) {
			if (SJB_UploadFileManager::getErrorId('import_file')) {
				$errors[SJB_UploadFileManager::getErrorId('import_file')] = 1;
			}

			$template_processor->assign('errors', $errors);
			$template_processor->assign('charSets', SJB_HelperFunctions::getCharSets());
			$template_processor->display('import_users.tpl');
		} else {
			$csv_delimiter = SJB_Request::getVar('csv_delimiter', null);

			if ($extension == 'xls')
				$import_file = new SJB_ImportFileXLS($file_info);
			elseif ($extension == 'csv')
				$import_file = new SJB_ImportFileCSV($file_info, $csv_delimiter);

			$import_file->parse($encodingFromCharset);
			$user            = $this->CreateUser(array(), $user_group_id);
			$imported_data   = $import_file->getData();
			$count           = 0;
			$usersID         = array();
			
			foreach ($imported_data as $key => $importedColumn) {
				if ($key == 1) {
					$imported_user_processor = new SJB_ImportedUserProcessor($importedColumn, $user);
					continue;
				}
				if (!$importedColumn)
					continue;
				
				$userInfo = $imported_user_processor->getData($importedColumn);
				$extUserID = isset($userInfo['extUserID']) ? $userInfo['extUserID'] : '';
				$user     = $this->CreateUser(array(), $user_group_id);
				$user->addExtUserIDProperty();
				foreach ($user->getProperties() as $property) {
					if ($property->id == 'active') {
						$property->type->property_info['value'] = $property->value;
					}
					elseif ($property->getType() == 'location') {
						$locationFields = array($property->id.'.Country', $property->id.'.State', $property->id.'.City', $property->id.'.ZipCode', $property->id.'.Latitude', $property->id.'.Longitude');
						$locationFieldAdded = array();
						foreach ($locationFields as $locationField) {
							if (array_key_exists($locationField, $userInfo)) {
								$userInfo[$property->id][str_replace($property->id.'.', '', $locationField)] = $userInfo[$locationField];
								$locationFieldAdded[] = str_replace($property->id.'.', '', $locationField);
								unset($userInfo[$locationField]);
							}
						}
						if ($property->id == 'Location') {
							$locationFields = array('Country', 'State', 'City', 'ZipCode', 'Latitude', 'Longitude');
							foreach ($locationFields as $locationField) {
								if (array_key_exists($locationField, $userInfo) && !in_array($locationField, $locationFieldAdded) && !$user->getProperty($locationField)) {
									$userInfo[$property->id][$locationField] = $userInfo[$locationField];
									unset($userInfo[$locationField]);
								}
							}
						}
					}
				}

				$user = $this->CreateUser($userInfo, $user_group_id);
				$user->addExtUserIDProperty($extUserID);
				
				$username = SJB_Array::get($userInfo, 'username');
				if (empty($username)) {
					$errors[] = 'Empty username is not allowed, record ignored.';
				} elseif (!is_null(SJB_UserManager::getUserSIDbyUsername($username))) {
					$errors[] = '\'' . $userInfo['username'] . '\' - this user name already exists, record ignored.';
				} else {
					$originalMd5Password = $user->getPropertyValue('password');

					foreach ($user->getProperties() as $property) {
						if ($property->getType() == 'logo' && $property->getValue()) {
							$fieldInfo = SJB_UserProfileFieldDBManager::getUserProfileFieldInfoBySID($property->getSID());
							SJB_UploadFileManager::fileImport($userInfo, $fieldInfo);
						}
					}
					SJB_UserManager::saveUser($user);

					$this->extraProperties($user, $userInfo, $usersID);

					if (!empty($originalMd5Password)) {
						SJB_UserManager::saveUserPassword($user->getSID(), $originalMd5Password);
					}
					$_FILES = array(); // cleanup files after import
					$count++;
				}
			}

			$template_processor->assign('imported_users_count', $count);
			$template_processor->assign('errors', $errors);
			$template_processor->display('import_users_result.tpl');
		}
	}

	private function CreateUser($user_info, $user_group_id)
	{
		$user_group_sid = SJB_UserGroupManager::getUserGroupSIDByID($user_group_id);
		return new SJB_User($user_info, $user_group_sid);
	}

	private static $columns = array();

	private static function isColumnExists($col)
	{
		if (empty(self::$columns)) {
			$columns = SJB_DB::query('show columns from `users`');
			foreach ($columns as $row) {
				$columns[$row['Field']] = true;
			}
		}
		return !empty(self::$columns[$col]);
	}

	private function extraProperties($user, $userInfo, &$usersID)
	{
		$savedProperties = array(
			'user_group' => 1,
		);
		foreach ($user->getProperties() as $property) {
			if (!in_array($property->id, array('file', 'Logo'))) {
				$savedProperties[$property->id] = 1;
			}
		}

		$queryFields = '';
		foreach (array_diff_key($userInfo, $savedProperties) as $key => $value) {
			if ($key == 'id') {
				$usersID[$value] = $user->getSID();
				continue;
			}

			if ($key == 'product') {
				$products = $value? explode(',', $value): array();
				$i        = sizeof($products);
				while(--$i != -1) {
					$productProperties = @unserialize($products[$i]);
					if (!$productProperties) {
						continue;
					}

					$productSid = SJB_ProductsManager::getProductSidByName($productProperties['name']);
					if (!$productSid) {
						continue;
					}

					$contract = new SJB_Contract(array('product_sid' => $productSid, 'numberOfListings' => $productProperties['number_of_listings']));
					$contract->setPrice($productProperties['price']);
					$contract->setCreationDate($productProperties['creation_date']);
					$contract->setExpiredDate($productProperties['expired_date']);
					$contract->setStatus($productProperties['status']);
					$contract->setUserSID($user->getSID());
					$contract->saveInDB();
					SJB_ContractSQL::updatePostingsNumber($contract->id, $productProperties['number_of_postings']);
					SJB_ProductsManager::incrementPostingsNumber($productSid, $productProperties['number_of_postings']);
				}

				continue;
			}

			if ($key == 'registration_date') {
				$isValid = SJB_UserRegistrationDateValidator::isValid($userInfo['registration_date']);
				if ($isValid !== true) {
					if (!isset($errors['registrationDate'])) {
						$errors['registrationDate'][] = $isValid;
					}

					if (isset($userInfo['username'])) {
						$errors['registrationDate'][] = $userInfo['username'] . ', ';
					}

					continue;
				}
			}

			if (!empty($value) && self::isColumnExists($key)) {
				$queryFields .= $queryFields? ", `" . SJB_DB::quote($key) . "` = '" . SJB_DB::quote($value) . "'": "`" . SJB_DB::quote($key) . "` = '" . SJB_DB::quote($value) . "'";
			}
		}

		if (!empty($queryFields)) {
			SJB_DB::queryExec("UPDATE ?w SET " . $queryFields . " WHERE `sid` = ?n", 'users', $user->getSID());
		}
	}
}
