<?php

class SJB_GuestAlertsManagePagination extends SJB_Pagination
{
    public function __construct()
    {
        $this->item = 'job alerts';
        $this->countActionsButtons = 2;
        $actionsForSelect = array(
            'activate' => array('name' => 'Activate'),
            'deactivate' => array('name' => 'Deactivate'),
            'delete' => array('name' => 'Delete')
        );
        $this->setActionsForSelect($actionsForSelect);

        $fields = array(
            'email' => array('name' => 'Email'),
            'subscription_date' => array('name' => 'Signed Up'),
            'email_frequency' => array('name' => 'Frequency'),
            'last_send' => array('name' => 'Last Sent'),
            'status' => array('name' => 'Status'),
        );
        $this->setSortingFieldsToPaginationInfo($fields);

        parent::__construct('subscription_date', 'DESC');
    }
}
