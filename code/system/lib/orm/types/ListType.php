<?php

class SJB_ListType extends SJB_Type
{
	var $list_values;

	public function __construct($property_info)
	{
		parent::__construct($property_info);
		$this->list_values = isset($property_info['list_values']) ? $property_info['list_values'] : array();
		if (!empty($property_info['template'])) {
			$this->default_template = $property_info['template'];
		} else {
			$this->default_template = 'list.tpl';
		}
	}

	function getPropertyVariablesToAssign()
	{
		$propertyVariables = parent::getPropertyVariablesToAssign();
		$propertyVariables['hidden'] = $this->property_info['hidden'];
		
		$newPropertyVariables = array(
			'list_values' 		=> $this->list_values,
			'caption'	  		=> $this->property_info['caption'],
		);
		return array_merge($newPropertyVariables, $propertyVariables);
	}

	function isValid()
	{
		return true;
	}
	
	function getSQLValue()
	{
		return $this->property_info['value'];
	}

    function getKeywordValue()
	{
		$result = '';
		foreach ($this->list_values as $listValue) {
			if ($this->property_info['value'] == $listValue['id']) {
				if (!empty($listValue['Code']) || !empty($listValue['Name'])) {
					$result .= " {$listValue['Code']} ";
					$result .= " {$listValue['Name']} ";
				}
				else
					$result .= " {$listValue['caption']} ";
			}
		}
		return $result;
	}

	function getSQLFieldType()
	{
		return 'TEXT NULL';
	}

}
