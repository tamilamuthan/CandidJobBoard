<?php

/**
 * Integration SJB to JobG8 plugin class
 * 
 * May get some simple test params:
 * 
 * /jobg8_outgoing/:
 * test    - if value is not empty, set test mode on
 * listing - ID of listing to test send to jobg8
 * action  - type of test action to send to jobg8 (type of XML-request. Possible values: post, amend, delete)
 * 
 * http://example.site.url/jobg8_outgoing/?test=1&listing=40005&action=post
 * will be generate for listing 40005 "post" XML-request and send it to JobG8, use plugin settings
 * 
 * 
 * /jobg8_incoming/:
 * test   - if value is not empty, set test mode on
 * action - type of test data to post on SJB JobBoard (emulation of JobG8 incoming data)
 * 
 * http://example.site.url/jobg8_incoming/?test=1&action=post
 * 
 * will be generate incoming data for "post" action, and post it to SJB Job Board
 * 
 */

require_once 'JobG8/Jobg8.php';

class JobG8IntegrationPlugin extends SJB_PluginAbstract 
{
	const INCOMING_LOG_FILENAME = 'jobg8_incoming.log';
	const RESPONSE_LOG_FILENAME = 'jobg8_response.log';
	const OUTGOING_LOG_FILENAME = 'jobg8_outgoing.log';

	/**
	 * @var array
	 * @key => label
	 */
	public static $jobPostingTypes = array(
		'applications' => 'Applications',
		'traffic' => 'Traffic'
	);

	/**
	 * @var array
	 */
	public static $postingTypeStates = array();
	
	/**
	 * Initialization of plugin functions.
	 * 
	 * This will add new functions in modules. After this, new functions may be
	 * called via http://site.url/system/<module_name>/<function_name>/
	 */
	public static function init()
	{
		$moduleManager = SJB_System::getModuleManager();
		$miscellaneous = $moduleManager->modules['miscellaneous']['functions'];
		$newMiscellaneous = array(
			'jobg8_outgoing' => array (
				'display_name'	=> 'Jobg8 Outgoing',
				'script'		=> 'jobg8_outgoing.php',
				'type'			=> 'user',
				'access_type'	=> array('user'),
			),
			'jobg8_incoming' => array (
				'display_name'	=> 'Jobg8 Incoming',
				'script'		=> 'jobg8_incoming.php',
				'type'			=> 'user',
				'access_type'	=> array('user'),
			)
		);
		$allFunctions = array_merge( $miscellaneous, $newMiscellaneous );
		$moduleManager->modules['miscellaneous']['functions'] = $allFunctions;
		$classifieds = $moduleManager->modules['classifieds']['functions'];
		$newClassifieds = array(
			'apply_now_jobg8' => array (
				'display_name'	=> 'Apply Now',
				'script'		=> 'apply_now_jobg8.php',
				'type'			=> 'user',
				'access_type'	=> array('user'),
			),
		);
		$allFunctions = array_merge( $classifieds, $newClassifieds );
		$moduleManager->modules['classifieds']['functions'] = $allFunctions;

		self::$postingTypeStates['applications'] = SJB_Settings::getSettingByName('jobG8BuyApplicationsStatus');
		self::$postingTypeStates['traffic'] = SJB_Settings::getSettingByName('jobG8BuyTrafficStatus');
	}
	
	/**
	 * @return array
	 */
	public function pluginSettings()
	{
		$allProductList    = array();
		$products = SJB_ProductsManager::getAllProductsInfo();
		foreach ($products as $product) {
			$allProductList[] = array(
				'id'      => $product['sid'],
				'caption' => $product['name'],
			);
		}
		$jobCategoryInfo   = SJB_ListingFieldDBManager::getListingFieldInfoByID('JobCategory');
		$jobCategoriesList = SJB_ListingFieldDBManager::getListValuesBySID($jobCategoryInfo['sid']);
		$fields = array(
			'common' => array(
				array (
					'id'            => 'jobg8_jobboard_id',
					'caption'       => 'Your Jobboard ID',
					'type'          => 'string',
					'length'        => '5',
				),
				array (
					'id'            => 'jobg8_cid',
					'caption'       => 'Your Jobg8 Account',
					'type'          => 'string',
					'length'        => '6',
				),
				array (
					'id'            => 'jobg8_password',
					'caption'       => 'Your Jobboard Password',
					'type'          => 'string',
					'length'        => '50',
				),
				array (
					'id'            => 'jobg8_wsdl_url',
					'caption'       => 'Jobg8 WSDL URL',
					'type'          => 'string',
					'length'        => '255',
				),
			)
		);
		foreach (self::$jobPostingTypes as $jobPostingTypeId => $jobPostingType) {
			$fields['types'][$jobPostingType] = array(
				array(
					'type'          => 'separator',
					'caption'       => 'Buy ' . $jobPostingType .' Filters',
					'comment'       => '<br />Please select the jobs you would like to distribute to jobg8 for buying qualified ' . $jobPostingTypeId . '. <br />PLEASE NOTE: If you do not check any of the options, all jobs will be sent with the Pay Per Posting model. These filters operate separately, not together. <br /><span style="color: #f00">For example, if you enter Company 1 and select the product for Employers Product, this will send all jobs from Company 1 AND all jobs from any companies with this employer product to Jobg8.</span>'
				),
				// FILTER BY COMPANY NAME SETTINGS
				array (
					'id'            => $jobPostingTypeId . '_jobg8_company_name_filter',
					'caption'       => 'To distribute jobs and buy ' . $jobPostingTypeId . ' for certain customers only, please check this box and enter the company names (must be the same format as their name in the User Profile):',
					'type'          => 'boolean',
					'order'         => 10,
				),
				array (
					'id'            => $jobPostingTypeId . '_jobg8_company_list',
					'caption'       => '',
					'type'          => 'text',
					'order'         => 11,
				),
				// FILTER BY Products
				array (
					'id'            => $jobPostingTypeId . '_jobg8_product_filter',
					'caption'       => 'To distribute jobs and buy ' . $jobPostingTypeId . ' for customers with certain products, please check this box and select the product:',
					'type'          => 'boolean',
					'order'         => 12,
				),
				array (
					'id'            => $jobPostingTypeId . '_jobg8_product_list',
					'caption'       => '',
					'type'          => 'multilist',
					'list_values'   => $allProductList,
					'order'         => 13,
					'comment'       => 'Please use the "Control" key to choose two or more options.',
				),
				// FILTER BY JOB CATEGORY
				array (
					'id'            => $jobPostingTypeId . '_jobg8_job_category_filter',
					'caption'       => 'To distribute jobs and buy ' . $jobPostingTypeId . ' for postings within certain Categories, please check this box and select the Categories:',
					'type'          => 'boolean',
					'order'         => 14,
				),
				array (
					'id'            => $jobPostingTypeId . '_jobg8_job_category_list',
					'caption'       => '',
					'type'          => 'multilist',
					'list_values'   => $jobCategoriesList,
					'order'         => 15,
					'comment'       => 'Please use the "Control" key to choose two or more options.',
				),
			);
		}
		return $fields;
	}

	/**
	 * @param int $listingSID
	 * @return boolean
	 */
	public static function addListingToJobg8($listingSID)
	{
		if (!self::isInstalled()) {
			return;
		}
		$jobg8 = new JobG8();
		foreach (self::$jobPostingTypes as $jobPostingTypeId => $jobPostingType) {
			if (!self::$postingTypeStates[$jobPostingTypeId]) {
				continue;
			}
			$jobg8->setPostingType($jobPostingTypeId);
			$postingAction = $jobg8->isListingSidExistByActions($listingSID, array('delete', 'post'));
			if ($jobg8->isListingForJobG8($listingSID) && !$postingAction) {
				SJB_DB::queryExec("INSERT INTO `listings_to_jobg8` SET `listing_sid` = ?n, `action` = 'post', `postingType` = '{$jobPostingTypeId}'", $listingSID);
			}
			else if ($postingAction == 'delete') {
				//fixes the problem with post => send job to JobG8 => deactivate => activate
				SJB_DB::queryExec("DELETE FROM `listings_to_jobg8` WHERE `listing_sid` = ?n AND `action` = 'delete' AND `postingType` = '{$jobPostingTypeId}'", $listingSID);
			}
		}
	}

	/**
	 * @param int $listingSID
	 * @return boolean
	 */
	public static function amendListingToJobg8($listingSID)
	{
		if (!self::isInstalled()) {
			return;
		}
		$jobg8 = new JobG8();
		$listingIsActive = self::isListingActive($listingSID);
		foreach (self::$jobPostingTypes as $jobPostingTypeId => $jobPostingType) {
			if (!self::$postingTypeStates[$jobPostingTypeId]) {
				continue;
			}
			$jobg8->setPostingType($jobPostingTypeId);
			if ($listingIsActive && $jobg8->isListingForJobG8($listingSID) && !$jobg8->isListingSidExistByActions($listingSID, array('amend', 'post'))) {
				SJB_DB::queryExec("INSERT INTO `listings_to_jobg8` SET `listing_sid` = ?n, `action` = 'amend', `postingType` = '{$jobPostingTypeId}'", $listingSID);
			}
		}
	}

	/**
	 * @param int $listingSID
	 * @return boolean
	 */
	public static function deleteListingFromJobg8($listingSID)
	{
		if (!self::isInstalled()) {
			return;
		}
		$jobg8 = new JobG8();
		foreach (self::$jobPostingTypes as $jobPostingTypeId => $jobPostingType) {
			if (!self::$postingTypeStates[$jobPostingTypeId]) {
				continue;
			}
			$jobg8->setPostingType($jobPostingTypeId);
			if ($jobg8->isListingForJobG8($listingSID)) {
				$listingAction = $jobg8->getListingActionByListingSID($listingSID);
				if (!empty($listingAction)) {
					if ($listingAction == 'post') {
						SJB_DB::queryExec("DELETE FROM `listings_to_jobg8` WHERE `listing_sid` = ?n AND `postingType` = '{$jobPostingTypeId}'", $listingSID);
					} elseif ($listingAction == 'amend') {
						SJB_DB::queryExec("UPDATE `listings_to_jobg8` SET `action` = 'delete' WHERE `listing_sid` = ?n AND `postingType` = '{$jobPostingTypeId}'", $listingSID);
					}
				} else {
					SJB_DB::queryExec("INSERT INTO `listings_to_jobg8` SET `listing_sid` = ?n, `action` = 'delete', `postingType` = '{$jobPostingTypeId}'", $listingSID);
				}
			}
		}
	}

	/**
	 * @param $listingSid
	 */
	public static function beforeListingDelete($listingSid)
	{
		if (!self::isInstalled()) {
			return;
		}
		if (self::isListingActive($listingSid)) {
			self::deleteListingFromJobg8($listingSid);
		}
		JobG8::deleteJobProperties($listingSid);
	}

	/**
	 * Generate outgoing XML and send it to JobG8
	 */
	public static function sendJobsToJobG8()
	{
		error_log('jobg8_outgoing_start');
		$outgoing = new JobG8_OutgoingActions();
		foreach (self::$jobPostingTypes as $jobPostingTypeId => $jobPostingType) {
			// if posting method is disabled
			if (!self::$postingTypeStates[$jobPostingTypeId]) {
				continue;
			}
			$outgoing->setPostingType($jobPostingTypeId);
			if ($test = SJB_Request::getVar('test', false)) {
				$listingSid = SJB_Request::getInt('listing');
				$testAction = SJB_Request::getString('action');
				$listingsSIDs[$testAction][] = $listingSid;
			} else {
				$listingsSIDs = $outgoing->getListingSIDsToSend();
			}
			$outgoingXML = $outgoing->getOutgoingXML($listingsSIDs);
			$uploadedFileID = '';
			if (!empty($outgoingXML)) {
				$jobg8WSDLUrl	 = SJB_Settings::getSettingByName('jobg8_wsdl_url');
				$jobg8JobBoardID = SJB_Settings::getSettingByName('jobg8_jobboard_id');
				$jobg8Password	 = SJB_Settings::getSettingByName('jobg8_password');
				// UPLOAD
				$objUpload = new JobG8_UploadAbstractFile();
				$objUpload->setJobBoardID($jobg8JobBoardID);
				$objUpload->setPassword($jobg8Password);
				$objUpload->setFileContent($outgoingXML);
				$client = new SoapClient($jobg8WSDLUrl);
				$uploadFileType = $jobPostingTypeId == 'applications' ? 'UploadAdvertsFile' : 'UploadTrafficFile';
				$uploadMethodResult = $uploadFileType . 'Result';
				$result = $client->__soapCall($uploadFileType, array($objUpload));
				$uploadedFileID = $result->$uploadMethodResult;
				// GET RESPONSE
				$objResponse = new JobG8_GetFileResponse();
				$objResponse->setJobBoardID($jobg8JobBoardID);
				$objResponse->setPassword($jobg8Password);
				$objResponse->setFileName($result->$uploadMethodResult);
				// show result on test mode
				if ($test) {
					echo "<h2>{$jobPostingType}</h2>";
					echo "<hr><input type='text' size='70' onclick='this.select();' value='{$uploadedFileID}'/><br>";
					echo "<textarea rows='30' cols='100' onclick='this.select();' readonly>{$outgoingXML}</textarea><br><hr>";
					SJB_HelperFunctions::d($result, $uploadedFileID, $client->GetFileResponse($objResponse), $outgoingXML);
				}
				if (strpos($uploadedFileID, 'Error') === false) {
					$outgoing->removeListingsFromJobg8Table($listingsSIDs, $outgoing->listingSIDsNotPassFilters);
				}
			}
			$logData = $outgoing->getOutgoingLogData($listingsSIDs, $uploadedFileID, $outgoing->errors);
			$outgoing->log(self::OUTGOING_LOG_FILENAME, $logData);
		}
		error_log('jobg8_outgoing_end');
	}

	/**
	 * Get jobs and actions from JobG8 XML
	 */
	public static function getJobsFromJobG8()
	{
		error_log('jobg8_incoming_start');
		$xml  = file_get_contents('php://input');
		$incoming = new JobG8_IncomingActions();
		$logData = $incoming->getIncomingLogData($xml);
		$incoming->log(self::INCOMING_LOG_FILENAME, $logData);
		if (SJB_Request::getVar('test')) {
			$action = SJB_Request::getVar('action');
			$xml = $incoming->getTestData($action);
		}
		try {
			if (empty($xml)) {
				throw new Exception('No Data To Parse');
			}
			if (!(SJB_Authorization::login($incoming::JOBG8_USERNAME, SJB_System::getSettingByName($incoming::JOBG8_USER_PASSWORD_ID), false, $errors, false))) {
				throw new Exception('No Such User To JobG8');
			}
			$xmlResponse = $incoming->getXMLResponse($xml);
		} catch (Exception $e) {
			$xmlResponse = '<?xml version="1.0" encoding="utf-8"?><Body><Error><![CDATA['. $e->getMessage() .']]></Error></Body>';
		}
		for ($i = 0; $i < ob_get_level(); $i++) {
			ob_end_clean();
		}
		header("Content-type: text/xml; charset=UTF8");
		echo $xmlResponse;
		$logData = $incoming->getIncomingLogData($xmlResponse);
		$incoming->log(self::RESPONSE_LOG_FILENAME, $logData);
		error_log('jobg8_incoming_end');
	}

	public static function deleteExpiredJobG8Listings()
	{
		if (!self::isInstalled()) {
			return;
		}
		$daysOld = 1;
		$listings = SJB_DB::query("
			SELECT l.`sid` 
			FROM `listings` l
				INNER JOIN `jobg8_listings_properties` jlp ON l.`sid` = jlp.`listingSid`
			WHERE l.`expiration_date` < DATE_SUB( NOW(), INTERVAL {$daysOld} DAY) 
				AND l.`active` = 0 
		");
		if (!empty($listings)) {
			foreach ($listings as $listing) {
				SJB_ListingManager::deleteListingBySID($listing['sid']);
			}
		}
	}
	
	public static function install()
	{
		$jobg8 = new JobG8();
		$jobg8->install();
	}

	/**
	 * @param SJB_User $user
	 * @throws Exception
	 */
	public static function isJobg8UserDelete($user)
	{
		$jobg8 = new JobG8();
		$userName = is_object($user) ? $user->getUserName() : $user;
		if ($userName == $jobg8::JOBG8_USERNAME) {
			throw new Exception('The "jobg8" user can not be deleted');
		}
	}

	/**
	 * @param $listingId
	 * @return bool
	 */
	public static function isListingActive($listingId) {
		return (bool) SJB_DB::queryValue('SELECT `active` FROM listings WHERE sid = ?n LIMIT 1', $listingId);
	}
	
	public static function handleSystemBoot()
	{
		$plugin = SJB_PluginManager::getPluginByName('JobG8IntegrationPlugin');
		$isPluginActive = $plugin && $plugin['active'] == '1';
		if ($isPluginActive) {
			if (SJB_Request::getVar('action') == 'settings' && SJB_Request::getVar('plugin') == 'JobG8IntegrationPlugin') {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('ADMIN_SITE_URL') . '/system/miscellaneous/jobg8_settings/?action=settings');
			}
			if (SJB_Navigator::getURI() == '/system/miscellaneous/jobg8_settings/') {
				SJB_System::getModuleManager()->includeModule(SJB_BASE_DIR . 'system/plugins/jobg8_integration_plugin/module', 'miscellaneous');
				require_once __DIR__ . '/module/miscellaneous/jobg8_settings.php';
			}
		}
	}

	public static function isInstalled()
	{
		return SJB_Settings::getValue('jobg8Installed');
	}
}