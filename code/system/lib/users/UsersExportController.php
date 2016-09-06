<?php

class SJB_UsersExportController
{
	const USER_OPTIONS_INDEX = '__options__';

	public static function createUser($userGroupSID)
	{
		$userGroupSID = SJB_UserGroupManager::getUserGroupSIDByID($userGroupSID);
		$user = new SJB_User(array(), $userGroupSID);
		$user->addUserGroupProperty();
		$user->addRegistrationDateProperty();
		$user->addProductProperty(null, $userGroupSID);
		return $user;
	}

	public static function getSearchPropertyAliases()
	{
		$property_aliases = new SJB_PropertyAliases();

		$property_aliases->addAlias(array(
				'id' => 'user_group',
				'real_id' => 'user_group_sid',
				'transform_function' => 'SJB_UserGroupManager::getUserGroupSIDByID'
			)
		);

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

		$property_aliases->addAlias(array(
				'id' => 'user_group',
				'real_id' => 'user_group_sid',
				'transform_function' => 'SJB_UserGroupManager::getUserGroupNameBySID'
			)
		);

		$property_aliases->addAlias(array(
				'id' => 'product',
				'real_id' => 'sid',
				'transform_function' => 'SJB_ContractManager::getAllContractsInfoByUserSID'
			)
		);
		return $property_aliases;
	}

	public static function getExportData(array $usersSids, array $exportProperties, SJB_PropertyAliases $aliases)
	{
		$exportData = new SJB_ExportIterator;
		$exportData->setArray($usersSids);
		$exportData->setAdditionalParameters(array('exportProperties' => $exportProperties, 'aliases' => $aliases));
		$exportData->setCallbackFunction('SJB_UsersExportController::generateExportData');
		return $exportData;
	}
	
	public static function generateExportData($parameters)
	{
		$exportProperties = $aliases = $sid = null;
		
		extract($parameters);
		$exportData     = array();
		$userInfo       = SJB_UserManager::getUserInfoBySID($sid);
		$userInfo['id'] = $userInfo['sid'];
		$userInfo       = $aliases->changePropertiesInfo($userInfo);
		
		if (!empty($userInfo['product'])) {
			$contracts = $userInfo['product'];
			$userInfo['product'] = array();
			foreach ($contracts as $contract) {
				$productInfo = SJB_ProductsManager::getProductInfoBySID($contract['product_sid']);
				if ($productInfo) {
					$extraInfo = !empty($contract['serialized_extra_info']) ? unserialize($contract['serialized_extra_info']) : null;
					$userInfo['product'][] = serialize(
						array(
							'name'               => $productInfo['name'],
							'creation_date'      => $contract['creation_date'],
							'expired_date'       => $contract['expired_date'],
							'price'              => $contract['price'],
							'number_of_postings' => $contract['number_of_postings'],
							'number_of_listings' => $extraInfo ? $extraInfo['number_of_listings'] : 0,
							'status'             => $contract['status'],
						)
					);
				}
			}
			$userInfo['product'] = implode(',', $userInfo['product']);
		} else {
			$userInfo['product'] = '';
		}
		
		$exportData[$sid][self::USER_OPTIONS_INDEX]['user_group_id'] = SJB_Array::get($userInfo, 'user_group');
		foreach ($exportProperties as $propertyId => $value) {
			$exportData[$sid][$propertyId] = isset($userInfo[$propertyId]) ? $userInfo[$propertyId] : null;
		}
		
		self::changeListProperties($exportData);
		self::cleanOptions($exportData);
		self::changeFileProperties($exportProperties, $exportData, 'file');
		self::changeFileProperties($exportProperties, $exportData, 'Logo');
		self::changeLocationProperties($exportProperties, $exportData);
		
		return $exportData[$sid];
	}

	private static function changeListProperties(&$export_data)
	{
		$listFieldsInfo = SJB_UserProfileFieldManager::getFieldsInfoByType('list');
		$multilistFieldsInfo = SJB_UserProfileFieldManager::getFieldsInfoByType('multilist');
		$fieldsInfo = array_merge($listFieldsInfo, $multilistFieldsInfo);
		foreach ($export_data as $user_sid => $property) {
			$userGroupSID = (int)SJB_UserGroupManager::getUserGroupSIDByName(SJB_Array::get($property[self::USER_OPTIONS_INDEX], 'user_group_id'));
			foreach ($fieldsInfo as $field_info) {
				$fieldID = SJB_Array::get($field_info, 'id');
				$fieldUserGroupSID = (int)SJB_Array::get($field_info, 'user_group_sid');
				if ($fieldUserGroupSID === $userGroupSID && !empty($property[$fieldID])) {
					$fieldInfo = SJB_UserProfileFieldManager::getFieldInfoBySID($field_info['sid']);
					switch (strval($fieldInfo['type'])) {
						case 'list':
							foreach ($fieldInfo['list_values'] as $listValues) {
								if ($listValues['id'] == $property[$field_info['id']]) {
									$export_data[$user_sid][$field_info['id']] = $listValues['caption'];
									break;
								}
							}
							break;
						case 'multilist':
							$multilistValues = explode(',', $property[$field_info['id']]);
							$multilistDisplayValues = array();
							foreach ($fieldInfo['list_values'] as $listValues) {
								if (in_array($listValues['id'], $multilistValues))
									$multilistDisplayValues[] = $listValues['caption'];
							}
							$export_data[$user_sid][$field_info['id']] = implode(',', $multilistDisplayValues);
							break;
					}
				}
			}
		}
	}

	private static function changeFileProperties(&$exportProperties, &$exportData, $fileType)
	{
		$filePropertiesInfo = SJB_UserProfileFieldManager::getFieldsInfoByType($fileType);
		foreach ($filePropertiesInfo as $propertyInfo) {
			if (isset($exportProperties[$propertyInfo['id']])) {
				foreach ($exportData as $userSid => $property) {
					$exportData[$userSid][$propertyInfo['id']] = SJB_UploadPictureManager::getUploadedFileLink($exportData[$userSid][$propertyInfo['id']]);
				}
			}
		}
	}
	
	private static function changeLocationProperties(&$exportProperties, &$exportData)
	{
		$locationFieldsInfo = SJB_UserProfileFieldManager::getFieldsInfoByType('location');
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
				foreach ($exportData as $userSID => $property) {
					if (isset($property[$fieldInfo['id']]) && is_array($property[$fieldInfo['id']])) {
						$propertyLocation = array();
						foreach ($property[$fieldInfo['id']] as $locationField => $fieldValue) {
							$propertyLocation[$fieldInfo['id'].'.'.$locationField] = $fieldValue;
						}
						unset($property[$fieldInfo['id']]);
						ksort($propertyLocation);
						$exportData[$userSID] = array_merge($property, $propertyLocation);
					}
				}
			}
		}
	}

	private static function cleanOptions(&$export_data)
	{
		foreach ($export_data as &$properties)
			unset($properties[self::USER_OPTIONS_INDEX]);
	}
}
