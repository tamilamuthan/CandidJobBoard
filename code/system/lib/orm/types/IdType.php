<?php

class SJB_IdType extends SJB_Type
{
	public function __construct($propertyInfo)
	{
		parent::__construct($propertyInfo);
		$this->sql_type 		= 'SIGNED';
		$this->default_template = 'id.tpl';
	}

	function isValid()
	{
		if (!empty($this->property_info['validators'])) {
			foreach ($this->property_info['validators'] as $validator) {
				$isValid = $validator::isValid($this);
				if ($isValid !== true) {
					return $isValid;
				}
			}
		}
		return true;
	}

	function getSQLValue()
	{
		if (empty($this->property_info['value'])) {
			return 0;
		}
		return $this->property_info['value'];
	}

	function getKeywordValue()
	{
		return  $this->property_info['value'];
	}

	function getSQLFieldType()
	{
		return "INT( 10 ) NULL";
	}
}