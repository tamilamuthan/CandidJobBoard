<?php

class SJB_Payment_PaymentPage extends SJB_Function
{
	public function execute()
	{
		$invoiceSID = SJB_Request::getVar('invoice_sid', null, 'default', 'int');
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', false);
		$checkPaymentErrors = array();
		$currentUser = SJB_UserManager::getCurrentUser();
		if ($action == 'pay_for_products') {
			$subTotalPrice = SJB_Request::getVar('sub_total_price', 0);
			$products = SJB_ShoppingCart::getAllProductsByUserSID($currentUser->getSID());
			$codeInfo = array();$index = 1;
			$items = array();
			foreach ($products as $product) {
				$product_info = unserialize($product['product_info']);
				$items['products'][$index] = $product_info['sid'];
				$qty = !empty($product_info['number_of_listings'])?$product_info['number_of_listings']:null;
				if ($qty > 0) {
					$items['price'][$index] = round($product_info['price']/$qty, 2);
				} else {
					$items['price'][$index] = round($product_info['price'], 2);
				}
				$items['amount'][$index] = $product_info['price'];
				$items['custom_item'][$index] = '';
				$items['qty'][$index] = $qty;
				$index++;
				SJB_PromotionsManager::preparePromoCodeInfoByProductPromoCodeInfo($product_info, $codeInfo);
			}
			$userSID = $currentUser->getSID();
			$invoiceSID = SJB_InvoiceManager::generateInvoice($items, $userSID, $subTotalPrice);
			SJB_PromotionsManager::addCodeToHistory($codeInfo, $invoiceSID, $userSID);
		}
		if (!is_null($invoiceSID)) {
			$invoice_info = SJB_InvoiceManager::getInvoiceInfoBySID($invoiceSID);
			if (empty($invoice_info)) {
				echo SJB_System::executeFunction('miscellaneous', '404_not_found');
				return;
			}
			$invoice = new SJB_Invoice($invoice_info);
			if (SJB_PromotionsManager::isPromoCodeExpired($invoiceSID)) {
				$checkPaymentErrors['PROMOTION_TOO_MANY_USES'] = true;
			} else {
				$invoice->setSID($invoiceSID);
				if (count($invoice->isValid($invoiceSID)) == 0) {
					$invoiceUserSID = $invoice->getPropertyValue('user_sid');
					$currentUserSID = SJB_UserManager::getCurrentUserSID();
					if ($invoiceUserSID === $currentUserSID) {
						$payment_gateway_forms = SJB_InvoiceManager::getPaymentForms($invoice);
						$productNames = explode(',', $invoice->getProductNames());
						$tp->assign('productsNames', $productNames);
						$tp->assign('gateways', $payment_gateway_forms);
						$tp->assign('invoice_info', $invoice_info);
					} else {
						$checkPaymentErrors['NOT_OWNER'] = true;
					}
				} else {
					$checkPaymentErrors['WRONG_INVOICE_PARAMETERS'] = true;
				}
			}
			$tp->assign('checkPaymentErrors', $checkPaymentErrors);
			$tp->assign('selectedGateway', SJB_Request::getVar('gateway'));
			$tp->display('invoice_payment_page.tpl');
		} else {
			echo SJB_System::executeFunction('miscellaneous', '404_not_found');
		}
	}
}
