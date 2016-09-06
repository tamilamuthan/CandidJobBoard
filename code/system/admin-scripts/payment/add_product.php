<?php

class SJB_Admin_Payment_AddProduct extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('manage_products');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$productErrors = array();

		$_REQUEST['listing_type_sid'] = SJB_Request::getVar('user_group_sid') == SJB_UserGroup::EMPLOYER ? SJB_ListingTypeManager::JOB : SJB_ListingTypeManager::RESUME;
		$userGroup = SJB_UserGroupManager::getUserGroupInfoBySID(SJB_Request::getVar('user_group_sid'));

		$product = new SJB_Product($_REQUEST);
		$pages = $product->getProductPages();
		$addProductForm = new SJB_Form($product);
		$addProductForm->registerTags($tp);
		$form_submitted = SJB_Request::getVar('action', '') == 'save';
		if ($form_submitted) {
			$productErrors = $product->isValid($product);
		}

		if ($form_submitted && $addProductForm->isDataValid($errors) && !$productErrors) {
			$product->saveProduct($product);
			$product->savePermissions($_REQUEST);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/products/' . strtolower($userGroup['id']) . '/');
		}
		$errors = array_merge($errors, $productErrors);
		$formFieldsInfo = $addProductForm->getFormFieldsInfo();
		$formFields = array();
		foreach ($pages as $pageID => $page) {
			foreach ($formFieldsInfo as $formFieldInfo)
				if (in_array($formFieldInfo['id'], $page['fields']))
					$formFields[$pageID][] = $formFieldInfo;
			if (!isset($formFields[$pageID]))
				$formFields[$pageID] = array();
		}

		$tp->assign('form_fields', $formFields);
		$tp->assign('request', $_REQUEST);
		$tp->assign('params', http_build_query($_REQUEST));
		$tp->assign('pages', $pages);
		$tp->assign('listingType', SJB_ListingTypeManager::getListingTypeInfoBySID($_REQUEST['listing_type_sid']));
		$tp->assign('userGroup', $userGroup);
		$tp->assign('errors', $errors);
		$tp->display('add_product.tpl');
	}
}