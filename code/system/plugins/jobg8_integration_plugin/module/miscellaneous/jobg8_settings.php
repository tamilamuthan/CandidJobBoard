<?php

class SJB_Admin_Miscellaneous_JobG8Settings extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_jobg8plugin');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$template = __DIR__ . '/jobg8_settings.tpl';
		$action = SJB_Request::getVar('action');
		$formSubmitted = SJB_Request::getVar('submit');
		switch ($action) {
			case'install':
				SJB_Event::dispatch('installJobG8');
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
				break;
			case'mapping':
				$mappingType = SJB_Request::getVar('type');
				$mappingField = SJB_Request::getVar('mappingField');
				$sjbFieldValues = SJB_Request::getVar($mappingType, array());
				$allowedMappingFields = SJB_Request::getVar('allow', array());
				$mapper = new JobG8_Mapper();
				if (SJB_Request::getVar('changeMappingField')) {
					$mapper->saveMappingField($mappingType, $mappingField);
				}
				if ($formSubmitted && !$mapper->isAllFieldMappedByType($sjbFieldValues, $mappingType, $allowedMappingFields)) {
					foreach ($sjbFieldValues as $sjbFieldSID => $sjbFieldValue) {
						if (is_array($sjbFieldValue)) {
							$sjbFieldValue = implode(',', $sjbFieldValue);
						}
						$allowedMappingField = empty($allowedMappingFields[$sjbFieldSID]) ? 0 : 1;
						$mapper->setMappingValue($sjbFieldSID, $sjbFieldValue, $allowedMappingField);
					}
					if ($formSubmitted == 'save') {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					}
				}
				$categoryFieldSID = SJB_ListingFieldManager::getListingFieldSIDByID($mapper->categoryMappingFieldID);
				$employmentTypeFieldSID = SJB_ListingFieldManager::getListingFieldSIDByID($mapper->employmentMappingFieldID);
				$errors = $mapper->errors;
				$tp->assign('categoryMappingFieldValues', $mapper->getMappingInfoByType($mapper::CATEGORY_MAPPING_TYPE));
				$tp->assign('employmentMappingFieldValues', $mapper->getMappingInfoByType($mapper::EMPLOYMENT_MAPPING_TYPE));
				$tp->assign('sjbCategories', SJB_ListingFieldDBManager::getListValuesBySID($categoryFieldSID));
				$tp->assign('sjbEmploymentTypes', SJB_ListingFieldDBManager::getListValuesBySID($employmentTypeFieldSID));
				$tp->assign('categoryMappingFieldID', $mapper->categoryMappingFieldID);
				$tp->assign('employmentMappingFieldID', $mapper->employmentMappingFieldID);
				$tp->assign('errors', $errors);
				$template = __DIR__ . '/../../JobG8/mapping.tpl';
				break;
			case'saveSettings':
				SJB_Settings::updateSettings($_REQUEST);
				if ($formSubmitted == 'save') {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/system/miscellaneous/plugins/');
				}
				break;
		}
		$jobg8Plugin = new JobG8IntegrationPlugin();
		$tp->assign('settings', $jobg8Plugin->pluginSettings());
		$tp->assign('savedSettings', SJB_Settings::getSettings());
		$tp->display($template);
	}
}
