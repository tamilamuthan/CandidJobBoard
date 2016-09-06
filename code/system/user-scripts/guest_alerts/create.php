<?php

class SJB_GuestAlerts_Create extends SJB_Function
{
	public function execute()
	{
		$searchID = SJB_Request::getVar('searchId', '');
		$criteria = array(
			'active' => array(
				'equal' => 1
			),
			'listing_type' => array(
				'equal' => 'Job'
			),
		);
		if ($searchID) {
			$criteriaSaver = new SJB_ListingCriteriaSaver($searchID);
			$criteria = $criteriaSaver->getCriteria();
		}

		$tp = SJB_System::getTemplateProcessor();
		$isFormSubmitted = SJB_Request::getVar('action');
		$guestAlert = new SJB_GuestAlert($_REQUEST);
		if (SJB_Authorization::isUserLoggedIn() && !$guestAlert->getPropertyValue('email')) {
			$userInfo = SJB_UserManager::getCurrentUserInfo();
			$guestAlert->setPropertyValue('email', $userInfo['username']);
		}
		$form = new SJB_Form($guestAlert);
		$form->registerTags($tp);
		$errors = array();
		$template = 'create.tpl';

		if ($isFormSubmitted && $form->isDataValid($errors)) {
			$guestAlert->addDataProperty(serialize($criteria));
			$guestAlert->save();
			$tp->assign('email', $guestAlert->getAlertEmail());
			$template = 'alert_created.tpl';
		} else {
			$form_fields = $form->getFormFieldsInfo();
			$tp->assign('form_fields', $form_fields);
			$tp->assign('searchId', $searchID);
			$tp->assign('errors', $errors);
		}

		$tp->display($template);
	}
}
