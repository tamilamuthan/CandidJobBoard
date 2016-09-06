<?php

class SJB_NotificationGroups
{
	const GROUP_ID_USER		= 'user';
	const GROUP_ID_LISTING 	= 'listing';
	const GROUP_ID_PRODUCT 	= 'product';
	const GROUP_ID_OTHER	= 'other';
	const GROUP_ID_ALERTS	= 'alerts';

	const LABEL_CONFIGURABLE_FOR_USER 	= 'configurable_for_user';

	private $notificationListingType = 'Listing';

	/**
	 * @var array
	 */
	private $groups = array(
		self::GROUP_ID_USER 	=> 'General User Notifications',
		self::GROUP_ID_LISTING 	=> 'Listing Notifications',
		self::GROUP_ID_PRODUCT	=> 'Product Notifications',
		self::GROUP_ID_OTHER	=> 'Other Notifications',
		self::GROUP_ID_ALERTS	=> 'Alerts Notifications',
	);

	/**
	 * Notifications By Groups
	 * @var array
	 */
	private $notifications = array();

	/**
	 * @var array
	 */
	private $userSideNotifications;

	/**
	 * @return array
	 */
	public function getGroups()
	{
		return $this->groups;
	}

	/**
	 * @return array
	 */
	public function getNotifications()
	{
		$this->notifications = array(
			self::GROUP_ID_USER 		=> array(
				'welcome_email' => array(
					'id' => 'welcome_email',
					'caption' => 'Welcome email',
					self::LABEL_CONFIGURABLE_FOR_USER => 0,
				),
			),
			self::GROUP_ID_LISTING 	=> array(
				'notify_on_listing_activation' => array(
					'id' => 'notify_on_listing_activation',
					'caption' => "Notify on {$this->notificationListingType}s Activation",
					self::LABEL_CONFIGURABLE_FOR_USER => 1,
				),
				'notify_on_listing_expiration' => array(
					'id' => 'notify_on_listing_expiration',
					'caption' => "Notify on {$this->notificationListingType}s Expiration",
					self::LABEL_CONFIGURABLE_FOR_USER => 1,
				),
			),
			self::GROUP_ID_PRODUCT 	=> array(
				'notify_subscription_activation' => array(
					'id' => 'notify_subscription_activation',
					'caption' => 'Notify on Products Activation',
					self::LABEL_CONFIGURABLE_FOR_USER => 1,
				),
			),
		);
		return $this->notifications;
	}

	/**
	 * @return array
	 */
	public function getUserSideNotifications()
	{
		if (is_null($this->userSideNotifications))
			$this->retrieveUserSideNotifications();

		return $this->userSideNotifications;
	}

	/**
	 * prepare notifications for frontend
	 */
	private function retrieveUserSideNotifications()
	{
		$this->userSideNotifications = array();
		foreach ($this->getNotifications() as $groupID => $notifications)
			foreach ($notifications as $notificationID => $notificationData)
				if (SJB_Array::get($notificationData, self::LABEL_CONFIGURABLE_FOR_USER) > 0)
					$this->userSideNotifications[$groupID][$notificationID] = $notificationData;
	}

	public function setNotificationListingType($userGroupID)
	{
		switch ($userGroupID) {
			case 'Employer' :
				$this->notificationListingType = 'Job';
				break;
			case 'JobSeeker':
				$this->notificationListingType = 'Resume';
				break;
			default:
				$this->notificationListingType = 'Listing';
				break;
		}
	}
}
