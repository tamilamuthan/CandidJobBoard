<?php

class SJB_Admin_Users_EditUserGroup extends SJB_Function
{
	public function execute()
	{
		// fixme: deprecated
		return;
		$tp = SJB_System::getTemplateProcessor();
		$userGroupSID = SJB_Request::getVar('sid', null);
		$errors = array();

		if (!is_null($userGroupSID)) {
//			$action = SJB_Request::getVar("action", false);
//			$product_sid = SJB_Request::getVar("product_sid", false);
//			if ($action && $product_sid !== false) {
//				switch ($action) {
//					case 'move_up':
//						SJB_ProductsManager::moveUpProductBySID($product_sid, $userGroupSID);
//						break;
//					case 'move_down':
//						SJB_ProductsManager::moveDownProductBySID($product_sid, $userGroupSID);
//						break;
//				}
//			}
			$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userGroupSID);
			$userGroupInfo = array_merge($userGroupInfo, $_REQUEST);
			$userGroup = new SJB_UserGroup($userGroupInfo);
			$userGroup->setSID($userGroupSID);

			$productSIDs = SJB_ProductsManager::getProductsInfoByUserGroupSID($userGroupSID);
			$productsInfo = array();
			$user_sids_in_group = SJB_UserManager::getUserSIDsByUserGroupSID($userGroupSID);
			$usersPerProduct = array();
			$productProperty = array(
					'id'			=> 'default_product',
					'caption'		=> 'Default Product',
					'type'			=> 'list',
					'list_values'	=> array(),
					'is_required'	=> false,
					'is_system'		=> true,
					'order'			=> 1,
					'value'			=> $userGroupInfo['default_product'],
			);
			foreach ($productSIDs as $product) {
				$productsInfo[] = $product;
				$productProperty['list_values'][] = array(
					'id' => $product['sid'],
					'caption' => $product['name'],
				);
				$usersPerProduct[$product['sid']] = count(array_intersect(
						$user_sids_in_group,
						SJB_UserManager::getUserSIDsByProductSID($product['sid'])
				));
			}
			$userGroup->addProperty($productProperty);

			$form = new SJB_Form($userGroup);
			$isSubmit = SJB_Request::getVar('submit');

			if ($isSubmit && $form->isDataValid($errors)) {

				SJB_UserGroupManager::saveUserGroup($userGroup);

				if ($isSubmit == 'save_info') {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/user-groups/');
				}
			}


			$form->registerTags($tp);
			$tp->assign('object_sid', $userGroup->getSID());
			$tp->assign('notifications', $userGroup->getNotifications());
			$tp->assign('notificationGroups', $userGroup->getNotificationsGroups());
			$tp->assign('user_group_sid', $userGroupSID);
			$tp->assign('user_group_products_info', $productsInfo);
			$tp->assign('user_group_product_user_number', $usersPerProduct);
			$tp->assign('form_fields', $form->getFormFieldsInfo());
		}
		else {
			$errors['USER_GROUP_SID_NOT_SET'] = 1;
		}

		$tp->assign('user_group_info', isset($userGroupInfo) ? $userGroupInfo : null);
		$tp->assign('errors', $errors);
		$tp->assign('object_sid', $userGroupSID);
		$tp->display('edit_user_group.tpl');
	}
}
