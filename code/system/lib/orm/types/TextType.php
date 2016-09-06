<?php

class SJB_TextType extends SJB_StringType
{
    public function __construct($property_info)
	{
		parent::__construct($property_info);
		$this->default_template = !empty($this->property_info['template']) ? $this->property_info['template'] : 'text.tpl';
		$this->property_info['maxlength'] = 65500;
		if (!empty($property_info['maxlength'])) {
			$this->property_info['maxlength'] = $property_info['maxlength'];
		}
	}

	function getPropertyVariablesToAssign()
	{
		return array(
			'id'                  => $this->property_info['id'],
			'type'                => $this->property_info['type'],
			'isClassifieds'       => $this->property_info['is_classifieds'],
			'value'               => $this->property_info['value'],
			'default_value'       => $this->property_info['default_value'],
		);
	}

	function getSQLFieldType()
	{
		return 'LONGTEXT NULL';
	}
}