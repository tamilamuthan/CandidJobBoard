<?php

class SJB_Admin_Payment_ManageInvoices extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_invoices');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$template = SJB_Request::getVar('template', 'manage_invoices.tpl');
		$searchTemplate = SJB_Request::getVar('search_template', 'invoice_search_form.tpl');
		$action = SJB_Request::getVar('action_name');
		if (!empty($action)) {
			$invoicesSIDs = SJB_Request::getVar('invoices', array());
			$_REQUEST['restore'] = 1;
			switch ($action) {
				case  'paid':
					foreach (array_keys($invoicesSIDs) as $invoiceSID) {
						SJB_InvoiceManager::markPaidInvoiceBySID($invoiceSID);
						SJB_PromotionsManager::markPromotionAsPaidByInvoiceSID($invoiceSID);
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-invoices/");
					break;
				case  'unpaid':
					foreach (array_keys($invoicesSIDs) as $invoiceSID)
						SJB_InvoiceManager::markUnPaidInvoiceBySID($invoiceSID);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-invoices/');
					break;
				case 'delete':
					foreach (array_keys($invoicesSIDs) as $invoiceSID)
						SJB_InvoiceManager::deleteInvoiceBySID($invoiceSID);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-invoices/');
					break;
				default:
					unset($_REQUEST['restore']);
					break;
			}
		}

		/***************************************************************/
		$_REQUEST['action'] = 'search';
		$invoice = new SJB_Invoice(array());

		$searchFormBuilder = new SJB_SearchFormBuilder($invoice);
		$criteriaSaver = new SJB_InvoiceCriteriaSaver();
		if (isset($_REQUEST['restore']))
			$_REQUEST = array_merge($_REQUEST, $criteriaSaver->getCriteria());
		$criteria = $searchFormBuilder->extractCriteriaFromRequestData($_REQUEST, $invoice);
		$searchFormBuilder->setCriteria($criteria);
		$searchFormBuilder->registerTags($tp);
		$tp->display($searchTemplate);

		/********************** S O R T I N G *********************/
		$paginator = new SJB_InvoicePagination();
		$innerJoin = false;
		if ($paginator->sortingField == 'username') {
			$innerJoin = array('users' => array('sort_field' => array(SJB_UserGroup::JOBSEEKER => 'username', SJB_UserGroup::EMPLOYER => 'CompanyName'), 'join_field' => 'sid', 'join_field2' => 'user_sid', 'main_table' => 'invoices','join' => 'LEFT JOIN'));
		}
		$searcher = new SJB_InvoiceSearcher(array('limit' => ($paginator->currentPage - 1) * $paginator->itemsPerPage, 'num_rows' => $paginator->itemsPerPage), $paginator->sortingField, $paginator->sortingOrder, $innerJoin);

		$foundInvoices = array();
		$foundInvoicesInfo = array();

		if (SJB_Request::getVar('action', '') == 'search') {
			$foundInvoices = $searcher->getObjectsByCriteria($criteria);
			if (empty($foundInvoices) && $paginator->currentPage != 1) {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-invoices/?page=1');
			}
			$criteriaSaver->setSession($_REQUEST, $searcher->getFoundObjectSIDs());
		} elseif (isset($_REQUEST['restore'])) {
			$foundInvoices = $criteriaSaver->getObjectsFromSession();
		}
		foreach ($foundInvoices as $id => $invoice) {
			$userSID = $invoice->getPropertyValue('user_sid');
			$foundInvoices[$id] = $invoice;
			$foundInvoicesInfo[$invoice->getSID()] = SJB_InvoiceManager::getInvoiceInfoBySID($invoice->getSID());
			$foundInvoicesInfo[$invoice->getSID()]['user'] = SJB_UserManager::getUserInfoBySID($userSID);
		}

		/****************************************************************/
		$paginator->setItemsCount($searcher->getAffectedRows());

		$form_collection = new SJB_FormCollection($foundInvoices);
		$form_collection->registerTags($tp);

		$tp->assign('paginationInfo', $paginator->getPaginationInfo());
		$tp->assign("found_invoices", $foundInvoicesInfo);
		$tp->display($template);
	}
}
