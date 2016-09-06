<?php

class SJB_InvoiceManager extends SJB_ObjectManager
{
	public static function saveInvoice($invoice)
	{
		$serializedItemsDetails['items'] = $invoice->getPropertyValue('items');
		$products = isset($serializedItemsDetails['items']['products']) ? $serializedItemsDetails['items']['products'] : array();
		$products = implode(',', $products);
		$invoice->addProperty(
			array ( 'id'		=> 'serialized_items_info',
					'type'		=> 'text',
					'value'		=> serialize($serializedItemsDetails),
					'is_system' => true,
			)
		);
		$invoice->addProperty(
				array ( 'id'		=> 'product_sid',
						'type'		=> 'string',
						'value'		=> $products,
						'is_system' => true,
				)
		);
		$invoice->deleteProperty('items');

		$serializedTaxDetails['tax_info'] = $invoice->getPropertyValue('tax_info');
		$invoice->addProperty(
			array ( 'id'		=> 'serialized_tax_info',
					'type'		=> 'text',
					'value'		=> serialize($serializedTaxDetails),
					'is_system' => true,
			)
		);
		$invoice->deleteProperty('tax_info');

		$dateProperty = $invoice->getProperty('date');
		$value = $dateProperty->getValue();
		if (!$dateProperty->type->getConvertToDBDate() && $value != null) {
			$invoice->setPropertyValue('date', SJB_I18N::getInstance()->getDate($value));
		}

		$invoice->setPropertyValue('sub_total', SJB_I18N::getInstance()->getFloat($invoice->getPropertyValue('sub_total')));
		$invoice->setPropertyValue('total', SJB_I18N::getInstance()->getFloat($invoice->getPropertyValue('total')));
		if ($invoice->getSID() && $invoice->getPropertyValue('status') == 'Paid') {
			$invoice->addProperty(
				array ( 'id' => 'status_paid',
						'type' => 'boolean',
						'value' => 1,
						'is_system' => true,
				)
			);
		}
		parent::saveObject('invoices', $invoice);

		if ($value == null) {
			SJB_DB::query('UPDATE `invoices` SET `date`= NOW() WHERE `sid`=?n',$invoice->getSID());
		}
	}

	public static function getInvoiceInfoBySID($invoiceSID)
	{
		$invoice_info = parent::getObjectInfoBySID('invoices', $invoiceSID);
		if (!empty($invoice_info['serialized_items_info'])) {
			$serialized_items_info = unserialize($invoice_info['serialized_items_info']);
			$invoice_info = array_merge($invoice_info, $serialized_items_info);
		}
		if (!empty($invoice_info['serialized_tax_info'])) {
			$serialized_tax_info = unserialize($invoice_info['serialized_tax_info']);
			$invoice_info = array_merge($invoice_info, $serialized_tax_info);
		}
		return $invoice_info;
    }

	public static function getObjectBySID($invoiceSID)
	{
    	$invoiceInfo = SJB_InvoiceManager::getInvoiceInfoBySID($invoiceSID);
    	
		if (is_null($invoiceInfo)) {
    		return null;
		}
    	$invoice = new SJB_Invoice($invoiceInfo);
		$invoice->setSID($invoiceSID);
		return $invoice;
	}

	public static function deleteInvoiceBySID($invoiceSID)
	{
		return SJB_InvoiceManager::deleteObject('invoices', $invoiceSID);
	}

	public static function markPaidInvoiceBySID($invoiceSID)
	{
		$invoiceInfo = self::getInvoiceInfoBySID($invoiceSID);
		if ($invoiceInfo['status_paid'] != 1) {
			$invoice = SJB_InvoiceManager::getObjectBySID($invoiceSID);
			$userSID = $invoice->getPropertyValue('user_sid');
			if (SJB_UserManager::isUserExistsByUserSid($userSID)) {
				$items = $invoice->getPropertyValue('items');
				$productSIDs = $items['products'];
				foreach ($productSIDs as $key => $productSID) {
					if ($productSID != -1) {
						if (SJB_ProductsManager::isProductExists($productSID)) {
							$productInfo = $invoice->getItemValue($key);
							$listingNumber = $productInfo['qty'];
							$contract = new SJB_Contract(array('product_sid' => $productSID, 'numberOfListings' => $listingNumber));
							$contract->setUserSID($userSID);
							$contract->setPrice($items['amount'][$key]);
							if ($contract->saveInDB()) {
								SJB_ListingManager::activateListingsAfterPaid($userSID, $productSID, $contract->getID(), $listingNumber);
								SJB_ShoppingCart::deleteItemsFromCartByUserSID($userSID);
								if ($contract->isFeaturedProfile()) {
									SJB_UserManager::makeFeaturedBySID($userSID);
								}
								SJB_Notifications::sendSubscriptionActivationLetter($userSID, $productInfo, $invoice);
							}
						}
					} else {
						$type = SJB_Array::getPath($items,'custom_info/'. $key .'/type');
						switch ($type) {
							case 'featuredListing':
								$listingId = SJB_Array::getPath($items,'custom_info/' . $key . '/listing_id');
								SJB_ListingManager::makeFeaturedBySID($listingId);
								break;
							case 'activateListing':
								$listingId = SJB_Array::getPath($items,'custom_info/' . $key . '/listing_id');
								SJB_ListingManager::activateListingBySID($listingId);
								break;
						}
					}
				}
			}
			$total = $invoice->getPropertyValue('total');
			if ($total > 0) {
				$gatewayID = $invoice->getPropertyValue('payment_method');
				$gatewayID = isset($gatewayID) ? $gatewayID : 'cash_payment';
				$transactionId = md5($invoiceSID . $gatewayID);
				$transactionInfo = array(
						'transaction_id'=> $transactionId,
						'invoice_sid' => $invoiceSID,
						'amount' => $total,
						'payment_method'=> $gatewayID,
						'user_sid' => $invoice->getPropertyValue('user_sid')
				);
				$transaction = new SJB_Transaction($transactionInfo);
				SJB_TransactionManager::saveTransaction($transaction);
			}
		}
		return SJB_DB::query("UPDATE `invoices` SET `status` = ?s, `status_paid` = 1 WHERE `sid` = ?n", SJB_Invoice::INVOICE_STATUS_PAID, $invoiceSID);
	}

	public static function markUnPaidInvoiceBySID($invoiceSID)
	{
		return SJB_DB::query("UPDATE `invoices` SET `status` = ?s WHERE `sid` = ?n", SJB_Invoice::INVOICE_STATUS_UNPAID, $invoiceSID);
	}

	public static function getPaymentForms($invoice)
	{
		$activeGateways = SJB_PaymentGatewayManager::getActivePaymentGatewaysList();
		$gatewaysFormInfo = array();
		foreach ($activeGateways as $gatewayInfo) {
			$gateway = SJB_PaymentGatewayManager::getObjectByID($gatewayInfo['id']);
			$gatewaysFormInfo[$gateway->getPropertyValue('id')] = $gateway->buildTransactionForm($invoice);
		}
		return $gatewaysFormInfo;
	}

	public static function getExistingInvoiceSID($userSID, $itemsInfo, $taxInfo, $status)
	{
		return SJB_DB::queryValue('select `sid` from `invoices` where `status` = ?s  and `user_sid` = ?n and `serialized_items_info` = ?s and `serialized_tax_info` = ?s',
			$status, $userSID, serialize($itemsInfo), serialize($taxInfo));
	}

	public static function getTotalPrice($subTotal, $taxAmount)
	{
		return $subTotal + $taxAmount;
	}

	public static function generateInvoice($items, $userSID, $subTotalPrice)
	{
		$taxInfo = SJB_TaxesManager::getTaxInfoByPrice($subTotalPrice);
		$taxAmount = 0;
		if (!empty($taxInfo['tax_amount'])) {
			$taxAmount = $taxInfo['tax_amount'];
		}
		$totalPrice = SJB_InvoiceManager::getTotalPrice($subTotalPrice, $taxAmount);
		$invoiceSID = null;
		if ($totalPrice > 0) {
			$invoiceSID = SJB_InvoiceManager::getExistingInvoiceSID($userSID, $items, $taxInfo, SJB_Invoice::INVOICE_STATUS_UNPAID);
		}
		if (!$invoiceSID) {
			$invoiceInfo = array(
				'user_sid' => $userSID,
				'include_tax' => !empty($taxInfo) ? 1 : 0,
				'total' => $totalPrice,
				'sub_total' => $subTotalPrice,
				'status' => $totalPrice == 0 ? SJB_Invoice::INVOICE_STATUS_VERIFIED : SJB_Invoice::INVOICE_STATUS_UNPAID,
				'tax_info' => $taxInfo,
				'items' => $items,
			);
			$invoice = new SJB_Invoice($invoiceInfo);
			SJB_InvoiceManager::saveInvoice($invoice);
			$invoiceSID = $invoice->getSID();
		}
		return $invoiceSID;
	}

	public static function getInvoicesInfo()
	{
		//TODO: можно ускорить и сделать так же как в листингах
		$res = array();
		$periods = array(
			'Today' => '`i`.`date` >= CURDATE()',
			'Last 7 days' => '`i`.`date` >= date_sub(curdate(), interval 7 day)',
			'Last 30 days' => '`i`.`date` >= date_sub(curdate(), interval 30 day)',
			'Total' => '1=1',
		);

		foreach ($periods as $key => $value) {
			$res[$key] = SJB_DB::queryValue("SELECT IFNULL(SUM(i.total), 0) AS `payment` FROM `invoices` i WHERE {$value} AND `status` = 'Paid'");
		}
		return $res;
	}

	public static function getInvoiceQuantityByProductSID($productSID)
	{
		return SJB_DB::queryValue("SELECT COUNT(*) FROM invoices WHERE FIND_IN_SET(?s, `product_sid`)", $productSID);
	}
}


