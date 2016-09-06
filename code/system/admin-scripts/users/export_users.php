<?php

class SJB_Admin_Users_ExportUsers extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('export_users');
		return parent::isAccessible();
	}

	public function execute()
	{
		ini_set('max_execution_time', 0);
		$tp          = SJB_System::getTemplateProcessor();
		$userGroupID = SJB_Request::getVar('user_group_id', 0);
		$_REQUEST['user_group']['equal'] = $userGroupID;
		
		$user              = SJB_UsersExportController::createUser($userGroupID);
		$user->addUserGroupProperty();
		$searchFormBuilder = new SJB_SearchFormBuilder($user);
		$criteria          = $searchFormBuilder->extractCriteriaFromRequestData($_REQUEST, $user);
		$searchFormBuilder->registerTags($tp);
		$searchFormBuilder->setCriteria($criteria);
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$exportProperties = SJB_Request::getVar('export_properties', array());
			if (empty($exportProperties)) {
				SJB_FlashMessages::getInstance()->addWarning('EMPTY_EXPORT_PROPERTIES');
			} else {
				$innerJoin = false;
				if (!empty($_REQUEST['product']['multi_like'])) {
					$products = $_REQUEST['product']['multi_like'];
					if (is_array($products)) {
						$products = implode(',', $products);
					}
					$whereParam = implode(',', explode(',', SJB_DB::quote($products)));
					$innerJoin = array(
						'contracts' => array(
							'join_field'   => 'user_sid',
							'join_field2'  => 'sid',
							'join'         => 'INNER JOIN',
							'where'        => "AND FIND_IN_SET(`contracts`.`product_sid`, '{$whereParam}')"
						)
					);
					unset($criteria['system']['product']);
				}
				
				$searcher      = new SJB_UserSearcher(false, false, 'ASC', $innerJoin);
				$searchAliases = SJB_UsersExportController::getSearchPropertyAliases();
				$foundUsersSid = $searcher->getObjectsSIDsByCriteria($criteria, $searchAliases);
				if (!empty($foundUsersSid)) {
					$result = SJB_ExportController::createExportDirectories();
					
					if ($result === true) {
						$exportProperties['extUserID'] = 1;
						if (!empty($exportProperties['GooglePlace'])) {
							$exportProperties['Location'] = 1;
						}
						$exportAliases = SJB_UsersExportController::getExportPropertyAliases();
						$exportData    = SJB_UsersExportController::getExportData($foundUsersSid, $exportProperties, $exportAliases);
						
						$fileName = mb_strtolower($userGroupID) . 's.xls';
						SJB_HelperFunctions::makeXLSExportFile($exportData, $fileName, 'Users');
						if (!file_exists(SJB_System::getSystemSettings('EXPORT_FILES_DIRECTORY') . "/{$fileName}")) {
							SJB_FlashMessages::getInstance()->addWarning('CANT_CREATE_EXPORT_FILES');
						} else {
							SJB_ExportController::sendExportFile($fileName);
						}
					}
				} else {
					SJB_FlashMessages::getInstance()->addWarning('EMPTY_EXPORT_DATA');
				}
			}
		}
		
		$userSystemProperties = SJB_UserManager::getAllUserSystemProperties();
		if ($userGroupID == 'JobSeeker') {
			unset($userSystemProperties['system'][array_search('featured', $userSystemProperties['system'])]);
		}

		$userGroup            = SJB_UserGroupManager::getUserGroupInfoBySID(SJB_UserGroupManager::getUserGroupSIDByID($userGroupID));
		$userCommonProperties = array();
		$userGroupProperties  = SJB_UserProfileFieldManager::getFieldsInfoByUserGroupSID($userGroup['sid']);
		foreach ($userGroupProperties as $key => $userGroupProperty) {
			if ($userGroupProperty['id'] == 'Location') {
				unset($userGroupProperties[$key]);
			}
		}
		$userCommonProperties[$userGroup['id']] = $userGroupProperties;

		$tp->assign('userSystemProperties', $userSystemProperties);
		$tp->assign('userCommonProperties', $userCommonProperties);
		$tp->assign('userGroup', $userGroup);
		$tp->display('export_users.tpl');
	}
}
