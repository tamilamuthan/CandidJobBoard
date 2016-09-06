<?php

class SJB_Admin_UserPages_EditUserPages extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_site_pages');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		$action = null;
		$is_new = 0;
		$form_submitted = SJB_Request::getVar('submit');

		if (SJB_Request::getMethod() == SJB_Request::METHOD_POST) {
			$page_data = SJB_UserPage::extractPageData($_REQUEST);
			$page = new SJB_UserPage();
			$page->setPageData($page_data);

			if (SJB_Request::getVar('action', '') == 'new') {
				$action = 'new';
				$is_new = 1;
			}
			if (!empty($_REQUEST['uri']) && SJB_System::doesUserPageExists($_REQUEST['uri']) && $action == 'new') {
				$errors['PAGE_ALREADY_EXISTS'] = 1;
			}
			
			if (empty($errors)) {
				if ($page->isDataValid() && $page->save()) {
					if ($form_submitted == 'save_page')
						unset($page);
				} else {
					$errors = $page->getErrors();
				}
			}
		}

		elseif (SJB_Request::getMethod() == SJB_Request::METHOD_GET && SJB_Request::getVar('action', false)) {
			$page = new SJB_UserPage();
			switch (SJB_Request::getVar('action')) {
				case 'delete_page':
					SJB_UserPage::deletePage($_REQUEST['uri']);
					$page = null;
					break;

				case 'edit_page':
					if (SJB_System::doesUserPageExists($_REQUEST['uri'])) {
						$page->loadPageDataFromDatabase($_REQUEST['uri']);
						$action = 'edit';
					}
					else {
						$errors['NO_SUCH_PAGE'] = 1;
					}
					break;

				case 'new':
					$page_data = SJB_UserPage::extractPageData($_REQUEST);
					$page->setPageData($page_data);
					$action = 'new';
					$is_new = 1;
					break;
			}
		}

		if (isset($page)) {
			$page->loadModulesFunctions();
			$list_functions = $page->functions;
			foreach ($list_functions as $module => $functions) {
				sort($functions);
				$list_functions[$module] = $functions;
			}
		}

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('ERRORS', $errors);
		$tp->assign('IS_NEW', $is_new);
		if (isset($page)) {
			$user_page_data = $page->getDisplayedPageData();
			$tp->assign('a_params', $page->a_params);

			$tp->assign('user_page', $user_page_data);
			$tp->assign('action', $action);
			$tp->display('edit_user_pages_add_form.tpl');
		} else {
			$list_of_pages = SJB_PageManager::get_pages(true);
			$tp->assign('pages_list', $list_of_pages);
			$tp->display('user_pages_list.tpl');
		}
	}
}
