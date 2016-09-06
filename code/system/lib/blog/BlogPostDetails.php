<?php

class SJB_BlogPostDetails extends SJB_ObjectDetails
{
	public static function getDetails()
	{
		$details = array (
			array (
				'id'			=> 'title',
				'caption'		=> 'Title',
				'type'			=> 'string',
				'length'		=> '255',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 0,
			),
			array (
				'id'			=> 'text',
				'caption'		=> 'Content',
				'type'			=> 'text',
				'maxlength'		=> '999999999',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 1,
			),
			array (
				'id'			=> 'image',
				'caption'		=> 'Image',
				'type'			=> 'picture',
				'length'		=> '255',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 2,
				'width'         => 700,
				'height'        => 700,
			),
			array (
				'id'			=> 'date',
				'caption'		=> 'Publish Date',
				'type'			=> 'date',
				'length'		=> '20',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 3,
			),
			array (
				'id'			=> 'description',
				'caption'		=> 'Meta Description',
				'type'			=> 'string',
				'length'		=> '255',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 4,
			),
			array (
				'id'			=> 'keywords',
				'caption'		=> 'Meta Keywords',
				'type'			=> 'string',
				'length'		=> '255',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 5,
			),
			array (
				'id'			=> 'active',
				'caption'		=> 'Active',
				'type'			=> 'boolean',
				'default_value'	=> 1,
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 6,
			),
		);
		return $details;
	}
}