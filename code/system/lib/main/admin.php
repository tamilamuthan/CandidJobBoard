<?php

class SJB_Admin
{
	/**
	 * @package Users
	 * @subpackage Administrators
	 */

	/**
	 * authorizing administrator
	 *
	 * Function checks if there's active administrator.
	 * If it is, then it return true. If it's not it outputs
	 * form for logging into system untill administrator logins system
	 *
	 * @return bool 'true' administrator has authorized or 'false' otherwise
	 */
	public static function admin_auth()
	{
		$error = array();
		$tp = SJB_System::getTemplateProcessor();
		if (SJB_Request::getVar('action') == 'login') {
			if (SJB_System::getSystemSettings('isSaas')) {
				$response = SJB_HelperFunctions::whmcsCall('validatelogin', array(
					'email' => SJB_Request::getVar('username', ''),
					'password2' => SJB_Request::getVar('password'),
				));

				if (empty($response) || $response['result'] !== 'success') {
					$error['LOGIN_PASS_NOT_CORRECT'] = true;
				}

				if (empty($error)) {
					$response = SJB_HelperFunctions::whmcsCall('getclientsproducts', array('clientid' => $response['userid']));
					if (empty($response) || $response['result'] !== 'success') {
						$error['LOGIN_PASS_NOT_CORRECT'] = true;
					}
					if (empty($error)) {
						foreach ($response['products']['product'] as $product) {
							if (strpos($product['domain'], 'mysmartjobboard.') !== false && $product['domain'] == SJB_System::getSystemSettings('HTTPHOST') && $product['status'] == 'Active') {
								return SJB_Admin::admin_login(SJB_Request::getVar('username', ''), SJB_Request::getVar('password'), $product['id']);
							}
						}
					}
				}
				$error['LOGIN_PASS_NOT_CORRECT'] = true;
			} else {
				if (!SJB_Admin::isAdminExist(SJB_Request::getVar('username', ''), SJB_Request::getVar('password'))) {
					$error['LOGIN_PASS_NOT_CORRECT'] = true;
				}
				if (empty($error)) {
					return SJB_Admin::admin_login(SJB_Request::getVar('username', ''));
				}
			}
		}
		header('Content-type: text/html;charset=utf-8', true);
		$tp->assign('form_hidden_params', SJB_HelperFunctions::form(array('action' => 'login') + SJB_HelperFunctions::get_request_data_params()));
		$tp->assign('ERROR', $error);
		$tp->display('auth.tpl');
		return false;
	}

	/**
	 * checking for existing authorized administrator
	 * Function checks if administrator has authorized
	 * @return 'true' if administrator has authorized or 'false' otherwise
	 */
	public static function admin_authed()
	{
		return !is_null(SJB_Session::getValue('username')) && !is_null(SJB_Session::getValue('usertype')) && SJB_Session::getValue('usertype') == "admin";
	}

	/**
	 * logging into system as administrator
	 *
	 * Function logs administrator into system.
	 * If operation succeded it registers session variables 'username' and 'usertype'
	 *
	 * @param string $username user's name
	 * @param string $password user's password
	 * @param string $productId
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	public static function admin_login($username, $password = '', $productId = '')
	{
		SJB_Session::setValue('username', SJB_DB::quote($username));
		if ($password && $productId) {
			SJB_Session::setValue('password', $password);
			SJB_Session::setValue('whmcsProductId', $productId);
		}
		SJB_Session::setValue('usertype', 'admin');
		setcookie('admin_mode', 'on', null, '/');
		return true;
	}

	public static function isAdminExist($username, $password)
	{
		$username = SJB_DB::quote($username);
		$password = md5(SJB_DB::quote($password));

		$value = SJB_DB::queryValue("SELECT * FROM `administrator` WHERE `username` = ?s AND `password` = '?w'", $username, $password);

		return !empty($value);
	}

	/**
	 * logging administrator out of system
	 *
	 * Function logs administrator out of system
	 */
	public static function admin_log_out()
	{
		SJB_Session::unsetValue('username');
		SJB_Session::unsetValue('usertype');
		setcookie("admin_mode", '', time()-3600, '/');
	}
}
