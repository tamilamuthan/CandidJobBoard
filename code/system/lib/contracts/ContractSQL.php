<?php

class SJB_ContractSQL
{
	
	public static function selectInfoByID($id)
	{
		$result = SJB_DB::query("SELECT * FROM contracts WHERE id=?n", $id);
		return array_pop($result);
	}
	
	public static function selectInfoByUserSID($user_sid)
	{
		return SJB_DB::query("SELECT * FROM contracts WHERE user_sid=?n ORDER BY `id` DESC", $user_sid);
	}
	
	public static function insert($contract_info)
	{
		$contract_id = $contract_info['contract_id'];
		if (!empty($contract_id)) {
			if (!empty($contract_info['expired_date'])) {
				return SJB_DB::query("UPDATE `contracts` SET `product_sid` = ?n, `creation_date` = ?s, `expired_date` = ?s, `price` = ?s, `status` = ?s WHERE `id` = ?n",
					$contract_info['product_sid'], $contract_info['creation_date'], $contract_info['expired_date'], $contract_info['price'], $contract_info['status'], $contract_id);
			} else {
				return SJB_DB::query("UPDATE `contracts` SET `product_sid` = ?n, `creation_date` = ?s, `price` = ?s, `status` = ?s WHERE `id` = ?n",
					$contract_info['product_sid'],  $contract_info['creation_date'], $contract_info['price'], $contract_info['status'], $contract_id);
			}
		}
		else {
			if (!empty($contract_info['expired_date'])) {
				return SJB_DB::query("INSERT INTO `contracts`(`user_sid`, `product_sid`, `creation_date`, `expired_date`, `price`, `gateway_id`, `invoice_id`, `status`) VALUES(?n, ?n, ?s, ?s, ?s, ?s, ?s, ?s)",
					$contract_info['user_sid'], $contract_info['product_sid'],  $contract_info['creation_date'], $contract_info['expired_date'], $contract_info['price'], $contract_info['gateway_id'], $contract_info['invoice_id'], $contract_info['status']);
			} else {
				return SJB_DB::query("INSERT INTO `contracts`(`user_sid`, `product_sid`, `creation_date`, `price`, `gateway_id`, `invoice_id`, `status`) VALUES(?n, ?n, ?s, ?s, ?s, ?s, ?s)",
					$contract_info['user_sid'], $contract_info['product_sid'],  $contract_info['creation_date'], $contract_info['price'], $contract_info['gateway_id'], $contract_info['invoice_id'], $contract_info['status']);
			}
		}
	}
	
	public static function updateContractExtraInfoByProductSID($contract)
	{
		$productSID = $contract->product_sid;
		$productExtraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productSID);
		SJB_DB::query("UPDATE `contracts` SET `serialized_extra_info` = ?s WHERE `id` = ?n", serialize($productExtraInfo), $contract->id);
	}
	
	public static function delete($contract_id)
	{
		return SJB_DB::query("DELETE FROM `contracts` WHERE `id`=?s", $contract_id);
	}
	
	public static function incrementPostingsNumber($contractSID)
	{
		return SJB_DB::query("UPDATE `contracts` SET `number_of_postings` = `number_of_postings` + 1 WHERE `id` = ?n", $contractSID);
	}

	public static function updatePostingsNumber($contractSID, $postingsNumber)
	{
		return SJB_DB::query("UPDATE `contracts` SET `number_of_postings` = {$postingsNumber} WHERE `id` = ?n", $contractSID);
	}
}

