<?php

include_once('PHPMailer/class.phpmailer.php');

class SJB_Email
{
	protected $email;
	
	function __construct($recipient_email, $data = array())
	{
		$this->email = new SJB_EmailInternal($recipient_email, $data);
	}
	
	function __call($method, $params)
	{
		$result = call_user_func_array(array($this->email, $method), $params);
		if ($method == 'send' && is_array($result))  {
			$result		= isset($result['status'])		? $result['status']		: false;
		}
		return $result;
	}
	
	function __get($property) 
	{
		return $this->email->$property;
	}
	
	function __set($property, $value)
	{
		$this->email->$property = $value;
	}
}


class SJB_EmailInternal
{
	var $mail = NULL;
	var $text = NULL;

	const EMAIL_DATA_LABEL = 'emailData';

	public function setText($text)
	{
		$this->text = $text;
	}

	public function setSubject($subject)
	{
		$this->subject = $subject;
	}

	var $subject 		 = NULL;
	var $recipient_email = NULL;

	var $reply_to = NULL;
	var $fileAttachment = null;
	
	private $cc = array();
	private $fromName = '';
	private $fromEmail = '';

	/**
	 * @var SJB_I18N
	 */
	public $i18n;

	function SJB_EmailInternal($recipient_email, $data = array())
	{
		$this->recipient_email = $recipient_email;
		$tp = SJB_System::getTemplateProcessor();
		$at = $tp->getSystemAccessType();
		$tp->setSystemAccessType('user');
		$tp->assign('siteUrl', SJB_H::getCustomDomainUrl());
		foreach ($data as $key => $value) {
			if (self::EMAIL_DATA_LABEL != $key) {
				$tp->assign($key, $value);
			}
		}
		if (!empty($data[self::EMAIL_DATA_LABEL])) {
			foreach ($data[self::EMAIL_DATA_LABEL] as $emailDataKey => $emailDataVal) {
				$data[self::EMAIL_DATA_LABEL][$emailDataKey] = $tp->fetch('eval:' . $emailDataVal);
			}
			$this->subject = $data[self::EMAIL_DATA_LABEL]['subject'];
			$this->text = $data[self::EMAIL_DATA_LABEL]['message'];
		}

		$tp->setSystemAccessType($at);
	}

	/**
	 * @param array $value
	 * @param SJB_TemplateProcessor $tp
	 */
	protected function parseEmailData(&$value, SJB_TemplateProcessor $tp)
	{
		foreach ($value as &$emailDataVal) {
			$emailDataVal = $tp->fetch('eval:' . $emailDataVal);
		}
	}

	public function addCC($cc)
	{
		array_push($this->cc, $cc);
	}

	function translate($params, $phrase_id, &$smarty, $repeat)
	{
		if ($repeat) {
			return null; // see Smarty manual
		}

		$this->i18n = SJB_I18N::getInstance();
		$mode = isset($params['mode']) ? $params['mode'] : null;
		$phrase_id = trim($phrase_id);
		$res = $this->i18n->gettext('', $phrase_id, $mode);
		return $this->replace_with_template_vars($res, $smarty);
	}

	function replace_with_template_vars($res, &$smarty)
	{
		if (preg_match_all("/{[$]([a-zA-Z0-9_]+)}/", $res, $matches)) {
			foreach($matches[1] as $varName){
				$value = $smarty->getTemplateVars($varName);
				$res = preg_replace("/{[$]".$varName."}/u",$value,$res);
			}
		}
		return $res;
	}

	function getText()
	{
		return $this->text;
	}
	
	function getCarbonCopy()
	{
		return $this->cc;
	}
	
	function setReplyTo($reply_to)
	{
		$this->reply_to = $reply_to;
	}
	
	function setFile($file)
	{
		$this->fileAttachment = $file;
	}

	function send()
	{
		if (empty($this->recipient_email) || !filter_var($this->recipient_email, FILTER_VALIDATE_EMAIL)) {
			return false;
		}

		try {
			$mailSettings = array(
				'smtp' => SJB_Settings::getSettingByName('smtp'),
				'smtp_host' => SJB_Settings::getSettingByName('smtp_host'),
				'smtp_port' => SJB_Settings::getSettingByName('smtp_port'),
				'smtp_sender' => SJB_Settings::getSettingByName('smtp_sender'),
				'smtp_username' => SJB_Settings::getSettingByName('smtp_username'),
				'smtp_password' => SJB_Settings::getSettingByName('smtp_password'),
				'smtp_security' => SJB_Settings::getSettingByName('smtp_security'),
				'sendmail_path' => SJB_Settings::getSettingByName('sendmail_path'),
				'system_email' => SJB_Settings::getSettingByName('system_email'),
				'FromName' => SJB_Settings::getSettingByName('site_title')
			);
			if (SJB_System::getSystemSettings('isSaas')) {
				$settings = SJB_System::getSystemSettings('env')['SES'];
				$mailSettings = array_merge($mailSettings, array(
					'smtp' => 1,
					'smtp_host' => $settings['smtp_host'],
					'smtp_port' => $settings['smtp_port'],
					'smtp_sender' => $settings['smtp_sender'],
					'smtp_username' => $settings['smtp_user'],
					'smtp_password' => $settings['smtp_pass'],
					'smtp_security' => $settings['smtp_sec'],
				));
				$user = trim(SJB_Settings::getValue('domain'));
				if (empty($user)) {
					$user = SJB_System::getSystemSettings('HTTPHOST');
				}
				$user = preg_replace('/^www\.|\.mysmartjobboard\.\w{3}/ui', '', $user);
				$user = preg_replace('/\.com$/ui', '', $user);
				$this->setFromEmail($user . '@' . $settings['smtp_sender_domain']);
				$mailSettings['smtp_sender'] = $this->getFromEmail();
			}
			$mail = $this->prepareMail($mailSettings);
			$sent = $mail->Send();
			return array('status' => $sent);
		} catch (Exception $e) {
			SJB_Error::getInstance()->addWarning($e->getMessage(), array('exception' => $e));
		}
		return array('status' => false, 'error_msg' => $e->getMessage());
	}

	public function prepareMail($mailSettings)
	{
		$mail = new PHPMailer(true);
		$mail->MsgHTML($this->text);
		$mail->From = $this->getFromEmail() ? $this->getFromEmail() : $mailSettings['system_email'];
		$mail->Sender = $mail->From;
		$mail->FromName = $this->getFromName() ? $this->getFromName() : $mailSettings['FromName'];
		$mail->Subject = $this->subject;
		$mail->AddAddress($this->recipient_email);
		$mail->CharSet = "UTF-8";

		if ($mailSettings['smtp'] == 1) {
			$mail->IsSMTP();
			$mail->Port = $mailSettings['smtp_port'];
			$mail->SMTPAuth = true;
			$mail->Host = $mailSettings['smtp_host'];
			$mail->Username = $mailSettings['smtp_username'];
			$mail->Password = $mailSettings['smtp_password'];
			$mail->Sender = $mailSettings['smtp_sender'];
			if (empty($this->reply_to)) {
				$mail->AddReplyTo($mailSettings['system_email']);
			}
			$smtpSecurity = $mailSettings['smtp_security'];

			if ($smtpSecurity != 'none') {
				$mail->set('SMTPSecure', $smtpSecurity);
			}
		} elseif ($mailSettings['smtp'] == 0) {
			if ($mailSettings['sendmail_path'] != '') {
				$mail->isSendmail();
				$mail->Sendmail =  $mailSettings['sendmail_path'];
			}
		}

		if (!empty($this->cc)) {
			if (is_array($this->cc)) {
				foreach ($this->cc as $cc) {
					$mail->AddCC($cc);
				}
			} else {
				$mail->AddCC($this->cc);
			}
		}

		if (!empty($this->reply_to)) {
			$mail->AddReplyTo($this->reply_to);
		}

		if ($this->fileAttachment) {
			$mail->AddAttachment($this->fileAttachment);
		}
		return $mail;
	}

	public function setFromName($fromName)
	{
		$this->fromName = $fromName;
	}

	public function getFromName()
	{
		return $this->fromName;
	}

	public function setFromEmail($fromEmail)
	{
		$this->fromEmail = $fromEmail;
	}

	public function getFromEmail()
	{
		return $this->fromEmail;
	}

	public function setRecipientEmail($recipient_email)
	{
		$this->recipient_email = $recipient_email;
	}
}

class SJB_EmailNone extends SJB_Email
{
	/**
	 * @param string $emailName
	 * @return bool
	 */
	public function send($emailName = '')
	{
		$i18n = SJB_I18N::getInstance();
		$notificationName = $i18n->gettext('Backend', $emailName);
		$text = $i18n->gettext('Backend', 'email was not sent because template for it was not found.');
		$errorMsg = '"'.$notificationName.'" ' . $text;
		$this->email->setSubject($errorMsg);
		$this->email->setText($errorMsg);
		return false;
	}
}

class SJB_EmailDoNotSend extends SJB_Email
{
	public function __construct()
	{
	}

	public function send()
	{
		return false;
	}
}

