<?php

class SJB_BlogPagination extends SJB_Pagination
{
    public function __construct()
    {
        $this->item = 'blog posts';
        $this->countActionsButtons = 2;
        $actionsForSelect = array(
            'activate'   => array('name' => 'Activate'),
            'deactivate' => array('name' => 'Deactivate'),
            'delete'     => array('name' => 'Delete')
        );
        $this->setActionsForSelect($actionsForSelect);

        $fields = array(
            'title'  => array('name' => 'Title'),
            'date'   => array('name' => 'Publish Date'),
            'active' => array('name' => 'Status'),
        );
        $this->setSortingFieldsToPaginationInfo($fields);

        parent::__construct('date', 'DESC');
    }
}