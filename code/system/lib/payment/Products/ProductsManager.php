<?php

class SJB_ProductsManager
{
	/**
	 * @param SJB_Product $product
	 */
	public static function saveProduct($product)
	{
		$serializedDetails = array();
		$product->setFloatNumbersIntoValidFormat();
		$properties = $product->getProperties();
		$isDefault = false;
		if ($product->getPropertyValue('default')) {
			$isDefault = true;
		} elseif ($product->getSID() && SJB_UserGroupManager::getDefaultProduct($product->getPropertyValue('user_group_sid')) == $product->getSID()) {
			SJB_UserGroupManager::setDefaultProduct($product->getPropertyValue('user_group_sid'), 0);
		}
		$product->deleteProperty('default');

		foreach ($properties as $name => $property) {
			if (!in_array($name, array('sid', 'name', 'detailed_description', 'user_group_sid', 'availability_from', 'availability_to', 'trial', 'active', 'order', 'number_of_postings'))) {
				$serializedDetails[$name] = $product->getPropertyValue($name);
				$product->deleteProperty($name);
			}
		}
		$product->addProperty(
			array ( 'id'		=> 'serialized_extra_info',
					'type'		=> 'text',
					'value'		=> serialize($serializedDetails),
					'is_system' => true,
			)
		);
		$productSID = $product->getSID();
		SJB_ObjectDBManager::saveObject('products', $product);
		if ($isDefault) {
			SJB_UserGroupManager::setDefaultProduct($product->getPropertyValue('user_group_sid'), $product->getSID());
		}
		if (!$productSID) {
			$max_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM `products` WHERE `user_group_sid`=?n", $product->getPropertyValue('user_group_sid'));
			$max_order = empty($max_order) ? 1 : $max_order;
			SJB_DB::query("UPDATE `products` SET `order` = ?n WHERE `sid` = ?n", ++$max_order, $product->getSID());
		}
	}

	public static function getProductInfoBySID($productSID)
	{
		$cacheId = 'ProductManager::getProductInfoBySID' . $productSID;
		if (SJB_MemoryCache::has($cacheId)) {
			return SJB_MemoryCache::get($cacheId);
		}

		$product = SJB_ObjectDBManager::getObjectInfo("products", $productSID);
		if (!empty($product['serialized_extra_info'])) {
			$serialized_extra_info = unserialize($product['serialized_extra_info']);
			$product = array_merge($product, $serialized_extra_info);
		}

		SJB_MemoryCache::set($cacheId, $product);
		return $product;
	}

	public static function getProductSidByName($productName)
	{
		return SJB_DB::queryValue("SELECT `sid` FROM ?w WHERE name = ?s", 'products', $productName);
	}

	public static function getProductNameBySid($productSid)
	{
		return SJB_DB::queryValue("SELECT `name` FROM ?w WHERE sid = ?n", 'products', $productSid);
	}

	public static function getProductExtraInfoBySID($productSID)
	{
		$product = SJB_ObjectDBManager::getObjectInfo("products", $productSID);
		$serialized_extra_info = array();
		if (!empty($product['serialized_extra_info'])) 
			$serialized_extra_info = unserialize($product['serialized_extra_info']);
		$serialized_extra_info['product_sid'] = (string)$productSID;
		return $serialized_extra_info;
	}
	
	public static function getAllProductsInfo($order = true)
	{
		SJB_DB::query("UPDATE `products` SET `active` = 0 WHERE `availability_to` < CURRENT_DATE()");
		if ($order)
			$productsSIDs = SJB_DB::query("SELECT * FROM `products` ORDER BY `user_group_sid`, `order`");
		else
			$productsSIDs = SJB_DB::query("SELECT * FROM `products` ORDER BY `sid`");
		$products = array();
		foreach ($productsSIDs as $productSID)
			$products[] = self::getProductInfoBySID($productSID['sid']);
		return $products;
	}

	/**
	 * @param int $userGroupSID
	 * @return array
	 */
	public static function getUserGroupProducts($userGroupSID)
	{
		$productsSIDs = SJB_DB::query('SELECT `sid` FROM `products` WHERE `user_group_sid` = ?n ORDER BY `order`', $userGroupSID);
		$products = array();
		foreach ($productsSIDs as $productSID) {
			$products[] = self::getProductInfoBySID($productSID['sid']);
		}
		return $products;
	}
	
	public static function deleteProductBySID($productSID)
	{
		return SJB_ObjectDBManager::deleteObjectInfoFromDB('products', $productSID);	
	}
	
	public static function activateProductBySID($productSID)
	{
		return SJB_DB::query('UPDATE `products` SET `active` = 1 WHERE `sid` = ?n', $productSID);
	}
	
	public static function deactivateProductBySID($productSID)
	{
		return SJB_DB::query('UPDATE `products` SET `active` = 0 WHERE `sid` = ?n', $productSID);
	}
	
	public static function getProductsInfoByUserGroupSID($userGroupSID)
	{
		$productsSIDs = SJB_DB::query("SELECT * FROM `products` WHERE `user_group_sid` = ?n ORDER BY `order`", $userGroupSID);
		$products = array();
		foreach ($productsSIDs as $productSID)
			$products[] = self::getProductInfoBySID($productSID['sid']);
		return $products;
	}
	
	public static function getProductsByUserGroupSID($userGroupSID, $userSID) 
	{
		$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
		$trialProducts = !empty($userInfo['trial'])?" AND ((`trial` = 1 AND `sid` NOT IN ({$userInfo['trial']})) OR `trial` = 0)":'';
		$productsSIDs = SJB_DB::query("SELECT * FROM `products` p
									   WHERE `user_group_sid` = ?n
									   AND (`availability_from` is NULL || `availability_from` <= NOW()) && (`availability_to` is NULL || `availability_to` >= CURRENT_DATE())
									   AND `active` = 1 {$trialProducts} ORDER BY `order`", $userGroupSID);
		$products = array();
		foreach ($productsSIDs as $productSID)
			$products[] = self::getProductInfoBySID($productSID['sid']);
		return $products;
	}
	
	public static function getAllActiveProducts()
	{
		$productsSIDs = SJB_DB::query("SELECT * FROM `products` WHERE  (`availability_from` is NULL || `availability_from` <= NOW()) && (`availability_to` is NULL || `availability_to` >= CURRENT_DATE()) AND `active` = 1 ORDER BY `user_group_sid`, `order`");
		$products = array();
		foreach ($productsSIDs as $productSID)
			$products[] = self::getProductInfoBySID($productSID['sid']);
		return $products;
	}
	
	public static function getProductsIDsByUserGroupSID($userGroupSID)
	{
		$productsSIDs = SJB_DB::query("SELECT * FROM `products` WHERE `user_group_sid` = ?n AND (`availability_from` is NULL || `availability_from` <= NOW()) && (`availability_to` is NULL || `availability_to` >= CURRENT_DATE()) ORDER BY `order`", $userGroupSID);
		$products = array();
		foreach ($productsSIDs as $productSID)
			$products[] = $productSID['sid'];
		return $products;
	}
	
	public static function moveUpProductBySID($productSID = 0, $userGroupSID = 0)
	{
		$productInfo = self::getProductInfoBySID($productSID);
		if (empty($productInfo)) 
			return false;

		$currentOrder = $productInfo['order'];
		$upOrder = SJB_DB::queryValue("SELECT MAX(`order`) FROM `products` WHERE `order` < ?n  AND `user_group_sid` = ?n",
								$currentOrder, $userGroupSID);
		if ($upOrder == 0)
			return false;
		
		SJB_DB::query("UPDATE `products` SET `order` = ?n WHERE `order` = ?n  AND `user_group_sid` = ?n", 
					$currentOrder, $upOrder, $userGroupSID);
		SJB_DB::query("UPDATE `products` SET `order` = ?n WHERE `sid` = ?n", $upOrder, $productSID);
		return true;	
	}
	
	public static function moveDownProductBySID($productSID = 0, $userGroupSID = 0)
	{
		$productInfo = self::getProductInfoBySID($productSID);
		if (empty($productInfo)) 
			return false;

		$currentOrder = $productInfo['order'];
		$lessOrder = SJB_DB::queryValue("SELECT MIN(`order`) FROM `products` WHERE `order` > ?n AND `user_group_sid` = ?n",
								$currentOrder, $userGroupSID);
		if ($lessOrder == 0)
			return false;
		SJB_DB::query("UPDATE `products` SET `order` = ?n WHERE `order` = ?n AND `user_group_sid` = ?n",
					$currentOrder, $lessOrder, $userGroupSID);
		SJB_DB::query("UPDATE `products` SET `order` = ?n WHERE `sid` = ?n", $lessOrder, $productSID);
		return true;	
	}
	
	public static function createTemplateStructureForProduct($productInfo)
	{
		if (!empty($productInfo)) {
			$productInfo = unserialize($productInfo);
			$productInfo = !empty($productInfo['product_sid']) ? SJB_ProductsManager::getProductInfoBySID($productInfo['product_sid']) : $productInfo;
			$productInfo = $productInfo?$productInfo:array();
			$METADATA = array (
			    'METADATA'			=> array(
	    			'caption'			=> array('type' => 'string', 'propertyID' => 'caption'),
	    			'detailed_description'		=> array('type' => 'text', 'propertyID' => 'detailed_description'),
	    		)
			);
			return array_merge($productInfo, $METADATA);
		}
		return array();
	}

	public static function createTemplateStructureForProductForEmailTpl($productInfo)
	{
		$productSID = SJB_Array::get($productInfo, 'sid');
		if ($productSID) {
			$product = self::getProductInfoBySID($productSID);
			return array (
				'sid'				=> $product['sid'],
				'caption'			=> $product['name'],
				'detailed_description'	=> $product['detailed_description'],
				'METADATA'			=> array(
					'caption'			=> array('type' => 'string', 'propertyID' => 'caption'),
					'detailed_description'		=> array('type' => 'text', 'propertyID' => 'detailed_description'),
				)
			);
		}
		return array();
	}

	public static function generateQueryBySID($sid)
	{
		if (!empty($sid)) {
			return '"product_sid";s:'.strlen($sid).':"'.$sid.'";';
		}
		return false;
	}

	public static function isProductExists($productSID)
	{
		return SJB_DB::queryValue("SELECT COUNT(*) FROM `products` WHERE `sid` = ?n", $productSID) > 0;
	}

	public static function incrementPostingsNumber($productSID, $incrementNumber = 1)
	{
		return SJB_DB::query("UPDATE `products` SET `number_of_postings` = `number_of_postings` + ?n WHERE `sid` = ?n", $incrementNumber, $productSID);
	}
}
