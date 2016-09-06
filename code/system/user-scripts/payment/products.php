<?php

class SJB_Payment_UserProducts extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$current_user = SJB_UserManager::getCurrentUser();
		$action = SJB_Request::getVar('action', 'productList');
		$productSID = SJB_Request::getVar('product_sid', 0, 'default', 'int');
		$template = 'products.tpl';
		$errors = array();

		switch ($action) {
			case 'view_product_detail':
				if (!SJB_UserManager::isUserLoggedIn() || $current_user->mayChooseProduct($productSID, $errors)) {
					$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
					$productInfo['listingTypeID'] = SJB_ListingTypeManager::getListingTypeIDBySID($productInfo['listing_type_sid']);
					$event = SJB_Request::getVar('event', false);
					if ($event && $productInfo && !$errors) {
						if (SJB_UserManager::isUserLoggedIn()) {
							SJB_ShoppingCart::addToShoppingCart($productInfo, $current_user->getSID());
						} else {
							if (!$errors) {
								$id = time();
								$_SESSION['products'][$id]['product_info'] = serialize($productInfo);
								$_SESSION['products'][$id]['sid'] = $id;
								$_SESSION['products'][$id]['user_sid'] = 0;
							}
						}
						if (!$errors) {
							SJB_HelperFunctions::redirect(SJB_System::getSystemsettings('SITE_URL') . '/shopping-cart/');
						}
					}

					if (!empty($productInfo['expiration_period']) && !is_numeric($productInfo['expiration_period']))
						$productInfo['period'] = ucwords($productInfo['expiration_period']);
					elseif (!empty($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'fixed') {
						$productInfo['fixed_period'] = 1;
					}

					$userGroupID = SJB_UserGroupDBManager::getUserGroupIDBySID($productInfo['user_group_sid']);
					$tp->assign('productInfo', $productInfo);
					$tp->assign('userGroupID', $userGroupID);
					$tp->assign('productSID', $productSID);
					$tp->assign('mayChooseProduct', true);
				}
				$tp->assign('errors', $errors);
			case 'productList':
				$postingProductsOnly = SJB_Request::getVar('postingProductsOnly', false);
				$availableProducts = array();
				if (SJB_UserManager::isUserLoggedIn() && $postingProductsOnly) {
					$availableProducts = SJB_ProductsManager::getProductsByUserGroupSID($current_user->getUserGroupSID(), $current_user->getSID());
					$availableListingType = SJB_UserGroupManager::getUserGroupIDBySID($current_user->getUserGroupSID()) == 'Employer' ? 'job' : 'resume';
					$trialProducts = $current_user->getTrialProductSIDByUserSID();
					foreach ($availableProducts as $key => $availableProduct) {
						if (in_array($availableProduct['sid'], $trialProducts) || ($postingProductsOnly && empty($availableProduct['post_' . $availableListingType]))) {
							unset($availableProducts[$key]);
						}
					}

					$tp->assign('postingProductsOnly', $postingProductsOnly);
				} elseif ($userGroupID = SJB_Request::getVar('userGroupID', false)) {
					$userGroupSID = SJB_UserGroupManager::getUserGroupSIDByID($userGroupID);
					$availableProducts = SJB_ProductsManager::getProductsByUserGroupSID($userGroupSID, 0);
				} else {
					$availableProducts = SJB_ProductsManager::getAllActiveProducts();
				}

				foreach ($availableProducts as $key => $availableProductInfo) {
					$availableProduct = new SJB_Product($availableProductInfo);
					$availableProduct->setNumberOfListings(1);
					$availableProducts[$key]['price'] = $availableProduct->getPrice();
					if (isset($availableProducts[$key]['listing_type_sid'])) {
						$availableProducts[$key]['listing_type_id'] = SJB_ListingTypeDBManager::getListingTypeIDBySID($availableProducts[$key]['listing_type_sid']);
					}
				}
				SJB_Event::dispatch('RedefineTemplateName', $template, true);
				SJB_Event::dispatch('RedefineProductsDisplayInfo', $availableProducts, true);
				$tp->assign('availableProducts', $availableProducts);
				break;
		}
		$tp->display($template);
	}
}
