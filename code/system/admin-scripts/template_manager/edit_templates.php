<?php

class SJB_Admin_TemplateManager_EditTemplates extends SJB_Function
{
	public function execute()
	{
		if (SJB_System::getSystemSettings("isSaas")) {
			return;
		}
		$tp = SJB_System::getTemplateProcessor();
		$template_editor = new SJB_TemplateEditor();

		$module_name = SJB_Request::getVar('module_name', '', 'GET');

		// if set simple_view - not shown navigation to user
		$simple_view = SJB_Request::getVar('simple_view', false);

		if (!$template_editor->doesModuleExists($module_name))
			$module_name = '';
		$template_name = SJB_Request::getVar('template_name', '', 'GET');

		if (!$template_editor->doesModuleTemplateExists($module_name, $template_name))
			$template_name = '';
		$modules = $template_editor->getModuleWithTemplatesList();

		global $error;
		$error = array();
		$result = '';

		$action = SJB_Request::getVar('action', '');
		$form_submitted = SJB_Request::getVar('submit');

		// actions
		if (!empty($action)) {
			$theme = SJB_Settings :: getValue('TEMPLATE_USER_THEME', 'default');

			// TODO: !!!!если редактируется с юзерской части , то проверить есть ли тэмплэйт в теме , если нет , брать с _system
			switch ($action)
			{
				case 'delete':
					$template_editor->deleteTemplate($template_name, $module_name, $theme);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-templates/?module_name=' . $module_name);
					break;

				case 'edit':
					if (SJB_Request::getVar('templ_module') && SJB_Request::getVar('templ_name')) {
						$newTemplName = trim(SJB_Request::getVar('templ_name'));
						$newModuleName = SJB_Request::getVar('templ_module');

						if (!$template_editor->isTemplateNameValid($newTemplName))
							$error[] = 'NOT_VALID_FILENAME_FORMAT';

						if (!$template_editor->doesModuleExists($newModuleName))
							$error[] = 'MODULE_ERROR';

						if (empty($error)) {
							if ('edit' == $action) {
								if ($template_editor->moveTemplate(SJB_Request::getVar('templ_name_or'), SJB_Request::getVar('templ_module_or'), $theme, $newModuleName, $newTemplName)) {
									if ($form_submitted == 'save_template')
										SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-templates/?module_name=' . $newModuleName);
									SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-templates/?module_name=' . $newModuleName . '&template_name=' . $newTemplName);
									exit();
								}
								else {
									$error[] = 'CANT_MOVE_FILE';
								}
							}
							else {
								if ($template_editor->createTemplate($theme, $newModuleName, $newTemplName, $error))
									SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-templates/?module_name=' . $newModuleName . '&template_name=' . $newTemplName);
								$error[] = 'CANT_CREATE_FILE';
							}
						}
					}
					break;

				default:
					break;
			}
		}

		// не работало в юзерской
		if (empty($template_name))
			$template_name = SJB_Request::getVar('template_name', '');
		if (empty($module_name))
			$module_name = SJB_Request::getVar('module_name', '');

		// edittemplate
		if (!empty($template_name) && !empty($module_name)) {
			$menu_path = array(
				array(
					'reference' => '?',
					'name' => 'Edit Templates',
				),
				array(
					'reference' => "?module_name={$module_name}",
					'name' => $modules[$module_name]['display_name'],
				),
				array(
					'name' => $template_name,
					'reference' => '',
				)
			);

			$tp->assign('navigation', $menu_path);
			$tp->assign('errors', $error);
			$tp->assign('title', 'Edit Templates: ' . $modules[$module_name]['display_name'] . ' / Template: ' . $template_name);
			$tp->assign('show_clear_cache_setting', false);
			if (!$simple_view) {
				$tp->display('navigation.tpl');
			}

			echo SJB_System::executeFunction('template_manager', 'edit_template');
		}
		else {
			if (!empty($module_name)) {
				$menu_path = array(
					array(
						'reference' => '?',
						'name' => 'Edit Templates'
					),
					array(
						'reference' => '',
						'name' => $modules[$module_name]['display_name'],
					),
				);
				$tp->assign('navigation', $menu_path);
				$tp->assign('title', 'Edit Templates');
				$tp->assign('errors', $error);
				$tp->assign('show_clear_cache_setting', false);

				if (!$simple_view)
					$tp->display('navigation.tpl');

				echo SJB_System::executeFunction('template_manager', 'template_list');
			}
			else {
				$menu_path = array(
					array(
						'reference' => '',
						'name' => 'Edit Templates'
					),
				);

				$tp->assign('navigation', $menu_path);
				$tp->assign('title', 'Edit Templates');
				$tp->assign('show_clear_cache_setting', true);
				$tp->assign('result', $result);
				$tp->assign('errors', $error);

				if (!$simple_view)
					$tp->display('navigation.tpl');

				echo SJB_System::executeFunction('template_manager', 'module_list');
			}
		}
	}
}
