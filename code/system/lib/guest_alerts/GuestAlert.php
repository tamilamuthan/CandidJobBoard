<?php

class SJB_GuestAlert extends SJB_Object
{
	const STATUS_ACTIVE = '1';
	const STATUS_INACTIVE = '0';

	function __construct($detailsInfo = array())
	{
		$this->db_table_name = 'guest_alerts';
		$this->details = new SJB_GuestAlertDetails($detailsInfo);
	}

	private $key;

	public function getKey()
	{
		if (empty($this->key)) {
			$this->key = SJB_DB::queryValue('select `alert_key` from `guest_alerts` where `sid` = ?n', $this->getSID());
		}
		return $this->key;
	}

	public function save()
	{
		$isExisting = $this->getSID();
		SJB_ObjectDBManager::saveObject($this->db_table_name, $this);
		if (!$isExisting) {
			SJB_DB::query('UPDATE `guest_alerts` SET `last_send` = CURRENT_DATE(), `alert_key` = md5(concat(`sid`, `email`, `data`)) WHERE `sid` = ?n', $this->getSID());
		}
	}

	public function update()
	{
		return SJB_ObjectDBManager::saveObject($this->db_table_name, $this);
	}

	public function addDataProperty($requested_data)
	{
		$this->details->addDataProperty($requested_data);
	}

	public function addSubscriptionDateProperty($value = '')
	{
		$this->details->addSubscriptionDateProperty($value);
	}

	/**
	 * @return string|null
	 */
	public function getAlertEmail()
	{
		$emailValue = $this->getPropertyValue('email');
		if (is_array($emailValue))
			$emailValue = array_pop($emailValue);
		return $emailValue;
	}

	public function addStatusProperty($value = 1)
	{
		$this->details->addStatusProperty($value);
	}

	public function setStatus($status)
	{
		$this->setPropertyValue('status', $status);
	}
}
