<?php

require_once 'JobG8_Mapper.php';
require_once 'JobG8_GetFileResponse.php';
require_once 'JobG8_UploadAbstractFile.php';
require_once "JobG8_IncomingActions.php";
require_once "JobG8_OutgoingActions.php";
require_once "JobG8_Filter.php";

class JobG8
{
	const JOBG8_USERNAME = 'jobg8';
	const JOBG8_USER_PASSWORD_ID = 'jobG8UserPassword';
	const JOBG8_LISTING_PROPERTIES_TABLE = 'jobg8_listings_properties';

	private $tableName = 'listings_to_jobg8';
	protected $postingType;

	/**
	 * @param int $listingSID
	 * @return bool
	 */
	public function isListingForJobG8($listingSID)
	{
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingSID);
        if (!empty($listingInfo['data_source']) || $jobProperties = $this->getJobProperties($listingSID)) {
            return false;
        }
        $listingInfo = array_merge($listingInfo, $jobProperties);
		$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
		if ($listingTypeID != 'Job') {
			return false;
		}
		$filter = new JobG8_Filter($this->postingType);
		return $filter->isPassedByFilters($listingInfo);
	}
	
	/**
	 * @param string $fileName
	 * @param string $data
	 */
	public function log($fileName, $data)
	{
		$file = SJB_BASE_DIR . 'system/cache/' . $fileName;
		$flags = FILE_APPEND;
		if (file_exists($file) && filesize($file) > 15000000) {
			$flags = null;
		}
		file_put_contents($file, $data, $flags);
	}

	/**
	 * @param int $listingSID
	 * @param string $action
	 * @return string
	 */
	public function isListingSidExistByActions($listingSID, $action)
	{
		$actionClause = is_array($action) ? '`action` IN (?l)' : '`action` = ?s';
		return SJB_DB::queryValue("SELECT `action` FROM ?w WHERE `listing_sid` = ?n AND {$actionClause} AND `postingType` = ?s", $this->tableName, $listingSID, $action, $this->postingType);
	}

	/**
	 * @param int $listingSID
	 * @return string
	 */
	public function getListingActionByListingSID($listingSID)
	{
		return SJB_DB::queryValue("SELECT `action` FROM ?w WHERE `listing_sid` = ?n AND `postingType` = ?s", $this->tableName, $listingSID, $this->postingType);
	}

	/**
	 * @param $listingSid
	 * @return array
	 */
	public function getJobProperties($listingSid)
	{
		$result = array();
		$listingProperties = SJB_DB::query("SELECT `jobReference`, `jobType` FROM ?w WHERE `listingSid` = ?s", JobG8::JOBG8_LISTING_PROPERTIES_TABLE, $listingSid);
		if ($listingProperties && is_array($listingProperties)) {
			$result = array_pop($listingProperties);
		}
		return $result;
	}

	/**
	 * @param $listingSid
	 * @param $property
	 * @return string
	 */
	public static function getJobProperty($listingSid, $property)
	{
		$result = '';
		$allowedProperties = array('jobReference', 'jobType');
		if ($property && in_array($property, $allowedProperties)) {
			$result = SJB_DB::queryValue("SELECT ?w FROM ?w WHERE `listingSid` = ?s", $property, JobG8::JOBG8_LISTING_PROPERTIES_TABLE, $listingSid);
		}
		return $result;
	}

	/**
	 * @param array $listingSIDs
	 * @param array $listingSIDsNotPassFilters
	 */
	public function removeListingsFromJobg8Table($listingSIDs, $listingSIDsNotPassFilters)
	{
		$listingSIDsForRemove = array();
		foreach ($listingSIDs as $action) {
			$listingSIDsForRemove = array_merge($listingSIDsForRemove, $action);
		}
		if (!empty($listingSIDsNotPassFilters)) {
			$listingSIDsForRemove = array_diff($listingSIDsForRemove, $listingSIDsNotPassFilters);
		}
		SJB_DB::query("DELETE FROM ?w WHERE `listing_sid` IN (?l) AND `postingType` = ?s", $this->tableName, $listingSIDsForRemove, $this->postingType);
	}

	/**
	 * @param $postingType
	 */
	public function setPostingType($postingType) {
		if ($postingType) {
			$this->postingType = $postingType;
		}
	}

	/**
	 * @return array
	 */
	public function getListingSIDsToSend()
	{
		$listingsInfo = SJB_DB::query('SELECT `listing_sid`, `action` FROM ?w WHERE `postingType` = ?s', $this->tableName, $this->postingType);
		$listingSIDs   = array(
			'post'   => array(),
			'amend'  => array(),
			'delete' => array()
		);
		foreach ($listingsInfo as $listingInfo) {
			$listingSIDs[$listingInfo['action']][] = $listingInfo['listing_sid'];
		}
		
		return $listingSIDs;
	}

	public function install()
	{
		$mapper = new JobG8_Mapper();
		$defaultMappingFields = array(
			$mapper::CATEGORY_MAPPING_TYPE   => $mapper->categoryMappingFieldID,
			$mapper::EMPLOYMENT_MAPPING_TYPE => $mapper->employmentMappingFieldID,
		);
		foreach ($defaultMappingFields as $mappingType => $defaultMappingField) {
			if (SJB_ListingFieldManager::getListingFieldSIDByID($defaultMappingField)) {
				SJB_Settings::saveSetting($mappingType .'MappingFieldID', $defaultMappingField);
			}
		}
		$mapper->createMappingTable();
		$mapper->setCategoryMappingType();
		$mapper->setEmploymentMappingType();
		$mapper->setDefaultCategoryMapping();
		$mapper->setDefaultEmploymentMapping();

		$this->createJobg8User(SJB_UserGroup::EMPLOYER);
		$this->createProductForJobg8User(SJB_UserGroup::EMPLOYER);
		$this->createListingsToJobg8Table();
		$this->createJobg8ListingsPropertiesTable();
		SJB_Settings::saveSetting('jobg8Installed', 1);
		SJB_Settings::saveSetting('jobG8BuyApplicationsStatus', 1);
	}

	/**
	 * @param int $userGroupSID
	 */
	private function createJobg8User($userGroupSID)
	{
		$jobg8Password = SJB_Authorization::generateSessionKey();
		$userInfo = array(
			'username' => self::JOBG8_USERNAME,
			'password' => $jobg8Password,
		);
		SJB_Settings::addSetting(self::JOBG8_USER_PASSWORD_ID, $jobg8Password);
		$userSID = SJB_UserManager::getUserSIDbyUsername($userInfo['username']);
		if (empty($userSID)) {
			$user = SJB_ObjectMother::createUser($userInfo, $userGroupSID);
			$user->deleteProperty('active');
			$user->deleteProperty('featured');
			SJB_UserManager::saveUser($user);
			SJB_UserManager::activateUserByUserName($userInfo['username']);
		}
	}

	/**
	 * @param int $userGroupSID
	 */
	private function createProductForJobg8User($userGroupSID)
	{
		$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID('Job');
		$userSID = SJB_UserManager::getUserSIDbyUsername(self::JOBG8_USERNAME);
		$productInfo = array(
			'name' => 'JobG8 Hidden Product (Do not delete)',
			'detailed_description' => 'JobG8 Hidden Product (Do not delete)',
			'user_group_sid' => $userGroupSID,
			'listing_type_sid' => $listingTypeSID,
			'listing_duration' => 28
		);
		SJB_ContractManager::deleteAllContractsByUserSID($userSID);
		$this->deleteJobG8Product($userGroupSID);
		$product = new SJB_Product($productInfo, 'mixed_product');
		$product->saveProduct($product);
		$contract = new SJB_Contract(array('product_sid' => $product->getSID()));
		$contract->setUserSID($userSID);
		$contract->saveInDB();
	}
	
	private function deleteJobG8Product($userGroupSID)
	{
		$products = SJB_ProductsManager::getProductsInfoByUserGroupSID($userGroupSID);
		foreach ($products as $product) {
			if (strpos(strtolower($product['name']), 'jobg8') !== false) {
				SJB_ProductsManager::deleteProductBySID($product['sid']);
			}
		}
	}
	
	private function createListingsToJobg8Table()
	{
		SJB_DB::queryExec('
			CREATE TABLE IF NOT EXISTS ?w (
				`sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`listing_sid` int(10) unsigned NOT NULL,
				`action` varchar(10) NOT NULL,
				`postingType` ENUM(\'traffic\',\'applications\') NOT NULL DEFAULT \'applications\',
				PRIMARY KEY (`sid`),
				KEY `listing_sid` (`listing_sid`),
				KEY `action` (`action`),
				KEY `postingType` (`postingType`)
			) DEFAULT CHARSET=utf8',
			$this->tableName
		);
	}

	private function createJobg8ListingsPropertiesTable()
	{
		SJB_DB::queryExec('
			CREATE TABLE IF NOT EXISTS ?w (
				`sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`listingSid` int(10) unsigned NOT NULL,
				`jobReference` varchar(100) DEFAULT NULL,
				`jobType` varchar(100) DEFAULT NULL,
				PRIMARY KEY (`sid`),
				KEY `listingSid` (`listingSid`),
				KEY `jobReference` (`jobReference`),
				KEY `jobType` (`jobType`)
			) DEFAULT CHARSET=utf8',
			self::JOBG8_LISTING_PROPERTIES_TABLE
		);
	}

	/**
	 * @param $listingSid
	 */
	public static function deleteJobProperties($listingSid)
	{
		SJB_DB::queryExec("DELETE FROM ?w WHERE `listingSid` = ?s LIMIT 1", JobG8::JOBG8_LISTING_PROPERTIES_TABLE, $listingSid);
	}
}