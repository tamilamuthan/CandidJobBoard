<?php

class SJB_Product extends SJB_Object
{
	public  $pages = array();
	public  $permissions = array();

	/**
	 * @var SJB_ProductDetails
	 */
	public	$details = null;

	function __construct($productInfo = array())
	{
		$this->db_table_name = 'products';
		$this->getProductDetails($productInfo);
	}
	
	public function getProductPages()
	{
		return $this->pages;
	}
	
	public function getExpirationPeriod()
	{
		return $this->details->getExpirationPeriod($this);
	}
	
	public function getPrice()
	{
		return $this->details->getPrice($this);
	}

	/**
	 * @param $listingTypeSid
	 * @return array
	 */
	public function getAccessPermissions($listingTypeSid)
	{
		$permissions = array();
		$listingType = SJB_ListingTypeManager::getListingTypeInfoBySID($listingTypeSid);
		if ($listingType['id'] == 'Job') {
			$permissions[] = 'apply_for_a_job';
			$permissions[] = 'resume_access';
		}
		return $permissions;
	}

	public function getProductDetails($productInfo = array())
	{
		$this->details = new SJB_MixedProduct($productInfo);
		$this->pages = $this->details->getPages();
	}
	
	public function savePermissions($request)
	{
		$this->details->savePermissions($request, $this);
	}
	
	public function saveProduct($product)
	{
		$price = trim($product->getPropertyValue('price'));
		if (empty($price)) {
			$product->setPropertyValue('price', 0);
		}
		SJB_ProductsManager::saveProduct($product);
		return $product;
	}
	
	public function setNumberOfListings($numberOfListings)
	{
		if (method_exists($this->details,'setNumberOfListings'))
			$this->details->setNumberOfListings($numberOfListings);
	}
	
	public function isValid($product)
	{
		return $this->details->isValid($product);
	}

	public static function isFeaturedProfile()
	{
	}
}