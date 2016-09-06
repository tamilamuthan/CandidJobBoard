<?php

class SJB_ExportController
{
	public static function createListing($listing_type_id)
	{
		$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
		$listing = new SJB_Listing(array(), $listing_type_sid);
		$listing->addUsernameProperty();
		$listing->addListingTypeIDProperty();
		$listing->addActivationDateProperty();
		$listing->addExpirationDateProperty();
		return $listing;
	}

	public static function getSearchPropertyAliases()
	{
		$property_aliases = new SJB_PropertyAliases();
		$property_aliases->addAlias(array
		(
			'id' 				 => 'listing_type',
			'real_id' 			 => 'listing_type_sid',
			'transform_function' => 'SJB_ListingTypeManager::getListingTypeSIDByID',
		));
		$property_aliases->addAlias(array
		(
			'id' 				 => 'username',
			'real_id' 			 => 'user_sid',
			'transform_function' => 'SJB_ExportController::getUserSIDByUsername',
		));
		return $property_aliases;
	}

	public static function getUserSIDByUsername($raw_value)
	{
		$sid = SJB_UserManager::getUserSIDByUsername($raw_value);
		if (empty($sid) && !empty($raw_value))
			$sid = -1;
		return $sid;
	}

	public static function getExportPropertyAliases()
	{
		$property_aliases = new SJB_PropertyAliases();
		$property_aliases->addAlias(array
		(
			'id' 				 => 'listing_type',
			'real_id' 			 => 'listing_type_sid',
			'transform_function' => 'SJB_ListingTypeManager::getListingTypeIDBySID',
		));
		$property_aliases->addAlias(array
		(
			'id' 				 => 'username',
			'real_id' 			 => 'user_sid',
			'transform_function' => 'SJB_UserManager::getUserNameByUserSID',
		));
		$property_aliases->addAlias(array
		(
			'id' 				 => 'extUserID',
			'real_id' 			 => 'user_sid',
			'transform_function' => 'SJB_UserManager::getExtUserIDByUserSID',
		));
		return $property_aliases;
	}

	public static function getExportData(array $listingsSid, array $exportProperties, SJB_PropertyAliases $aliases)
	{
		$exportData = new SJB_ExportIterator();
		$exportData->setArray($listingsSid);
		$exportData->setAdditionalParameters(array('exportProperties' => $exportProperties, 'aliases' => $aliases));
		$exportData->setCallbackFunction('SJB_ExportController::generateExportData');
		return $exportData;
	}
	
	public static function generateExportData($parameters)
	{
		$exportProperties = $aliases = $sid = null;
		
		extract($parameters);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($sid);
		$listingInfo = $aliases->changePropertiesInfo($listingInfo);
		$exportData  = array();
		$i18n        = SJB_I18N::getInstance();
		
		foreach ($exportProperties as $propertyId => $value) {
			if ('ApplicationSettings' == $propertyId) {
				$exportData[$sid][$propertyId] = isset($listingInfo[$propertyId]['value']) ? $listingInfo[$propertyId]['value'] : null;
			} else {
				$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($propertyId);
				if (!empty($fieldInfo['type']) && $fieldInfo['type'] == 'complex' && isset($listingInfo[$propertyId])) {
					$complexFields = $listingInfo[$propertyId];
					if (is_string($listingInfo[$propertyId]))
						$complexFields = unserialize($complexFields);
					if (is_array($complexFields)) {
						$fieldsInfo = SJB_ListingComplexFieldManager::getListingFieldsInfoByParentSID($fieldInfo['sid']);
						foreach ($fieldsInfo as $key => $info) {
							$fieldsInfo[$info['id']] = $info;
							unset($fieldsInfo[$key]);
						}
						$domDocument = new DOMDocument();
						$rootElement = $domDocument->createElement($propertyId . 's');
						$domDocument->appendChild($rootElement);
						$propertyElements = array();
						$createPropertyElements = true;
						foreach ($complexFields as $fieldName => $fieldValue) {
							$fieldInfo = isset($fieldsInfo[$fieldName]) ? $fieldsInfo[$fieldName] : array();
							foreach ($fieldValue as $key => $value) {
								if (isset($fieldInfo['type']) && $fieldInfo['type'] == 'date' && $value != '') {
									$value = $i18n->getDate($value);
								}
								if ($createPropertyElements) {
									$propertyElement = $domDocument->createElement($propertyId);
									$rootElement->appendChild($propertyElement);
									$propertyElements[$key] = $propertyElement;
								}
								$fieldElement = $domDocument->createElement($fieldName);
								if (isset($propertyElements[$key])) {
									$propertyElements[$key]->appendChild($fieldElement);
								}
								$valElement = $domDocument->createTextNode(XML_Util::replaceEntities($value));
								$fieldElement->appendChild($valElement);
							}
							$createPropertyElements = false;
						}
						$exportData[$sid][$propertyId] = $domDocument->saveXML();
					} else {
						$exportData[$sid][$propertyId] = null;
					}
				} else {
					$exportData[$sid][$propertyId] = isset($listingInfo[$propertyId]) ? $listingInfo[$propertyId] : null;
				}
			}
		}
		
		self::changeListProperties($exportProperties, $exportData);
		self::changeFileProperties($exportProperties, $exportData, 'picture');
		self::changeFileProperties($exportProperties, $exportData, 'file');
		self::changeLocationProperties($exportProperties, $exportData);
		
		return $exportData[$sid];
	}

	private static function changeListProperties(&$exportProperties, &$exportData)
	{
		$listFieldsInfo = SJB_ListingFieldManager::getFieldsInfoByType('list');
		$multilistFieldsInfo = SJB_ListingFieldManager::getFieldsInfoByType('multilist');
		$fieldsInfo = array_merge($listFieldsInfo, $multilistFieldsInfo);
		foreach ($fieldsInfo as $field_info) {
			$fieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($field_info['sid']);
			if (isset($exportProperties[$field_info['id']])) {
				foreach ($exportData as $listing_sid => $property) {
					switch (strval($fieldInfo['type'])) {
						case 'list':
							foreach ($fieldInfo['list_values'] as $listValues) {
								if ($listValues['id'] == $property[$field_info['id']]) {
									$exportData[$listing_sid][$field_info['id']] = $listValues['caption'];
									break;
								}
							}
							break;
						case 'multilist':
							$multilistValues = explode(',', $exportData[$listing_sid][$field_info['id']]);
							$multilistDisplayValues = array();
							foreach ($fieldInfo['list_values'] as $listValues) {
								if (in_array($listValues['id'], $multilistValues)) 
									$multilistDisplayValues[] = $listValues['caption'];
							}
							$exportData[$listing_sid][$field_info['id']] = implode(',', $multilistDisplayValues);
							break;
					}
				}
			}
		}
	}

	private static function changeFileProperties(&$exportProperties, &$exportData, $file_type)
	{
		$file_properties_info = SJB_ListingFieldManager::getFieldsInfoByType($file_type);

		foreach ($file_properties_info as $property_info) {
			if (isset($exportProperties[$property_info['id']])) {
				// listings walkthrough
				foreach ($exportData as $listing_sid => $property) {
					if ($file_type == 'file') {
						$exportData[$listing_sid][$property_info['id']] =
							SJB_System::getSystemSettings('USER_SITE_URL') . SJB_TemplateProcessor::listing_url(SJB_ListingManager::getListingInfoBySID($listing_sid)) .
							'?filename=' . rawurlencode(SJB_UploadFileManager::getUploadedSavedFileName($exportData[$listing_sid][$property_info['id']]));
					} else {
						$exportData[$listing_sid][$property_info['id']] = SJB_UploadPictureManager::getUploadedFileLink($exportData[$listing_sid][$property_info['id']]);
						$exportData[$listing_sid][$property_info['id']] = preg_replace('|/[^/]+/\.\./|', '/', $exportData[$listing_sid][$property_info['id']]);
					}
				}
			}
		}
	}

	private static function changeLocationProperties(&$exportProperties, &$exportData)
	{
		$locationFieldsInfo = SJB_ListingFieldManager::getFieldsInfoByType('location');
		foreach ($locationFieldsInfo as $fieldInfo) {
			if (isset($exportProperties[$fieldInfo['id']])) {
				unset($exportProperties[$fieldInfo['id']]);
				$exportLocationProperties[$fieldInfo['id'].'.Country'] = $fieldInfo['id'].'.Country';
				$exportLocationProperties[$fieldInfo['id'].'.State'] = $fieldInfo['id'].'.State';
				$exportLocationProperties[$fieldInfo['id'].'.City'] = $fieldInfo['id'].'.City';
				$exportLocationProperties[$fieldInfo['id'].'.ZipCode'] = $fieldInfo['id'].'.ZipCode';
				$exportLocationProperties[$fieldInfo['id'].'.Latitude'] = $fieldInfo['id'].'.Latitude';
				$exportLocationProperties[$fieldInfo['id'].'.Longitude'] = $fieldInfo['id'].'.Longitude';
				ksort($exportLocationProperties);
				$exportProperties = array_merge($exportProperties, $exportLocationProperties);
				foreach ($exportData as $listingSID => $property) {
					if (isset($property[$fieldInfo['id']]) && is_array($property[$fieldInfo['id']])) {
						$propertyLocation = array();
						foreach ($property[$fieldInfo['id']] as $locationField => $fieldValue) {
							$propertyLocation[$fieldInfo['id'].'.'.$locationField] = $fieldValue;
						}
						unset($property[$fieldInfo['id']]);
						ksort($propertyLocation);
						$exportData[$listingSID] = array_merge($property, $propertyLocation);;
					}
				}
			}
		}
	}

	public static function _getFileExportURL($file_name, $file_group, $listing_sid, $file_export_name = false)
	{
		$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");
		$file_name_parsed = explode(".", $file_name);
		$file_extension = end($file_name_parsed);
		$file_export_name = $file_export_name ? $file_export_name : $listing_sid . "." . $file_extension;
		return "{$export_files_dir}/{$file_group}/{$file_export_name}";
	}

	public static function _getUploadedFileURL($file_name, $file_group)
	{
		$uploaded_files_dir = SJB_System::getSystemSettings("UPLOAD_FILES_DIRECTORY");
		return "{$uploaded_files_dir}/{$file_group}/{$file_name}";
	}

	public static function createExportDirectories()
	{
		$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");

		if (!is_dir($export_files_dir))
			mkdir($export_files_dir, 0777);

		return true;
	}

	public static function sendExportFile($file)
	{
		$exportDir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");
		$filePath = SJB_Path::combine($exportDir, $file);
		
		for ($i = 0; $i < ob_get_level(); $i++) {
			ob_end_clean();
		}
		@ini_set('zlib.output_compression', 0);
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename=" . $file);
		header("Content-Length: " . filesize($filePath));
		readfile($filePath);
		SJB_Filesystem::delete($exportDir);
		exit();
	}
}
