<?php

class SJB_BrowseManager
{
	public $type;
	
	function SJB_BrowseManager($listing_type_id, array $parameters)
	{
		$this->listing_type_id = $listing_type_id;
		$this->schema = $this->_createSchema($parameters);
		$this->params = $this->_createParams($parameters);
		$this->searcherFactory = new SJB_CategorySearcherFactory();
		$this->requestdata = $this->_getRequestdata($parameters);
    }

	public function _createParams(array $parameters)
	{
		$paramProvider = new SJB_FixedUrlParamProvider();
		$params = $paramProvider->getParams($parameters);
		return array_slice($params, 0, count($this->schema));
	}

	function getParams()
	{
		return $this->params;
	}
	
	function canBrowse()
	{
		return $this->getLevel() <= $this->_getMaxLevel();
	}
	
	function getRequestDataForSearchResults()
	{
		return $this->requestdata;
	}
	
	public function getItemsFromDB($uri, $decorate = false)
	{
		$items = SJB_DB::queryValue("SELECT `data` FROM `browse` WHERE `page_uri` = ?s", $uri);
		$items = unserialize($items);
		if ($decorate) {
			$searcherFactory  = $this->searcherFactory;
			$categorySearcher = $searcherFactory->getCategorySearcher($this->_getField());
			$items            = $categorySearcher->decorateItems($this->requestdata, $items);
		}
		
		return $items;
	}

	public function getItems($parameters, $decorate = false, array $listingSids = array())
	{
		if ($this->canBrowse()) {
			return $this->_getItems($decorate, $listingSids);
		}
		
		return array();
	}
	
	private function _getItems($decorate, array $listingSids)
	{
 		if ($this->getLevel() > $this->_getMaxLevel()) {
 			trigger_error("Requested browse level is more than max level", 256);
 			return;
		}
		
		$searcherFactory  = $this->searcherFactory;
		$categorySearcher = $searcherFactory->getCategorySearcher($this->_getField());
		$items            = $categorySearcher->getItems($this->requestdata, $listingSids);
		if ($decorate) {
			$items = $categorySearcher->decorateItems($this->requestdata, $items);
		}
		return $items;
	}

	private function _createSchema(array $parameters)
	{
		$res = array();
		$i = 1;
		$parent = isset($parameters['parent']) ? $parameters['parent'] : false;
		while (isset($parameters['level' . $i . 'Field'])) {
			$field = $parameters['level' . $i . 'Field'];
			if (!empty($parent)) {
				$property = SJB_ListingManager::getPropertyByParentID($parent, $field);
				$field = $parent.'_'.$field;
			}
			else
				$property = SJB_ListingManager::getPropertyByPropertyName($field);

            if (empty($property))
				return $res;

			$type = $property->getType();
			$res[] = array(
				'field' => $field,
				'homepage' => isset($parameters['homepage']) ? $parameters['homepage'] : 0,
				'type' => $type,
				'sid' => $property->getSID(),
				'parent' => $parent
			);
			
			$i++;
		}

		return $res;
	}

	private function _getRequestdata(array $parameters)
	{
		$res = array();
		for ($i = 0; $i < $this->getLevel(); $i++ ) {
			$value = $this->_getValue($i);
			$filterItem = $this->schema[$i];
			$field = $filterItem['field'];
			$this->type = $filterItem['type'];
			switch ($filterItem['type']) {
				case 'string' :
				case 'integer' :
					$res[$field]['equal'] = $value;
					break;
				case 'list' :
				case 'multilist':
					$listingFieldListItemManager = new SJB_ListingFieldListItemManager();
					$listValues = $listingFieldListItemManager->getHashedListItemsByFieldSID($filterItem['sid']);
					foreach ($listValues as $id => $val) {
						if ($id == $value) {
							$value = $id;
						}
					}
					if ($filterItem['type'] == 'multilist')
						$res[$field]['multi_like'][] = $value;
					else 
						$res[$field]['equal'] = $value;
					break;
			}
		}
		$res['active']['equal'] = 1;
		if (!empty($this->listing_type_id)) {
			$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($this->listing_type_id);
			if (!$listing_type_sid)
				trigger_error("Can't set filter by listing type for unknown type: '" . $this->listing_type_id . "'.", E_USER_WARNING);
			$res['listing_type_sid']['equal'] = $listing_type_sid;
		}
		
		return $res;
	}

	function _getField()
	{
		return isset($this->schema[$this->getLevel()]) ? $this->schema[$this->getLevel()] : array();
	}

	function getFieldID()
	{
		$field = $this->_getField();
		return isset($field['field']) ? $field['field'] : null;
	}

	function _getFieldByLevel($level)
	{
		return isset($this->schema[$level]) ? $this->schema[$level] : array();
	}
	
	function _getValue($i)
	{
		$params = $this->_getParams();
		return $params[$i];
	}

	function getLevel()
	{
		return count($this->_getParams());
	}
	
	function _getMaxLevel()
	{
		return count($this->schema) - 1;
	}
	
	function _getParams()
	{
		return $this->params;
	}
	
	function getNavigationElements($user_page_uri) 
	{
		$page_uri = $user_page_uri;
		$elements = array();
		foreach ($this->params as $level => $param) {
			$field = $this->_getFieldByLevel($level);
			$metadata = $this->_getMetaDataByFieldData($field);
			
			$page_uri = SJB_Path::combineURL($page_uri, $param);
			if (in_array($field['type'], array('list', 'multilist'))) {
				$fm = new SJB_ListingFieldListItemManager();
				$fi = $fm->getListItemBySID($param);
				if ($fi) {
					$param = $fi->getValue();
				}
			}
			$element = array(
				'caption' => $param,
				'uri' => $page_uri,
				'metadata' => $metadata
			);
			$elements[] = $element;
		}
		return $elements;
	}
	
	function getBrowsingMetaData()
	{
		$field = $this->_getField();
		$metadata = $this->_getMetaDataByFieldData($field); 
		
		return array
		(
			'browseItem' => array
			(
				'caption' => $metadata,
			),
		);
	}
	
	function _getMetaDataByFieldData($field)
	{
		$metadata = null;
		
		if (!in_array($field['type'], array('multilist', 'list'))) {
			$metadata['type'] = $field['type'];
		}
		
		return $metadata;
	}	
	
	function getType()
	{
		return $this->type;
	}
}

