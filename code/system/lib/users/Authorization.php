<?php

class SJB_Authorization
{
	public static function login($username, $password, $keep_signed, &$errors, $login_as_user = false, $autoriseByUsername = false)
	{
		$login = SJB_UserManager::login($username, $password, $errors, $autoriseByUsername, $login_as_user);
		if ($login) {
			$userInfo = SJB_UserManager::getUserInfoByUserName($username);
			if (!$userInfo['active']) {
				$errors['USER_NOT_ACTIVE'] = 1;
				return false;
			}

			$loginParams = array('username' => $username, 'password' => $password);
			SJB_Event::dispatch('Login', $loginParams);

			if ($keep_signed)
				SJB_Authorization::keepUserSignedIn($userInfo);

			SJB_DB::query('update `users` set `ip` = ?s where `sid` = ?n', $_SERVER['REMOTE_ADDR'], $userInfo['sid']);

			SJB_Authorization::setSessionForUser($userInfo);
			return true;
		} 
		
		return false;
	}

	public static function keepUserSignedIn($user_info)
	{
		$session_key = SJB_Authorization::generateSessionKey();
		SJB_Authorization::setKeepCookieForUser($session_key);
		SJB_UserManager::saveUserSessionKey($session_key, $user_info['sid']);
	}

	public static function generateSessionKey($length = 32)
	{
		$s = "abcdefghijklmnopqrstuvwxyz0123456789";
		$len = strlen($s);
		$key = '';
		for ($i = 0; $i < $length; $i++) {
			$key .= $s[mt_rand(0, $len - 1)];
		}
		return $key;
	}

	public static function setSessionForUser($user_info)
	{
		SJB_Session::setValue('current_user', $user_info);
	}

	public static function setKeepCookieForUser($session_key, $prolong_cookie = true)
	{
		if ($prolong_cookie)
			setcookie('session_key', $session_key, time() + 30 * 24 * 3600, '/');
		else
			setcookie('session_key', $session_key, time() - 30 * 24 * 3600, '/');
	}

	public static function updateCurrentUserSession()
	{
		if (SJB_Authorization::isUserLoggedIn()) {
			$sessionCurrentUser = SJB_Session::getValue('current_user');
			$currentUserInfo = SJB_UserManager::getUserInfoByUserName($sessionCurrentUser['username']);
			SJB_Session::setValue('current_user', $currentUserInfo);
		}
	}
	
	public static function isUserLoggedIn()
	{
		$sessionCurrentUser = SJB_Session::getValue('current_user');
		if (!is_null($sessionCurrentUser)) {
			return true;
		}
		return SJB_Authorization::checkForKeep();
	}
	
	public static function checkForKeep()
	{		
		if (isset($_COOKIE['session_key'])) {			
			$user_sid = SJB_UserManager::getUserSIDBySessionKey($_COOKIE['session_key']);			
			if (!is_null($user_sid)) {
				$userInfo = SJB_UserManager::getUserInfoBySID($user_sid);
				SJB_Session::setValue('current_user', $userInfo);
				SJB_Authorization::setKeepCookieForUser($_COOKIE['session_key']);
				return true;
			}
		}
		return false;
	}

	public static function logout()
	{
		if (isset($_COOKIE['session_key'])) {
			$session_key = $_COOKIE['session_key'];
			SJB_UserManager::removeUserSessionKey($session_key);
			SJB_Authorization::setKeepCookieForUser($session_key, false);
		}
		SJB_Session::setValue('current_user', null);
		SJB_Event::dispatch('Logout');	
	}
	
	public static function getCurrentUserInfo()
	{
		$currentUser = SJB_Session::getValue('current_user');
		if (!empty($currentUser))
			return $currentUser;

		if (isset($_COOKIE['session_key'])) {
			$user_sid = SJB_UserManager::getUserSIDBySessionKey($_COOKIE['session_key']);
			if (!empty($user_sid)) {
				$userInfo = SJB_UserManager::getUserInfoBySID($user_sid);
				SJB_Session::setValue('current_user', $userInfo);
				SJB_Authorization::setKeepCookieForUser($_COOKIE['session_key']);
				return $userInfo;
			}
		}
		return null;
	}
}

