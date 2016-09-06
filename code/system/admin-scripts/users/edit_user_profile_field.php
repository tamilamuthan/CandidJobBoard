<?php

class SJB_Admin_Users_EditUserProfileField extends SJB_Function
{
	public function execute()
	{
		// fixme: deprecated
		return;
		$tp = SJB_System::getTemplateProcessor();
		$user_group_sid = SJB_Request::getVar('user_group_sid', null);
		$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
		$user_profile_field_sid = SJB_Request::getVar('sid', null);

		if (!is_null($user_profile_field_sid)) {
			$user_profile_field_info = SJB_UserProfileFieldManager::getFieldInfoBySID($user_profile_field_sid);
			$user_profile_field_old_id = $user_profile_field_info['id'];
			$user_profile_field_info = array_merge($user_profile_field_info, $_REQUEST);
			$user_profile_field = new SJB_UserProfileField($user_profile_field_info);
			$user_profile_field->setSID($user_profile_field_sid);
			$user_profile_field->setUserGroupSID($user_group_sid);

//			if (in_array($user_profile_field->field_type, array( 'multilist', 'list'))) {
//				$sort_by_alphabet = array(
//					'id' => 'sort_by_alphabet',
//					'caption' => 'Sort Values By Alphabet',
//					'value' => (isset($user_profile_field_info['sort_by_alphabet']) ? $user_profile_field_info['sort_by_alphabet'] : ''),
//					'type' => 'boolean',
//					'lenght' => '',
//					'is_required' => false,
//					'is_system' => true,
//				);
//				$user_profile_field->addProperty($sort_by_alphabet);
//			}

			$edit_form = new SJB_Form($user_profile_field);
			$form_submitted = SJB_Request::getVar('action');

			if (in_array($user_profile_field->field_type, array('multilist'))) {
				$user_profile_field->addDisplayAsProperty($user_profile_field_info['display_as']);
			}
			// infill instructions should be the last element in form
			if (!in_array($user_profile_field->getFieldType(), array('complex','location'))) {
				if ($form_submitted) {
					$user_profile_field->addInfillInstructions(SJB_Request::getVar('instructions'));
				} else {
					$user_profile_field->addInfillInstructions((isset($user_profile_field_info['instructions']) ? $user_profile_field_info['instructions'] : ''));
				}
			}

			$edit_form = new SJB_Form($user_profile_field);

			$errors = array();

			if ($form_submitted && $edit_form->isDataValid($errors)) {
				SJB_UserProfileFieldManager::saveUserProfileField($user_profile_field);
				$user_profile_field_new_id = $user_profile_field_info['id'];
				if ($user_profile_field_old_id != $user_profile_field_new_id) {
					SJB_UserProfileFieldManager::changeUserPropertyIDs($user_group_sid, $user_profile_field_old_id, $user_profile_field_new_id);
				}

				if ($form_submitted == 'save_info') {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-user-profile/?user_group_sid=' . $user_group_sid);
				}
			}
			if (($user_profile_field_info['id'] == 'Location') && empty($errors['ID'])) {
				$edit_form->makeDisabled('id');
			}
			$edit_form->registerTags($tp);
			$edit_form->makeDisabled('type');
			$tp->assign('user_group_sid', $user_group_sid);
			$tp->assign('form_fields', $edit_form->getFormFieldsInfo());
			$tp->assign('errors', $errors);
			$tp->assign('field_type', $user_profile_field->getFieldType());
			$tp->assign('user_profile_field_info', $user_profile_field_info);
			$tp->assign('user_profile_field_sid', $user_profile_field_sid);
			$tp->assign('user_group_info', $user_group_info);
			$tp->display('edit_user_profile_field.tpl');
		}

	}
}
