<?php

class SJB_LanguageActionFactory
{
	public static function get($action, $params)
	{
		switch ($action)
		{
			case 'activate':
			case 'deactivate':
				$i18n = SJB_I18N::getInstance();
				$lang = $i18n->getLanguageData(SJB_Array::get($params, 'language'));
				$lang['activeFrontend'] = $action == 'activate';
				return new SJB_UpdateLanguageAction($i18n, $lang);
				break;
			default:
				return new SJB_LanguageAction();
		}
	}
}

