<?php

class SJB_UserManager extends SJB_ObjectManager
{
	public static function getCurrentUserInfo()
	{
		if (SJB_Authorization::isUserLoggedIn())
			return SJB_Authorization::getCurrentUserInfo();
		return null;
	}

	public static function isUserLoggedIn()
	{		
		return SJB_Authorization::isUserLoggedIn();
	}

	/**
	 * Get current user
	 *
	 * @return SJB_User|null
	 */
	public static function getCurrentUser()
	{
		$user_info = SJB_UserManager::getCurrentUserInfo();
		$user = null;
		if (!is_null($user_info)) {
			$user = new SJB_User($user_info, $user_info['user_group_sid']);
			$user->setSID($user_info['sid']);
		}
		return $user;
	}

	/**
	 * Gets user object by sid
	 *
	 * @param int $user_sid
	 * @return SJB_User
	 */
	public static function getObjectBySID($user_sid)
	{
		$user_info = SJB_UserManager::getUserInfoBySID($user_sid);
		if (!is_null($user_info)) {
			$user = new SJB_User($user_info, $user_info['user_group_sid']);
			$user->setSID($user_info['sid']);
			return $user;
		}
		return null;
	}

	public static function getUserInfoBySID($user_sid)
	{
		return SJB_UserDBManager::getUserInfoBySID($user_sid);
	}

	public static function isUserActiveBySID($user_sid)
	{
		return SJB_DB::queryValue("SELECT `active` FROM `users` WHERE `sid` = ?n", $user_sid);
	}

	/**
	 * @static
	 * @param SJB_User $user
	 * @return bool
	 */
	public static function saveUser(SJB_User $user)
	{
		\SJB\Location\Helper::fixLocation($user);
		$newUserInDB = !$user->isSavedInDB();
		if ($newUserInDB) {
			$user->createVerificationKey();
		}

		SJB_UserDBManager::saveUser($user);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_USERS));
		if ($newUserInDB) {
			SJB_Event::dispatch('onAfterUserCreated', $user);
		}

		if (SJB_Authorization::isUserLoggedIn()) {
			SJB_Authorization::updateCurrentUserSession();
		}

		return true;
	}

	public static function getAllUsersInfo()
	{
		return SJB_UserDBManager::getAllUsersInfo();
	}

	public static function getUsersNumberByGroupSID($user_group_sid)
	{
		return SJB_DB::queryValue("SELECT COUNT(*) FROM ?w WHERE user_group_sid = ?n", "users", $user_group_sid);
	}

	public static function getUserSIDsByUserGroupSID($user_group_sid)
	{
		$sql_result = SJB_DB::query("SELECT `sid` FROM `users` WHERE `user_group_sid`=?n", $user_group_sid);
		return SJB_UserManager::_getUserSIDsFromRawSIDInfo($sql_result);
	}

	public static function getUserSIDsByProductSID($productSID)
	{
		$sql_result = SJB_DB::query(
			"SELECT DISTINCT users.sid  
			FROM users 
			INNER JOIN contracts ON users.sid = contracts.user_sid 
			INNER JOIN products ON products.sid = contracts.product_sid 
			WHERE products.sid=?n 
			GROUP BY users.sid", $productSID);
		
		return SJB_UserManager::_getUserSIDsFromRawSIDInfo($sql_result);
	}

	public static function _getUserSIDsFromRawSIDInfo($raw_sid_info)
	{
		$result = array();
		foreach($raw_sid_info as $found_sid_info)
			$result[] = $found_sid_info['sid'];
		return $result;
	}

	public static function deleteUserById($id)
	{
		$user = SJB_UserManager::getObjectBySID($id);
		if (empty($user)) {
			SJB_UserDBManager::deleteEmptyUsers();
			return true;
		}

		SJB_Event::dispatch('onBeforeUserDelete', $user);

        $listings = SJB_ListingDBManager::getListingsSIDByUserSID($id);
		SJB_ListingManager::deleteListingBySID($listings);

		// delete user logo file
		$pictProp = $user->getProperty('Logo');
		if ($pictProp) {
			SJB_UploadFileManager::deleteUploadedFileByID($pictProp->value);
		}

		// delete social info
		$socialReference = SJB_SocialPlugin::getProfileSocialID($user->getSID());
		if ($socialReference) {
			SJB_SocialPlugin::deleteProfileSocialInfoByReference($socialReference);
		}
		

        $result = SJB_UserDBManager::deleteUserById($id) && SJB_ContractManager::deleteAllContractsByUserSID($id);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_USERS));
        return $result;
	}

	public static function activateUserByUserName($username)
	{
		$result = SJB_UserDBManager::activateUserByUserName($username);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_USERS));
		return $result;
	}
	
	public static function deactivateUserByUserName($username)
	{
        SJB_Event::dispatch('onBeforeUserDeactivate', $username);
        SJB_UserDBManager::deactivateUserByUserName($username);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_USERS));
	}

	public static function getUserInfoByUserName($username)
	{
		return SJB_UserDBManager::getUserInfoByUserName($username);
	}
	
	public static function getUserInfoByExtUserID($extUserID, $listingTypeID)
	{
		return SJB_UserDBManager::getUserInfoByExtUserID($extUserID, $listingTypeID);
	}

	public static function login($username, $password, &$errors, $autorizeByUsername = false, $login_as_user)
	{
		$userExists = SJB_DB::queryValue("SELECT count(*) FROM `users` WHERE `username` = ?s", $username);

		if ($userExists && $autorizeByUsername)
		    return true;

		if ($userExists) {
			if (!$login_as_user)
				$userAuthorized = SJB_DB::queryValue("SELECT count(*) FROM `users` WHERE `username` = ?s AND `password` = ?s", $username, md5($password));
			else
				$userAuthorized = SJB_DB::queryValue("SELECT count(*) FROM `users` WHERE `username` = ?s AND `password` = ?s", $username, $password);

			if (!$userAuthorized) {
				$errors['INVALID_PASSWORD'] = 1;
				return false;
			}
			return true;
		}

		$errors['NO_SUCH_USER'] = 1;
		return false;
	}

	public static function getCurrentUserSID()
	{
		$user_info = SJB_UserManager::getCurrentUserInfo();
		if (!is_null($user_info))
			return $user_info['sid'];
		return null;
	}

	public static function getUserNameByUserSID($user_sid)
	{
		return SJB_UserDBManager::getUserNameByUserSID($user_sid);
	}
	
	public static function getExtUserIDByUserSID($user_sid)
	{
		return SJB_UserDBManager::getExtUserIDByUserSID($user_sid);
	}

	public static function getUserSIDbyUsername($username)
	{
		$user_info = SJB_UserManager::getUserInfoByUserName($username);
		if (!empty($user_info))
			return $user_info['sid'];
		return null;
	}

	public static function getUserSIDsLikeUsername($username)
	{
		return SJB_UserDBManager::getUserSIDsLikeUsername($username);
	}

	public static function getUserSIDsLikeCompanyName($username)
	{
		return SJB_UserDBManager::getUserSIDsLikeCompanyName($username);
	}

	public static function getUserPassword($username)
	{
		return SJB_DB::queryValue("SELECT `password` FROM `users` WHERE `username` = ?s", $username);
	}

	public static function changeUserPassword($user_sid, $password)
	{
		return SJB_DB::query("UPDATE `users` SET `password` = ?s WHERE `sid` = ?s", md5($password), $user_sid);
	}

	public static function saveUserSessionKey($session_key, $user_sid)
	{
		SJB_DB::query("INSERT INTO user_sessions SET session_key = ?s, user_sid = ?n, remote_ip = ?s, user_agent = ?s, start = UNIX_TIMESTAMP()", $session_key, $user_sid, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
	}

	public static function removeUserSessionKey($session_key)
	{
		SJB_DB::query("DELETE FROM user_sessions WHERE session_key = ?s", $session_key);
	}

	public static function getUserSIDBySessionKey($session_key)
	{
		return SJB_DB::queryValue("SELECT user_sid FROM user_sessions WHERE session_key = ?s", $session_key);
	}

	public static function getUserGroupByUserSid($userSid)
	{
		return SJB_DB::queryValue("SELECT `user_group_sid` FROM `users` WHERE sid = ?n", $userSid);
	}

	/**
	 * @param SJB_User $user
	 * @return array
	 */
    public static function createTemplateStructureForUser($user)
	{
		if (!$user)
			return array();
		$structure = $user->getUserInfo();
		if (SJB_MemoryCache::has('userGroupInfo' . $user->getUserGroupSID())) {
			$user_group_info = SJB_MemoryCache::get('userGroupInfo' . $user->getUserGroupSID());
		}
		else {
			$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user->getUserGroupSID());
			SJB_MemoryCache::set('userGroupInfo' . $user->getUserGroupSID(), $user_group_info);
		}
		foreach ($user->getProperties() as $property) {
			$value = $property->getValue();
			if ($property->getType() == 'list') {
				$listValues = isset($property->type->property_info['list_values']) ? $property->type->property_info['list_values'] : array();
				foreach ($listValues as $listValue) {
					if ($listValue['id'] == $value) 
						$structure[$property->getID()] = $listValue['caption'];
				}
			}
			elseif ($property->getType() == 'location') {
				foreach($property->type->fields as $locationField) {
					if (isset($structure[$property->getID()]) && array_key_exists($locationField['id'], $structure[$property->getID()])) {
						$listValues = isset($locationField['list_values']) ? $locationField['list_values'] : array();
						foreach ($listValues as $listValue) {
							if ($listValue['id'] == $value[$locationField['id']]) {
								$structure[$property->getID()][$locationField['id']] = $listValue['caption'];
							}
						}
					}
				}
			} else {
				$structure[$property->getID()] = $value;
			}
		}

		$structure['id'] = $user->getID();
		$structure['isJobg8'] = strpos($structure['username'], 'jobg8_') !== false;
		$structure['group'] = array('id' 		=> $user_group_info['id'],
									'caption'	=> $user_group_info['name']);

		$structure['METADATA'] = array(
			'group' => array(
				'caption' => array('type' => 'string', 'propertyID' => 'caption'),
			),
			'registration_date' => array('type' => 'date'),
		);
		$structure['METADATA'] = array_merge($structure['METADATA'], parent::getObjectMetaData($user));
		return $structure;
	}

    public static function createTemplateStructureForCurrentUser()
	{
		return SJB_UserManager::createTemplateStructureForUser(SJB_UserManager::getCurrentUser());
	}

	/**
	 * gets all groups info
	 *
	 * @return array
	 */
	public static function getGroupsInfo()
	{
		$res = array();
		//TODO: можно ускорить и сделать так же как в листингах
		$periods = array(
			'Total' => '1=1',
			'Today' => '`u`.`registration_date` >= CURDATE()',
			'Last 7 days' => '`u`.`registration_date` >= date_sub(curdate(), interval 7 day)',
			'Last 30 days' => '`u`.`registration_date` >= date_sub(curdate(), interval 30 day)',
		);

		$user_groups_structure = SJB_UserGroupManager::createTemplateStructureForUserGroups();

		foreach ($user_groups_structure as $userGroup) {
			foreach ($periods as $key => $value) {
				$res[$userGroup['caption']][$key] = SJB_DB::queryValue("
					select	ifnull(count(u.user_group_sid), 0) as `count`
					from users u
					where {$value} and u.user_group_sid = {$userGroup['sid']}");
			}
		}
		return $res;
	}

	/**
	 * gets all users info
	 *
	 * @return array
	 */
	public static function getUsersInfo()
	{
		$usersInfo = SJB_DB::query("select ifnull(count(*), 0) as `count`, ifnull(sum(users.active), 0) as `active` from users");
		return array_shift($usersInfo);
	}

	/**
	 * @param  int $numberOfProfiles
	 * @return array
	 */
	public static function getFeaturedProfiles($numberOfProfiles)
	{
		$logosInfo = SJB_UserProfileFieldManager::getFieldsInfoByType('logo');
		$logoFields = array();
		foreach ($logosInfo as $logoInfo) {
			if (!empty($logoInfo['id'])) {
				$logoFields[] = " `{$logoInfo['id']}` != '' ";
			}
		}
		
		$whereLogo = empty($logos) ? '' : 'AND (' . implode(' OR ', $logoFields) . ')';
		$usersInfo = SJB_DB::query("SELECT `sid` FROM `users` WHERE `featured`=1 AND `active`=1 {$whereLogo} ORDER BY RAND() LIMIT 0, ?n", $numberOfProfiles);

		$users = array();
		$sids = array();
		foreach ($usersInfo as $userInfo) {
			$user    = SJB_UserManager::getObjectBySID($userInfo['sid']);
			$users[] = !empty($user) ? SJB_UserManager::createTemplateStructureForUser($user) : null;
			$sids[] = $userInfo['sid'];
		}

		if ($sids) {
			$listingType = SJB_ListingTypeManager::getListingTypeInfoBySID(SJB_ListingTypeManager::getListingTypeSIDByID('Job'));
			$countListings = SJB_ListingDBManager::getActiveJobsNumberForUsers($sids, $listingType);
			foreach ($users as $key => $user) {
				$users[$key]['countListings'] = empty($countListings[$user['sid']]) ? 0 : $countListings[$user['sid']];
			}
		}

		return $users;
	}

	public static function getUserNameByCompanyName($companyName)
	{
		$userName = SJB_DB::queryValue("SELECT `username` FROM `users` WHERE `companyName`=?s", $companyName);
		return $userName ? $userName : false;
	}

	public static function getAllUserSystemProperties()
	{
		$system_properties = array('id', 'username', 'password', 'product', 'user_group', 'active', 'featured', 'ip', 'registration_date');
		return array('system' => $system_properties);
	}
	
    public static function makeFeaturedBySID($user_sid)
	{
       return SJB_DB::query("UPDATE users SET featured = 1 WHERE sid = ?n", $user_sid);
	}
	
	public static function removeFromFeaturedBySID($user_sid)
	{
		return SJB_DB::query("UPDATE users SET featured = 0 WHERE sid = ?n", $user_sid);
	}

	public static function getUserIdByKeywords($keywords)
	{
		return SJB_DB::queryValue("SELECT `sid` FROM `users` WHERE `username` = ?s OR `companyName` = ?s LIMIT 1", $keywords, $keywords);
	}

	/**
	 * @param int $userSID
	 * @param string $originalMd5Password
	 * @return array|bool|int
	 */
	public static function saveUserPassword($userSID, $originalMd5Password)
	{
		return SJB_DB::query('UPDATE `users` SET `password` = ?s WHERE `sid` = ?n', $originalMd5Password, $userSID);
	}

	public static function issetFieldByName($fieldName)
	{
		return SJB_DB::query("SHOW COLUMNS FROM `users` WHERE `Field` = ?s", $fieldName);
	}

	public static function isUserExistsByUserSid($userSid)
	{
		return SJB_DB::queryValue("SELECT count(*) FROM `users` WHERE `sid` = ?n LIMIT 1", $userSid);
	}

	public static function replaceCompanyNameWithSIDs($companyNames) 
	{
		if (empty($companyNames)) {
			return 0;
		}
		$foundSids = SJB_DB::query("SELECT `sid` FROM `users` WHERE `CompanyName` IN (?l)", $companyNames);
		$companySids = array();
		foreach ($foundSids as $sid) {
			$companySids[] = $sid['sid'];
		}
		return $companySids;
	}

}
