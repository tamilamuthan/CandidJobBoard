<?php

class SJB_ShoppingCart
{
	public static function addToShoppingCart($productInfo, $userSID)
	{
		self::deleteItemsFromCartByUserSID($userSID);
		return SJB_DB::query("INSERT INTO `shopping_cart` (`user_sid`, `product_info`) VALUES (?n, ?s)", $userSID, serialize($productInfo));
	}
	
	public static function getAllProductsByUserSID($userSID)
	{
		return SJB_DB::query("SELECT * FROM `shopping_cart` WHERE `user_sid` = ?n ORDER BY `sid` DESC", $userSID);
	}
	
	public static function updateItemBySID($sid, $productInfo)
	{
		return SJB_DB::query("UPDATE `shopping_cart` SET `product_info` = ?s WHERE `sid` = ?n ", serialize($productInfo), $sid);
	}
	
	public static function deleteItemsFromCartByUserSID($userSID)
	{
		return SJB_DB::query("DELETE FROM `shopping_cart` WHERE `user_sid` = ?n", $userSID);
	}

	public static function createInfoForCustomProduct($userSid, $productSid, $listingSid, $price, $name, $type)
	{
		$productName  = SJB_ProductsManager::getProductNameBySid($productSid);
		$userGroupSid = SJB_UserManager::getUserGroupByUserSid($userSid);
		
		$productInfo = array(
			'custom_item' => "{$name} ({$productName})",
			'custom_info' =>  array(
				'listing_id'       => (int) $listingSid,
				'type'             => $type,
				'extraDescription' => "({$productName})",
				'productSid'       => (int) $productSid,
			),
			'name'               => $name,
			'number_of_listings' => 1,
			'QtyPeriod'          => 1,
			'price'              => $price,
			'pricing_type'       => 'fixed',
			'sid'                => -1,
			'user_group_sid'     => $userGroupSid,
		);
		$productInfo['serialized_extra_info'] = serialize($productInfo);
		
		return $productInfo;
	}

	public static function getProductsInfoFromCartByProductSID($productSID, $currentUserID)
	{
		$serializedProductSIDForShopCart = '"sid";s:'.strlen($productSID).':"'.$productSID.'";';
		return SJB_DB::query("SELECT `sid`,`product_info` FROM `shopping_cart` WHERE `user_sid` = ?n AND `product_info` REGEXP '({$serializedProductSIDForShopCart})' ORDER BY `sid` DESC", $currentUserID);
	}
}
