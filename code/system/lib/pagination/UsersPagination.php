<?php

class SJB_UsersPagination extends SJB_Pagination
{
	public function __construct($userGroupInfo, $template)
	{
        $companyTypes = array('Employer','Investor','Entrepreneur');

		if ($userGroupInfo['id'] == 'JobSeeker' || in_array($userGroupInfo['id'], $companyTypes)) {
			$this->item = mb_strtolower($userGroupInfo['name'], 'utf8') . 's';
		} else {
			$this->item = '\'' . mb_strtolower($userGroupInfo['name'], 'utf8') . '\' users';
		}

		if ($template == 'choose_user.tpl') {
			$this->actionsForSelect = false;
		} else {
			$this->countActionsButtons = 2;
			$this->popUp = true;
			$actionsForSelect = array(
				'activate'                  => array('name' => 'Activate'),
				'deactivate'                => array('name' => 'Deactivate'),
				'delete'                    => array('name' => 'Delete'),
			);
			$this->setActionsForSelect($actionsForSelect);
		}

		$fields = array(
			'CompanyName'       => array('name' => 'Company Name', 'isVisible' => in_array($userGroupInfo['id'], $companyTypes)),
			'name'              => array('name' => 'Name', 'isVisible' => $userGroupInfo['id'] == 'JobSeeker', 'isSort' => false),
			'username'          => array('name' => 'Email'),
			'Location'          => array('name' => 'Location', 'isVisible' => in_array($userGroupInfo['id'], $companyTypes), 'isSort' => false),
			'registration_date' => array('name' => 'Registration Date'),
			'active'            => array('name' => 'Status', 'isSort' => false),
		);

		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('registration_date', 'DESC');
	}
}
