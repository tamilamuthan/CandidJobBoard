<?php

class SJB_ListingManager extends SJB_ObjectManager
{
    
    private static $systemProperties = array();
	
	public static function saveListing(&$listing, $listingSidsForCopy = array())
	{
		return SJB_ListingDBManager::saveListing($listing, $listingSidsForCopy);
	}

	public static function getListingsNumberByListingTypeSID($listing_type_sid)
	{
		return SJB_ListingDBManager::getListingsNumberByListingTypeSID($listing_type_sid);
	}

	public static function getListingsNumberByUserSID($user_sid)
	{
		return SJB_ListingDBManager::getListingsNumberByUserSID($user_sid);
	}

	public static function getAllListingSIDs()
	{
		return SJB_ListingDBManager::getAllListingSIDs();
	}

	public static function getListingInfoBySID($listing_sid)
	{
		$listing_info = SJB_ListingDBManager::getListingInfoBySID($listing_sid);
		if (empty($listing_info))
			return null;
		$listing_info['id'] = $listing_info['sid'];
		return $listing_info;
	}

	/**
	 * Returns Listing object by id 
	 *
	 * @param int $listing_sid
	 * @return SJB_Listing
	 */
	public static function getObjectBySID($listing_sid)
	{
		$listing_info = SJB_ListingManager::getListingInfoBySID($listing_sid);
		$listing = null;
		if (!is_null($listing_info)) {
			$listing = new SJB_Listing($listing_info, $listing_info['listing_type_sid']);
			$listing->setSID($listing_sid);
			$listing->setUserSID($listing_info['user_sid']);
			$productInfo = !empty($listing_info['product_info'])?unserialize($listing_info['product_info']):array();
			$listing->setProductInfo($productInfo);
		}
		return $listing;
	}

	public static function getActiveListingsByUserSID($user_sid, $count = false)
	{
		$active_listings_sid = SJB_ListingDBManager::getActiveListingsSIDByUserSID($user_sid);
		if ($count) {
			return count($active_listings_sid);
		}
		$active_listings = array();
		foreach ($active_listings_sid as $active_listing_sid)
			$active_listings[] = SJB_ListingManager::getObjectBySID($active_listing_sid);
		return $active_listings;
	}

	public static function getActiveListingNumberByUserSID($user_sid)
	{
		return SJB_ListingManager::getActiveListingsByUserSID($user_sid, true);
	}

	public static function getListingsByUserSID($user_sid)
	{
		$listings_sid = SJB_ListingDBManager::getListingsSIDByUserSID($user_sid);
		$listings = array();
		foreach ($listings_sid as $listing_sid)
			$listings[] = SJB_ListingManager::getObjectBySID($listing_sid);
		return $listings;
	}

	/**
	 * @param array|int $listingSids
	 * @param bool $updateBrowsePages
	 * @param bool $notification
	 * @param bool|string $date
	 * @return bool
	 */
	public static function activateListingBySID($listingSids, $updateBrowsePages = true, $notification = true, $date = false)
	{
		if (is_array($listingSids)) {
			$activatedListings = array();
			foreach ($listingSids as $listingSid) {
				if (self::_activateListingBySID($listingSid, $notification, $date)) {
					$activatedListings[] = $listingSid;
				}
			}
			
			if ($updateBrowsePages && !empty($activatedListings)) {
				SJB_BrowseDBManager::addListings($activatedListings);
			}
			return true;
		}
		else if (self::_activateListingBySID($listingSids, $notification, $date)) {
			if ($updateBrowsePages) {
				SJB_BrowseDBManager::addListings($listingSids);
			}
			return true;
		}
		
		return false;
	}

	private static function _activateListingBySID($listingSid, $notification = true, $date = false)
	{
		if (SJB_ListingDBManager::activateListingBySID($listingSid, $date)) {
			if (SJB_ListingManager::setListingExpirationDateBySid($listingSid, $date)) {
				SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));
				SJB_Event::dispatch('listingActivated', $listingSid);
				if ($notification) {
					$listing = self::getObjectBySID($listingSid);
					SJB_Notifications::sendUserListingActivatedLetter($listing, $listing->getUserSID());
				}
				return true;
			}
		}
		return false;
	}

	public static function setListingExpirationDateBySid($listing_sid, $date = false)
	{
		return SJB_ListingDBManager::setListingExpirationDateBySid($listing_sid, $date);
	}

	/**
	 * @param array|int $listingSids
	 */
	public static function deleteListingBySID($listingSids)
	{
		SJB_BrowseDBManager::deleteListings($listingSids);
		if (is_array($listingSids)) {
			foreach ($listingSids as $listingSid) {
				self::_deleteListingBySID($listingSid);
			}
		} else {
			self::_deleteListingBySID($listingSids);
		}
	}
	
	private static function _deleteListingBySID($listing_sid)
	{
		SJB_Event::dispatch('beforeListingDelete', $listing_sid);
		SJB_UploadFileManager::deleteUploadedFilesByListingSID($listing_sid);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));
		SJB_ListingDBManager::deleteListingBySID($listing_sid);
		SJB_Applications::removeByListing($listing_sid);
	}

	/**
	 * @param array|int $listingSids
	 * @param bool      $deleteRecordFromActivePeriod
	 * @return bool
	 */
	public static function deactivateListingBySID($listingSids, $deleteRecordFromActivePeriod = false)
	{
		SJB_BrowseDBManager::deleteListings($listingSids);
		
		if (is_array($listingSids)) {
			foreach ($listingSids as $listingSid) {
				self::_deactivateListingBySID($listingSid, $deleteRecordFromActivePeriod);
			}
			return true;
		}
		
		return self::_deactivateListingBySID($listingSids, $deleteRecordFromActivePeriod);
	}

	private static function _deactivateListingBySID($listingSid, $deleteRecordFromActivePeriod = false)
	{
		$result = SJB_ListingDBManager::deactivateListingBySID($listingSid, $deleteRecordFromActivePeriod);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));
		SJB_Event::dispatch('listingDeactivated', $listingSid);
		return $result;
	}
	
	public static function getPropertyByPropertyName($property_name, $listing_type_sid = 0)
	{
		$property_info = SJB_ListingFieldDBManager::getListingFieldInfoByID($property_name);
		if (empty($property_info)) {
			$listing_details = SJB_ListingDetails::getDetails($listing_type_sid);
			if (isset($listing_details[$property_name]))
				$property_info = $listing_details[$property_name];
			else
				return null;
		}

		return new SJB_ObjectProperty($property_info);
	}

	public static function propertyIsCommon($property_name)
	{
		$common_property = SJB_ListingManager::getPropertyByPropertyName($property_name);
		return !empty($common_property);
	}

	public static function propertyIsSystem($property_name)
	{
	    if (empty(self::$systemProperties)) {
	        self::$systemProperties = SJB_DB::query("SHOW COLUMNS FROM `listings`");
	    }
		foreach (self::$systemProperties as $property)
			if ($property['Field'] == $property_name)
				return true;
		return false;
	}

	public static function getAllListingPropertiesID($listing_type_id = null)
	{
		$common_properties = SJB_ListingFieldManager::getCommonListingFieldsInfo();
		$extra_properties  = array();
		if (!empty($listing_type_id)) {
			$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
			if (!empty($listing_type_sid))
				$extra_properties  = SJB_ListingFieldManager::getListingFieldsInfoByListingType($listing_type_sid);
		}
		return array(
			'system' => array('id', 'listing_type', 'username', 'active', 'keywords', 'featured', 'views', 'activation_date', 'expiration_date'),
			'common' => $common_properties,
			'extra'  => $extra_properties,
		); 
	}

	public static function getExpiredListingsSID()
	{
		return SJB_ListingDBManager::getExpiredListingsSID();
	}

	public static function getDeactivatedListingsSID()
	{
		return SJB_ListingDBManager::getDeactivatedListingsSID();
	}

        /**
         *
         * @param int $sid
         * @return int | boolean | null
         */
	public static function getIfListingHasExpiredBySID( $sid )
	{
		return SJB_ListingDBManager::getIfListingHasExpiredBySID( $sid );
	}

	public static function getUserSIDByListingSID($listing_sid)
	{
		return SJB_ListingDBManager::getUserSIDByListingSID($listing_sid);
	}

	/**
	 * @param SJB_Listing $listing
	 */
	public static function createTemplateStructureForListing($listing, $extraInfo = array())
	{
		$listing_info = parent::getObjectInfo($listing);

		if (SJB_MemoryCache::has('listingTypeInfo'. $listing->getListingTypeSID())) {
			$listing_type_info = SJB_MemoryCache::get('listingTypeInfo'. $listing->getListingTypeSID());
		}
		else {
			$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($listing->getListingTypeSID());
			SJB_MemoryCache::set('listingTypeInfo'. $listing->getListingTypeSID(), $listing_type_info);
		}
		foreach ($listing->getProperties() as $property) {
			if ($property->isComplex()) {
				$isPropertyEmpty = true;
				$properties = $property->type->complex->getProperties();
				$properties = is_array($properties)?$properties:array();
				foreach ($properties as $subProperty) {
					if (!empty($listing_info['user_defined'][$property->getID()][$subProperty->getID()]) && is_array($listing_info['user_defined'][$property->getID()][$subProperty->getID()])) {
						foreach ($listing_info['user_defined'][$property->getID()][$subProperty->getID()] as $subValue) {
							if (!empty($subValue)) {
								$isPropertyEmpty = false;
							}
						}
					}
				}
				if ($isPropertyEmpty) {
					$listing_info['user_defined'][$property->getID()] = '';
				}
			}
			if ($property->getType() == 'list') {
				$value = $property->getValue();
				$properties =  $property->type->property_info;
				$listValues = isset($properties['list_values'])?$properties['list_values']:array();
				foreach ($listValues as $listValue) {
					if ($listValue['id'] == $value) 
						$listing_info['user_defined'][$property->getID()] = $listValue['caption'];
				}
			} elseif ($property->getType() == 'multilist') {
				$value = $property->getValue();
				if (!is_array($property->getValue()))
					$value = explode(',', $property->getValue());
				$properties =  $property->type->property_info;
				$listValues = isset($properties['list_values'])?$properties['list_values']:array();
				$listing_info['user_defined'][$property->getID()] = array();
				foreach ($listValues as $listValue) {
					if (in_array($listValue['id'],$value)) 
						$listing_info['user_defined'][$property->getID()][$listValue['id']] = $listValue['caption'];
				}
			} elseif ($property->getType() == 'location' && is_array($listing_info['user_defined'][$property->getID()])) {
				foreach($property->type->fields as $locationField) {
					if (array_key_exists($locationField['id'], $listing_info['user_defined'][$property->getID()])) {
						$listValues = isset($locationField['list_values']) ? $locationField['list_values'] : array();
						$value = $property->getValue();
						$value = isset($value[$locationField['id']]) ? $value[$locationField['id']] : '';
						foreach ($listValues as $listValue) {
							if ($listValue['id'] == $value) {
								$listing_info['user_defined'][$property->getID()][$locationField['id']] = $listValue['caption'];
							}
						}
					}
				}
			}
		}

		$cache = SJB_Cache::getInstance();
		$cacheId = md5('SJB_UserManager::getObjectBySID' . $listing_info['system']['user_sid']);
		$user_info = array();
		if ($cache->test($cacheId)) {
			$user_info = $cache->load($cacheId);
		} else {
			$user = SJB_UserManager::getObjectBySID($listing_info['system']['user_sid']);
			$user_info	= !empty($user) ? SJB_UserManager::createTemplateStructureForUser($user) : null;
			$cache->save($user_info, $cacheId, array(SJB_Cache::TAG_USERS));
		}

		$productInfo = SJB_ProductsManager::createTemplateStructureForProduct($listing_info['system']['product_info']);

		$structure = array
        (
			'id'				        => $listing_info['system']['id'],
			'type'				        => array
											(
												'id' 		=> $listing_type_info['id'],
												'caption' 	=> $listing_type_info['name']
											),
			'user'				        => $user_info,
			'activation_date'	        => $listing_info['system']['activation_date'],
			'expiration_date'	        => $listing_info['system']['expiration_date'],
			'featured'			        => $listing_info['system']['featured'],
			'views'				        => $listing_info['system']['views'],
			'active'			        => $listing_info['system']['active'],
			'product'			        => $productInfo,
			'contract_id'               => $listing_info['system']['contract_id'],
			'external_id'               => $listing_info['system']['external_id'],
        );

		if (SJB_Settings::getSettingByName('jobg8Installed') && SJB_PluginManager::isPluginActive('JobG8IntegrationPlugin')) {
			$structure['jobType'] = JobG8::getJobProperty($listing_info['system']['id'], 'jobType');
		}

        $structure['METADATA'] = array
		( 
			'activation_date'	=> array('type' => 'date'), 
			'expiration_date'	=> array('type' => 'date'), 
			'views'				=> array('type' => 'integer'), 
		);

		$structure = array_merge($structure, $listing_info['user_defined']); 
		$structure['METADATA'] = array_merge($structure['METADATA'], parent::getObjectMetaData($listing)); 
		
		$listing_user_meta_data = array();
		if (isset($user_info['METADATA'])) {
			$listing_user_meta_data = $user_info['METADATA'];
			unset($user_info['METADATA']);
		}
		
		$listing_product_meta_data = array();
		if (isset($productInfo['METADATA'])) {
			$listing_product_meta_data = $productInfo['METADATA'];
			unset($productInfo['METADATA']);
		}
		
		$listing_type_meta_data = array('caption' => array('type' => 'string', 'propertyID' => 'listing_type'));
		
		$structure['METADATA'] = array_merge($structure['METADATA'], array ('user' 		=> $listing_user_meta_data,
																			'product' 	=> $listing_product_meta_data,
																			'type' 		=> $listing_type_meta_data));

        return array_merge($structure, $listing_info['user_defined']);
	}
	
	public static function createMetadataForListing($listing, $user)
	{
		$structure['METADATA'] = array 
		( 
			'activation_date'	=> array('type' => 'date'), 
			'expiration_date'	=> array('type' => 'date'), 
			'views'				=> array('type' => 'integer'), 
		);
		$structure['METADATA'] = array_merge($structure['METADATA'], parent::getObjectMetaData($listing)); 
		$listing_user_meta_data = array(
			'group' => array(
				'caption' => array('type' => 'string', 'propertyID' => 'caption'),
			),
			'registration_date' => array('type' => 'date'),
		);
		$listing_user_meta_data = array_merge($listing_user_meta_data, parent::getObjectMetaData($user)); 
	
		$listing_product_meta_data = array(
    			'caption'			=> array('type' => 'string', 'propertyID' => 'caption'),
    			'description'		=> array('type' => 'text', 'propertyID' => 'detailed_description'),
		);
		$listing_type_meta_data = array('caption' => array('type' => 'string', 'propertyID' => 'listing_type'));
		$structure['METADATA'] = array_merge($structure['METADATA'], array ('user' 		=> $listing_user_meta_data,
																			'product' 	=> $listing_product_meta_data,
																			'type' 		=> $listing_type_meta_data));
		return $structure['METADATA'];
	}

	public static function getLastListings($number_of_listings, $listing_type)
	{
		$sqlAccess = ' AND l.`access_type` = "everyone"';
		$listings_info = SJB_DB::query("SELECT l.*, lt.id FROM listings as l
				LEFT JOIN listing_types as lt ON (lt.sid = l.listing_type_sid)
				WHERE lt.id = ?s AND l.active = 1 $sqlAccess ORDER BY  l.activation_date DESC LIMIT 0, ?n", $listing_type, $number_of_listings);
		
		$listings = array();
		foreach ($listings_info as $listing_info) {
			$listing = SJB_ListingManager::getObjectBySID($listing_info['sid']);
			$listings[] = $listing;
		}
		
		return $listings;
	}

	public static function filterListingSIDByActiveAndType($found_listings_sids, $listing_type_sid)
	{
		$sids_string = join("', '", $found_listings_sids);
		$and_listing_type_sid = '';
		
		if (!empty($listing_type_sid))
			$and_listing_type_sid = ' AND `listing_type_sid`=?n';
		
		$sids = SJB_DB::query("SELECT `sid` FROM `listings` WHERE `sid` IN ('?w') AND `active`=1" . $and_listing_type_sid, $sids_string, $listing_type_sid);
		$result_sids = array();
		foreach ($sids as $sid)
			$result_sids[] = $sid['sid'];

		return $result_sids;
	}

	public static function incrementViewsCounterForListing($listingId)
	{
		$listingViews = SJB_DB::query('SELECT `views` FROM `listings` WHERE `sid` = ?n limit 1', $listingId);
		$viewKey = 'viewed_listing_' . $listingId;
		if (empty($listingViews) || SJB_Session::getValue($viewKey)) {
			return false;
		}
		SJB_Session::setValue($viewKey, true);
		return SJB_DB::query('UPDATE `listings` SET `views` = `views` + 1 WHERE `sid` = ?n limit 1', $listingId);
	}

	public static function getListingSIDByID($id)
	{
		return $id;
	}

	public static function makeCheckoutedBySID($listing_sid)
	{
		return SJB_DB::query("UPDATE `listings` SET `checkouted` = 1 WHERE `sid` = ?n", $listing_sid);
	}

	public static function unmakeCheckoutedBySID($listing_sid)
	{
		return SJB_DB::query("UPDATE `listings` SET `checkouted` = 0 WHERE `sid` = ?n", $listing_sid);
	}

	public static function makeFeaturedBySID($listing_sid)
	{
		return SJB_DB::query("UPDATE listings SET featured = 1 WHERE sid = ?n", $listing_sid);
	}

	public static function unmakeFeaturedBySID($listing_sid)
	{
		return SJB_DB::query("UPDATE listings SET featured = 0 WHERE sid = ?n", $listing_sid);
	}

	/**
	 * Uploaded resumes and jobs statistics
	 * @return array
	 */
	public static function getListingsInfo()
	{
		$res = array();

		// условие запроса сформируем в зависимости от требуемого периода
		$periods = array(
			'Today' => '`l`.`activation_date` >= CURDATE()',
			'Last 7 days' => '`l`.`activation_date` >= date_sub(curdate(), interval 7 day)',
			'Last 30 days' => '`l`.`activation_date` >= date_sub(curdate(), interval 30 day)',
			'Total' => '1=1',
		);
		$listingTypes = SJB_ListingTypeManager::createTemplateStructureForListingTypes();

		// условие в запрос будем подставлять заранее заготовленное из массива
		// nwy: разбил подсчет общего количества и подсчет активных листингов на 2 запроса
		// так быстрее при большом количестве листингов
		foreach ($listingTypes as $listingType) {
			foreach ($periods as $key => $value) {
				$res[$listingType["id"]]["periods"][$key] = SJB_DB::queryValue("
					select count(*)
					from listings l
					where {$value} and l.listing_type_sid = {$listingType["sid"]} and `l`.`preview` = 0 and `l`.`active` = 1");
			}
		}
		return $res;
	}
	
	public static function copyFilesAndPicturesFromListing($srcListingSid, $dstListingSid)
	{
		if ($srcListingSid == $dstListingSid) {
			return;
		}
		$listing = SJB_ListingManager::getObjectBySID($srcListingSid);
		if ($listing) {
			foreach ($listing->getProperties() as $listingProperty) {
				if (in_array($listingProperty->getType(), array('file', 'picture'))) {
					self::changeFilesFieldValue($dstListingSid, $listingProperty);
				}
			}
		}
	}

	private static function changeFilesFieldValue($dstListingSid, $listingProperty)
	{
		$value = $listingProperty->getValue();

		$uploadedFileId   = SJB_Array::get($value, 'file_id');
		$uploadedFileInfo = SJB_UploadFileManager::getUploadedFileInfo($uploadedFileId);
		if ($uploadedFileInfo) {
			$uploadFileManager = new SJB_UploadFileManager();
			$newUploadedFileId = $listingProperty->getID() . '_' . $dstListingSid;
			$uploadFileManager->setFileGroup($listingProperty->getType() == 'picture' ? 'pictures' : 'files');
			$uploadFileManager->copyFile($uploadedFileInfo, $newUploadedFileId);
			$listingProperty->setValue($newUploadedFileId);
		}
	}

	public static function isListingAccessableByUser($listingId, $userId)
	{
		$listingRequest  = SJB_DB::query("SELECT `access_list`, `access_type`, `user_sid` FROM `listings` WHERE `sid` = ?n", $listingId);
		$accessType      = '';
		$listingOwnerSid = '';
		$accessList = '';
		if (!empty($listingRequest)) {
			$accessType      = $listingRequest[0]['access_type'];
			$listingOwnerSid = $listingRequest[0]['user_sid'];
			$accessList = $listingRequest[0]['access_list'];
		}
		unset($listingRequest);

		if ($listingOwnerSid == $userId) {
			return true;
		}
		
		$access = false;
		switch ($accessType) {
			case 'everyone': 
				$access = true;
				break;
			case 'no_one':
				$access = in_array($userId, explode(',', $accessList));
				if (!$access) {
					$result = SJB_DB::query('SELECT `id` FROM `applications` WHERE `resume` = ?n AND `listing_id` IN (select sid from listings where user_sid = ?n) LIMIT 1', $listingId, $userId);
					$access = !empty($result);
				}
				break;
		}

		return $access;
	}


	public static function setListingAccessibleToUser($listing_id, $user_id)
	{
		$accessData = SJB_DB::query("SELECT `access_list` FROM `listings` WHERE `sid` = ?n", $listing_id);
		$accessData = array_pop($accessData);
		$accessList = '';
		if (empty($accessData['access_list'])) {
			$accessList = $user_id;
		} else {
			// employer id duplication possible
			$accessList = $accessData['access_list'] . ',' . $user_id;
		}
		SJB_DB::query('UPDATE `listings` SET `access_list` = ?s WHERE `sid` = ?n', $accessList, $listing_id);
	}
	
	public static function getCountListingsByContractID($contract_id)
	{
		return SJB_DB::queryValue("SELECT count(*) FROM `listings` WHERE `contract_id` = ?n", $contract_id);
	}
	
	public static function updateKeywords($keywords, $listingSID)
	{
		return SJB_DB::query("UPDATE `listings` SET `keywords` = ?s WHERE `sid`=?n", $keywords, $listingSID);
	}

	/**
	 * Checks if listng with specified external_id exists
	 *
	 * @param string $ext_id
	 * @return boolean
	 */
	public static function isListingExistsByExternalId($ext_id)
	{
		$is_listing_exist = false;
		if (!empty($ext_id)) {
			$is_listing_exist = SJB_DB::query("SELECT `external_id` FROM `listings` WHERE `external_id` = ?s", $ext_id);
			$is_listing_exist = array_pop($is_listing_exist);
		}
		return $is_listing_exist;
	}
	
	/**
	 * Gets listing sid with specified external_id
	 *
	 * @param string $externalId
	 * @return integer|null
	 */
	public static function getListingSidByExternalId($externalId)
	{
		$listingSid = null;
		if (!empty($externalId)) {
			$result = SJB_DB::query("SELECT `sid` FROM `listings` WHERE `external_id` = ?s", $externalId);
			if (!empty($result)) {
				$listingSid = $result[0]['sid'];
			}
		}
		return $listingSid;
	}

	/**
	 * this function is used to receive some listing SID,
	 * for Display Listing FIelds Builder.
	 * 
	 * @param int $listing_type_id
	 * @return int
	 */
	public static function getListingIDByListingTypeID($listing_type_id)
	{
		$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
		return SJB_DB::queryValue('SELECT `sid` FROM `listings` WHERE `listing_type_sid` = ?n AND `active` = 1 LIMIT 1', $listing_type_sid);
	}
	
	public static function getLastAddedListingByUserSID($userSID)
	{
		$activeListings = SJB_ListingDBManager::getActiveListingsNumberByUserSID($userSID);
		$where = '';
		if ($activeListings)
			$where = " AND `active`=1 ";
		$sid = SJB_DB::queryValue("SELECT `sid` FROM `listings` WHERE `user_sid` = ?n {$where} ORDER BY `activation_date` DESC LIMIT 1",$userSID);
		if (!empty($sid))
			return self::getObjectBySID($sid);
		return false;
	}

	public static function isListingExists($listingId)
	{
		return count(SJB_DB::query('select `sid` from `listings` where `sid` = ?n limit 1', $listingId)) > 0;
	}
	
	public static function hasListingProduct($listing_sid)
	{
		$result = SJB_DB::queryValue("SELECT `product_info` FROM `listings` WHERE `sid`=?n", $listing_sid);
		if ($result)
			return $result;
		return false;
	}
	
	public static function insertProduct($listing_sid, $productInfo)
	{
		$productInfo = serialize($productInfo);
		return SJB_DB::query("UPDATE `listings` SET `product_info` = ?s WHERE `sid` =?n", $productInfo, $listing_sid);
	}

	public static function deletePreviewListingsByUserSID($userSID) 
	{
		$previewListings = SJB_ListingDBManager::getAllPreviewListingsByUserSID($userSID);
		if ($previewListings) {
			foreach ($previewListings as $listing)
				self::deleteListingBySID($listing['sid']);
		}
	}
	
	public static function unFeaturedListings()
	{
		SJB_DB::query("UPDATE  `listings` SET `featured` = 0, `featured_expiration` = NULL WHERE `featured_expiration` < NOW() AND `active` = 1");
	}
	
	public static function canCurrentUserAddListing(& $error, $listingTypeId = false)
	{
		$acl = SJB_Acl::getInstance();

		if (SJB_UserManager::isUserLoggedIn()) {
			$current_user = SJB_UserManager::getCurrentUser();
			if ($current_user->hasContract()) {
				$contracts_id = $current_user->getContractID();
				$contractsSIDs = $contracts_id ? implode(',', $contracts_id) : 0;
				$resultContractInfo = SJB_DB::query("SELECT `id`, `product_sid`, `expired_date`, `number_of_postings` FROM `contracts` WHERE `id` in ({$contractsSIDs}) ORDER BY `expired_date` DESC");
				$PlanAcces = count($resultContractInfo) > 0 ? true : false;
				if ($PlanAcces && $acl->isAllowed('post_' . $listingTypeId)) {
					$productsInfo = array();
					$is_contract = false;
					foreach ($resultContractInfo as $contractInfo) {
						if ($acl->isAllowed('post_' . $listingTypeId, $contractInfo['id'], 'contract')) {
							$permissionParam = $acl->getPermissionParams('post_' . $listingTypeId, $contractInfo['id'], 'contract');
							if (empty($permissionParam) || $acl->getPermissionParams('post_' . $listingTypeId, $contractInfo['id'], 'contract') > $contractInfo['number_of_postings']) {
								$product = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
								$productsInfo[$contractInfo['id']]['product_name'] = $product['name'];
								$productsInfo[$contractInfo['id']]['expired_date'] = $contractInfo['expired_date'];
								$productsInfo[$contractInfo['id']]['contract_id'] = $contractInfo['id'];
							}
						}
						$is_contract = true;
					}

					if ($is_contract && count($productsInfo) > 0)
						return $productsInfo;
					else
						$error = 'LISTINGS_NUMBER_LIMIT_EXCEEDED';
				}
				else
					$error = 'DO_NOT_MATCH_POST_THIS_TYPE_LISTING';
			}
			else
				$error = 'NO_CONTRACT';
		}
		else {
			$error = 'NOT_LOGGED_IN';
		}
		return false;
	}

	public static function getPropertyByParentID($parentID, $fieldID, $listing_type_sid = 0) 
	{
		$parentSID = SJB_ListingFieldManager::getListingFieldSIDByID($parentID);
		$fields = SJB_ListingFieldManager::getListingFieldsInfoByParentSID($parentSID);
		$fieldSID = null;
		foreach ($fields as $field) {
			if ($field['id'] == $fieldID)
				$fieldSID = $field['sid'];
		}
		if ($fieldSID) {
			$property_info = SJB_ListingFieldDBManager::getListingFieldInfoBySID($fieldSID);
			$property_info['id'] = $parentID."_".$property_info['id'];
			$fieldID = $property_info['id'];
			return new SJB_ObjectProperty($property_info);
		}
		return null;
	}

	/**
	 * @param int $userSID
	 * @param int $productSID
	 * @param int $contractID
	 * @param int $listingNumber
	 */
	public static function activateListingsAfterPaid($userSID, $productSID, $contractID, $listingNumber)
	{
		$limit = '';
		if ($listingNumber != null) {
			$limit = 'LIMIT 0,' . $listingNumber;
		}
		$serializedProductSID = SJB_ProductsManager::generateQueryBySID($productSID);
		$listingsSIDsToProceed = SJB_DB::query("SELECT `sid` FROM `listings` WHERE `checkouted` = 0 AND `contract_id` = 0 AND `user_sid` = ?n AND `product_info` REGEXP '({$serializedProductSID})' ORDER BY `sid` DESC {$limit}", $userSID);
		if (!empty($listingsSIDsToProceed)) {
			foreach ($listingsSIDsToProceed as $listingSIDToProceed) {
				SJB_DB::query('UPDATE `listings` SET `contract_id` = ?n, `checkouted` = 1 WHERE `sid` = ?n', $contractID, $listingSIDToProceed['sid']);
				self::activateListingBySID($listingSIDToProceed['sid']);
			}
			SJB_ProductsManager::incrementPostingsNumber($productSID, count($listingsSIDsToProceed));
			SJB_ContractSql::updatePostingsNumber($contractID, count($listingsSIDsToProceed));
		}
		return $listingsSIDsToProceed;
	}

	/**
	 * @param $listingSID
	 * @return string
	 */
	public static function getListingUrlBySID($listingSID)
	{
		return SJB_System::getSystemSettings('USER_SITE_URL') . SJB_TemplateProcessor::listing_url(
			SJB_ListingManager::getListingInfoBySID($listingSID)
		);
	}

	private static function getListingDescriptionPreparedForSharer($listingInfo)
	{
		$description = strip_tags(trim($listingInfo['JobDescription']));
		$description = html_entity_decode($description, ENT_COMPAT, 'UTF-8');
		if (!empty($description)) {
			return htmlspecialchars(mb_substr($description, 0, 300, 'UTF-8'));
		}
		
		return '';
	}

	/**
	 * @param $listingSID
	 */
	public static function setMetaOpenGraph($listingSID)
	{
		if (!empty($listingSID)) {
			$listing           = SJB_ListingManager::getObjectBySID($listingSID);
			$listingStructure  = SJB_ListingManager::createTemplateStructureForListing($listing);
			$siteUrl           = SJB_System::getSystemSettings("SITE_URL");
			$locationFormatted = !empty($listingStructure['GooglePlace']) ? " (" . $listingStructure['GooglePlace'] . ")" : '';
			$title             = htmlspecialchars(strip_tags(trim($listingStructure['Title']))) . $locationFormatted;
			$logoImage         = !empty($listingStructure['user']['Logo']['file_url']) ? $listingStructure['user']['Logo']['file_url'] : '';
			$description       = self::getListingDescriptionPreparedForSharer($listingStructure);
			$listingUrl        = SJB_ListingManager::getListingUrlBySID($listingSID);

			$openGraphMetaBlock = "<meta property=\"og:type\" content=\"website\" />\n\t" .
				"<meta property=\"og:url\" content=\"{$listingUrl}\" />\n\t" .
				"<meta property=\"og:title\" content=\"{$title}\" />\n\t" .
				"<meta property=\"og:description\" content=\"{$description}\" />\n\t" .
				"<meta property=\"og:site_name\" content=\"{$siteUrl}\" />\n\t" .
				"<meta property=\"og:image\" content=\"{$logoImage}\" />";

			$head = SJB_System::getPageHead();
			SJB_System::setPageHead($head . ' ' .  $openGraphMetaBlock);
		}
	}

	/**
	 * @param $listingSID
	 * @return int
	 */
	public static function isListingCheckOuted($listingSID)
	{
		return SJB_DB::queryValue("SELECT `checkouted` FROM `listings` WHERE `sid` = ?n", $listingSID);

	}
}
