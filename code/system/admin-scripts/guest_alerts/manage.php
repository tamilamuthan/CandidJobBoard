<?php

class SJB_Admin_GuestAlerts_Manage extends SJB_Function
{
	/**
	 * @var array
	 */
	private $errors = array();

	/**
	 * @var SJB_TemplateProcessor
	 */
	private $tp;

	/**
	 * @var SJB_GuestAlertCriteriaSaver
	 */
	public $criteriaSaver;

	public $criteria;


	public function isAccessible()
	{
		$this->setPermissionLabel('manage_guest_email_alerts');
		return parent::isAccessible();
	}

	public function execute()
	{
		$action = SJB_Request::getVar('action_name');
		if (!empty($action)) {
			$guestAlertsSIDs = SJB_Request::getVar('guestAlerts', array());
			if (is_array($guestAlertsSIDs)) {
				foreach ($guestAlertsSIDs as $guestAlertSID) {
					try {
						$guestAlert = SJB_GuestAlertManager::getObjectBySID($guestAlertSID);

						switch ($action) {
							case 'activate':
								$guestAlert->setStatus(SJB_GuestAlert::STATUS_ACTIVE);
								$guestAlert->update();
								break;
							case 'deactivate':
								$guestAlert->setStatus(SJB_GuestAlert::STATUS_INACTIVE);
								$guestAlert->update();
								break;
							case 'delete':
								SJB_GuestAlertManager::deleteGuestAlertBySID($guestAlert->getSID());
								break;
						}
					} catch (Exception $e) {
						$translatedErrorMessage = SJB_I18N::getInstance()->gettext('Backend', $e->getMessage());
						array_push($this->errors, $translatedErrorMessage . ': ' . $guestAlertSID);
					}
				}
			}
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/guest-alerts/');
		}

		$this->tp = SJB_System::getTemplateProcessor();

		$guestAlert = new SJB_GuestAlert(array());
		$guestAlert->addSubscriptionDateProperty();
		$guestAlert->addStatusProperty();
		$guestAlert->addProperty(
			array(
				'id' => 'signed_up',
				'caption' => 'Date',
				'type' => 'list',
				'list_values' => array(
					array(
						'id' => '0',
						'caption' => 'Today',
					),
					array(
						'id' => '7',
						'caption' => 'Last 7 days',
					),
					array(
						'id' => '14',
						'caption' => 'Last 14 days',
					),
					array(
						'id' => '30',
						'caption' => 'Last 30 days',
					),
				),
				'is_required' => false,
				'is_system' => true,
				'order' => 1000000,
			)
		);

		$searchFormBuilder = new SJB_SearchFormBuilder($guestAlert);
		$this->criteriaSaver = new SJB_GuestAlertCriteriaSaver();

		if (isset($_REQUEST['restore'])) {
			$_REQUEST = array_merge($_REQUEST, $this->criteriaSaver->getCriteria());
		}

		$this->criteria = $searchFormBuilder->extractCriteriaFromRequestData($_REQUEST, $guestAlert);
		$searchFormBuilder->setCriteria($this->criteria);
		$searchFormBuilder->registerTags($this->tp);

		if (isset($_REQUEST['signed_up']['equal'])) {
			$period = $_REQUEST['signed_up']['equal'];
			$i18n = SJB_I18N::getInstance();
			$_REQUEST['subscription_date']['not_less'] = $i18n->getDate(date('Y-m-d', strtotime("- {$period} days")));
			unset ($_REQUEST['signed_up']);
			$this->criteria = $searchFormBuilder->extractCriteriaFromRequestData($_REQUEST, $guestAlert);
		}

		if (SJB_Request::getVar('action', '') == 'search') {
			$_REQUEST['restore'] = 1;
		}

		$paginator = new SJB_GuestAlertsManagePagination();
		$limit = array('limit' => ($paginator->currentPage - 1) * $paginator->itemsPerPage, 'num_rows' => $paginator->itemsPerPage);
		$searcher = new SJB_GuestAlertSearcher($limit, $paginator->sortingField, $paginator->sortingOrder);

		$foundGuestAlerts = $searcher->getObjectsSIDsByCriteria($this->criteria);
		$this->criteriaSaver->setSession($_REQUEST, $searcher->getFoundObjectSIDs());
		foreach ($foundGuestAlerts as $id => $guestAlertSID) {
			$foundGuestAlerts[$id] = SJB_GuestAlertManager::getGuestAlertInfoBySID($guestAlertSID);
		}

		$paginator->setItemsCount($searcher->getAffectedRows());
		$this->tp->assign('paginationInfo', $paginator->getPaginationInfo());

		$this->tp->display('search_form.tpl');
		$this->tp->assign('searchFields', $this->getSearchFieldsForTemplate());
		$this->tp->assign('errors', $this->errors);
		$this->tp->assign('guestAlerts', $foundGuestAlerts);

		$this->tp->display('manage.tpl');
	}

	public function getSearchFieldsForTemplate()
	{
		$searchFields = '';
		foreach ($_REQUEST as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $fieldName => $fieldValue)
					$searchFields .= "&{$key}[{$fieldName}]={$fieldValue}";
			}
		}
		return $searchFields;
	}
}
