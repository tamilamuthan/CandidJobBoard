<?php

class SJB_EmailTemplateDetails extends SJB_ObjectDetails
{
	var $properties;
	var $details;

	function __construct($info)
	{
		foreach (self::getDetails() as $detail_info) {
			$detail_info['value'] = '';
			if (isset($info[$detail_info['id']]))
				$detail_info['value'] = $info[$detail_info['id']];
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails()
	{
		return array(
			array(
				'id'			=> 'name',
				'caption'		=> 'Template Name',
				'type'			=> 'string',
				'length'		=> '20',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 1,
			),
			array (
				'id'			=> 'cc',
				'caption'		=> 'CC',
				'type'			=> 'email',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 2,
			),
			array (
				'id'			=> 'subject',
				'caption'		=> 'Subject',
				'type'			=> 'string',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 3,
			),
			array (
				'id'			=> 'text',
				'caption'		=> 'Text',
				'type'			=> 'text',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 4,
			),
		);
	}

}