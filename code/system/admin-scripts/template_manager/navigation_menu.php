<?php

class SJB_Admin_TemplateManager_NavigationMenu extends SJB_Function
{
	public function execute()
	{
		if (SJB_Request::getMethod() == SJB_Request::METHOD_POST) {
			$menuItems = SJB_Request::getVar('menu_item', array());
			$links = SJB_Request::getVar('link', array());

			if ($menuItems) {
				// remove prototype element
				array_pop($menuItems);
				array_pop($links);

				SJB_DB::query('truncate `navigation_menu`');
				foreach ($menuItems as $key => $menuItem) {
					SJB_DB::query('insert into `navigation_menu` (`name`, `url`) values(?s, ?s)', $menuItem, $links[$key]);
				}
			}
		}

		$tp = SJB_System::getTemplateProcessor();
		$menuItems = SJB_DB::query('select * from `navigation_menu`');
		$tp->assign('menuItems', $menuItems);
		$tp->assign('system_pages', self::getSystemPages());
		$tp->assign('pages', SJB_PageManager::get_pages(true));
		$tp->display('navigation_menu.tpl');
	}

	public static function getSystemPages()
	{
		return array(
			'/' => 'Home',
			'/add-listing/?listing_type_id=Job' => 'Post a Job',
			'/jobs/' => 'Search Jobs',
			'/companies/' => 'Company Search',
			'/resumes/' => 'Resume Search',
			'/employer-products/' => 'Employer Products',
			'/jobseeker-products/' => 'Job Seeker Products',
			'/blog/' => 'Blog',
		);
	}
}
