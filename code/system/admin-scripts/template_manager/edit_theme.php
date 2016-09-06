<?php

class SJB_Admin_TemplateManager_ThemeEditor extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$templateEditor = new SJB_TemplateEditor();
		$setNewTheme = SJB_Request::getVar('theme', false);
		$theme = SJB_Request::getVar('theme', SJB_Settings::getValue('TEMPLATE_USER_THEME', 'default'));

		if ($setNewTheme) {
			SJB_Settings::setValue('TEMPLATE_USER_THEME', $theme);
		}

		if (!$templateEditor->doesThemeExists(SJB_Settings::getValue('TEMPLATE_USER_THEME', 'default'))) {
			SJB_Settings::setValue('TEMPLATE_USER_THEME', 'default');
			$theme = 'default';
		}
		else if ($setNewTheme) {
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/edit-themes/');
		}

		$tp->assign('themes', $templateEditor->getThemeList());
		$tp->assign('theme', $theme);

		if (isset($_REQUEST['action'])) {
			switch (SJB_Request::getVar("action")) {
				case "delete_theme":
					if (isset($_REQUEST['theme_name']) && $templateEditor->doesThemeExists($_REQUEST['theme_name'])) {
						$templateEditor->deleteEntireTheme($_REQUEST['theme_name']);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/edit-themes/');
					}
					break;
			}
		}

		$tp->display('theme_editor.tpl');
	}
}
