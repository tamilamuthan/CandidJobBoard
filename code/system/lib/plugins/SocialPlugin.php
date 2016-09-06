<?php

class SJB_SocialPlugin
{

	protected static $oProfile = null;
	protected static $loadedPlugins = array();
	protected static $isSyncAllowed = false;

	const SESSION_USER_GROUP_ID_KEY = 'userGroupID';
	const REQUEST_USER_GROUP_ID_KEY = 'user_group_id';
	const SOCIAL_LOGIN_ERROR = 'social_login_error';
	const SOCIAL_ACCESS_ERROR = 'social_access_error';

	/**
	 * @var FacebookSocialPlugin|SocialLoginPlugin
	 */
	protected static $oSocialPlugin = null;
	protected static $aUserFieldsNotRequiredInRegistration = array('password');
	protected static $aUserFields = array('email', 'LastName');
	protected static $aListingFields = array();

	/**
	 * take data from SocialNetwork server or from local server
	 * @var boolean
	 */
	protected $takeDataFromServer = false;

	/**
	 * get all available plugins
	 * @return array
	 */
	public static function getAvailablePlugins()
	{
		$aNetworks = array();
		foreach (self::$loadedPlugins as $name => $object) {
			array_push($aNetworks, $name);
		}
		return $aNetworks;
	}


	public static function loadPlugin($network, $object)
	{
		if (!isset(self::$loadedPlugins[$network])) {
			self::$loadedPlugins[$network] = $object;
		}

		$requestedNetwork = SJB_Request::getString('network');

		if ($requestedNetwork === $network) {
			if (SJB_Request::getVar('returnToShoppingCart', false)) {
				SJB_Session::setValue('fromAnonymousShoppingCart', true);
			}
			self::$oSocialPlugin = self::getSocialPlugin($network);
			self::$oSocialPlugin->init();
		}
	}

	/**
	 * @param string $network
	 * @return SocialLoginPlugin|GooglePlusSocialPlugin|FacebookSocialPlugin
	 */
	public static function getSocialPlugin($network)
	{
		return (isset(self::$loadedPlugins[$network])) ? self::$loadedPlugins[$network] : false;
	}

	/**
	 * @return SocialLoginPlugin|FacebookSocialPlugin|GooglePlusSocialPlugin|null
	 */
	public static function getActiveSocialPlugin()
	{
		if (self::$oSocialPlugin) {
			return self::$oSocialPlugin;
		}
		return null;
	}

	protected static function pushLoadedPlugin($network)
	{
		array_push(self::$loadedPlugins, $network);
	}


	public static function getProfileObject()
	{
		return self::$oProfile;
	}


	function pluginSettings()
	{
		return array();
	}

	/**
	 * @param SJB_User $user
	 * @return SJB_User
	 */
	public static function addReferenceDetails(SJB_User $user)
	{
		if (self::$oProfile && self::$oSocialPlugin) {
			self::definePasswordAndUsernameByEmail($user);

			$user->addProperty(array(
				'id' => 'reference_uid',
				'type' => 'string',
				'value' => self::getNetwork() . '_' . self::$oProfile->id,
				'is_system' => true));
			$user->addProperty(array(
				'id' => 'active',
				'type' => 'boolean',
				'value' => true,
				'is_system' => true));
		}
		return $user;
	}


	public static function ifUserIsRegistered($network)
	{
		if (self::$oProfile && $network) {
			if ($network == 'google_plus') {
				$id = self::$oProfile['id'];
			} else {
				$id = self::$oProfile->id;
			}
			return self::$oSocialPlugin->ifUserIsRegisteredByReferenceUid($network . '_' . $id);
		}
		return false;
	}


	public static function ifUserIsRegisteredByReferenceUid($referenceUid)
	{
		$result = SJB_DB::query('SELECT `sid` FROM `users` WHERE `reference_uid` = ?s', $referenceUid);

		if (!empty($result)) {
			$result = array_shift($result);
			return $result['sid'];
		}

		return false;
	}

	/**
	 * @param SJB_User $user
	 * @return SJB_User
	 */
	public static function addListingFieldsIntoRegistration(SJB_User $user)
	{
		if (self::getProfileObject() && !empty(self::$aListingFields)) {
			if ('JobSeeker' == SJB_Request::getVar(self::REQUEST_USER_GROUP_ID_KEY)) {
				$listing_type_id = 'Resume';
				$listing = SJB_ObjectMother::createListing($_REQUEST, SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id));
				foreach (self::$aListingFields as $field) {
					$user->addProperty($listing->getProperty($field));
				}
			}
		}
		return $user;
	}

	/**
	 * makes all users required fields as Not Required,
	 * but not fields from SJB_SocialPlugin::$aUserFields
	 *
	 * @param SJB_User $user
	 * @return SJB_User
	 */
	public static function makeRegistrationFieldsNotRequired(SJB_User $user)
	{
		if (self::getProfileObject()) {
			/** @var $oProperty SJB_ObjectProperty */
			foreach ($user->getProperties() as $oProperty) {
				if (!in_array($oProperty->getID(), self::$aUserFields) && !in_array($oProperty->getID(), self::$aListingFields)) {
					if ($oProperty->isRequired() && in_array($oProperty->getID(), self::$aUserFieldsNotRequiredInRegistration)) {
						$oProperty->makeNotRequired();
					}
				}
			}
		}
		return $user;
	}


	/**
	 *
	 * @param SJB_Object|SJB_User|SJB_Listing $object
	 * @return SJB_User|SJB_Listing
	 */
	public function fillObjectOutSocialData(SJB_Object $object)
	{
		if (is_object(self::$oSocialPlugin)) {
			self::$oSocialPlugin->fillObjectOutSocialData($object);
		}
		return $object;
	}

	/**
	 * @param SJB_Object $user
	 * @return SJB_Object
	 */
	public static function fillRegistrationDataWithUser(SJB_Object $user)
	{
		if (is_object(self::$oSocialPlugin)) {
			self::$oSocialPlugin->fillRegistrationDataWithUser($user);
		}
		return $user;
	}

	/**
	 * @param SJB_User $user
	 * @return SJB_User
	 */
	public static function definePasswordAndUsernameByEmail(SJB_User $user)
	{
		$email = $user->getPropertyValue('username');
		$user->setPropertyValue('username', $email);
		$password = substr(md5(microtime(true) . $email), 0, 6);
		$user->setPropertyValue('password', $password);
		return $user;
	}


//	/**
//	 * sends registration letter to user
//	 *
//	 * @param SJB_User $user
//	 * @return boolean
//	 */
//	public static function sendUserSocialRegistrationLetter(SJB_User $user)
//	{
//		if (self::$oSocialPlugin) {
//			return SJB_Notifications::sendUserSocialRegistrationLetter($user, self::getNetworkCaption());
//		}
//		return false;
//	}

	/**
	 * get current social network
	 * @return string
	 */
	public static function getNetwork()
	{
		if (self::$oSocialPlugin) {
			return self::$oSocialPlugin->getNetwork();
		}

		return null;
	}

	/**
	 * get current social network Caption
	 * @return string
	 */
	public static function getNetworkCaption()
	{
		if (self::$oSocialPlugin) {
			return self::$oSocialPlugin->getNetworkCaption();
		}

		return null;
	}

	public static function login()
	{
		if (self::$oSocialPlugin) {
			if (!self::$oProfile) {
				return null;
			}
			$errors = array();
			if ($userSID = self::ifUserIsRegistered(self::getNetwork())) {
				$user = SJB_UserManager::getObjectBySID($userSID);
				$GLOBALS[self::SOCIAL_LOGIN_ERROR] = array();
				if ($user && SJB_Authorization::login($user->getUserName(), false, false, $errors, '', true)) {
					if (!is_null(SJB_Session::getValue('fromAnonymousShoppingCart'))) {
						SJB_Session::unsetValue('fromAnonymousShoppingCart');
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/shopping-cart/?');
					} else {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-account/');
					}
				}
				elseif ($user && !empty($errors)) {
					self::cleanCurrrentSessionData(self::getNetwork());
					$GLOBALS[self::SOCIAL_LOGIN_ERROR] = $errors;
				}
				return false;
			}
		}

		return null;
	}


	public static function logout()
	{
		SJB_Session::unsetValue('sn');
		if (self::$oSocialPlugin) {
			return self::$oSocialPlugin->logout();
		}
		return null;
	}


	public static function getProfilePublicUrlByProfileID($profileID)
	{
		if ($profileID) {
			$socialProfileID = self::getProfileSocialID($profileID);
			if ($socialProfileID) {
				$aSoc = explode('linkedin_', $socialProfileID);
				if (is_array($aSoc)) {
					return SocialLoginPlugin::getProfilePublicUrlByProfileLinkedinID($aSoc[1]);
				}
			}
		}

		return null;
	}


	public static function getProfileSocialID($profileSID)
	{
		$result = SJB_DB::query('SELECT `reference_uid` from `users` where `sid` = ?n', $profileSID);
		if (!empty($result)) {
			$result = array_shift($result);
			return $result['reference_uid'];
		}
		return null;
	}

	public static function getProfileSocialAutoFillData($profileSID)
	{
		$aReturn = array('allow' => false);
		if (self::getProfileSocialID($profileSID)) {
			$aReturn['allow'] = true;
			if (self::$oSocialPlugin && self::getProfileObject()) {
				$aReturn['logged'] = true;
				$aReturn['network'] = self::$oSocialPlugin->getNetwork();
			}
		}
		return $aReturn;
	}


	public static function postRegistration()
	{
		if (self::$oSocialPlugin && self::$oProfile) {
			self::$oSocialPlugin->saveProfileSystemInfo();
		}
	}


	public static function setUserSocialIDByUserSID($userSID, $socialID)
	{
		if ($network = self::getNetwork()) {
			return SJB_DB::query('UPDATE `users` SET `reference_uid` = ?s WHERE `sid` = ?n', $network . '_' . $socialID, $userSID);
		}
	}


	public static function autofillListing($aAutofillData)
	{

		if (self::getNetwork()
				&& !$aAutofillData['formSubmitted']
				&& 'Resume' == $aAutofillData['listingTypeID']
				&& isset($_REQUEST['autofill'])
		) {
			self::$isSyncAllowed = true;
			if (self::$oSocialPlugin instanceof SJB_SocialPlugin) {
				self::$oSocialPlugin->fillRequestOutSocialData($_REQUEST);
			}
			unset($_REQUEST['autofill']);
		}
	}


	public static function autofillListingForm($aAutofillData)
	{
		if (self::getNetwork() && 'Resume' == $aAutofillData['listingTypeID']) {
			$aAutofillData['tp']->assign('socialAutoFillData', self::getProfileSocialAutoFillData($aAutofillData['userSID']));
		}
	}

	/**
	 * @param  array $socPlugins
	 * @return array
	 */
	public static function getSocialNetworks(array $socPlugins)
	{
		$result = array();
		
		if (empty($socPlugins)) {
			return $result;
		}
		
		$socNetworks = array (
			'facebook'    => array ('name' => 'Facebook'),
			'linkedin'    => array ('name' => 'Linkedin'),
			'google_plus' => array ('name' => 'Google+')
		);
		
		foreach ($socPlugins as $key => $socPlugin) {
			$result[$key]['id'] = $socPlugin;
			if (isset($socNetworks[$socPlugin])) {
				$result[$key]['name'] = $socNetworks[$socPlugin]['name'];
			} else {
				$result[$key]['name'] = $socPlugin;
			}
		}
		
		return $result;
	}

	public function saveUserGroupIDIfPossible()
	{
		$userGroupID = $this->getRequestUserGroupIDValue();
		if ($userGroupID)
			$this->setSessionUserGroupID($userGroupID);
	}

	/**
	 * redirects user to "/registration-social/" page
	 */
	public function redirectToRegistrationSocialPage()
	{
		$userGroupIDPart = '';

		$sessionUserGroupId = $this->getSessionUserGroupID();
		if (!empty($sessionUserGroupId)) {
			$userGroupIDPart = '?' . self::REQUEST_USER_GROUP_ID_KEY . '=' . $sessionUserGroupId;
			$this->unsetSessionUserGroupID();
		}

		$userGroupID = $this->getRequestUserGroupIDValue();
		if (empty($userGroupIDPart) && $userGroupID) {
			$userGroupIDPart = '?' . self::REQUEST_USER_GROUP_ID_KEY . '=' . $userGroupID;
		}

		SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/registration-social/' . $userGroupIDPart);
	}

	public static function getSessionUserGroupID()
	{
		return SJB_Session::getValue(self::SESSION_USER_GROUP_ID_KEY);
	}

	public static function setSessionUserGroupID($value)
	{
		SJB_Session::setValue(self::SESSION_USER_GROUP_ID_KEY, $value);
	}

	public static function unsetSessionUserGroupID()
	{
		SJB_Session::unsetValue(self::SESSION_USER_GROUP_ID_KEY);
	}

	public static function getRequestUserGroupIDValue()
	{
		return SJB_Request::getVar(self::REQUEST_USER_GROUP_ID_KEY);
	}

	protected static function cleanSessionData($network)
	{
		$sessionSN = SJB_Session::getValue('sn');
		if (!empty($sessionSN['authorized']) && $sessionSN['network'] !== $network) {
			SJB_Session::unsetValue('sn');
		}
	}

	protected static function cleanCurrrentSessionData($network)
	{
		$sessionSN = SJB_Session::getValue('sn');
		if (!empty($sessionSN['authorized']) && $sessionSN['network'] == $network) {
			SJB_Session::unsetValue('sn');
		}
	}

	protected static function flagSocialPluginInSession($network)
	{
		SJB_Session::setValue('sn', array('authorized' => true, 'network' => $network));
	}

	public static function deleteProfileSocialInfoByReference($reference)
	{
		$some = explode('_', $reference);
		$network = SJB_Array::get($some, 0);
		if ($network) {
			$fieldID = $network . '_id';
			$id = explode($network.'_', $reference);
			$id = SJB_Array::get($id, 1);
			if (SJB_DB::table_exists($network)) {
				SJB_DB::query('DELETE FROM `?w` WHERE `?w` = ?s', $network, $fieldID, $id);
			}
		}
	}

	protected function registerError(Exception $e)
	{
		$GLOBALS[self::SOCIAL_ACCESS_ERROR]['SOCIAL_ACCESS_ERROR'] = $e->getMessage();
	}

}
