<?php

class SJB_AchievementSQL
{
	public static function selectInfoByID($id)
	{
		$result = SJB_DB::query("SELECT * FROM achievements WHERE id=?n", $id);
		return array_pop($result);
	}
	
	public static function selectInfoByUserSID($user_sid)
	{
		return SJB_DB::query("SELECT * FROM achievements WHERE user_sid=?n ORDER BY `id` DESC", $user_sid);
	}
	
	public static function insert($achievement_info)
	{
		$achievement_id = $achievement_info['achievement_id'];
		if (!empty($achievement_id)) {
			return SJB_DB::query("UPDATE `achievements` SET `badge_sid` = ?n, `creation_date` = ?s  WHERE `id` = ?n",
					$achievement_info['badge_sid'],  $achievement_info['creation_date'], $achievement_id);
		} else {
            return SJB_DB::query("INSERT INTO `achievements`(`user_sid`, `badge_sid`, `creation_date`) VALUES(?n, ?n, ?s)",       
                    $achievement_info['user_sid'], $achievement_info['badge_sid'],  $achievement_info['creation_date']); 
		}
	}
	
	public static function delete($achievement_id)
	{
		return SJB_DB::query("DELETE FROM `achievements` WHERE `id`=?s", $achievement_id);
	}
}
