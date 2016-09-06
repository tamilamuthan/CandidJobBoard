<?php

class SJB_StaticContent_ShowStaticContent extends SJB_Function
{
	public function execute()
	{
		$page_id = SJB_Request::getVar('pageid', null);
		$content = SJB_Request::getInstance()->page_config->getPageContent();
		$title = SJB_Request::getInstance()->page_config->getPageTitle();
		if ($page_id) {
			$requested_page_config = new SJB_UserPageConfig($page_id);
			$requested_page_config->ExtractPageInfo();
			$content = $requested_page_config->getPageContent();
			$title = $requested_page_config->getPageTitle();
		}
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('staticContent', $content);
		$tp->assign('staticContentTitle', $title);
		$tp->display('static_content.tpl');
	}
}
