<?php

class SJB_Admin_Payment_EditProduct extends SJB_Function
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
		$action = SJB_Request::getVar('action', false);
		$sid = SJB_Request::getVar('sid', 0);
		$errors = array();

		$productInfo = SJB_ProductsManager::getProductInfoBySID($sid);

		if ($productInfo) {
			$productInfo = array_merge($productInfo, $_REQUEST);
			$userGroup = SJB_UserGroupManager::getUserGroupInfoBySID($productInfo['user_group_sid']);
			$form_submitted = $action == 'save' || $action == 'apply_product';
			if (!$form_submitted) {
				$productInfo['default'] = $userGroup['default_product'] == $sid;
			}
			$product = new SJB_Product($productInfo);
			$product->setSID($sid);
			$pages = $product->getProductPages();

			$editProductForm = new SJB_Form($product);
			$editProductForm->registerTags($tp);

			$activeError = array();

			if ($form_submitted && !empty($productInfo['active'])) {
				if ( !empty($productInfo['availability_to']) && SJB_I18N::getInstance()->getInput('date', $productInfo['availability_to']) < date('Y-m-d'))
					$activeError['INVALID_ACTIVATION'] = 'The product cannot be activated. Please change the availability date.';
			}
			if ($form_submitted) {
				$productErrors = $product->isValid($product);
				$activeError = array_merge($activeError, $productErrors);
			}

			if ($form_submitted && $editProductForm->isDataValid($errors) && !$activeError) {
				$product->saveProduct($product);
				$product->savePermissions($_REQUEST);
				if ($action == 'save')
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/products/' . strtolower($userGroup['id']) . '/');
			} else {
				$product->setFloatNumbersIntoValidFormat();
			}
			$errors = array_merge($errors, $activeError);

			$formFieldsInfo = $editProductForm->getFormFieldsInfo();
			$formFields = array();
			foreach ($pages as $pageID => $page) {
				foreach ($formFieldsInfo as $formFieldInfo)
					if (in_array($formFieldInfo['id'], $page['fields']))
						$formFields[$pageID][] = $formFieldInfo;
				if (!isset($formFields[$pageID]))
					$formFields[$pageID] = array();
			}

			$tp->assign('form_fields', $formFields);
			$tp->assign('product_info', $productInfo);
			$tp->assign('params', http_build_query($_REQUEST));
			$tp->assign('pageTab', SJB_Request::getVar('page', false));
			$tp->assign('pages', $pages);
			$tp->assign('errors', $errors);
			$tp->assign('userGroup', $userGroup);
			$tp->display('edit_product.tpl');
		}
	}
}
