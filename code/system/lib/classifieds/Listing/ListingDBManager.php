<?php

class SJB_ListingDBManager extends SJB_ObjectDBManager
{
	/**
	 * @param  $listing SJB_Listing
	 * @param array $listingSidsForCopy
	 * @return array|bool
	 */
	public static function saveListing($listing, $listingSidsForCopy = array())
	{
		\SJB\Location\Helper::fixLocation($listing);
		$listing_type_sid = $listing->getListingTypeSID();
		if (!is_null($listing_type_sid)) {
			$expirationDate = $listing->getPropertyValue('expiration_date');
			if ($expirationDate && !$listing->getSID() && !$listing->getProperty('preview')) {
				$i18n = SJB_I18N::getInstance();
				$currentLanguage = $i18n->getLanguageData($i18n->getCurrentLanguage());
				if ($expirationDate == strftime($currentLanguage['date_format'], strtotime('+ ' . $listing->product_info['listing_duration'] . ' day'))) {
					$listing->setPropertyValue('expiration_date', '');
				}
			}
			parent::saveObject('listings', $listing, false, $listingSidsForCopy);
			SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));

			if (!SJB_ListingManager::hasListingProduct($listing->getSID()))
				SJB_ListingManager::insertProduct($listing->getSID(), $listing->getProductInfo());
			
			return SJB_DB::query('UPDATE `?w` SET `listing_type_sid` = ?n, `user_sid` = ?n, `keywords` = ?s, ' .
								 '`activation_date` = ' . ($listing->getActivationDate() == null ? 'NOW()' : "'{$listing->getActivationDate()}'") . ' WHERE `sid` = ?n',
						'listings', $listing_type_sid, $listing->getUserSID(), $listing->getKeywords(), $listing->getSID());
		}
		return false;
	}
	
	public static function getListingsNumberByListingTypeSID($listing_type_sid)
	{
		return SJB_DB::queryValue('SELECT COUNT(*) FROM `?w` WHERE `listing_type_sid`=?n', 'listings', $listing_type_sid);
	}

	public static function getListingsNumberByUserSID($user_sid)
	{
		$userContractsSIDs = SJB_ContractManager::getAllContractsSIDsByUserSID($user_sid);
		$userContractsSIDs = $userContractsSIDs ? implode(',', $userContractsSIDs) : 0;
		return SJB_DB::queryValue("SELECT COUNT(*) FROM `listings` WHERE `user_sid` = ?n AND `contract_id` in ({$userContractsSIDs})", $user_sid);
	}

	public static function getActiveListingsNumberByUserSID($user_sid)
	{
		$typeWhere = '';
		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		foreach ($listingTypes as $listingType) {
		    if (!empty($typeWhere))
		        $typeWhere .= ' OR ';
			$typeWhere .= "(`listing_type_sid` = {$listingType['sid']})";
		}
		return SJB_DB::queryValue("SELECT COUNT(*) FROM `listings` WHERE `active` = 1 AND `user_sid` = ?n AND ({$typeWhere})", $user_sid);
	}

	public static function getActiveJobsNumberForUsers($usersSID, $listingType)
	{
		$results = SJB_DB::query("SELECT COUNT(*) as `count`, `user_sid` FROM `listings` WHERE `user_sid` in (?l) AND `listing_type_sid` = {$listingType['sid']} AND `active` = 1 GROUP BY `user_sid`", $usersSID);
		$users = array();
		foreach ($results as $result) {
			$users[$result['user_sid']] = $result['count'];
		}
		return $users;
	}

	public static function getAllListingSIDs()
	{
		return SJB_DB::query('SELECT `sid`, `sid` as `id` FROM `listings`');
	}

	public static function getListingInfoBySID($listing_sid)
	{
    	return parent::getObjectInfo('listings', $listing_sid);
	}

	public static function getActiveListingsSIDByUserSID($user_sid)
	{
		$listings_info = SJB_DB::query('SELECT * FROM `listings` WHERE `active` = 1 AND `user_sid` = ?n', $user_sid);
		$listings_sid = array();
		foreach ($listings_info as $listing_info)
			$listings_sid[] = $listing_info['sid'];
		return $listings_sid;
	}
	
	public static function getListingsSIDByUserSID($userSid, $limit = false)
	{
		$limit = $limit ? ' LIMIT ' . $limit : '';
		
		$query = "SELECT `sid` FROM `listings` WHERE `user_sid` = {$userSid} " . $limit;
		$cache = SJB_Cache::getInstance();
		if ($cache->test(md5($query))) {
			$listings_info = $cache->load(md5($query));
		} else {
			$listings_info = SJB_DB::query('SELECT `sid` FROM `listings` WHERE `user_sid` = ?n ' . $limit, $userSid);
			$cache->save($listings_info, md5($query), array(SJB_Cache::TAG_LISTINGS));
		}
		$listings_sid = array();
		foreach ($listings_info as $listing_info)
			$listings_sid[] = $listing_info['sid'];
		return $listings_sid;
	}

	public static function activateListingBySID($listing_sid, $date = false)
	{
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listing_sid);
		if ($listingInfo['active']) {
			return false;
		}
		
		$extraInfo = $listingInfo['product_info'];
		if ($extraInfo) {
			$extraInfo = unserialize($extraInfo);
			if ($extraInfo['featured']) {
			}
		}

		$activation_date = empty($date) ? 'NOW()' : "'" . $date . "'";
		if (SJB_DB::query("UPDATE `listings` SET `active` = 1, `activation_date` = {$activation_date} WHERE `sid` = ?n", $listing_sid)) {
			$numberOfDays = SJB_DB::query('SELECT `number_of_days` FROM `listings_active_period` WHERE `listing_sid` = ?n', $listing_sid);
			$numberOfDays = $numberOfDays ? array_pop($numberOfDays) : 0;
			$numberOfDays = $numberOfDays['number_of_days'];
			$sql = array();
			if ($numberOfDays) {
				$sql[] = " `expiration_date` = {$activation_date} + INTERVAL {$numberOfDays} DAY ";
			} else {
				if ($extraInfo['featured']) {
					$sql[] = " `featured` = 1 ";
				}
			}
			$sql = implode(', ', $sql);
			if ($sql) {
				SJB_DB::query("UPDATE `listings` SET {$sql} WHERE `sid` = ?n", $listing_sid);
			}
			return true;
		}
		return false;
	}

	public static function setListingExpirationDateBySid($listing_sid, $date = false)
	{
		$product_info = SJB_DB::queryValue('SELECT `product_info` FROM `listings` WHERE `sid` = ?n', $listing_sid);
		$activation_date = empty($date) ? 'NOW()' : "'" . $date . "'";
		if (!empty($product_info)) {
			$product_info = unserialize($product_info);
		}
		if (empty($product_info['listing_duration'])) {
			$product_info['listing_duration'] = 365;
		}
		if (!empty($product_info['listing_duration'])) {
			SJB_DB::queryExec('
				UPDATE `listings`
				SET `expiration_date` = ' . $activation_date . ' + INTERVAL ?n DAY
				WHERE `sid` = ?n 
				AND (`expiration_date` is NULL OR `expiration_date` < NOW() OR `expiration_date` > (NOW() + INTERVAL ?n DAY))',
				$product_info['listing_duration'], $listing_sid, $product_info['listing_duration']
			);
		}
		return true;
	}

	public static function setListingExpirationDate($listing_sid, $date = false)
	{
		SJB_DB::queryExec('
            UPDATE `listings`
            SET `expiration_date` = ' . (empty($date) ? 'NOW()' : "'" . $date . "'") . '
            WHERE `sid` = ?n', $listing_sid);
		return true;
	}

	public static function deleteListingBySID($listing_sid)
	{
		parent::deleteObjectInfoFromDB('listings', $listing_sid);
	}

	public static function deactivateListingBySID($listingSID, $deleteRecordFromActivePeriod = false)
	{
		if (SJB_DB::query('UPDATE `listings` SET `active` = 0 WHERE `sid` = ?n', $listingSID)) {
			if ($deleteRecordFromActivePeriod) {
				SJB_DB::query('DELETE FROM  `listings_active_period` WHERE `listing_sid`=?n', $listingSID);
				SJB_DB::query('UPDATE `listings` SET `featured` = 0 WHERE `sid`=?n', $listingSID);
			} else {
				$numberOfDays = SJB_DB::query('SELECT `number_of_days` FROM `listings_active_period` WHERE `listing_sid` = ?n', $listingSID);
				$expirationDate = SJB_DB::query('SELECT `expiration_date`, `featured_expiration` FROM `listings` WHERE `sid` = ?n', $listingSID);
				$expirationDate = array_pop($expirationDate);
				$featuredExpiration = !empty($expirationDate['featured_expiration']) ? $expirationDate['featured_expiration'] : date('Y-m-d');
				$expirationDate = $expirationDate['expiration_date'];
				if ($expirationDate) {
					if ($numberOfDays) {
						SJB_DB::query('UPDATE `listings_active_period` SET `number_of_days` = DATEDIFF(?s, NOW()) WHERE `listing_sid` = ?n', $expirationDate, $featuredExpiration, $listingSID);
					} else {
						SJB_DB::query('INSERT INTO `listings_active_period` (`listing_sid`, `number_of_days`) VALUES (?n, DATEDIFF(?s, NOW()))', $listingSID, $expirationDate);
					}
				}
			}
			return true;
		}
		return false;
	}
	
	public static function getExpiredListingsSID()
	{
		$listings = SJB_DB::query('SELECT `sid` FROM `listings` WHERE `expiration_date` < NOW() AND `active` = 1');
		if (empty($listings))
			return array();
		$listings_sid = array();
		foreach ($listings as $listing)
			$listings_sid[] = $listing['sid'];
		return $listings_sid;
	}

	public static function getDeactivatedListingsSID()
	{
		$period = SJB_Settings::getSettingByName('period_delete_expired_listings');
		$listings = SJB_DB::query('SELECT `l`.`sid` FROM `listings` `l`
								   LEFT JOIN `listings_active_period` `lap` ON `lap`.`listing_sid` = `l`.`sid`
								   WHERE `l`.`expiration_date` < NOW() - INTERVAL ?n DAY AND `l`.`active` = 0
								   AND (`lap`.`number_of_days` is NULL OR `lap`.`number_of_days` = 0)', $period);
		if (empty($listings))
			return array();
		$listings_sid = array();
		foreach ($listings as $listing)
			$listings_sid[] = $listing['sid'];
		return $listings_sid;
	}

	public static function getIfListingHasExpiredBySID($listingSID)
	{
		$listing = SJB_DB::query('SELECT `sid` FROM `listings` WHERE `expiration_date` < NOW() AND `listings`.`sid` = ?n LIMIT 1', $listingSID );
		if (!empty($listing))
			return true;
		return false;
	}

	public static function getUserSIDByListingSID($listing_sid)
	{
		return SJB_DB::queryValue('SELECT `user_sid` FROM `listings` WHERE `sid` = ?n', $listing_sid);
	}

	public static function getAllPreviewListingsByUserSID($userSID)
	{
		return SJB_DB::query("SELECT * FROM `listings` WHERE `user_sid` = ?n AND `preview` = 1", $userSID);
	}

	public static function getNumberOfCheckoutedListingsByProductSID($productSID, $currentUserID)
	{
		$serializedProductSID = SJB_ProductsManager::generateQueryBySID($productSID);
		return SJB_DB::queryValue("SELECT COUNT(`sid`) FROM `listings` WHERE `checkouted` = 0 AND `contract_id` = 0 AND `user_sid` = ?n AND `product_info` REGEXP '({$serializedProductSID})'", $currentUserID);
	}

	/**
	 * @param string $permissionName
	 * @param int    $listingSID
	 * @return string
	 */
	public static function getPermissionByListingSid($permissionName, $listingSID)
	{
		$listingInfo        = SJB_ListingManager::getListingInfoBySID($listingSID);
		$productInfo        = unserialize($listingInfo['product_info']);
		$productPermissions = SJB_Acl::getInstance()->getPermissions('product', $productInfo['product_sid']);
		return isset($productPermissions[$permissionName]) ? $productPermissions[$permissionName]['value'] : 'inherit';
	}
}
