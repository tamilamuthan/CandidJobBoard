<?php

class SJB_Admin_Miscellaneous_Plugins extends SJB_Function
{
	private $socialMediaPlugins = array(
		'SocialLoginPlugin'      => 'linkedin',
		'FacebookSocialPlugin'      => 'facebook',
		'GooglePlusSocialPlugin'    => 'googleplus',
	);

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$saved = false;
		$action = SJB_Request::getVar('action');
		$form_submitted = SJB_Request::getVar('submit');
		$template = 'plugins.tpl';
		$errors = array();
		if (SJB_Request::getVar('error', false))
			$errors[] = SJB_Request::getVar('error', false);
		$messages = array();
		if (SJB_Request::getVar('message', false))
			$messages[] = SJB_Request::getVar('message', false);

		switch ($action) {
			case 'save':
				$paths = SJB_Request::getVar('path');
				$active = SJB_Request::getVar('active');
				foreach ($paths as $key => $path) {
					$config = SJB_PluginManager::getPluginConfigFromIniFile($path);
					$config['active'] = $active[$key];
					$saved = SJB_PluginManager::savePluginConfigIntoIniFile($path, $config);
					if (!$saved)
						$errors[] = 'Failed to save ' . $key . ' settings';
				}
				SJB_PluginManager::reloadPlugins();
				break;

			case 'save_settings':
				$request = $_REQUEST;
				$request = self::checkRequiredFields($request);
				if (!isset($request['setting_errors'])) {
					SJB_Settings::updateSettings($request);
					if ($form_submitted == 'save') {
						break;
					} else if ($form_submitted == 'apply') {
						$pluginName = SJB_Request::getVar('plugin');
						SJB_HelperFunctions::redirect('?action=settings&plugin=' . $pluginName);
					}
				}
				else {
					unset($request['setting_errors']);
					$errors = $request;
				}

			case 'settings':
				$pluginName = SJB_Request::getVar('plugin');
				$plugin = SJB_PluginManager::getPluginByName($pluginName);
				if (isset($plugin['name'])) {
					$pluginObj = new $plugin['name'];
					$settings = $pluginObj->pluginSettings();
					$template = 'plugin_settings.tpl';
					$savedSettings = SJB_Settings::getSettings();
					SJB_Event::dispatch('RedefineSavedSetting', $savedSettings, true);
					SJB_Event::dispatch('RedefineTemplateName', $template, true);
					$tp->assign('plugin', $plugin);
					$tp->assign('settings', $settings);
					$tp->assign('savedSettings', $savedSettings);
				}
				break;
		}

		$listPlugins = SJB_PluginManager::getAllPluginsList();
		$plugins = array();
		foreach ($listPlugins as $key => $plugin) {
			$group = !empty($plugin['group']) ? $plugin['group'] : 'Common';
			$plugins[$group][$key] = $plugin;
			if (array_key_exists($key, $this->socialMediaPlugins)) {
				$plugins[$group][$key]['socialMedia'] = $this->socialMediaPlugins[$key];
			}
		}
		$tp->assign('saved', $saved);
		$tp->assign('groups', $plugins);
		$tp->assign('errors', $errors);
		$tp->assign('messages', $messages);
		$tp->display($template);
	}

	public static function checkRequiredFields($settings)
	{
		if (isset($settings['plugin'])) {
			$pluginObj      = new $settings['plugin'];
			$settingsFields = $pluginObj->pluginSettings();
			$errors         = array();
			foreach ($settingsFields as $settingsField) {
				if (!empty($settingsField['is_required']) && $settingsField['is_required'] === true && empty($settings[$settingsField['id']])) {
						$errors[$settingsField['caption']] = $settingsField['caption'].' is empty';
				}
				else if (!empty($settingsField['validators'])) {
					foreach ($settingsField['validators'] as $validator) {
						$isValid = $validator::isValid($settings[$settingsField['id']]);
						if ($isValid !== true) {
							$errors[$settingsField['caption']] = $isValid;
						}
					}
				}
			}

			if ($errors) {
				$errors['setting_errors'] = true;
				return $errors;
			}
		}

		return $settings;
	}
}
