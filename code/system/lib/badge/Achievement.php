<?php

class SJB_Achievement
{
	var $achievement_id 	= null;
	var $user_sid  			= null;
	var $badge_sid		 	= null;
	var $creation_date 		= null;
	
	function SJB_Achievement($input_info)
	{
		if ( isset($input_info['achievement_id']) )
			$this->achievement_id = $input_info['achievement_id'];
		if ( isset($input_info['badge_sid']) ) 		
			$this->_constructorByBadge($input_info);
		else
			$this->_constructorByID($input_info['achievement_id']);
		if (isset($input_info['user_sid']) && $input_info['user_sid'] != false)
			$this->user_sid = $input_info['user_sid'];
	}
	 
    function saveInDB()
    {
    	$result = SJB_AchievementSQL::insert($this->_getHashedFields());
    	if ($result) {
    		if (!$this->id) {
    			$this->id = $result;
    		}
    		$userInfo = SJB_UserManager::getUserInfoBySID($this->user_sid);
    		$user = new SJB_User($userInfo, $userInfo['user_group_sid']);
    		$user->updateSubscribeOnceUsersProperties($this->badge_sid, $this->user_sid);
    	}
    	
    	return (bool)$result;
    }

	function _getHashedFields()
	{
		$fields['badge_sid'] 			= $this->badge_sid;
		$fields['creation_date']		= $this->creation_date? $this->creation_date: date("Y-m-d");
		$fields['achievement_id']		= $this->id;
		$fields['user_sid']				= $this->user_sid;
		return $fields;
	}

	function _constructorByID($id)
	{
		$achievement_info = SJB_AchievementSQL::selectInfoByID($id);
		if ($achievement_info) {
			$this->id = $id;
			$this->achievement_id	  		= $achievement_info['id'];
			$this->badge_sid 			= $achievement_info['badge_sid'];
			$this->user_sid				= $achievement_info['user_sid'];
		}
	}
	
	function _constructorByBadge($badgeInfo)
	{
		$badgeSID = $badgeInfo['badge_sid'];
		$this->badge_sid = $badgeSID;
	}
	
	function setCreationDate($creation_date)
	{
		$this->creation_date = $creation_date? $creation_date: date("Y-m-d");
	}

	function getID()
	{
		return $this->id;
	}
	
	function setUserSID($user_sid)
	{
		$this->user_sid = $user_sid;
	}
	
	function getUserSID()
	{
		return $this->user_sid;
	}
    
    function delete()
    {
        return SJB_AchievementSQL::delete($this->achievement_id);   
    }
}
