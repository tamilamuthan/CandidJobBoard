<?php

require_once 'MCAPI.class.php';

class MailChimpPlugin extends SJB_PluginAbstract
{
	public static function init()
	{
		if (SJB_Settings::getSettingByName('mc_subscribe_new_users')) {
			SJB_Event::handle('onAfterUserCreated', array('MailChimpPlugin', 'subscribeUser'));
		}
	}

	function pluginSettings ()
	{
		return array(
			array (
				'id'			=> 'mc_subscribe_new_users',
				'caption'		=> 'Automatically subscribe newly registered users',
				'type'			=> 'boolean',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'mc_apikey',
				'caption'		=> 'API Key',
				'type'			=> 'string',
				'comment'		=> 'Please check this MC page for more info: <a href="https://us4.admin.mailchimp.com/account/api">https://us4.admin.mailchimp.com/account/api</a></p>',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'mc_emplistId',
				'caption'		=> 'Employers List ID',
				'type'			=> 'string',
				'length'		=> '50',
				'comment'		=> 'MailChimp Account &gt; Lists &gt; List Settings &gt; List Settings & Unique ID',
				'order'			=> null,
			),
			array (
				'id'			=> 'mc_jslistId',
				'caption'		=> 'Job Seekers List ID',
				'type'			=> 'string',
				'length'		=> '50',
				'comment'		=> 'MailChimp Account &gt; Lists &gt; List Settings &gt; List Settings & Unique ID',
				'order'			=> null,
			),
		);
	}

	/**
	 * @param SJB_User|string $user
	 * @param string $email
	 * @param string $name
	 * @param string $error
	 * @return bool
	 */
	public static function subscribeUser($user = '', $email = '', $name = '', &$error = '')
	{
		$lastName = '';
		$params = array();
		if (!empty($user)) {
			$email = $user->getUserName();
			if ($user->getPropertyValue('FullName')) {
				$firstName = $user->getPropertyValue('FullName');
				if (strpos($firstName, ' ') !== false) {
					list($firstName, $lastName) = explode(' ', $firstName, 2);
				}
				$params['FNAME'] = $firstName;
				if ($lastName) {
					$params['LNAME'] = $lastName;
				}
			}
		} else {
			return false;
		}

		$apikey = SJB_Settings::getSettingByName('mc_apikey');
		$listId = SJB_Settings::getSettingByName($user->getUserGroupSID() == 41 ? 'mc_emplistId' : 'mc_jslistId');

		$api = new MCAPI($apikey);

		// By default this sends a confirmation email - you will not see new members
		// until the link contained in it is clicked!
		$api->listSubscribe($listId, $email, $params, 'html', false);
		if ($api->errorCode) {
			$error = $api->errorMessage;
			return false;
		}
		return true;
	}
}
