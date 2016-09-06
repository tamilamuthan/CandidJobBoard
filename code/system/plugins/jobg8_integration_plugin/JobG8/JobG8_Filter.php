<?php

class JobG8_Filter
{
	private $filterByCompany = false;
	private $companiesList = array();
	private $filterByProduct = false;
	private $productsList = array();
	private $filterByCategory = false;
	private $categoryList = array();
	private $postingType;

	
	public function __construct($postingType)
	{
		$this->postingType = $postingType;
		$this->resetFiltersByDefault();
	}

	/**
	 * @param  array $listingInfo
	 * @return bool
	 */
	public function isPassedByFilters($listingInfo)
	{
		if ($this->isPassedByProductsFilter($listingInfo) && $this->isPassedByCompanyNameFilter($listingInfo) && $this->isPassedByCategoryFilter($listingInfo)) {
			return true;
		}
		return false;
	}

	private function resetFiltersByDefault()
	{
		$filterByCompany = SJB_System::getSettingByName($this->postingType . '_jobg8_company_name_filter');
		$companiesList   = array();
		if ($filterByCompany) {
			$list = SJB_System::getSettingByName($this->postingType . '_jobg8_company_list');
			$list = str_replace("\r", '', $list);
			$companiesList = explode("\n", $list);
		}
		$this->filterByCompany = $filterByCompany;
		$this->companiesList   = $companiesList;
		$filterByProduct = SJB_System::getSettingByName($this->postingType . '_jobg8_product_filter');
		$productsList    = array();
		if ($filterByProduct) {
			$list         = SJB_System::getSettingByName($this->postingType . '_jobg8_product_list');
			$productsList = explode(',', $list);
		}
		$this->filterByProduct = $filterByProduct;
		$this->productsList    = $productsList;
		$filterByCategory = SJB_System::getSettingByName($this->postingType . '_jobg8_job_category_filter');
		$categoryList     = array();
		if ($filterByCategory) {
			$list         = SJB_System::getSettingByName($this->postingType . '_jobg8_job_category_list');
			$categoryList = explode(',', $list);
		}
		$this->filterByCategory = $filterByCategory;
		$this->categoryList     = $categoryList;
	}

	/**
	 * @param array $listingInfo
	 * @return bool
	 */
	private function isPassedByProductsFilter($listingInfo)
	{
		if ($this->filterByProduct) {
			$productSid = null;
			if (!empty($listingInfo['product_info'])) {
				$productInfo = unserialize($listingInfo['product_info']);
				$productSid  = $productInfo['product_sid'];
			}
			return in_array($productSid, $this->productsList);
		}
		return true;
	}

	/**
	 * @param array $listingInfo
	 * @return bool
	 */
	private function isPassedByCategoryFilter($listingInfo)
	{
		if ($this->filterByCategory) {
			$jobCategory = $listingInfo['JobCategory'];
			if (!empty($jobCategory)) {
				$jobCategory = explode(',', $jobCategory);
				foreach ($jobCategory as $categorySid) {
					if (in_array($categorySid, $this->categoryList)) {
						return true;
					}
				}
			}
			return false;
		}
		return true;
	}

	/**
	 * @param array $listingInfo
	 * @return bool
	 */
	private function isPassedByCompanyNameFilter($listingInfo)
	{
		if ($this->filterByCompany) {
			$listingUserSid = $listingInfo['user_sid'];
			$userInfo = SJB_UserManager::getUserInfoBySID($listingUserSid);
			if (!empty($userInfo['CompanyName']) && in_array($userInfo['CompanyName'], $this->companiesList)) {
				return true;
			}
			return false;
		}
		return true;
	}
}