<?php

class SJB_StringType extends SJB_Type
{
	public function __construct($property_info)
	{
		parent::__construct($property_info);
		$this->property_info['maxlength'] = 256;
		$this->default_template = 'string.tpl';
	}
	
	function isEmpty()
	{
		$value_is_empty = false;
		if (is_array($this->property_info['value'])) {
			foreach ($this->property_info['value'] as $field_value) {
				$field_value = $this->applyHtmlFilters($field_value);
				if ($field_value == '') {
					$value_is_empty = true;
					break;
				}
			}
		} else {
			$this->property_info['value'] = trim($this->property_info['value']);
			$field_value = $this->property_info['value'];
			$field_value = $this->applyHtmlFilters($field_value);
			$value_is_empty = ($field_value == '');
		}
		return $value_is_empty;
	}

	function isValid()
	{
		if ($this->property_info['id'] == 'ApplicationSettings') {
			if ($this->property_info['value']['add_parameter'] == 1) {
				if (!preg_match("^[\w\._-]+@[\w\._-]+\.\w{2,}\$^ui", $this->property_info['value']['value'])) {
					return 'NOT_VALID_EMAIL_FORMAT';
				}
			}
			if (strlen($this->property_info['value']['value']) <= $this->property_info['maxlength'])
				return true;
		} elseif (strlen($this->property_info['value']) <= $this->property_info['maxlength']) {
			return true;
		}
		return 'DATA_LENGTH_IS_EXCEEDED';
	}
	
	function getPropertyVariablesToAssign()
	{
		$value = SJB_HelperFunctions::getClearVariablesToAssign($this->property_info['value']);
		
		if ($this->property_info['id'] == 'ApplicationSettings' && !is_array($value)) {
			$value = array(
				'value' => $value,
				'add_parameter' => ''
			);
		}
		
		return array(
			'id'                  => $this->property_info['id'],
			'type'                => $this->property_info['type'],
			'isClassifieds'       => $this->property_info['is_classifieds'],
			'value'               => $value,
			'default_value'       => $this->property_info['default_value'],
			'hidden'              => $this->property_info['hidden']
		);
	}

	function getSQLValue()
	{
		if ($this->property_info['id'] == 'ApplicationSettings' && !empty($this->property_info['value']['add_parameter']) || is_array($this->property_info['value'])) {
			return $this->property_info['value']['value'];
		}
		
		$this->property_info['value'] = $this->applyHtmlFilters($this->property_info['value']);
		return $this->property_info['value'];
	}

	function getAddParameter()
	{
		if (isset($this->property_info['value']['add_parameter']) && $this->property_info['id'] == 'ApplicationSettings')
			return SJB_DB::quote($this->property_info['value']['add_parameter']);
		return '';
	}

    function getKeywordValue()
	{
		if (!is_array($this->property_info['value']))
			return $this->property_info['value'];
		return '';
	}

    function htmlspecialchars_decode($string,$style=ENT_COMPAT)
    {
        $translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS,$style));
        return strtr($string, $translation);
    }

	public static function applyHtmlFilters($string)
	{
		$string = trim($string);
		if (SJB_Settings::getValue('escape_html_tags') == 'htmlpurifier' && SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') != 'admin'){
			$filters = str_replace(',', '', SJB_Settings::getSettingByName('htmlFilter')); 
			$string = strip_tags($string, $filters);
		}
		return $string;
	}
}