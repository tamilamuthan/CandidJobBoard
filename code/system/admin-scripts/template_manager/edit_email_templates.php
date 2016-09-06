<?php

class SJB_Admin_TemplateManager_EditEmailTemplates extends SJB_Function
{
	/**
	 * @var SJB_TemplateProcessor
	 */
	public $tp;

	/**
	 * @var string
	 */
	protected $successMessage;

	/**
	 * @var string
	 */
	protected $error;

	/**
	 * @var string
	 */
	protected $errors = array();
	/**
	 * @var string
	 */
	protected $template;

	public function __construct($acl, $params, $aclRoleID)
	{
        parent::__construct($acl, $params, $aclRoleID);
		$this->tp = SJB_System::getTemplateProcessor();
		$this->successMessage = '';
		$this->error = '';
		$this->template = 'manage_email_templates.tpl';
	}

	public function execute()
	{
		$errors = array();

		$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
		$etSID = SJB_Array::get($passed_parameters_via_uri, 0);

		if ($etSID) {
			$this->editEmailTemplate($etSID, $errors);
		} else {
			$this->tp->assign('templates', SJB_EmailTemplateEditor::getEmailTemplates());
		}

		if ($errors || $this->errors) {
			$errors = array_merge($errors, $this->errors);
		}

		$this->tp->assign('message', $this->successMessage);
		$this->tp->assign('error', $this->error);
		$this->tp->assign('errors', $errors);
		$this->tp->display($this->template);
	}

	protected function editEmailTemplate($sid, &$errors = array())
	{
		$tplInfo = SJB_EmailTemplateEditor::getEmailTemplateInfoBySID($sid);

		if ($tplInfo) {
			$tplInfo = array_merge($tplInfo, $_REQUEST);
			$emailTemplate = new SJB_EmailTemplate($tplInfo);

			$emailTemplate->setSID($sid);

			$emailTemplate_edit_form = new SJB_Form($emailTemplate);
			$form_is_submitted = SJB_Request::getVar('action');
			
			// php tags are not allowed
			if (SJB_HelperFunctions::findSmartyRestrictedTagsInContent($this->tp, $emailTemplate->getPropertyValue('text')))
				$errors['Text'] = 'Php tags are not allowed';
			if ($form_is_submitted && $emailTemplate_edit_form->isDataValid($errors)) {
				SJB_EmailTemplateEditor::saveEmailTemplate($emailTemplate);
				if ($form_is_submitted == 'save_info') {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-email-templates/' . $emailTemplate->getPropertyValue('group'));
				}
				$this->successMessage = 'You have successfully saved your changes';
			}

			$emailTemplate_edit_form->registerTags($this->tp);

			$this->tp->assign('form_fields', $emailTemplate_edit_form->getFormFieldsInfo());;
			$this->tp->assign('tplInfo', $tplInfo);

			$this->template = 'edit_email_template.tpl';

		} else {
			$this->error = 'INVALID_EMAIL_TEMPLATE_SID_WAS_SPECIFIED';
		}
	}

}

