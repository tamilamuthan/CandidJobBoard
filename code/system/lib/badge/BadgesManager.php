<?php

class SJB_BadgesManager
{
	/**
	 * @param SJB_Badge $badge
	 */
	public static function saveBadge($badge)
	{
		$badge->setFloatNumbersIntoValidFormat();
		$badgeSID = $badge->getSID();
		SJB_ObjectDBManager::saveObject('badges', $badge);
	}

	public static function getBadgeInfoBySID($badgeSID)
	{
		$cacheId = 'BadgeManager::getBadgeInfoBySID' . $badgeSID;
		if (SJB_MemoryCache::has($cacheId)) {
			return SJB_MemoryCache::get($cacheId);
		}
		$badge = SJB_ObjectDBManager::getObjectInfo("badges", $badgeSID);
		SJB_MemoryCache::set($cacheId, $badge);
		return $badge;
	}

	public static function getBadgeSidByName($badgeName)
	{
		return SJB_DB::queryValue("SELECT `sid` FROM ?w WHERE name = ?s", 'badges', $badgeName);
	}

	public static function getBadgeNameBySid($badgeSid)
	{
		return SJB_DB::queryValue("SELECT `name` FROM ?w WHERE sid = ?n", 'badges', $badgeSid);
	}

	public static function getAllBadgesInfo()
	{
		SJB_DB::query("UPDATE `badges` SET `active` = 0");
		$badgesSIDs = SJB_DB::query("SELECT * FROM `badges` ORDER BY `sid`");
		$badges = array();
		foreach ($badgesSIDs as $badgeSID)
			$badges[] = self::getBadgeInfoBySID($badgeSID['sid']);
		return $badges;
	}

	/**
	 * @param int $userGroupSID
	 * @return array
	 */
	public static function getUserGroupBadges($userGroupSID)
	{
		$badgesSIDs = SJB_DB::query('SELECT `sid` FROM `badges` WHERE `user_group_sid` = ?n', $userGroupSID);
		$badges = array();
		foreach ($badgesSIDs as $badgeSID) {
			$badges[] = self::getBadgeInfoBySID($badgeSID['sid']);
		}
		return $badges;
	}
	
	public static function deleteBadgeBySID($badgeSID)
	{
		return SJB_ObjectDBManager::deleteObjectInfoFromDB('badges', $badgeSID);	
	}
	
	public static function activateBadgeBySID($badgeSID)
	{
		return SJB_DB::query('UPDATE `badges` SET `active` = 1 WHERE `sid` = ?n', $badgeSID);
	}
	
	public static function deactivateBadgeBySID($badgeSID)
	{
		return SJB_DB::query('UPDATE `badges` SET `active` = 0 WHERE `sid` = ?n', $badgeSID);
	}
	
	public static function getBadgesInfoByUserGroupSID($userGroupSID)
	{
		$badgesSIDs = SJB_DB::query("SELECT * FROM `badges` WHERE `user_group_sid` = ?n", $userGroupSID);
		$badges = array();
		foreach ($badgesSIDs as $badgeSID)
			$badges[] = self::getBadgeInfoBySID($badgeSID['sid']);
		return $badges;
	}
	
	public static function getBadgesByUserGroupSID($userGroupSID, $userSID) 
	{
		$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
		$badgesSIDs = SJB_DB::query("SELECT * FROM `badges` p
									   WHERE `user_group_sid` = ?n
									   AND `active` = 1", $userGroupSID);
		$badges = array();
		foreach ($badgesSIDs as $badgeSID)
			$badges[] = self::getBadgeInfoBySID($badgeSID['sid']);
		return $badges;
	}
	
	public static function getAllActiveBadges()
	{
		$badgesSIDs = SJB_DB::query("SELECT * FROM `badges` WHERE `active` = 1 ORDER BY `user_group_sid`");
		$badges = array();
		foreach ($badgesSIDs as $badgeSID)
			$badges[] = self::getBadgeInfoBySID($badgeSID['sid']);
		return $badges;
	}
	
	public static function getBadgesIDsByUserGroupSID($userGroupSID)
	{
		$badgesSIDs = SJB_DB::query("SELECT * FROM `badges` WHERE `user_group_sid` = ?n", $userGroupSID);
		$badges = array();
		foreach ($badgesSIDs as $badgeSID)
			$badges[] = $badgeSID['sid'];
		return $badges;
	}
	
	public static function createTemplateStructureForBadge($badgeInfo)
	{
		if (!empty($badgeInfo)) {
			$badgeInfo = unserialize($badgeInfo);
			$badgeInfo = !empty($badgeInfo['badge_sid']) ? SJB_BadgesManager::getBadgeInfoBySID($badgeInfo['badge_sid']) : $badgeInfo;
			$badgeInfo = $badgeInfo?$badgeInfo:array();
			$METADATA = array (
			    'METADATA'			=> array(
	    			'caption'			=> array('type' => 'string', 'propertyID' => 'caption'),
	    			'detailed_description'		=> array('type' => 'text', 'propertyID' => 'detailed_description'),
	    		)
			);
			return array_merge($badgeInfo, $METADATA);
		}
		return array();
	}

	public static function generateQueryBySID($sid)
	{
		if (!empty($sid)) {
			return '"badge_sid";s:'.strlen($sid).':"'.$sid.'";';
		}
		return false;
	}

	public static function isBadgeExists($badgeSID)
	{
		return SJB_DB::queryValue("SELECT COUNT(*) FROM `badges` WHERE `sid` = ?n", $badgeSID) > 0;
	}
}
