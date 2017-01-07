<?php

class SJB_Badge extends SJB_Object
{
    public  $pages = array();
	public  $permissions = array();
	public	$details = null;

	function __construct($badgeInfo = array())
	{
		$this->db_table_name = 'badges';
		$this->getBadgeDetails($badgeInfo);
	}
	
	public function getBadgeDetails($badgeInfo = array())
	{
		$this->details = new SJB_BadgeDetails($badgeInfo);
        $this->pages = $this->details->getPages();
	}
    
	public function getBadgePages()
	{
		return $this->pages;
	}
	
    public function saveBadge($badge)
	{
		SJB_BadgesManager::saveBadge($badge);
		return $badge;
	}
		
	public function isValid($product)
	{
		return $this->details->isValid($product);
	}
}
