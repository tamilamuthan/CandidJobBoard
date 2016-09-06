<?php

class SJB_UserGroupDetails extends SJB_ObjectDetails
{
	/**
	 * @var SJB_NotificationGroups
	 */
	protected $notificationGroups;

	/**
	 * @param array $object_info
	 */
	public function __construct($object_info)
	{
		$this->notificationGroups = new SJB_NotificationGroups();
		if (isset($object_info['id'])) {
			$this->notificationGroups->setNotificationListingType($object_info['id']);
		}
		parent::SJB_ObjectDetails($object_info);
	}

	public function getDetails()
	{
		$userGroupDetails = $this->getUserGroupDetails();
		$userGroupNotificationDetails = $this->getNotificationsDetails();
		return array_merge($userGroupDetails, $userGroupNotificationDetails);
	}

	public function getUserGroupDetails()
	{
		return array(
			array(
				'id' => 'name',
				'caption' => 'Group name',
				'type' => 'string',
				'length' => '20',
				'table_name' => 'user_groups',
				'is_required' => true,
				'is_system' => true,
			),
		);
	}

	public function getNotificationsDetails()
	{
		$notificationGroupsSet = $this->notificationGroups->getNotifications();
		$notifications = array();
		foreach ($notificationGroupsSet as $groupID => $groupNotifications) {
			foreach ($groupNotifications as &$notification)
				$this->prepareNotification($notification, $groupID);
			$notifications = array_merge($notifications, $groupNotifications);
		}

		return $notifications;
	}

	/**
	 * @param array $notification
	 * @param string $groupID
	 */
	public function prepareNotification(&$notification, $groupID)
	{
		$notification['type'] = 'list';
		$notification['list_values'] = SJB_EmailTemplateEditor::getEmailTemplatesForListByGroup($groupID);
		$notification['is_required'] = false;
		$notification['is_system'] = false;
	}

	/**
	 * @return \SJB_NotificationGroups
	 */
	public function getNotificationGroups()
	{
		return $this->notificationGroups;
	}
}