<?php

class SJB_Menu_UserMenu extends SJB_Function
{
	public function execute()
	{
		if (!SJB_UserManager::isUserLoggedIn()) {
			echo SJB_System::executeFunction('users', 'login');
			return;
		}
		$userInfo = SJB_Authorization::getCurrentUserInfo();
		$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userInfo['user_group_sid']);
		if ($userGroupInfo['id'] == 'JobSeeker') {
			SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . '/my-listings/resume/');
		} else {
			SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . '/my-listings/job/');
		}
	}
}

