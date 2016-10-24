<?php

class SJB_Payment_ShoppingCart extends SJB_Function
{
	public function execute()
	{
		if (!SJB_UserManager::isUserLoggedIn()) {
			echo SJB_System::executeFunction('users', 'login');
			return;
		}
		$products = SJB_Session::getValue('products');
		$products = $products ? $products : array();
		$currentUser = SJB_UserManager::getCurrentUser();
		foreach ($products as $product) {
			if (!empty($product['product_info'])) {
				$productInfo = unserialize($product['product_info']);
				$product = new SJB_Product($productInfo);
				$number_of_listings = !empty($productInfo['number_of_listings'])?$productInfo['number_of_listings']:1;
				$product->setNumberOfListings($number_of_listings);
				$productInfo['price'] = $product->getPrice();
				SJB_ShoppingCart::addToShoppingCart($productInfo, $currentUser->getSID());
			}
		}
		SJB_Session::unsetValue('products');

		$products = SJB_ShoppingCart::getAllProductsByUserSID($currentUser->getSID());
		foreach ($products as $key => $product) { // prevent users from buying other user group products
			$productInfo = unserialize($product['product_info']);
			if ($productInfo['user_group_sid'] != $currentUser->getUserGroupSID()) {
				SJB_ShoppingCart::deleteItemsFromCartByUserSID($currentUser->getSID());
			}
		}
		$products = SJB_ShoppingCart::getAllProductsByUserSID($currentUser->getSID());
		if (empty($products)) {
            $eSid = SJB_UserGroupManager::getUserGroupSIDByID('Entrepreneur');
            $iSid = SJB_UserGroupManager::getUserGroupSIDByID('Investor');
            $url = '';
			switch($currentUser->getUserGroupSID()) {
                case SJB_UserGroup::EMPLOYER: $url = '/empoloyer-products/'; break;    
                case $eSid: $url = '/entrepreneur-products/'; break;    
                case $iSid: $url = '/investor-products/'; break;    
                default: $url = '/jobseeker-products';
            }
            SJB_H::redirect(SJB_H::getSiteUrl() . $url); 
		}

		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', false);
		$applyPromoCode = SJB_Request::getVar('applyPromoCode', false);
		$action = $applyPromoCode ? 'applyPromoCode' : $action;
		$numberOfListings = SJB_Request::getVar('number_of_listings');
		$productInfo = null;
		$errors = array();
		switch ($action) {
			case 'checkout':
				if (empty($products)) {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/my-account/");
				}

				$subTotal = 0;
				foreach ($products as $key => $product) {
					$productInfo = unserialize($product['product_info']);
					if (isset($numberOfListings[$productInfo['sid']][$product['sid']])) {
						$productInfo['number_of_listings'] = $numberOfListings[$productInfo['sid']][$product['sid']];
						$productObj = new SJB_Product($productInfo);
						$number_of_listings = !empty($productInfo['number_of_listings'])?$productInfo['number_of_listings']:1;
						$productObj->setNumberOfListings($number_of_listings);
						$productInfo['price'] = $productObj->getPrice();
						if (!empty($productInfo['code_info'])) {
							SJB_PromotionsManager::applyPromoCodeToProduct($productInfo, $productInfo['code_info']);
						}
						SJB_ShoppingCart::updateItemBySID($product['sid'], $productInfo);
					}
					$subTotal += $productInfo['price'];
					$products[$key] = $productInfo;
					$products[$key]['item_sid'] = $product['sid'];
					$products[$key]['product_info'] = serialize($productInfo);
				}
				$index = 1;
				$items = array();
				$codeInfo = array();

				foreach ($products as $product) {
					$product_info = unserialize($product['product_info']);
					SJB_PromotionsManager::preparePromoCodeInfoByProductPromoCodeInfo($product, $product['code_info']);
					$qty = !empty($product_info['number_of_listings'])?$product_info['number_of_listings']:null;
					$items['products'][$index] = $product_info['sid'];
					if ($qty > 0)
						$items['price'][$index] = round($product['price']/ $qty, 2);
					else
						$items['price'][$index] = round($product['price'], 2);
					$items['amount'][$index] = $product['price'];
					$items['qty'][$index] = $qty;

					if (isset($product['custom_item'])) {
						$items['custom_item'][$index] = $product['custom_item'];
					} else {
						$items['custom_item'][$index] = "";
					}

					if (isset($product['custom_info'])) {
						$items['custom_info'][$index] = $product['custom_info'];
                    } elseif (!empty($product['proceedToListing'])) {
                        $items['custom_info'][$index]['proceedToListing'] = $product['proceedToListing'];
					}

					$index++;
					SJB_PromotionsManager::preparePromoCodeInfoByProductPromoCodeInfo($product_info, $codeInfo);
				}
				$userSID = $currentUser->getSID();
				$invoiceSID = SJB_InvoiceManager::generateInvoice($items, $userSID, $subTotal);
				SJB_PromotionsManager::addCodeToHistory($codeInfo, $invoiceSID, $userSID);
				if ($subTotal <= 0) {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/system/payment/callback/' . $invoiceSID . '/');
				} else {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/payment-page/?invoice_sid=" . $invoiceSID . '&gateway=' . SJB_Request::getVar('gateway'));
				}
				break;
			case 'applyPromoCode':
				$promotionCode = SJB_Request::getVar('promotion_code', false);
				if ($promotionCode) {
					$allowShoppingItems = array();
					$productSIDs = array();
					foreach ($products as $product) {
						$productInfo = unserialize($product['product_info']);
						if (!isset($productInfo['code_info'])) {
							if (isset($productInfo['custom_info'])) {
								$allowShoppingItems[] = $product['sid'];
								$productSIDs[] = $productInfo['custom_info']['productSid'];
							} else {
								$allowShoppingItems[] = $product['sid'];
								$productSIDs[] = $productInfo['sid'];
							}
						} else {
							$appliedPromoCode = $productInfo['code_info'];
						}
					}
					if ($codeInfo = SJB_PromotionsManager::checkCode($promotionCode, $productSIDs)) {
						$productSIDs = $codeInfo['product_sid']?explode(',', $codeInfo['product_sid']):false;
						$appliedProducts = array();
						$codeValid = false;
						foreach ($products as $key => $product) {
							$productInfo = unserialize($product['product_info']);
							if ($productInfo['sid'] != '-1') {
								$productSid = $productInfo['sid'];
							} else {
								$productSid = $productInfo['custom_info']['productSid'];
							}
							if (($productSIDs && in_array($productSid, $productSIDs)) && $allowShoppingItems && in_array($product['sid'], $allowShoppingItems)) {
								$currentUsesCount = SJB_PromotionsManager::getUsesCodeBySID($codeInfo['sid']);
								if (empty($codeInfo['maximum_uses']) || $codeInfo['maximum_uses'] > $currentUsesCount) {
									$codeValid = true;
									SJB_PromotionsManager::applyPromoCodeToProduct($productInfo, $codeInfo);
									$appliedProducts[] = $productInfo;
									SJB_ShoppingCart::updateItemBySID($product['sid'], $productInfo);
								}
							}
						}
						if (!$codeValid) {
							$errors['NOT_VALID'] = 'Invalid discount code';
							unset($promotionCode);
						}
						$tp->assign('applied_products', $appliedProducts);
						$tp->assign('code_info', $codeInfo);
					} else {
						$errors['NOT_VALID'] = 'Invalid discount code';
					}
					if (isset($promotionCode) && isset($appliedPromoCode)) {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/shopping-cart/');
					}
				} else {
					$errors['EMPTY_VALUE'] = 'Discount code';
				}
				break;
			case 'deletePromoCode':
				foreach ($products as $key => $product) {
					$productInfo = unserialize($product['product_info']);
					SJB_PromotionsManager::removePromoCodeFromProduct($productInfo);
					$numberOfListings = is_array($numberOfListings) ? array_pop($numberOfListings) : false;
					if (is_array($numberOfListings)) {
						foreach($numberOfListings as $listingSid => $listingsCount) {
							if ($listingSid == $product['sid']) {
								$productInfo['number_of_listings'] = $listingsCount;
							}
						}
					}
					SJB_ShoppingCart::updateItemBySID($product['sid'], $productInfo);
				}
				break;
		}
		$products = SJB_ShoppingCart::getAllProductsByUserSID($currentUser->getSID());
		$allowShoppingItems = array();
		foreach ($products as $product) {
			$productInfo = unserialize($product['product_info']);
			if (!empty($productInfo['code_info'])) {
				$promotionCode = $productInfo['code_info']['code'];
				$promotionCodeInfo = $productInfo['code_info'];
			} else {
				$allowShoppingItems[] = $product ['sid'];
			}
		}
		$promotionCode = isset($promotionCode) ? $promotionCode : '';
		$totalPrice = 0;
		$discountTotalAmount = 0;
		$numberOfListings = SJB_Request::getVar('number_of_listings', false);
		foreach ($products as $key => $product) {
			$productInfo = unserialize($product['product_info']);
			if ($allowShoppingItems && in_array($product['sid'], $allowShoppingItems)) {
				$this->applyPromoCodesToProduct($promotionCode, $productInfo);
				SJB_ShoppingCart::updateItemBySID($product['sid'], $productInfo);
			}
			if ($numberOfListings && array_key_exists('number_of_listings', $productInfo) && array_key_exists($productInfo['sid'], $numberOfListings)) {
				$productInfo['number_of_listings']  = $numberOfListings[$productInfo['sid']][$product['sid']];
			}
			$productObj = new SJB_Product($productInfo);
			$productExtraInfo = unserialize($productInfo['serialized_extra_info']);
			if (!empty($productInfo['expiration_period']) && !is_numeric($productInfo['expiration_period'])) {
				$productInfo['primaryPrice'] = $productExtraInfo['price'];
				$productInfo['period'] = ucwords($productInfo['expiration_period']);
			} elseif (!empty($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'fixed') {
				$productInfo['primaryPrice'] = $productObj->getPrice();
				$this->applyPromoCodesToProduct($promotionCode, $productInfo);
			}
			if (isset($productInfo['code_info'])) {
				$discountTotalAmount += (float)$productInfo['code_info']['promoAmount'];
			}
			$productInfo['primaryPrice'] = $productExtraInfo['price'];
			$this->applyPromoCodesToProduct($promotionCode, $productInfo);
			$totalPrice += (float)$productInfo['price'];
			$products[$key] = $productInfo;
			$products[$key]['item_sid'] = $product['sid'];
		}
		$taxInfo = SJB_TaxesManager::getTaxInfoByPrice($totalPrice);
		$tp->assign('tax', $taxInfo);
		$userGroupID = $productInfo ? SJB_UserGroupDBManager::getUserGroupIDBySID($productInfo['user_group_sid']) : false;
		$tp->assign('promotionCodeAlreadyUsed', $promotionCode && empty($errors));
		if (isset($promotionCodeInfo)) {
			$tp->assign('promotionCodeInfo', $promotionCodeInfo);
		}
		$tp->assign('errors', $errors);
		$taxInfo = SJB_TaxesManager::getTaxInfoByPrice($totalPrice);
		if ($taxInfo) {
			$totalPrice = SJB_InvoiceManager::getTotalPrice($totalPrice, $taxInfo['tax_amount']);
		}

		$tp->assign('total_price', $totalPrice);
		$tp->assign('discountTotalAmount', $discountTotalAmount);
		$tp->assign('products', $products);
		$tp->assign('userGroupID', $userGroupID);
		$tp->assign('gateways', SJB_PaymentGatewayManager::getActivePaymentGatewaysList());
		$tp->assign('selected_gateway', SJB_Request::getVar('gateway'));
		$tp->display('shopping_cart.tpl');
	}

	/**
	 * @param $itemSID
	 * @param $userSID
	 */
	public function findCheckoutedListingsByProduct($itemSID, $userSID)
	{
		$shopCartProduct = SJB_DB::query("SELECT `product_info` FROM `shopping_cart` WHERE `sid` = ?n", $itemSID);
		if (!empty($shopCartProduct)) {
			$productInfo = unserialize($shopCartProduct[0]['product_info']);
			$countCheckoutedListings = SJB_ListingDBManager::getNumberOfCheckoutedListingsByProductSID($productInfo['sid'], $userSID);
			if ($countCheckoutedListings != 0) {
				$serializedProductSIDForShopCart = '"sid";s:' . strlen($productInfo['sid']) . ':"' . $productInfo['sid'] . '";';
				$countOfOtherShopCartProducts = SJB_DB::queryValue("SELECT COUNT(`sid`) FROM `shopping_cart` WHERE `sid` != ?n AND `user_sid` = ?n AND `product_info` REGEXP '({$serializedProductSIDForShopCart})' ORDER BY `sid` ASC", $itemSID, $userSID);
				$limitCheckoutedListingsToDelete = $countCheckoutedListings - ($countOfOtherShopCartProducts * $productInfo['number_of_listings']);
				if ($limitCheckoutedListingsToDelete > 0) {
					$this->deleteCheckoutedListingsByProduct($userSID, $productInfo['sid'], $limitCheckoutedListingsToDelete);
				}
			}
		}
	}

	/**
	 * @param $userSID
	 * @param $productSID
	 * @param $limitCheckoutedListingsToDelete
	 */
	public function deleteCheckoutedListingsByProduct($userSID, $productSID, $limitCheckoutedListingsToDelete)
	{
		$serializedProductSID = SJB_ProductsManager::generateQueryBySID($productSID);
		$listingsToDelete = SJB_DB::query("SELECT `sid` FROM `listings` WHERE `checkouted` = 0 AND `contract_id` = 0 AND `user_sid` = ?n AND `product_info` REGEXP '({$serializedProductSID})' ORDER BY `sid` DESC LIMIT ?n", $userSID, $limitCheckoutedListingsToDelete);
		$criteriaSaver = new SJB_ListingCriteriaSaver('MyListings');
		$foundListingsSIDs = $criteriaSaver->getObjectSIDs();
		foreach ($listingsToDelete as $listing) {
			SJB_ListingManager::deleteListingBySID($listing['sid']);
			if ($foundListingsSIDs != null) {
				$key = array_search($listing['sid'], $foundListingsSIDs);
				unset($foundListingsSIDs[$key]);
			}
		}
		if ($foundListingsSIDs != null) {
			$criteriaSaver->setSessionForObjectSIDs($foundListingsSIDs);
		}
	}

	private function applyPromoCodesToProduct ($promotionCode, &$productInfo)
	{
		$allowShoppingItems = array();
		if (!isset($productInfo['code_info'])) {
			if (isset($productInfo['custom_info'])) {
				$allowShoppingItems[] = $productInfo['custom_info']['productSid'];
			} else {
				$allowShoppingItems[] = $productInfo['sid'];
			}
		}
		if ($codeInfo = SJB_PromotionsManager::checkCode($promotionCode, $allowShoppingItems)) {
			$productSIDs = $codeInfo['product_sid'] ? explode(',', $codeInfo['product_sid']) : false;
			if ($productInfo['sid'] != '-1') {
				$productSid = $productInfo['sid'];
			} else {
				$productSid = $productInfo['custom_info']['productSid'];
			}
			if (($productSIDs && in_array($productSid, $productSIDs))) {
				$currentUsesCount = SJB_PromotionsManager::getUsesCodeBySID($codeInfo['sid']);
				if (($codeInfo['maximum_uses'] != 0 && $codeInfo['maximum_uses'] > $currentUsesCount) || $codeInfo['maximum_uses'] == 0) {
					SJB_PromotionsManager::applyPromoCodeToProduct($productInfo, $codeInfo);
				}
			}
		}
	}
}