<?php

class SJB_AdminNotifications
{
	const EMAIL_TEMPLATE_SID_SND_CONTACT_FORM_MSG = 30;
	const EMAIL_TEMPLATE_SID_PRODUCT_PURCHASE_CONFIRMATION = 25;

	public static function sendContactFormMessage($name, $sEmail, $comments)
	{
		$params = array('name' => $name, 'email' => $sEmail, 'comments' => $comments);
		$admin_email = SJB_Settings::getSettingByName('system_email');
		$email = SJB_EmailTemplateEditor::getEmail($admin_email, self::EMAIL_TEMPLATE_SID_SND_CONTACT_FORM_MSG, $params);
		if ($email) {
			$email->setReplyTo($sEmail);
			return $email->send();
		}
		return null;
	}

	public static function sendProductConfirmationLetter($data)
	{
		$email = SJB_EmailTemplateEditor::getEmail(SJB_Settings::getSettingByName('system_email'), self::EMAIL_TEMPLATE_SID_PRODUCT_PURCHASE_CONFIRMATION, $data);
		return $email->send();
	}

}

