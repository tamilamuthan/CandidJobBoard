<?php

class SJB_TypesManager
{
	public static function getExtraDetailsByFieldType($field_type)
	{
		switch ($field_type) {
			
			case 'email':
				return SJB_EmailType::getFieldExtraDetails();
				
			case 'list':
				return SJB_ListType::getFieldExtraDetails();
				
			case 'multilist':
				return SJB_MultiListType::getFieldExtraDetails();
	
			case 'string':
				return SJB_StringType::getFieldExtraDetails();
	
			case 'text':
				return SJB_TextType::getFieldExtraDetails();
	
			case 'integer':
				return SJB_IntegerType::getFieldExtraDetails();
	
			case 'float':
				return SJB_FloatType::getFieldExtraDetails();

			case 'file':
				return SJB_UploadFileType::getFieldExtraDetails();

			case 'picture':
				return SJB_PictureType::getFieldExtraDetails();
			
			case 'logo':
				return SJB_LogoType::getFieldExtraDetails();
				
			case 'youtube':
				return SJB_YouTubeType::getFieldExtraDetails();

			case 'location':
				return SJB_LocationType::getFieldExtraDetails();

			case 'google_place':
				return SJB_GooglePlaceType::getFieldExtraDetails();

			default:
				return array();
		}
	}
}
