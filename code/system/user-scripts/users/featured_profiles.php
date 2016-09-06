<?php

class SJB_Users_FeaturedProfiles extends SJB_Function
{
	public function execute()
	{
		$template = SJB_Request::getVar('template', 'featured_profiles.tpl');
		$profiles = SJB_UserManager::getFeaturedProfiles(SJB_Request::getVar('items_count', 1));
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('profiles', $profiles);
		$tp->display($template);
	}
}