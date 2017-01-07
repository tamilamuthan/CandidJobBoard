<?php

class SJB_Admin_Badge_UserBadge extends SJB_Function
{
	public function isAccessible()
	{
       return (SJB_Settings::getSettingByName('gradlead_enable_application'));
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$userSID = SJB_Request::getVar('user_sid', false);
		$page = SJB_Request::getVar('page', '');
		$action = SJB_Request::getVar('action', false);
		$user = SJB_UserManager::getUserInfoBySID($userSID);
		$achievementID = SJB_Request::getVar("achievement_id", 0);
		$viewAchievementInfo = true;


		if ($user) {
			switch ($page) {
				case 'add_badge':
					if ($action == 'add_badge') {
						$badgeSID = SJB_Request::getVar('badge_sid', false);
						if ($badgeSID) {
							$badgeInfo = SJB_BadgesManager::getBadgeInfoBySID($badgeSID);
							$achievement = new SJB_Achievement(array('badge_sid' => $badgeSID));
							$achievement->setUserSID($userSID);
							$achievement->saveInDB();
							$tp->assign('achievement_added', 1);
						}
						else
							$errors['UNDEFINED_BADGE_SID'] = 1;
					}

					$badges = SJB_BadgesManager::getUserGroupBadges($user['user_group_sid']);
					$tp->assign('user_sid', $userSID);
					$tp->assign('badges', $badges);
					$tp->display('add_user_badge.tpl');
					break;

				case 'user_badges':
					if ($action == 'remove') {
						SJB_AchievementManager::deleteAchievement($achievementID, $userSID);
					}
					$achievements = SJB_AchievementManager::getAllAchievementsInfoByUserSID($userSID);

					foreach ($achievements as $key => $achievementInfo) {
						$achievements[$key] = $achievementInfo;
						$achievements[$key]['badge'] = SJB_BadgesManager::getBadgeInfoBySID($achievementInfo['badge_sid']);
					}
					$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
					$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userInfo['user_group_sid']);
					SJB_System::setGlobalTemplateVariable('wikiExtraParam', $userGroupInfo['id']);
					$tp->assign("user_group_info", $userGroupInfo);
					$tp->assign('achievements', $achievements);
					$tp->assign('user_sid', $userSID);
					$tp->display('user_badges.tpl');
					break;
	
				case 'user_badge':
					$errors = array();
					if ($action == 'change') {
						$achievementSIDs = SJB_Request::getVar('achievement_sids', array());
						$deletedAchievements = false;
						foreach ($achievementSIDs as $achievementSID => $val) {
							if (SJB_AchievementManager::deleteAchievement($achievementSID, $userSID)) 
								$deletedAchievements = true;
						}
						if ($deletedAchievements) {
							$tp->assign('deleted', 'yes');
							$viewAchievementInfo = false;
						}
						else
							$tp->assign('deleted', 'no');
					}

					$i18n = SJB_ObjectMother::createI18N();
					$achievementsInfo = array();
					if ($viewAchievementInfo) {
						$achievements = SJB_AchievementManager::getAllAchievementsInfoByUserSID($userSID);
						foreach ($achievements as $key => $achievement) {
							$achievementsInfo[$key] = $achievement;
							$achievementsInfo[$key]['badge'] = SJB_BadgesManager::getBadgeInfoBySID($achievement['badge_sid']);
							$achievementsInfo[$key]['creation_date'] = $i18n->getDate($achievement['creation_date']);
						}
					}
					$tp->assign('errors', $errors);
					$tp->assign('achievementsInfo', $achievementsInfo);
					$tp->assign('countAchievements', count($achievementsInfo));
					$tp->assign('user_sid', $userSID);
					$tp->assign('user', $user);
					$tp->display('user_badge.tpl');
					break;
			}
		}
		else {
			$errors['USER_DOES_NOT_EXIST'] = 1;
			$tp->assign('errors', $errors);
			$tp->display('../users/error.tpl');
		}
	}
}
