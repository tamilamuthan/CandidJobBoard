<?php

class SJB_GuestAlertManager extends SJB_ObjectManager
{
	const DB_TABLE_NAME = 'guest_alerts';

	public static function getGuestAlertInfoBySID($sid)
	{
		return parent::getObjectInfoBySID(self::DB_TABLE_NAME, $sid);
	}

	/**
	 * @param int $guestAlertSID
	 * @return SJB_GuestAlert
	 * @throws Exception
	 */
	public static function getObjectBySID($guestAlertSID)
	{
		$guestAlertInfo = self::getGuestAlertInfoBySID($guestAlertSID);
		if (empty($guestAlertInfo)) {
			throw new Exception ('No such Guest Alert found');
		}
		$guestAlert = new SJB_GuestAlert($guestAlertInfo);
		$guestAlert->setSID($guestAlertSID);
		$guestAlert->addStatusProperty(SJB_Array::get($guestAlertInfo, 'status'));
		return $guestAlert;
	}

	/**
	 * @param string $key
	 * @return SJB_GuestAlert
	 * @throws Exception
	 */
	public static function getGuestAlertByKey($key)
	{
		$alert = SJB_DB::queryValue('select sid from `guest_alerts` where `alert_key` = ?s', $key);
		if ($alert) {
			$alert = SJB_GuestAlertManager::getObjectBySID($alert);
		}
		if (!$alert) {
			throw new Exception('Alert not found');
		}
		return $alert;
	}

	public static function getGuestAlertsToNotify()
	{
		return SJB_DB::query('
			SELECT `sid`, `email`, `data`, `last_send`, `alert_key`
			FROM `guest_alerts`
			WHERE `status` = 1 AND (
					(`last_send` != CURDATE()
						AND (`email_frequency` = \'daily\'
							OR `email_frequency` = \'\'
						)
					)
					OR (`last_send` <= (CURDATE() - INTERVAL 7 DAY)
						AND  `email_frequency` = \'weekly\'
					)
					OR (`last_send` <= (CURDATE() - INTERVAL 1 MONTH)
						AND  `email_frequency` = \'monthly\'
					)
					OR `last_send` IS NULL
				)
	  ');
	}

	public static function deleteGuestAlertBySID($guestAlertSID)
	{
		return SJB_DB::query('DELETE FROM `guest_alerts` WHERE `sid` = ?n', $guestAlertSID);
	}

	public static function markGuestAlertAsSentBySID($guestAlertSID)
	{
		return SJB_DB::query('UPDATE `guest_alerts` SET `last_send` = CURDATE() WHERE `sid` = ?n', $guestAlertSID);
	}

	/**
	 * @param string $email
	 * @return bool|array
	 * @throws Exception
	 */
	public static function getGuestAlertsByEmail($email)
	{
		return SJB_DB::query('SELECT * FROM `guest_alerts` WHERE `email` = ?s', $email);
	}

	public static function getAlertsInfo()
	{
		$res = array();
		// условие запроса сформируем в зависимости от требуемого периода
		$periods = array(
			'Today' => '`a`.`subscription_date` >= CURDATE()',
			'Last 7 days' => '`a`.`subscription_date` >= date_sub(curdate(), interval 7 day)',
			'Last 30 days' => '`a`.`subscription_date` >= date_sub(curdate(), interval 30 day)',
			'Total' => '1=1',
		);

		foreach ($periods as $period => $where) {
			$res[$period] = SJB_DB::queryValue('
                select count(*)
                from `guest_alerts` a
                where ' . $where);
		}
		return $res;
	}

}
