<?php

class SJB_Admin_Miscellaneous_Settings extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('configure_system_settings');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$form_submitted = SJB_Request::getVar('action');
		$page = SJB_Request::getVar('page');

		if ($form_submitted) {

			if (SJB_System::getSystemSettings('isSaas') && SJB_Settings::getValue('domain') != SJB_Request::getVar('domain')) {
				$_REQUEST['domain'] =
				$domain = trim(mb_strtolower(preg_replace('|https?://|ui', '', SJB_Request::getVar('domain'))));
				$response = SJB_HelperFunctions::whmcsCall('parkdomain', array(
					'client_username' => SJB_Session::getValue('username'),
					'client_password' => SJB_Session::getValue('password'),
					'whmcsProductId' => SJB_Session::getValue('whmcsProductId'),
					'domain' => $domain,
				));
				if (empty($response) || $response['result'] != 'success') {
					SJB_Error::getInstance()->addError(sprintf('Unable to park domain %s to client %s (%s) - %s',
						$domain,
						SJB_Session::getValue('username'),
						SJB_Session::getValue('whmcsProductId'),
						print_r($response, true)
					));
					$errors[] = 'Unable to change domain';
				}
			}
			if (empty($errors)) {
				SJB_Settings::updateSettings($_REQUEST);
			} else {
				// leave form as is if we have some errors
				foreach (SJB_Settings::getSettings() as $key => $value) {
					if (isset($_REQUEST[$key]) && $_REQUEST[$key] != $value) {
						SJB_Settings::changeValue($key, $_REQUEST[$key]);
					}
				}

			}

			if ($form_submitted == 'apply_settings') {
				$tp->assign('page', $page);
			}
		}

		$i18n = SJB_I18N::getInstance();
		$tp->assign('settings', SJB_Settings::getSettings());
		if (SJB_System::getSystemSettings('isSaas')) {
			$tp->assign('ip', SJB_System::getSystemSettings('env')['SJB']['sites_ip']);
		}
		$tp->assign('timezones', timezone_identifiers_list());

		$tp->assign('errors', $errors);
		$tp->assign('i18n_domains', $i18n->getDomainsData());
		$tp->assign('currencies', SJB_CurrencyManager::getCurrencies());
		$tp->assign('date_formats', SJB_DateFormatter::getFormats());
		$tp->display('settings.tpl');
	}
}