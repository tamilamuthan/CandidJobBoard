<?php

class SJB_ProductDetails extends SJB_ObjectDetails
{
	public $properties;
	public $details;
	public $template = '';
	protected $listingType = null;
	protected $userGroup = null;

	function __construct($productInfo = array())
	{
		$this->listingType = SJB_ListingTypeManager::getListingTypeInfoBySID($productInfo['listing_type_sid']);
		$this->userGroup = SJB_UserGroupManager::getUserGroupInfoBySID($productInfo['user_group_sid']);
		$details_info = $this->getDetails();

		foreach ($details_info as $index => $property_info) 
			$sort_array[$index] = $property_info['order'];
		
		$sort_array = SJB_HelperFunctions::array_sort($sort_array);

		foreach ($sort_array as $index => $value) 
			$sorted_details_info[$index] = $details_info[$index];
		foreach ($sorted_details_info as $detail_info) {
			$detail_info['value'] = '';
			if (isset($productInfo[$detail_info['id']]))
				$detail_info['value'] = $productInfo[$detail_info['id']];
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public function getDetails()
	{
		$details = array(
			array(
				'id'			=> 'name',
				'caption'		=> 'Name',
				'type'			=> 'unique_string',
				'length'		=> '20',
				'table_name'	=> 'products',
				'validators' => array(
            		'SJB_UniqueSystemValidator'
				),
				'unique'		=> '1',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 1,
			),
			array(
				'id'			=> 'detailed_description',
				'caption'		=> 'Description',
				'type'			=> 'text',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 2,
			),
			array(
				'id'			=> 'user_group_sid',
				'caption'		=> 'User Group',
				'type'			=> 'string',
				'length'		=> '20',
				'table_name'	=> 'products',
				'value'			=> $this->userGroup['sid'],
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 4,
			),
			array(
				'id'			=> 'active',
				'caption'		=> 'Active',
				'type'			=> 'boolean',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 14,
				'default_value' => true
			),
			array(
				'id'			=> 'availability_from',
				'caption'		=> 'Available for Purchase',
				'type'			=> 'date',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 11,
			),
			array(
				'id'			=> 'availability_to',
				'caption'		=> 'AvailabilityTo',
				'type'			=> 'date',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 12,
			),
			array(
				'id'			=> 'trial',
				'caption'		=> 'Trial Product',
				'type'			=> 'boolean',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 13,
			),
		);
		return $details;
	}
	
	public function isValid($product)
	{
		return array();
	}
}