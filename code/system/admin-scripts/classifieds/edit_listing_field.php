<?php

class SJB_Admin_Classifieds_EditListingField extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_common_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$listing_field_sid = SJB_Request::getVar('sid', null);
		$tp = SJB_System::getTemplateProcessor();

		if (!is_null($listing_field_sid)) {
			$listing_field_info = SJB_ListingFieldManager::getFieldInfoBySID($listing_field_sid);
			$listing_field_info = array_merge($listing_field_info, $_REQUEST);
			$listing_field = new SJB_ListingField($listing_field_info);
			$listing_field->setSID($listing_field_sid);
			$form_submitted = SJB_Request::getVar('action', '');
//			if (in_array($listing_field->field_type, array( 'multilist', 'list'))) {
//				$sort_by_alphabet = array(
//						'id' => 'sort_by_alphabet',
//						'caption' => 'Sort Values By Alphabet',
//						'value' => (isset($listing_field_info['sort_by_alphabet']) ? $listing_field_info['sort_by_alphabet'] : ''),
//						'type' => 'boolean',
//						'lenght' => '',
//						'is_required' => false,
//						'is_system' => true,
//				);
//				$listing_field->addProperty($sort_by_alphabet);
//			}

			if (in_array($listing_field->field_type, array('multilist'))) {
				$listing_field->addDisplayAsProperty($listing_field_info['display_as']);
			}
			// infil instructions should be the last element in form
			if (!in_array($listing_field->getFieldType(), array('complex','location'))) {
				if ($form_submitted) {
					$listing_field->addInfillInstructions(SJB_Request::getVar('instructions'));
				} else {
					$listing_field->addInfillInstructions((isset($listing_field_info['instructions']) ? $listing_field_info['instructions'] : ''));
				}
			}

			$edit_form = new SJB_Form($listing_field);
			$errors = array();

			if ($form_submitted && $edit_form->isDataValid($errors)) {
				$old_listing_field_id = SJB_Request::getVar('old_listing_field_id', null);
				SJB_ListingFieldManager::saveListingField($listing_field);
				SJB_ListingFieldManager::changeListingPropertyIDs($listing_field_info['id'], $old_listing_field_id);

				if ($form_submitted == 'save_info')
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/listing-fields/');
			}

			$edit_form->registerTags($tp);
			$edit_form->makeDisabled('type');
			if (($listing_field_info['id'] == 'Location') && empty($errors['ID'])) {
				$edit_form->makeDisabled('id');
			}
			$tp->assign('object_sid', $listing_field);
			$tp->assign('form_fields', $edit_form->getFormFieldsInfo());
			$tp->assign('errors', $errors);
			$tp->assign('field_type', $listing_field->getFieldType());
			$tp->assign('listing_field_info', $listing_field_info);
			$tp->assign('field_sid', $listing_field_sid);
			$tp->display('edit_listing_field.tpl');
		}
	}
}
