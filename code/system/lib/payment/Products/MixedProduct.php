<?php

class SJB_MixedProduct extends SJB_ProductDetails 
{
	public function getDetails()
	{
		$details = parent::getDetails();
		$additionalDetails = array(
			array(
				'id'			=> 'listing_type_sid',
				'caption'		=> 'Listing Type',
				'type'			=> 'string',
				'length'		=> '20',
				'table_name'	=> 'products',
				'value'			=> $this->listingType['sid'],
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 1,
			),
			array(
				'id'			=> 'price',
				'caption'		=> 'Price',
				'type'			=> 'float',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 3,
			),
			array(
				'id'			=> 'number_of_listings',
				'caption'		=> 'Number of Jobs',
				'type'			=> 'integer',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'comment'		=> 'Leave empty or 0 for unlimited posting',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 5,
			),
			array(
				'id'			=> 'post_' . strtolower($this->listingType['id']),
				'caption'		=> 'Post ' . $this->listingType['name'] . ($this->listingType['id'] == 'Job' ? 's' : ''),
				'type'			=> 'boolean',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 4,
			),
			array(
				'id'			=> 'listing_duration',
				'caption'		=> $this->listingType['name'] . ' listing duration',
				'type'			=> 'integer',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 6,
			),
			array(
				'id'			=> 'featured',
				'caption'		=> 'Featured ' . $this->listingType['name'],
				'type'			=> 'boolean',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 7,
			),
			array(
				'id'			=> 'resume_access',
				'caption'		=> 'Resume Access',
				'type'			=> 'boolean',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 9,
			),
			array(
				'id'			=> 'expiration_period',
				'caption'		=> 'Product Expiration',
				'type'			=> 'integer',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'comment'		=> 'Set empty or zero for never expire',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 10,
			),
			array(
				'id'			=> 'default',
				'caption'		=> 'Assigned to ' . strtolower($this->userGroup['name']) . ' upon registration',
				'type'			=> 'boolean',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 12,
			),

		);
		if ($this->userGroup['id'] == 'Employer') {
			$additionalDetails[] = array(
				'id'			=> 'featured_employer',
				'caption'		=> 'Featured ' . $this->userGroup['name'],
				'type'			=> 'boolean',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 8,
			);
		}
		return array_merge($details, $additionalDetails);
	}
	
	public function getPages()
	{
		$pages = array(
			'general' => array(
				'name' => 'General Settings',
				'fields' => array('name', 'detailed_description', 'price', 'user_group_sid', 'availability_from',
					'availability_to', 'trial', 'listing_type_sid', 'listing_duration', 'featured', 'default', 'featured_employer',
					'pricing_type',  'number_of_listings', 'expiration_period', 'active', 'post_job', 'post_resume')
			),
		);
		if ($this->listingType['id'] == 'Job') {
			$pages['general']['fields'][] = 'resume_access';
		}
		return $pages;
	}
	
	public function savePermissions($request, $product)
	{
		$acl = SJB_Acl::getInstance();
		$resources = $acl->getResources();
		$type = 'product';
		$role = $product->getSID();
		SJB_Acl::clearPermissions($type, $role);
		$serialized_extra_info = unserialize($product->getPropertyValue('serialized_extra_info'));
		$listingTypeSid = $serialized_extra_info['listing_type_sid'];
		$listingTypeId = strtolower(SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSid));
		$userGroupSID = $product->getPropertyValue('user_group_sid');
		$groupPermissions = SJB_DB::query('select * from `permissions` where `type` = ?s and `role` = ?s', 'group', $userGroupSID);
		foreach ($groupPermissions as $key => $groupPermission) {
			$groupPermissions[$groupPermission['name']] = $groupPermission;
			unset($groupPermissions[$key]);
		}
	    foreach ($resources as $name => $resource) {
	    	$params = isset($request[$name . '_params'])?$request[$name . '_params']:'';
	    	$params1 = isset($request[$name . '_params1'])?$request[$name . '_params1']:'';
	    	$value = !empty($request[$name]) ? 'allow' : '';
	    	if ($name == 'post_' . $listingTypeId) {
	    		if ($value) {
					$value = 'allow';
					$params = $serialized_extra_info['number_of_listings'];
				} else {
					$value = 'deny';
				}
	    	}
			if ($name == $listingTypeId . '_access') {
	    		if ($value) {
					$value = 'allow';
				}
	    	}
	    	if (empty($value) && isset($groupPermissions[$name])) {
	    		$value = 'inherit';
	    		$params = $groupPermissions[$name]['params'];
	    	} elseif ($value == 'deny' && $params1) {
	    		$params = $params1;
	    	}
	        SJB_Acl::allow($name, $type, $role, $value, $params);
	    }
	}
	
	public function getExpirationPeriod($product)
	{
		return $product->getPropertyValue('expiration_period');
	}
	
	public function getPrice($product)
	{
		return $product->getPropertyValue('price');
	}
	
	public function setNumberOfListings($numberOfListings)
	{
		$this->number_of_listings = $numberOfListings;
	}
}
