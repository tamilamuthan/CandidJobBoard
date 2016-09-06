<?php

class SJB_Admin_Miscellaneous_Adminpswd extends SJB_Function
{
	public function execute()
	{
		if (SJB_System::getSystemSettings('isSaas')) {
			return;
		}
		$action = SJB_Request::getVar('action', '');
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();

		$formSubmitted = !empty($action) ? true : false;
		$adminCurrentDetails = SJB_AdminPasswordManager::getCurrentAdminDetails();
		$adminDetails = array_merge($adminCurrentDetails, $_REQUEST);

		$admin = new SJB_AdminPassword($adminDetails);
		$admin ->setSID($adminCurrentDetails['sid']);

		$adminPasswordForm = new SJB_Form($admin);
		$adminPasswordForm->registerTags($tp);
		$formFields = $adminPasswordForm->getFormFieldsInfo();
		if ($formSubmitted) {
			if ($action == 'change_admin_account' && $adminPasswordForm->isDataValid($errors)) {
				$oldPassword = $admin->getPropertyValue('password');
				$newPassword = SJB_AdminPasswordManager::getNewPasswordValue($adminDetails);

				if ($adminCurrentDetails['password'] != md5($oldPassword)) {
					$errors['Password'] = 'INVALID_PASSWORD';
				} else {
					if ($newPassword) {
						$admin->setPropertyValue('password', $newPassword);
					}
					$admin->deleteProperty('new_password');
					SJB_AdminPasswordManager::saveAdmin($admin);
					SJB_Session::setValue('username', $admin->getPropertyValue('username'));
				}
			}
		}
		$tp->assign('action', $action);
		$tp->assign('errors', $errors);
		$tp->assign('adminInfo', SJB_AdminPasswordManager::getCurrentAdminDetails());
		$tp->assign("form_fields", $formFields);
		$tp->display("adminpswd.tpl");
	}
}