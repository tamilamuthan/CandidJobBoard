<?php

class SJB_Admin_Users_UserGroups extends SJB_Function
{
	public function execute()
	{
		// fixme: deprecated
		return;
		$template_processor = SJB_System::getTemplateProcessor();
		$user_groups_structure = SJB_UserGroupManager::createTemplateStructureForUserGroups();
		$template_processor->assign("user_groups", $user_groups_structure);
		$template_processor->display("user_groups.tpl");
	}
}
