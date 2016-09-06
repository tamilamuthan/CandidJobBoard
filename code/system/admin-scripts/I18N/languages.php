<?php

class SJB_Admin_I18n_ManageLanguages extends SJB_Function
{
	public function execute()
	{
		return;
		// fixme: deprecated
		$errors = array();
		if (isset($_REQUEST['action'])) {
			$action_name = $_REQUEST['action'];
			$action = SJB_LanguageActionFactory::get($action_name, $_REQUEST);

			if ($action->canPerform()) {
				$action->perform();
			}
			else {
				$errors = $action->getErrors();
			}
		}

		$i18n = SJB_ObjectMother::createI18N();

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('langs', $i18n->getLanguagesData());
		$tp->assign('errors', $errors);
		$tp->display('languages.tpl');
	}
}
