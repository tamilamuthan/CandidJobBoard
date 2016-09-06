<?php

class SJB_ListingComplexFieldManager
{
	public static function getCommonListingFieldsInfo()
	{
		return SJB_ListingComplexFieldManager::getListingFieldsInfoByListingType(0);
	}

	public static function saveListingField($listing_field)
	{
		$result = SJB_ListingFieldDBManager::saveListingComplexField($listing_field);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return $result;
	}
	
	public static function getFieldInfoBySID($listing_field_sid)
	{
		return SJB_ListingFieldDBManager::getListingFieldInfoBySID($listing_field_sid, 'listing_complex_fields');
	}

	public static function deleteListingFieldBySID($listing_field_sid, $parentSID)
	{
		$field_info = SJB_ListingComplexFieldManager::getFieldBySID($listing_field_sid); 
		if (SJB_MemoryCache::has('ListingFieldsInfoByParentSID' . $parentSID)) {
			SJB_MemoryCache::delete('ListingFieldsInfoByParentSID' . $parentSID);
		}
		return SJB_ListingFieldDBManager::deleteComplexListingFieldBySID($field_info) &&
			SJB_ListingFieldDBManager::deleteComplexFieldProperties($listing_field_sid);
	}

	public static function getListingFieldsInfoByListingType($listing_type_sid)
	{
		if (!isset($GLOBALS["ListingFieldManagerCache"][$listing_type_sid]))
			$GLOBALS["ListingFieldManagerCache"][$listing_type_sid] = SJB_ListingFieldDBManager::getListingFieldsInfoByListingType($listing_type_sid);
		return $GLOBALS["ListingFieldManagerCache"][$listing_type_sid];
	}

	public static function getFieldBySID($listing_field_sid)
	{
		$listing_field_info = SJB_ListingFieldDBManager::getListingFieldInfoBySID($listing_field_sid, 'listing_complex_fields');
		if (empty($listing_field_info)) {
			return null;
		}
		else {
			$listing_field = new SJB_ListingField($listing_field_info);
			$listing_field->setSID($listing_field_sid);
			return $listing_field;
		}
	}

	public static function getListingFieldIDBySID($listing_field_sid)
	{
		$listing_field_info = SJB_ListingComplexFieldManager::getFieldInfoBySID($listing_field_sid);
		if (empty($listing_field_info))
			return null;
		return $listing_field_info['id'];
	}

	public static function changeListingPropertyIDs($new_listing_field_id, $old_listing_field_id)
	{
		return SJB_DB::query("UPDATE `listings_properties` SET `id` = ?s WHERE `id` = ?s", $new_listing_field_id, $old_listing_field_id);
	}

	public static function moveUpFieldBySID($field_sid)
	{
		$field_info = SJB_DB::query("SELECT * FROM listing_complex_fields WHERE  sid = ?n", $field_sid);
		if (empty($field_info))
		    return false;
		$field_info = array_pop($field_info);
		$current_order = $field_info['order'];
		$up_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM listing_complex_fields WHERE field_sid = ?n AND `order` < ?n",
								$field_info['field_sid'], $current_order);
		if ($up_order == 0)
		    return false;

		SJB_DB::query("UPDATE listing_complex_fields SET `order` = ?n WHERE `order` = ?n AND field_sid = ?n", 
					$current_order, $up_order, $field_info['field_sid']);
		SJB_DB::query("UPDATE listing_complex_fields SET `order` = ?n WHERE sid = ?n", $up_order, $field_sid);
		return $field_info['field_sid'];
	}

	public static function moveDownFieldBySID($field_sid)
	{
		$field_info = SJB_DB::query("SELECT * FROM listing_complex_fields WHERE sid = ?n", $field_sid);
		if (empty($field_info))
		    return false;
		$field_info = array_pop($field_info);
		$current_order = $field_info['order'];
		$less_order = SJB_DB::queryValue("SELECT MIN(`order`) FROM listing_complex_fields WHERE field_sid = ?n AND `order` > ?n",
								$field_info['field_sid'], $current_order);
		if ($less_order == 0)
		    return false;
		SJB_DB::query("UPDATE listing_complex_fields SET `order` = ?n WHERE `order` = ?n AND field_sid = ?n",
					$current_order, $less_order, $field_info['field_sid']);
		SJB_DB::query("UPDATE listing_complex_fields SET `order` = ?n WHERE sid = ?n", $less_order, $field_sid);
		return $field_info['field_sid'];
	}

	public static function getFieldsInfoByType($type)
	{
		$type_fields = SJB_DB::query("SELECT * FROM `listing_complex_fields` WHERE `type`=?s", $type);
		return $type_fields;
	}

	public static function getListingFieldsInfoByParentSID($field_sid)
	{
		return SJB_ListingFieldDBManager::getListingFieldsInfoByParentSID($field_sid);
	}
}
