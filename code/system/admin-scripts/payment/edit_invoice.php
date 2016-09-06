<?php

class SJB_Admin_Payment_EditInvoice extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('manage_invoices');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$template = 'edit_invoice.tpl';
		$errors = array();
		$invoiceErrors  = array();
	    $invoiceSID = SJB_Request::getVar('sid', false);
		$action = SJB_Request::getVar('action', false);
		$tcpdfError = SJB_Request::getVar('error', false);
		if ($tcpdfError) {
			$invoiceErrors[] = $tcpdfError;
		}
		$invoiceInfo = SJB_InvoiceManager::getInvoiceInfoBySID($invoiceSID);
		if ($invoiceInfo) {
			$product_info = array();
			if (array_key_exists('custom_info', $invoiceInfo['items'])) {
				$product_info = $invoiceInfo['items']['custom_info'];
			}
			$invoiceInfo = array_merge($invoiceInfo, $_REQUEST);
			$invoiceInfo['items']['custom_info'] = $product_info;
			$invoice = new SJB_Invoice($invoiceInfo);
			$invoice->setSID($invoiceSID);
			$userSID = $invoice->getPropertyValue('user_sid');
			$user = SJB_UserManager::getObjectBySID($userSID);
			$taxInfo = $invoice->getPropertyValue('tax_info');
			$products = array();
			if ($user) {
				$productsSIDs = SJB_ProductsManager::getProductsIDsByUserGroupSID($user->getUserGroupSID());
				foreach ($productsSIDs as $key => $productSID) {
					$products[$key] = SJB_ProductsManager::getProductInfoBySID($productSID);
				}
			}

			$addForm = new SJB_Form($invoice);
			$addForm->registerTags($tp);
			$tp->assign('products', $products);
			$tp->assign('invoice_sid', $invoiceSID);
			$tp->assign('include_tax', $invoiceInfo['include_tax']);
			$tp->assign('user', SJB_UserManager::createTemplateStructureForUser($user));
			if ($action) {
				switch ($action) {
					case 'print':
					case 'download_pdf_version':
						$template = 'print_invoice.tpl';
						$tp->assign('tax', $taxInfo);
						$tp->assign('theme_settings', ThemeManager::getThemeSettings(SJB_Settings::getValue('TEMPLATE_USER_THEME')));
						if ($action == 'download_pdf_version') {
							$template = 'invoice_to_pdf.tpl';
							$filename = 'invoice_' . $invoiceSID . '.pdf';
							try {
								SJB_HelperFunctions::html2pdf($tp->fetch($template), $filename);
								exit();
							} catch(Exception $e) {
								SJB_Error::getInstance()->addWarning($e->getMessage(), array('exception' => $e));
								SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/view-invoice/?sid=' . $invoiceSID . '&error=TCPDF_ERROR');
							}
						}
						break;
				}
			}
			$transactions = SJB_TransactionManager::getTransactionsByInvoice($invoiceSID);
			$tp->assign('tax', $taxInfo);
			$tp->assign('transactions', $transactions);
		} else {
			$tp->assign('action', 'edit');
			$errors[] = 'WRONG_INVOICE_ID_SPECIFIED';
			$template = 'errors.tpl';
		}
		$tp->assign('errors', array_merge($errors, $invoiceErrors));
		$tp->display($template);
	}
}
