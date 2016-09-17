<?php

class SJB_ListingPagination extends SJB_Pagination
{
	public function __construct($listingTypeInfo)
	{
		if ($listingTypeInfo['id'] == 'Job') {
			$fieldUserName = 'Employer';
		} elseif ($listingTypeInfo['id'] == 'Resume') {
			$fieldUserName = 'Name';
        } elseif ($listingTypeInfo['id'] == 'Idea') {
            $fieldUserName = 'Entrepreneur';
        } elseif ($listingTypeInfo['id'] == 'Opportunity') {
            $fieldUserName = 'Investor';
		} else {
			$fieldUserName = 'Email';
		}

		$this->item = mb_strtolower($listingTypeInfo['name'] . 's', 'utf8');
		$this->countActionsButtons = 3;
		$this->popUp = true;

		$actionsForSelect = array(
			'activate'              => array('name' => 'Activate'),
			'deactivate'            => array('name' => 'Deactivate'),
			'delete'                => array('name' => 'Delete'),
		);
		$this->setActionsForSelect($actionsForSelect);

        $supTypes = array('Job','Opportunity');
		$fields = array(
			'Title'             => array('name' => 'Title'),
			'username'          => array('name' => $fieldUserName),
			'product'           => array('name' => 'Product', 'isSort' => false, 'isVisible' => in_array($listingTypeInfo['id'], $supTypes)),
			'activation_date'   => array('name' => $listingTypeInfo['id'] == 'Job' ? 'Posting Date' : 'Posted'),
            'applications'      => array('name' => 'Applications', 'isSort' => false, 'isVisible' => in_array($listingTypeInfo['id'], $supTypes)),
			'active'            => array('name' => 'Status', 'isSort' => false),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('activation_date', 'DESC');
	}
}