<?php

class SJB_Classifieds_PayForListing extends SJB_Function
{
	public function execute()
	{
		$templateProcessor = SJB_System::getTemplateProcessor();
		$listingSid = isset($_REQUEST['listing_id']) ? $_REQUEST['listing_id'] : null;
		$listing    = SJB_ListingManager::getObjectBySID($listingSid);
		
		if (!is_null($listing) && !$listing->isActive()) {
			$listingInfo = SJB_ListingManager::getListingInfoBySID($listingSid);
			$productInfo = !empty($listingInfo['product_info']) ? unserialize($listingInfo['product_info']) : array();
			if ($listingInfo['checkouted'] == 1) {
				$price = 0;
				if (SJB_ListingManager::getIfListingHasExpiredBySID($listing->getID()) && !empty($productInfo['price'])) {
					$price = floatval($productInfo['price']);
				}
				$userSid        = $listing->getUserSID();
				$productSid     = $productInfo['product_sid'];
				$listingTitle   = $listing->getProperty('Title')->getValue();
				$listingTypeSid = $listing->getListingTypeSID();
				$listingTypeId  = SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSid);
				
				$newProductName = "Reactivation of \"{$listingTitle}\" {$listingTypeId}";
				$newProductInfo = SJB_ShoppingCart::createInfoForCustomProduct($userSid, $productSid, $listingSid, $price, $newProductName, 'activateListing');
				
				if ($price <= 0) {
					SJB_ListingManager::activateListingBySID($listing->getSID());
					SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . SJB_TemplateProcessor::listing_url($listing) . '?isBoughtNow=1');
				} else {
					SJB_ShoppingCart::addToShoppingCart($newProductInfo, $userSid);
					$shoppingUrl = SJB_System::getSystemSettings('SITE_URL') . '/shopping-cart/';
					SJB_HelperFunctions::redirect($shoppingUrl);
				}
			} elseif ($listingInfo['checkouted'] == 0) {
				$productsInfoFromShopppingCart = SJB_ShoppingCart::getProductsInfoFromCartByProductSID($productInfo['product_sid'], $listing->getUserSID());
				if (empty($productsInfoFromShopppingCart)) {
					$productInfoToShopCart = SJB_ProductsManager::getProductInfoBySID($productInfo['product_sid']);
					$productInfo['number_of_listings'] = 1;
					$productObj = new SJB_Product($productInfoToShopCart);
					$productObj->setNumberOfListings($productInfoToShopCart['number_of_listings']);
					$productInfoToShopCart['price'] = $productObj->getPrice();
					SJB_ShoppingCart::addToShoppingCart($productInfoToShopCart, $listing->getUserSID());
				}
				SJB_HelperFunctions::redirect(SJB_System::getSystemsettings('SITE_URL') . '/shopping-cart/');
			} else {
				$errors['LISTING_IS_NOT_COMPLETE'] = 1;
			}
		}
		elseif (is_null($listingSid)) {
			$errors['INVALID_LISTING_ID'] = 1;
		} elseif (!is_null($listing) && $listing->isActive()) {
			$errors['LISTING_ALREADY_ACTIVE'] = 1;
		} else {
			$errors['WRONG_LISTING_ID_SPECIFIED'] = 1;
		}
		
		$templateProcessor->assign("errors", isset($errors) ? $errors : null);
		$templateProcessor->display("pay_for_listing.tpl");
	}
}
