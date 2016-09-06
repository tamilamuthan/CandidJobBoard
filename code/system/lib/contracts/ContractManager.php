<?php

class SJB_ContractManager
{
	public static function deleteContract($contract_id, $user_sid = false)
	{
        $contract = new SJB_Contract( array('contract_id' => $contract_id, 'user_sid' => $user_sid) );
        if ($contract->isFeaturedProfile()) {
	        $allContracts = self::getAllContractsInfoByUserSID($user_sid);
	        $featured = 0;
	        foreach ($allContracts as $userContract) {
	        	if ($userContract['id'] != $contract_id) {
		        	$userContract = new SJB_Contract( array('contract_id' => $userContract['id'], 'user_sid' => $user_sid) );
		        	if ($userContract->isFeaturedProfile()) {
		        	 	$featured = 1; 
		        	 	break;
		        	 }
	        	}
	        }
	        if ($featured == 0) 
	        	SJB_UserManager::removeFromFeaturedBySID($user_sid);
        }
        $permissions = SJB_Acl::getInstance();
        $permissions->clearPermissions('contract', $contract_id);
        return $contract->delete();  
    }
    
	public static function deleteAllContractsByUserSID($user_sid)
	{
		return SJB_DB::query("DELETE FROM `contracts` WHERE `user_sid`=?n", $user_sid);
    }
    
    public static function deletePendingContractByInvoiceID($invoiceID, $userSID, $productSID)
    {
		return SJB_DB::query("DELETE FROM `contracts` WHERE `invoice_id` = ?s AND `user_sid` = ?s AND `product_sid` = ?n AND `status` = 'pending'", $invoiceID, $userSID, $productSID);
    }
    
	public static function getExpiredContractsID()
	{
		$expired_contracts = SJB_DB::query("SELECT id FROM contracts WHERE expired_date < NOW() AND expired_date != '0000-00-00'");
		$contracts_id = array();
		foreach ($expired_contracts as $expired_contract) {			
			$contracts_id[] = $expired_contract['id'];			
		}
		return $contracts_id;
	}
	
    public static function getInfo($contract_id)
    {
    	if ($contract_id == 0) {
    		return false;
    	}
        $contractInfo = SJB_ContractSQL::selectInfoByID($contract_id);

        if ($contractInfo && empty($contractInfo['serialized_extra_info']) ) {
        	$product= SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
        	$contractInfo['serialized_extra_info'] = $product['serialized_extra_info'];
        }

        return $contractInfo;
    }
    
    public static function getAllContractsInfoByUserSID($user_sid)
    {
    	if ($user_sid == 0) {
    		return false;
    	}
        $contractsInfo = SJB_ContractSQL::selectInfoByUserSID($user_sid);

        foreach($contractsInfo as $key => $contractInfo) {
	        if ($contractInfo && empty($contractInfo['serialized_extra_info']) ) {
	        	$product = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
	        	$contractInfo['serialized_extra_info'] = $product['serialized_extra_info'];
	        	$contractsInfo[$key] = $contractInfo;
	        }
        }
        return $contractsInfo;
    }
    
    public static function getAllContractsSIDsByUserSID($user_sid)
    {
    	if ($user_sid == 0) {
    		return false;
    	}
        $contractsInfo = SJB_ContractSQL::selectInfoByUserSID($user_sid);
		$result = array();
        foreach($contractsInfo as $contractInfo) {
			$result[] = $contractInfo['id'];
        }
        return $result;
    }
    
	public static function getExtraInfoByID($contract_id)
	{
    	$extra_info = SJB_DB::queryValue("SELECT serialized_extra_info FROM contracts WHERE id = ?n", $contract_id);
    	$contract_extra_info = false;
    	if (!empty($extra_info))
    		$contract_extra_info = unserialize($extra_info);

		return $contract_extra_info;
    }

    public static function getAllContractsByProductSID($productSID)
    {
    	 return SJB_DB::query("SELECT `id` FROM `contracts` WHERE `product_sid` = ?n",$productSID);
    }
    
	public static function getContractQuantityByProductSID($productSID)
	{		 
		$result = SJB_DB::queryValue("SELECT COUNT( DISTINCT users.sid)
							FROM users 
							INNER JOIN contracts ON users.sid = contracts.user_sid 
							INNER JOIN products ON products.sid = contracts.product_sid 
							WHERE products.sid=?n", $productSID);
		
		return $result ? $result : 0;
	}
	
	public static function updateExpirationPeriod($contractSID)
	{
		$contractInfo = self::getInfo($contractSID);
		if ($contractInfo) {
			$productInfo = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
			$product = new SJB_Product($productInfo);
			$expirationPeriod = $product->getExpirationPeriod();
			if ($expirationPeriod) {
				$expired_date = date("Y-m-d", strtotime("+" . $expirationPeriod . " day"));
				SJB_DB::query("UPDATE `contracts` SET `expired_date` = ?s WHERE `id` = ?n", $expired_date, $contractSID);
			}
		}
	}
	
	public static function getListingsNumberByContractSIDsListingType($contractsSIDs, $listingTypeID)
	{
		$acl = SJB_Acl::getInstance();
		$result = 0;
		foreach ($contractsSIDs as $contractSID) {
			if ($acl->isAllowed('post_' . $listingTypeID, $contractSID, 'contract')) {
				$contractInfo = self::getInfo($contractSID);
				$result += $contractInfo['number_of_postings'];
			}
		}
		return $result;
	}
	
	public static function getContractIDByInvoiceID($invoiceID, $productSID, $userSID)
	{
		$contractID = SJB_DB::queryValue("SELECT `id` FROM `contracts` WHERE `product_sid` = ?n AND `invoice_id` = ?s AND `user_sid` = ?n",$productSID, $invoiceID, $userSID);
		return $contractID ? $contractID : false;
	}

	public static function activateContract($contract_id, $user_sid = false)
	{
		$contractInfo = self::getInfo($contract_id);
		$number_of_listings = isset($contractInfo['number_of_postings']) ? $contractInfo['number_of_postings'] : 0;
		$product_sid = isset($contractInfo['product_sid']) ? $contractInfo['product_sid'] : 0;
		SJB_Acl::copyPermissions($product_sid, $contract_id, $number_of_listings);
		SJB_DB::query("UPDATE `contracts` SET `status` = 'active' WHERE `id` = ?n", $contract_id);
	}
}
