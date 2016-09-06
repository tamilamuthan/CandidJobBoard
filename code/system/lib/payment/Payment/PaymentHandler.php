<?php

class SJB_PaymentHandler
{
	/**
	 * @var null|int
	 */
	private $invoiceSID = null;
	private $product = null;
	private $gatewayID = '';
	
	public function __construct($invoiceSID, $gatewayID)
	{
		$this->invoiceSID = $invoiceSID;
		$this->gatewayID = $gatewayID;
	}
	
	public function setProduct($product)
	{
		$this->product = $product;
	}
	
	public function createContract($userSID, $invoiceID, $status = 'active')
	{
		$listingNumber = !empty($this->product['qty'])?$this->product['qty']:null;
		$contract = new SJB_Contract(array(
			'product_sid' => $this->product['sid'],
			'gateway_id' => $this->gatewayID,
			'invoice_id' => $invoiceID,
			'numberOfListings' => $listingNumber
		));
		if ($invoiceID) {
			SJB_ContractManager::deletePendingContractByInvoiceID($invoiceID, $userSID, $this->product['sid']);
		}
		$contract->setUserSID($userSID);
		$contract->setPrice($this->product['amount']);
		$contract->setStatus($status);
		if ($contract->saveInDB()) {
			SJB_ListingManager::activateListingsAfterPaid($userSID, $this->product['sid'], $contract->getID(), $listingNumber);
			SJB_ShoppingCart::deleteItemsFromCartByUserSID($userSID);
			if ($contract->isFeaturedProfile()) {
				SJB_UserManager::makeFeaturedBySID($userSID);
			}
			SJB_Notifications::sendSubscriptionActivationLetter($userSID, $this->product, SJB_InvoiceManager::getObjectBySID($invoiceID));
		}
	}
	
	public function deleteContract($invoiceID, $productSID, $userSID)
	{
		$contractID = SJB_ContractManager::getContractIDByInvoiceID($invoiceID, $productSID, $userSID);	
		if ($contractID) {
			SJB_ContractManager::deleteContract($contractID, $userSID);
		}
	}
	
	public function activateListing()
	{
		SJB_ListingManager::activateListingBySID(explode(",", $this->product['listings_ids']));
	}
	
	public function deactivateListing()
	{
		SJB_ListingManager::deactivateListingBySID(explode(",", $this->product['listings_ids']));
	}
	
	public function makeFeatured()
	{
		SJB_ListingManager::makeFeaturedBySID($this->product['listing_id']);
	}
	
	public function unmakeFeatured()
	{
		SJB_ListingManager::unmakeFeaturedBySID($this->product['listing_id']);
	}
}
