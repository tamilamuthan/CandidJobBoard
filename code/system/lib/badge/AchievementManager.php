<?php

class SJB_AchievementManager
{
	public static function deleteAchievement($achievement_id, $user_sid = false)
	{
        $achievement = new SJB_Achievement( array('achievement_id' => $achievement_id, 'user_sid' => $user_sid) );
        return $achievement->delete();  
    }
    
	public static function deleteAllAchievementsByUserSID($user_sid)
	{
		return SJB_DB::query("DELETE FROM `achievements` WHERE `user_sid`=?n", $user_sid);
    }
    
    public static function getInfo($achievement_id)
    {
    	if ($achievement_id == 0) {
    		return false;
    	}
        $achievementInfo = SJB_AchievementSQL::selectInfoByID($achievement_id);

        return $achievementInfo;
    }
    
    public static function getAllAchievementsInfoByUserSID($user_sid)
    {
    	if ($user_sid == 0) {
    		return false;
    	}
        $achievementsInfo = SJB_AchievementSQL::selectInfoByUserSID($user_sid);

        foreach($achievementsInfo as $key => $achievementInfo) {
	        if ($achievementInfo) {
	        	$badge = SJB_BadgesManager::getBadgeInfoBySID($achievementInfo['badge_sid']);
	        	$achievementsInfo[$key] = $achievementInfo;
	        }
        }
        return $achievementsInfo;
    }
    
    public static function getAllAchievementsSIDsByUserSID($user_sid)
    {
    	if ($user_sid == 0) {
    		return false;
    	}
        $achievementsInfo = SJB_AchievementSQL::selectInfoByUserSID($user_sid);
		$result = array();
        foreach($achievementsInfo as $achievementInfo) {
			$result[] = $achievementInfo['id'];
        }
        return $result;
    }
    
    public static function getAllAchievementsByBadgeSID($badgeSID)
    {
    	 return SJB_DB::query("SELECT `id` FROM `achievements` WHERE `badge_sid` = ?n",$badgeSID);
    }
    
	public static function getAchievementQuantityByBadgeSID($badgeSID)
	{		 
		$result = SJB_DB::queryValue("SELECT COUNT( DISTINCT users.sid)
							FROM users 
							INNER JOIN achievements ON users.sid = achievements.user_sid 
							INNER JOIN badges ON badges.sid = achievements.badge_sid 
							WHERE badges.sid=?n", $badgeSID);
		
		return $result ? $result : 0;
	}
}
