<?php

class SJB_Admin_Classifieds_EditComplexFields extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_listing_types_and_specific_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$action = SJB_Request::getVar('action', 'list');
		$fieldSID = SJB_Request::getVar('field_sid', false);
		$errors = null;

		if ($fieldSID) {

			$tp->assign('field_sid', $fieldSID);
			$field_info = SJB_ListingFieldManager::getFieldInfoBySID($fieldSID);
			$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($field_info['listing_type_sid']);
			$tp->assign("field_info", $field_info);
			$tp->assign("type_info", $listing_type_info);
			$tp->assign("type_sid", isset($listing_type_info['sid']) ? $listing_type_info['sid'] : false);
			switch ($action) {
				case 'add':
					$form_is_submitted = SJB_Request::getVar('submit_form', false);
					$sid = SJB_Request::getVar('sid', false);
					$request = $_REQUEST;
					if ($sid) {
						$listing_field_info = SJB_ListingFieldDBManager::getListingComplexFieldInfoBySID($sid);
						$request = array_merge($listing_field_info, $request);
					} else {
						return;
					}
					$listing_field = new SJB_ListingComplexField($request);
					if ($sid)
						$listing_field->setSID($sid);
					$add_listing_field_form = new SJB_Form($listing_field);
					$add_listing_field_form->registerTags($tp);
					if ($form_is_submitted && $add_listing_field_form->isDataValid($errors)) {

						$listing_field->addProperty(array
							(
								'id' => 'field_sid',
								'value' => $fieldSID,
								'type' => 'id',
								'is_required' => true,
								'is_system' => true,
							)
						);
						SJB_ListingComplexFieldManager::saveListingField($listing_field);
					}

					if (SJB_Request::getVar('apply') == 'no' && empty($errors)) {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . "/edit-listing-field/edit-fields/?field_sid=" . $fieldSID);
					}

					$add_listing_field_form->registerTags($tp);
					$tp->assign("sid", $listing_field->getSID());
					$tp->assign("field_type", $listing_field->getFieldType());
					$tp->assign("form_fields", $add_listing_field_form->getFormFieldsInfo());
					$tp->assign("errors", $errors);
					$tp->assign("action", $action);
					$tp->display("edit_complex_field.tpl");

					break;
				case 'edit':
					$listing_field_sid = SJB_Request::getVar('sid', 0);
					$listing_field_info = SJB_ListingFieldDBManager::getListingComplexFieldInfoBySID($listing_field_sid);
					$listing_field = new SJB_ListingComplexField($listing_field_info);
					$add_listing_field_form = new SJB_Form($listing_field);
					$add_listing_field_form->registerTags($tp);
					$add_listing_field_form->registerTags($tp);
					$tp->assign("field_type", $listing_field->getFieldType());
					$tp->assign("sid", $listing_field_sid);
					$tp->assign("form_fields", $add_listing_field_form->getFormFieldsInfo());
					$tp->assign("errors", $errors);
					$tp->display("edit_complex_field.tpl");
					break;
//				case 'move_up':
//					$fieldSID = SJB_ListingComplexFieldManager::moveUpFieldBySID($fieldSID);
//					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-listing-field/edit-fields/?field_sid=" . $fieldSID);
//					break;
//				case 'move_down':
//					$fieldSID = SJB_ListingComplexFieldManager::moveDownFieldBySID($fieldSID);
//					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-listing-field/edit-fields/?field_sid=" . $fieldSID);
//					break;
//				case 'delete':
//					$listing_field_sid = SJB_Request::getVar('sid', 0);
//					SJB_ListingComplexFieldManager::deleteListingFieldBySID($listing_field_sid, $fieldSID);
//					$action = 'list';
//					break;
			}

			if ($action == 'list') {
				$listing_fields_info = SJB_ListingComplexFieldManager::getListingFieldsInfoByParentSID($fieldSID);
				$listing_fields = array();
				$fields_info = array();

				foreach ($listing_fields_info as $listing_field_info) {
					$fields_info[] = $listing_field_info;
					$listing_field = new SJB_ListingField($listing_field_info);
					$listing_field->setSID($listing_field_info['sid']);

					$listing_fields[] = $listing_field;
				}

				$form_collection = new SJB_FormCollection($listing_fields);
				$form_collection->registerTags($tp);

				$tp->assign("listing_fields", $fields_info);
				$tp->display("listing_complex_fields.tpl");
			}
		}

	}
}
