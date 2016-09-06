<?php

class SJB_Admin_SocialMedia_SocialMedia extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$template = 'social_media.tpl';
		$formSubmitted = SJB_Request::getVar('submit');
		$action = SJB_Request::getVar('action');
		$subAction = SJB_Request::getVar('sub_action');
		$sid = SJB_Request::getVar('sid');
		$groups = array();
		$accountInfo = null;
		$messages = array();
		$savedSettings = array();
		
		if (SJB_Request::getVar('error', false)) {
			$errors[] = SJB_Request::getVar('error', false);
		}
		if (SJB_Request::getVar('message', false)) {
			$messages[] = SJB_Request::getVar('message', false);
		}
		$socNetworks = array (
			'facebook'      => array ('name' => 'Facebook'),
			'linkedin'      => array ('name' => 'Linkedin'),
			'googleplus'    => array ('name' => 'Google+'),
		);

		$network = SJB_Request::getVar('passed_parameters_via_uri');
		if (empty($network)) {
			$network = SJB_Request::getVar('soc_network');
		}
		switch ($network) {
//			case 'facebook':
//				$template = 'social_media_settings.tpl';
//				$objectName = 'SJB_FacebookSocial';
//				break;
			case 'linkedin':
				$template = 'social_media_settings.tpl';
				$objectName = 'SJB_LinkedInSocial';
				break;
//			case 'googleplus':
//				$template = 'social_media_settings.tpl';
//				$objectName = 'SJB_GooglePlusSocial';
//				break;
			default:
				SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . '/system/miscellaneous/plugins/');
				break;
		}
		
		switch ($action) {

			case 'save_settings':
				$request = $_REQUEST;
				$error = $this->checkFields($request, $objectName);
				if (!$error) {
					SJB_Settings::updateSettings($request);
					if ($formSubmitted == 'save') {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/social-media/');
					}
					else if ($formSubmitted == 'apply') {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/social-media/' . $network);
					}
				}
				
				$savedSettings = $request;
				break;
		}
		
		if ($network) {
			if (empty($savedSettings)) {
				$savedSettings = SJB_Settings::getSettings();
			}
			
			SJB_Event::dispatch('RedefineSavedSetting', $savedSettings, true);

			$tp->assign('network', $network);
			$tp->assign('savedSettings', $savedSettings);
			$tp->assign('networkName', $socNetworks[$network]['name']);
			$networkObject = new $objectName;
			$settings = $networkObject->getConnectSettings();
			$tp->assign('settings', $settings);
		} else {
			$tp->assign('socNetworks', $socNetworks);
		}
		
		$tp->assign('socNetworks', $socNetworks);
		$tp->assign('errors', $errors);
		$tp->assign('messages', $messages);
		$tp->display($template);
	}

	/**
	 * @param  array  $settings
	 * @param  string $socialPlugin
	 * @return bool
	 */
	private function checkFields(array $settings, $socialPlugin)
	{
		$pluginObj      = new $socialPlugin;
		$settingsFields = $pluginObj->getConnectSettings();
		$error          = false;
		foreach ($settingsFields as $settingsField) {
			if (!empty($settingsField['is_required']) && $settingsField['is_required'] === true && empty($settings[$settingsField['id']])) {
				SJB_FlashMessages::getInstance()->addWarning('EMPTY_VALUE', array('fieldCaption' => $settingsField['caption']));
				$error = true;
			}
			else if (!empty($settingsField['validators'])) {
				foreach ($settingsField['validators'] as $validator) {
					$isValid = $validator::isValid($settings[$settingsField['id']]);
					if ($isValid !== true) {
						SJB_FlashMessages::getInstance()->addWarning('EMPTY_VALUE', array('fieldCaption' => $settingsField['caption']));
						$error = true;
					}
				}
			}
		}
		
		return $error;
	}
}
