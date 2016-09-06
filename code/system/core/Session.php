<?php

class SJB_Session
{
	public static function init($url)
	{
		// get setting from config.php
		$storageType = SJB_System::getSystemSettings('SESSION_STORAGE');
		if ($storageType != 'files') {
			$sessionStorage = new SessionStorage();
			session_set_save_handler(
				array($sessionStorage, 'open'),
				array($sessionStorage, 'close'),
				array($sessionStorage, 'read'),
				array($sessionStorage, 'write'),
				array($sessionStorage, 'destroy'),
				array($sessionStorage, 'gc')
			);
		}
		
		$path = SJB_Session::getSessionCookiePath();
		ini_set('session.cookie_path', $path);
		ini_set('session.save_path', SJB_BASE_DIR . 'system/cache');
		ini_set('session.save_handler', 'files');
		Zend_Session::start();
	}
	
	public static function getSessionCookiePath()
	{
		$url_info = parse_url(SJB_System::getSystemSettings('USER_SITE_URL'));
		if (empty($url_info['path']))
			return '/';
		
		$path = $url_info['path'];
		if ($path[strlen($path) - 1] != '/')
			$path .= '/';
		return $path;
	}

	public static function getValue($name)
	{
		if (isset($_SESSION[$name]))
			return $_SESSION[$name];
		return null;
	}

	public static function setValue($name, $value)
	{
		switch($name) {
			case 'current_user':
				// update user_session_data_storage for logged user
				SJB_DB::query("UPDATE `user_session_data_storage` SET `user_sid` = ?n WHERE `session_id` = ?s", $value['sid'], self::getSessionId());
				break;
			default:
				break;
		}

		$_SESSION[$name] = $value;
	}

	public static function unsetValue($name)
	{
		unset($_SESSION[$name]);
	}

	public static function getSessionId()
	{
		return session_id();
	}

	public static function clearTemporaryData($maxLifeTime = null)
	{
		if (is_null($maxLifeTime)) {
			// get session.lifetime value by default
			$maxLifeTime = (integer) ini_get('session.gc_maxlifetime');
		}

		$expirationTime = time();

		$uploadedFiles = SJB_DB::query("SELECT * FROM `uploaded_files` WHERE (`id` LIKE '%Logo_tmp') OR (`id` LIKE '%Resume_tmp')");
		foreach ($uploadedFiles as $key => $value) {
			if (!empty($value['creation_time'])) {
				if ($value['creation_time'] +60*60*1 < $expirationTime) {
					SJB_UploadFileManager::deleteUploadedFileByID($value['id']);
				}
			}
		}

		// clear temporary data from `user_session_data_storage`
		SJB_DB::query("DELETE FROM `user_session_data_storage` WHERE `last_activity` <= DATE_SUB(NOW(), INTERVAL ?n SECOND)", $maxLifeTime);

		// clear temporary uploaded files from sessions, where last activity is older than $maxLifeTime
		// 1. get from `session` all records older than $maxLifeTime
		$expiredSessions = SJB_DB::query("SELECT `session_id` FROM `session` WHERE `time` <= (UNIX_TIMESTAMP() - ?n)", $maxLifeTime);
		// 2. check uploaded_files for values with ID's of expired sessions
		$expiredFiles = array();
		foreach ($expiredSessions as $session) {
			$sessionId = $session['session_id'];
			$tmpFiles = SJB_DB::query("SELECT `id` FROM `uploaded_files` WHERE `id` LIKE '?w_%_tmp'", $sessionId);
			foreach ($tmpFiles as $tmpFile)
				$expiredFiles[] = $tmpFile['id'];
		}
		if (!empty($expiredFiles)) {
			// 3. clean temporary ID value from `listings_properties` table
			SJB_DB::query("UPDATE `listings_properties` SET `value` = '' WHERE `value` IN (?l)", $expiredFiles);
			// 4. delete temporary uploaded files by ID's
			foreach ($expiredFiles as $fileId)
				SJB_UploadFileManager::deleteUploadedFileByID($fileId);
		}

		return true;
	}
}



class SessionStorage
{

	public static function open($save_path, $session_name)
	{
		return true;
	}

	public static function close()
	{
		return true;
	}

	public static function read($id)
	{
		$res = SJB_DB::query('select * from session where `session_id` = ?s', $id);
		if (count($res) > 0)
			return (string) $res[0]['data'];
		return '';
	}

	public static function write($id, $session_data)
	{
		$user_sid = 0;
		if (isset($_SESSION['current_user']))
			$user_sid = $_SESSION['current_user']['sid'];
		if (count(SJB_DB::query('select * from session where `session_id` = ?s', $id)) > 0)
			SJB_DB::query('update session set `data` = ?s, `time` = ?s, `user_sid` = ?n where `session_id` = ?s', $session_data, time(), $user_sid, $id);
		else
			SJB_DB::query('insert into session (`session_id`, `data`, `time`, `user_sid`) values (?s, ?s, ?s, ?n)', $id, $session_data, time(), $user_sid);
		return true;
	}

	public static function destroy($id)
	{
		SJB_DB::query('delete from `session` where `session_id` = ?s', $id);
		return true;
	}

	public static function gc($maxLifeTime)
	{
		$expirationTime = time();
		SJB_DB::query("delete from `session` where `time` + {$maxLifeTime} < {$expirationTime}");
		return true;
	}
	
}

