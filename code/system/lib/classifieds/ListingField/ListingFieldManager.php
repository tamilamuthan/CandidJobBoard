<?php

class SJB_ListingFieldManager
{
	public static function getCommonListingFieldsInfo($pageID = 0)
	{
		return SJB_ListingFieldManager::getListingFieldsInfoByListingType(0, $pageID);
	}

	public static function saveListingField($listing_field, $pages = array())
	{
		$result = SJB_ListingFieldDBManager::saveListingField($listing_field, $pages);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return $result;
	}
	
	public static function getFieldInfoBySID($listing_field_sid)
	{
		$cache = SJB_Cache::getInstance();
		$id = md5('SJB_ListingFieldDBManager::getListingFieldInfoBySID' . $listing_field_sid);
		if ($cache->test($id))
			return $cache->load($id);
		$listingFieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoBySID($listing_field_sid);
		$listingFieldInfo = self::setAddParameterToDefaultValueIfExists($listingFieldInfo);
		$cache->save($listingFieldInfo, $id, array(SJB_Cache::TAG_FIELDS));
		return $listingFieldInfo;
	}

	protected static function setAddParameterToDefaultValueIfExists($listingFieldInfo)
	{
		if (!empty($listingFieldInfo['add_parameter']))
			$listingFieldInfo['default_value'] = array(
				'value' => $listingFieldInfo['default_value'],
				'add_parameter' => $listingFieldInfo['add_parameter']);
		return $listingFieldInfo;
	}

	public static function deleteListingFieldBySID($listing_field_sid)
	{
        $field_info = SJB_ListingFieldManager::getFieldBySID($listing_field_sid);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_ListingFieldDBManager::deleteListingFieldBySID($listing_field_sid) &&
                SJB_ListingFieldDBManager::deleteFieldProperties($field_info->getPropertyValue('id'), $field_info->getPropertyValue('listing_type_sid')) && SJB_PostingPagesManager::removeFieldFromPage($field_info->sid, $field_info->listing_type_sid);
	}

	public static function getListingFieldsInfoByListingType($listing_type_sid, $pageID = 0)
	{
		if (isset($GLOBALS["ListingFieldManagerCache"][$listing_type_sid][$pageID]))
			return $GLOBALS["ListingFieldManagerCache"][$listing_type_sid][$pageID];
			
		$fields_info = SJB_ListingFieldDBManager::getListingFieldsInfoByListingType($listing_type_sid, $pageID);
		$GLOBALS["ListingFieldManagerCache"][$listing_type_sid][$pageID] = $fields_info;
		return $fields_info;
	}

	public static function getFieldBySID($listing_field_sid)
	{
		$listing_field_info = SJB_ListingFieldDBManager::getListingFieldInfoBySID($listing_field_sid);
		
		if (empty($listing_field_info)) {
			return null;
		}
		else {
			$listing_field = new SJB_ListingField($listing_field_info);
			$listing_field->setListingTypeSID($listing_field_info['listing_type_sid']);
			$listing_field->setSID($listing_field_sid);
			return $listing_field;
		}
	}

	public static function getListingFieldIDBySID($listing_field_sid)
	{
		$listing_field_info = SJB_ListingFieldManager::getFieldInfoBySID($listing_field_sid);
		if (empty($listing_field_info))
			return null;
		return $listing_field_info['id'];
	}

	public static function getListingFieldSIDByID($listing_field_id)
	{
		$listing_field_info = SJB_ListingFieldDBManager::getListingFieldInfoByID($listing_field_id);

		if (empty($listing_field_info))
			return null;
		return $listing_field_info['sid'];
	}

	public static function changeListingPropertyIDs($new_listing_field_id, $old_listing_field_id)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_DB::query("UPDATE `listings_properties` SET `id` = ?s WHERE `id` = ?s", $new_listing_field_id, $old_listing_field_id);
	}

	public static function moveUpFieldBySID($field_sid)
	{
		return SJB_ListingFieldDBManager::moveUpFieldBySID($field_sid);
	}

	public static function moveDownFieldBySID($field_sid)
	{
		return SJB_ListingFieldDBManager::moveDownFieldBySID($field_sid);
	}

	public static function getFieldsInfoByType($type)
	{
		$type_fields = SJB_DB::query("SELECT * FROM `listing_fields` WHERE `type`=?s", $type);
		return $type_fields;
	}

	public static function addLevelField($level)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		if (!SJB_DB::query("SHOW COLUMNS FROM `listing_fields` WHERE `Field` = ?s", 'level_'.$level)) {	
			$fieldLevel = 'level_'.$level;
			if ($level > 1) {
				$prevLevel = 'level_'.($level-1);
				SJB_DB::query("ALTER TABLE `listing_fields` ADD `{$fieldLevel}` VARCHAR( 255 ) NULL AFTER `{$prevLevel}`") ;
			}
			else {
				SJB_DB::query("ALTER TABLE `listing_fields` ADD `{$fieldLevel}` VARCHAR( 255 ) NULL") ;
			}
		}
	}

	public static function getListItemSIDByValue($fieldValue, $fieldSID)
	{
		$result = SJB_DB::query('SELECT `sid` FROM `listing_field_list` WHERE `value` = ?s AND `field_sid` = ?n',
			$fieldValue, $fieldSID);
		if (!empty($result)) {
			$result = SJB_Array::get(array_pop($result), 'sid');
		}
		return $result;
	}
	
	public static function getListingFieldsInfoByParentSID($parentSID, $hideHidden = false)
	{
		$where = '';
		if ($hideHidden)
			$where = " AND `hidden` = 0 ";
		$sids = SJB_DB::query("SELECT `sid` FROM `listing_fields` WHERE `parent_sid` = ?n {$where} ORDER BY `order`", $parentSID);
		$parentID = SJB_DB::queryValue("SELECT `id` FROM `listing_fields` WHERE `sid` = ?n", $parentSID);
		$fireldsInfo = array();
		foreach ($sids as $sid) {
			$fireldsInfo[$sid['sid']] = self::getFieldInfoBySID($sid['sid']);
			$fireldsInfo[$sid['sid']]['parentID'] = $parentID;
			$fireldsInfo[$sid['sid']]['is_system'] = true;
		}
		return $fireldsInfo;
	}
}
