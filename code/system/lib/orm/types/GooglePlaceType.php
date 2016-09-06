<?php

class SJB_GooglePlaceType extends SJB_StringType
{
	public function __construct($property_info)
	{
		parent::__construct($property_info);
		$this->property_info['maxlength'] = 256;
		$this->setDefaultTemplate('google_place.tpl');
	}
}
