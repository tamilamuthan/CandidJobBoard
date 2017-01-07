<?php

class SJB_Admin_Badge_Badges extends SJB_Function
{
	public function isAccessible()
	{
       return (SJB_Settings::getSettingByName('gradlead_enable_application'));
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', false);
		$sid = SJB_Request::getVar('sid', 0);
		$errors = array();

		switch ($action) {
			case 'activate':
				SJB_BadgesManager::activateBadgeBySID($sid);
				break;
			case 'deactivate':
				SJB_BadgesManager::deactivateBadgeBySID($sid);
				break;
			case 'delete':
				SJB_BadgesManager::deleteBadgeBySID($sid);
				break;
		}
    
		$userGroup = SJB_UserGroupManager::getUserGroupInfoBySID(SJB_UserGroupManager::getUserGroupSIDByID($this->params['user_group_id']));
		$badges = SJB_BadgesManager::getUserGroupBadges($userGroup['sid']);

		$tp->assign('userGroup', $userGroup);
		$tp->assign('errors', $errors);
		$tp->assign('badges', $badges);
		$tp->display('badges.tpl');
	}
}
