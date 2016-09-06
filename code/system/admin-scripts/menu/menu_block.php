<?php

class SJB_Admin_Menu_ShowLeftMenu extends SJB_Function
{
	private $pageID;
	private $handledHighlightGroups = array();

	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$this->pageID = SJB_PageManager::getPageParentURI(SJB_Navigator::getURI(), SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE'), false);
		if (empty($this->pageID) || $this->pageID == '/') {
			$this->pageID = $GLOBALS['uri'];
		}

		$tp->assign('left_admin_menu', $this->mark_active_items($GLOBALS['LEFT_ADMIN_MENU']));

		$tp->display('admin_left_menu.tpl');
	}

	private function mark_active_items($arr)
	{
		foreach ($arr as $key => $items) {
			$arr[$key]['active'] = false;
			foreach ($items as $item_key => $item) {
				$arr[$key][$item_key]['active'] = false;
				$item['highlight'][] = $item['reference'];
				foreach ($item['highlight'] as $menuItem) {
					if (stripos($menuItem, SJB_Navigator::getURIThis()) && SJB_Navigator::getURI() !== '/') {
						$arr[$key][$item_key]['active'] = true;
						$arr[$key]['active'] = true;
					}
				}
			}
			$arr[$key]['id'] = str_replace(' ', '_', $key);
		}
		return $arr;
	}
}
