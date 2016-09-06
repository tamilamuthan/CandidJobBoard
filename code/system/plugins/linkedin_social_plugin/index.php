<?php

if (!extension_loaded('openssl')) {
	$GLOBALS[SJB_SocialPlugin::SOCIAL_ACCESS_ERROR]['SOCIAL_ACCESS_ERROR'] = 'linkedin';
	SJB_Error::getInstance()->addWarning('Linkedin Social Plugin needs the "openssl" PHP extension.');
	return null;
}

require_once 'linkedin_social_plugin.php';

SJB_SocialPlugin::loadPlugin('linkedin', new SocialLoginPlugin());

if (SocialLoginPlugin::getNetwork() === SJB_SocialPlugin::getNetwork()) {

	SJB_Event::handle('Login_Plugin', array('SJB_SocialPlugin', 'login'));
	SJB_Event::handle('Logout', array('SJB_SocialPlugin', 'logout'), 1000);

	/*
	 * REGISTRATION
	 */
	SJB_Event::handle('FillRegistrationData_Plugin', array('SJB_SocialPlugin', 'fillRegistrationDataWithUser'));
	SJB_Event::handle('MakeRegistrationFieldsNotRequired_SocialPlugin', array('SocialLoginPlugin', 'makeRegistrationFieldsNotRequired'));
	SJB_Event::handle('SocialPlugin_AddListingFieldsIntoRegistration', array('SJB_SocialPlugin', 'addListingFieldsIntoRegistration'));

	SJB_Event::handle('AddReferencePluginDetails', array('SJB_SocialPlugin', 'addReferenceDetails'));

	/*
	 * LISTING AUTOFILL SYNCHRONIZATION
	 */
	SJB_Event::handle('SocialSynchronization', array('SJB_SocialPlugin', 'autofillListing'));
	SJB_Event::handle('SocialSynchronizationForm', array('SJB_SocialPlugin', 'autofillListingForm'));
}

