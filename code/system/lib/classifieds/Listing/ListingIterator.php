<?php

class SJB_Iterator extends ArrayIterator
{
    private $array = array();
    private $listing_type_sid = 0;  
    private $criteria = array(); 
    private $user_logged_in = false;
    private $current_user_sid = 0;
	static private $index = 0;
	static private $coordinates = array();

    public function __construct() 
    {
        reset($this->array);
    }

    public function rewind() 
    {
        reset($this->array);
    }

    public function current() 
    {
    	$listing_structure = array();
    	$info = current($this->array);
    	if (is_numeric($info)) {
	   		$sid = $info;
			$cache = SJB_Cache::getInstance();
			$cacheID = md5('ListingIterator::SJB_ListingManager::getObjectBySID' . $sid);
			if ($cache->test($cacheID)) {
				$listing = $cache->load($cacheID);
			}
			else {
				$listing = SJB_ListingManager::getObjectBySID($sid);
				$cache->save($listing, $cacheID, array(SJB_Cache::TAG_LISTINGS));
			}

			$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing);
			$listing_structure['activation_date'] = date('Y-m-d H:i:s', strtotime($listing_structure['activation_date']));
			$listing_structure['expiration_date'] = date('Y-m-d H:i:s', strtotime($listing_structure['expiration_date']));

			if (isset($listing->details->properties['EmploymentType'])) {
				$employmentInfo		= $listing->details->properties['EmploymentType']->type->property_info;
				$employmentTypes	= array();
				$employment			= explode(",", $employmentInfo['value']);

				foreach ($employmentInfo['list_values'] as $type) {
					$empType = str_replace(" ", "", $type['caption']);
					$employmentTypes[$empType] = 0;
					
					if ( in_array($type['id'], $employment) ) 
						$employmentTypes[$empType] = 1;
				}
				$listing_structure['myEmploymentType'] = $employmentTypes;
	    	}
    	}
    	elseif ($info) {
    		$listing_structure = $info;
    	}
        return $listing_structure;
    }

    public function key() 
    {
        return key($this->array);
    }

    public function next() 
    {
        next($this->array);
    }

    public function valid() 
    {
    	$currentItem = current($this->array);
        return !empty($currentItem);
    }
    
   	public function setListingsSids($listingSids) 
   	{
    	$this->array = $listingSids;
    }
    
   	public function setListingTypeSID($listingTypeSID) 
   	{
    	$this->listing_type_sid = $listingTypeSID;
    }
    
    public function setCriteria($criteria)
    {
    	$this->criteria = $criteria;
    }
    
    public function setUserLoggedIn($userLoggedIn)
    {
    	$this->user_logged_in = $userLoggedIn;
    }
    
    public function setCurrentUserSID($userSID)
    {
    	$this->current_user_sid = $userSID;
    }
    
    public function offsetSet($offset, $value) 
    {
        $this->array[$offset] = $value;
    }
    
    public function offsetExists($offset) 
    {
        return isset($this->array[$offset]);
    }
    
    public function offsetUnset($offset) 
    {
        unset($this->array[$offset]);
    }
    
    public function offsetGet($offset) 
    {
        return $this->array[$offset];
    }

	public function count()
	{
		return count($this->array);
	}

}
