<?php

class ApiPlugin extends SJB_PluginAbstract
{
	
	private static $writeLogs = true;
	
	/**
	 * log filename for API plugin
	 * @var string
	 */
	private static $logFilename = 'api_plugin.log';

	/**
	 * Initialization of plugin functions.
	 * 
	 * This will add new functions in modules. After this, new functions may be
	 * called via http://site.url/system/<module_name>/<function_name>/
	 *
	 */
	public static function init()
	{
		$moduleManager = SJB_System::getModuleManager();
		
		$miscellaneous = $moduleManager->modules['miscellaneous']['functions'];
		$newMiscellaneous = array(
			'api' => array(
				'display_name' => 'api',
				'script'       => 'api.php',
				'raw_output'   => true,
				'type'         => 'user',
				'access_type'  => array('user'),
			),
		);
		$allFunctions = array_merge($miscellaneous, $newMiscellaneous);
		$moduleManager->modules['miscellaneous']['functions'] = $allFunctions;
	}
	
	
	/**
	 * Write message to plugin log
	 * 
	 * @param string $message
	 */
	private static function writeToLog($message)
	{
		if (!is_string($message)) {
			$message = (string) $message;
		}
		
		$filename = SJB_BASE_DIR . 'system/cache/' . self::$logFilename;
		
		if (file_exists($filename) && filesize($filename) > 10000000) {
			$fp = fopen($filename, "w");
		} else {
			$fp = fopen($filename, "a");
		}
		flock($fp, LOCK_EX);
		fputs($fp, date("Y-m-d H:i:s") . "\t" . $message . "\n");
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	
	/**
	 * Get plugin settings
	 *
	 * @return array
	 */
	public function pluginSettings ()
	{
		return array();
	}


	/**
	 * Main API handler to get incoming request, and route to methods
	 * @param $requestData
	 * @internal param array $params
	 */
	public static function apiHandler($requestData)
	{
		if (self::$writeLogs) {
			self::writeToLog("Request: \n" . print_r($requestData, true));
		}
		$requestFunction = SJB_Array::get($requestData, 'function');
		$requestParams = SJB_Array::get($requestData, 'params');
		
		$callResult = new stdClass();
		$callResult->session_id = session_id();
		if (method_exists('ApiPlugin', $requestFunction)) {
			$callResult->result = "success";
			$callResult->function_result = call_user_func(array('ApiPlugin', $requestFunction), $requestParams);
		} else {
			$callResult->result = "error";
			$callResult->message = "function not found";
		}

		header('Content-Type: application/json');
		echo self::sjb_json_encode($callResult);
		
		if (self::$writeLogs) {
			self::writeToLog("Response: \n" . self::sjb_json_encode($callResult));
		}
	}


	/**
	 * JSON encode PHP array or object
	 * @param object|array $php_value
	 * @return string
	 */
	public static function sjb_json_encode($php_value)
	{
		if (function_exists('json_encode')) {
			return json_encode($php_value);
		}
		return Zend_Json::encode($php_value);
	}

	/**
	 * Parse JSON string
	 * @param $json_string
	 * @return mixed
	 * @throws Zend_Json_Exception
	 * @internal param array|object $php_value
	 */
	public static function sjb_json_decode($json_string)
	{
		if (function_exists('json_decode')) {
			return json_decode($json_string);
		} else {
			return Zend_Json::decode($json_string);
		}
	}

	private static function sjb_get_listing_property_metadata($property_name)
	{
		$empty_listing = new SJB_Listing(array(), 1);
		$empty_listing->addIDProperty();
		$empty_listing->addActivationDateProperty();
		$empty_listing->addUsernameProperty();
		$empty_listing->addKeywordsProperty();
		$empty_listing->addListingTypeIDProperty();
		$empty_listing->addPostedWithinProperty();

		$search_form_builder = new SJB_SearchFormBuilder($empty_listing);

		// CHECK FIELD TYPE
		if (isset($search_form_builder->object_properties[$property_name]->type->property_info['type'])) {
			$fieldType = $search_form_builder->object_properties[$property_name]->type->property_info['type'];

			if ($fieldType == 'list' || $fieldType == 'multilist') {
				// TRANSLATE
				if (isset($search_form_builder->object_properties[$property_name]->type->list_values)) {
					$i18n = SJB_I18N::getInstance();
					$domain = 'Property_' . $property_name;
					foreach ($search_form_builder->object_properties[$property_name]->type->list_values as $key => $val) {
						$trans = $i18n->gettext($domain, $val['caption'], 'default');
						$search_form_builder->object_properties[$property_name]->type->list_values[$key]['caption'] = $trans;
					}
				}
			}
		}

		return $search_form_builder->object_properties[$property_name];
	}

	/***************************************************************************
	 * API Functions
	 **************************************************************************/

	/**
	 * Get configuration options for mobile application
	 * @param array $params
	 * @return array
	 */
	public static function get_config($params)
	{
		$logo_url = SJB_System::getSystemSettings('SITE_URL') . SJB_System::getSystemSettings('MOBILE_LOGO_FILENAME');
		return array('logo_url' => $logo_url);
	}

	/**
	 * API authenticate function
	 * @param $params
	 * @return array|null|string
	 */
	public static function authenticate($params)
	{
		$username = SJB_Array::get($params, 'username', null);
		$password = SJB_Array::get($params, 'password', null);
		$errors = array();
		if ($username == 'anonymous' && $password == 'anonymous') {
			return array('username' => 'anonymous');
		}
		if (!SJB_UserManager::login($username, $password, $errors, false, false)) {
			return 'INVALID_USERNAME_OR_PASSWORD';
		} else {
			$userInfo = SJB_UserManager::getUserInfoByUserName($username);
			if (isset($userInfo['user_group_sid']) && $userInfo['user_group_sid'] == 41) {
				return 'FORBIDDEN_FOR_EMPLOYER';
			}
			SJB_Authorization::login($username, $password, true, $errors);
			return self::get_filtered_user_info($username);
		}
	}

	public static function get_user_info($params)
	{
		return self::get_filtered_user_info($params['username']);
	}

	private static function get_filtered_user_info($username)
	{
		$userInfo = SJB_UserManager::getUserInfoByUserName($username);
		$userInfo['group'] = $userInfo['user_group_sid'] == 41 ? 'Employer' : 'JobSeeker';

		unset($userInfo['password']);
		unset($userInfo['verification_key']);
		unset($userInfo['ip']);
		unset($userInfo['reference_uid']);

		return $userInfo;
	}

	/**
	 * Get list of JobCategory values
	 * @param array $params
	 */
	public static function get_categories($params)
	{
		return self::sjb_get_listing_property_metadata('JobCategory');
	}

	/**
	 * Get list of search distances values
	 * @param array $params
	 * @return array
	 */
	public static function get_distances($params)
	{
		$radius_search_unit = SJB_System::getSettingByName('radius_search_unit');
		$i18n = SJB_I18N::getInstance();
		$radius_search_unit = $i18n->gettext('Frontend', $radius_search_unit);
		return array(
			array('id' => 10, 'caption' => '10 ' . $radius_search_unit),
			array('id' => 20, 'caption' => '20 ' . $radius_search_unit),
			array('id' => 50, 'caption' => '50 ' . $radius_search_unit),
			array('id' => 100, 'caption' => '100 ' . $radius_search_unit),
			array('id' => 200, 'caption' => '200 ' . $radius_search_unit),
		);
	}

	/**
	 * Get list of listing types
	 * @param $params
	 * @return array
	 */
	public static function get_listing_types($params)
	{
		return array(
			array(
				'id' => 'Job',
				'caption' => 'Job'
			),
			array(
				'id' => 'Resume',
				'caption' => 'Resume'
			)
		);
	}

	/**
	 * Get list of listing fields values
	 * @param $params
	 * @return array
	 */
	public static function get_listing_fields_values($params)
	{
		$items = SJB_DB::query("SELECT * FROM listing_field_list");
		$list_items = array();
		foreach ($items as $item) {
			$list_items[$item['sid']] = $item['value'];
		}
		return $list_items;
	}

	/**
	 * Get Listing alerts
	 * @param $params
	 * @return array|bool
	 */
	public static function get_listing_alerts($params)
	{
		return SJB_GuestAlertManager::getGuestAlertsByEmail($params['email']);
	}

	/**
	 * Get listings by listing alert
	 * @param $params
	 * @return array
	 */
	public static function get_listing_alert_entries($params)
	{
		$jobAlert = SJB_GuestAlertManager::getGuestAlertInfoBySID($params['alert_sid']);
		if (empty($jobAlert)) {
			return array();
		} else {
			$searchResultsTP = new SJB_SearchResultsTP($jobAlert['data'], 'Job');
			return $searchResultsTP->getListingCollectionStructure($searchResultsTP->_getListingSidCollectionFromRequest());
		}
	}

	/**
	 * Get listings by user
	 * @param $params
	 * @return array
	 */
	public static function get_user_listings($params)
	{
		$user_sid = (int)SJB_Array::get($params, 'user_sid', 0);
		$result = SJB_DB::query("SELECT sid FROM listings WHERE user_sid = {$user_sid}");

		$listings = array();
		foreach ($result as $val) {
			$listing = SJB_ListingManager::getObjectBySID($val['sid']);
			$listing = SJB_ListingManager::createTemplateStructureForListing($listing);
			$listings[] = $listing;
		}
		return $listings;
	}

	/**
	 * Search listings by request data
	 * @param array $request
	 * @return array
	 */
	private static function search_listings_by_request($request)
	{
		$request['active']['equal'] = '1';
		$request['action'] = 'search';
		// set default value for listing type id

		$searchResultsTP = new SJB_SearchResultsTP($request, $request['listing_type']['equal']);
		$searchResultsTP->usePriority(true);
		$searchResultsTP->getChargedTemplateProcessor();

		// check current criteria_saver for listings
		$listings = $searchResultsTP->getListingSidCollectionForCurrentPage();
		$searchId = $searchResultsTP->searchId;

		$result = array();
		foreach ($listings as $sid) {
			$listing = SJB_ListingManager::createTemplateStructureForListing(SJB_ListingManager::getObjectBySID($sid));
			$listing['JobDescription'] = strip_tags($listing['JobDescription']);

			if (isset($listing['ApplicationSettings']) && is_array($listing['ApplicationSettings'])) {
				$listing['ApplicationSettingsType']  = $listing['ApplicationSettings']['add_parameter'] == '2' ? 'url' : 'email';
				$listing['ApplicationSettingsValue'] = $listing['ApplicationSettings']['value'];
			} else {
				$listing['ApplicationSettingsType']  = '';
				$listing['ApplicationSettingsValue'] = '';
			}

			// display category in job details as string
			$jobCategory = '';
			if (!empty($listing['JobCategory'])) {
				$jobCategory = implode(', ', $listing['JobCategory']);
			}
			$listing['JobCategory'] = $jobCategory;

			$result[] = $listing;
		}

		$result = array(
			'listings'       => $result,
			'searchId'       => (string) $searchId,
			'listingsNumber' => $searchResultsTP->listing_search_structure['listings_number']
		);

		return $result;
	}

	/**
	 * Generate some request values from $params and search listings by request
	 * @param array $params
	 * @return array
	 */
	public static function search_listings($params)
	{
		$request = array();
		$request['listing_type'] = array('equal' => SJB_Array::get($params, 'listing_type', 'Job'));

		$search_id = SJB_Array::get($params, 'search_id', '');
		if ($search_id && $search_id != '') {
			$request['searchId'] = $search_id;
		} else {
			$request['keywords'] = array('all_words' => SJB_Array::get($params, 'keywords', ''));
			$request['GooglePlace'] = array('location' => array('value' => SJB_Array::get($params, 'location', ''), 'radius' => SJB_Array::get($params, 'radius', '')));
			$request['default_listings_per_page'] = SJB_Request::getInt('listings_per_page', 20);
			if (SJB_Array::get($params, 'distance', false) !== false) {
				$request['ZipCode'] = array(
					'geo_coord' => array(
						'latitude'  => SJB_Array::get($params, 'latitude', 0.00),
						'longitude' => SJB_Array::get($params, 'longitude', 0.00),
						'distance'  => SJB_Array::get($params, 'distance', 0.00),
					)
				);
			}
		}

		$request['page'] = SJB_Array::get($params, 'page', 1);

		return self::search_listings_by_request($request);
	}

	/**
	 * Search listings by Search ID
	 * @param array $params
	 * @return array
	 */
	public static function search_listings_by_search_id($params)
	{
		$jobAlert = SJB_GuestAlertManager::getGuestAlertInfoBySID((int)SJB_Array::get($params, 'search_sid', 0));

		$request = $jobAlert['data'];
		$request['page']              = SJB_Array::get($params, 'page', 1);
		return self::search_listings_by_request($request);
	}

	/**
	 * Get listings by sids in array
	 * @param array $params
	 * @return array
	 */
	public static function get_listings($params)
	{
		$listings_structure = array();
		foreach ($params as $sid) {
			$listing = SJB_ListingManager::getObjectBySID($sid);
			if (is_null($listing)) {
				continue;
			}

			$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing);
			$listings_structure[$listing->getID()] = $listing_structure;
		}

		$result = array();
		foreach ($listings_structure as $sid => $listing) {
			$listing['JobDescription'] = strip_tags($listing['JobDescription']);
			$jobCategory = '';
			if (!empty($listing['JobCategory'])) {
				$jobCategory = implode(', ', $listing['JobCategory']);
			}
			$listing['JobCategory'] = $jobCategory;
			$result[] = $listing;
		}

		return $result;
	}

	/**
	 * Apply now for listing
	 * @param array $params
	 * @return string
	 */
	public static function apply_listing($params)
	{
		$current_user_sid = SJB_Array::get($params, 'user_sid', null);

		$request = array();
		$request['listing_id'] = SJB_Array::get($params, 'listing_sid', null);
		$request['comments'] = SJB_Array::get($params, 'comment', null);
		$request['id_resume'] = SJB_Array::get($params, 'attached_listing_sid', null);
		// unregistered user
		$request['name'] = SJB_Array::get($params, 'name', null);
		$request['email'] = SJB_Array::get($params, 'email', null);

		$notRegisterUserData = $request;
		$listing_info = '';

		$controller = new SJB_SendListingInfoController($request);
		if (!$controller->isListingSpecified()) {
			return 'listing not found';
		}

		$post = $controller->getData();

		if (!isset($post['submitted_data']['id_resume'])) {
			$canApplyWithoutResume = true;
			if (!$canApplyWithoutResume) {
				return 'cannot apply without resume';
			}
		}

		if (SJB_Applications::isApplied($post['submitted_data']['listing_id'], $current_user_sid) && !is_null($current_user_sid)) {
			return 'You already applied to this job';
		}

		$res = SJB_Applications::create(
			$post['submitted_data']['listing_id'],
			$current_user_sid,
			(isset($post['submitted_data']['id_resume'])) ? $post['submitted_data']['id_resume'] : "",
			$post['submitted_data']['comments'],
			'',
			'',
			'',
			$params
		);

		if ($res === false) {
			return "cannot apply";
		}

		if (isset($post['submitted_data']['id_resume']) && $post['submitted_data']['id_resume'] != 0) {
			$listing_info = SJB_ListingManager::getListingInfoBySID($post['submitted_data']['id_resume']);
			$emp_sid = SJB_ListingManager::getUserSIDByListingSID($post['submitted_data']['listing_id']);
			$accessible = SJB_ListingManager::isListingAccessableByUser($post['submitted_data']['id_resume'], $emp_sid);
			if (!$accessible) {
				SJB_ListingManager::setListingAccessibleToUser($post['submitted_data']['id_resume'], $emp_sid);
			}
		}

		if (!SJB_Notifications::sendApplyNow($post, '', $listing_info, $notRegisterUserData)) {
			return 'send error';
		}

		return 'success';
	}

	/**
	 * Save user
	 * @param array $params
	 * @return array|null
	 */
	public static function get_user_groups($params)
	{
		return SJB_UserGroupManager::getAllUserGroupsInfo();
	}

	/**
	 * Get user profile fields by user group id / user sid in array
	 * @param array $params
	 * @return array
	 */
	public static function get_user_profile_fields($params)
	{
		$userSID = SJB_Array::get($params, 'user_sid', null);
		$userGroupId = SJB_Array::get($params, 'user_group_id', null);
		$errors = SJB_Array::get($params, 'errors', array());
		if ($userSID) {
			$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
			$params = array_merge($userInfo, $params);
			$userGroupSid = !empty($userInfo['user_group_sid']) ? $userInfo['user_group_sid'] : null;
		} else {
			$userGroupSid = SJB_UserGroupManager::getUserGroupSIDByID($userGroupId);
		}
		$user = SJB_ObjectMother::createUser($params, $userGroupSid);
		$user->deleteProperty('active');
		$user->deleteProperty('featured');
		$registrationForm = SJB_ObjectMother::createForm($user);
		$formFields = $registrationForm->getFormFieldsInfo();
		foreach ($formFields as $fieldName => $fieldInfo) {
			if (in_array($fieldInfo['type'], array('picture', 'logo'))) {
				unset($formFields[$fieldName]);
			} else {
				switch ($fieldInfo['type']) {
				case 'list':
				case 'multilist':
					$property = $user->getPropertyInfo($fieldName);
					$formFields[$fieldName]['list_values'] = $property['list_values'];
					$formFields[$fieldName]['value'] = $property['value'];
					$formFields[$fieldName]['default_value'] = $property['default_value'];
					break;
				case 'location':
					$property = $user->getProperty($fieldName);
					$child = $property->type->child;
					$form = new SJB_Form($child);
					$childFormFields = $form->getFormFieldsInfo();
					foreach ($childFormFields as $childFielName => $childFieldInfo) {
						$childProperty = $child->getPropertyInfo($childFielName);
						switch ($childFieldInfo['type']) {
							case 'list':
							case 'multilist':
								if ($childFieldInfo['id'] == 'State') {
									$displayAS = !empty($childFieldInfo['display_as']) ? $childFieldInfo['display_as'] : 'state_name';
									$AllStates = SJB_DB::query("SELECT `sid`, ?w as `state_name`, `country_sid` FROM `states` WHERE `active` = 1 ORDER BY state_name", $displayAS);
									$listValues = array();
									foreach ($AllStates as $state) {
										$listValues[$state['country_sid']][] = array('id' => $state['sid'], 'caption' => $state['state_name']);
									}
									$childFormFields[$childFielName]['list_values'] = $listValues;
								} else {
									$childFormFields[$childFielName]['list_values'] = $childProperty['list_values'];
								}
								break;
						}
						$childFormFields[$childFielName]['value'] = $childProperty['value'];
					}
					$formFields[$fieldName]['fields'] =  $childFormFields;
					break;
				default:
					$property = $user->getPropertyInfo($fieldName);
					$formFields[$fieldName]['value'] = $property['value'];
					$formFields[$fieldName]['default_value'] = $property['default_value'];
					break;
				}
			}
		}
		return array('form_fields' => $formFields, 'errors' => $errors);
	}

	/**
	 * Save user
	 * @param array $params
	 * @return array|string
	 */
	public static function save_user($params)
	{
		$userGroupId = SJB_Array::get($params, 'user_group_id', null);
		$userSID = SJB_Array::get($params, 'user_sid', null);
		$password = SJB_Array::get($params, 'password', null);
		$params['password'] = array('original' => $password, 'confirmed' => $password);
		if ($userSID) {
			$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
			$userInfo = array_merge($userInfo, $params);
			$userGroupSid = !empty($userInfo['user_group_sid']) ? $userInfo['user_group_sid'] : null;
		} else {
			$userGroupSid = SJB_UserGroupManager::getUserGroupSIDByID($userGroupId);
		}

		if (!empty($userInfo)) {
			$user = new SJB_User($userInfo, $userGroupSid);
		} else {
			$user = SJB_ObjectMother::createUser($params, $userGroupSid);
		}
		$user->deleteProperty("active");
		$user->deleteProperty("featured");

		if ($userSID) {
			$user->setSID($userSID);
			$user->makePropertyNotRequired("password");
		}

		$errors = array();
		$profileForm = new SJB_Form($user);
		if ($profileForm->isDataValid($errors)) {
			if ($userSID) {
				$passworValue = $user->getPropertyValue('password');
				if (empty($passworValue['original'])) {
					$user->deleteProperty('password');
				}
				SJB_UserManager::saveUser($user);
				SJB_Authorization::updateCurrentUserSession();
			} else {
				$defaultProduct = SJB_UserGroupManager::getDefaultProduct($userGroupSid);
				SJB_UserManager::saveUser($user);
				$availableProductIDs = SJB_ProductsManager::getProductsIDsByUserGroupSID($userGroupSid);

				if ($defaultProduct && in_array($defaultProduct, $availableProductIDs)) {
					$contract = new SJB_Contract(array('product_sid' => $defaultProduct));
					$contract->setUserSID($user->getSID());
					$contract->saveInDB();
				}

				// Activation
				SJB_UserManager::activateUserByUserName($user->getUserName());
				SJB_Notifications::sendUserWelcomeLetter($user->getSID());
				SJB_Authorization::login($user->getUserName(), $params['password']['original'], false, $errors);
			}
			return 'success';
		} else {
			$params['errors'] = $errors;
			return self::get_user_profile_fields($params);
		}
	}

	public static function get_applications($params)
	{
		$user_sid = (int)SJB_Array::get($params, 'user_sid', 0);
		$applications = SJB_Applications::getByJobseeker($user_sid);
		$result = array();
		foreach ($applications as $application) {
			$result[] = $application['listing_id'];
		}

		return $result;
	}

	/**
	 * save job alert
	 * @param array $params
	 * @return bool
	 */
	public static function save_job_alert($params)
	{
		$result = self::search_listings($params['search_criteria']);

		$criteria_saver = new SJB_ListingCriteriaSaver($result['searchId']);
		$requested_data = $criteria_saver->getCriteria();

		if (is_array($criteria_saver->order_info)) {
			$requested_data = array_merge($requested_data, $criteria_saver->order_info);
		}
		$guestAlert = new SJB_GuestAlert($requested_data);
		$guestAlert->setPropertyValue('email', SJB_Array::get($params, 'email'));
		$guestAlert->setPropertyValue('email_frequency', SJB_Array::get($params, 'email_frequency'));
		$guestAlert->addDataProperty(serialize($requested_data));
		$guestAlert->save();
		return true;
	}

}

