<?php

class SJB_Admin_Users_Users extends SJB_Function
{
	public function isAccessible()
	{
		$passedParametersViaUri = SJB_UrlParamProvider::getParams();
		$userGroupID = $passedParametersViaUri ? array_shift($passedParametersViaUri) : false;
		$this->setPermissionLabel('manage_' . mb_strtolower($userGroupID, 'UTF-8'));
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$template = SJB_Request::getVar('template', 'users.tpl');
		$searchTemplate = SJB_Request::getVar('search_template', 'user_search_form.tpl');
		$passedParametersViaUri = SJB_UrlParamProvider::getParams();
		$userGroupID = $passedParametersViaUri ? array_shift($passedParametersViaUri) : false;
		$userGroupSID = $userGroupID ? SJB_UserGroupManager::getUserGroupSIDByID($userGroupID) : null;
		$errors = array();
		/********** A C T I O N S   W I T H   U S E R S **********/
		$action = SJB_Request::getVar('action_name');

		if (!empty($action)) {
			$users_sids = SJB_Request::getVar('users', array());
			$_REQUEST['restore'] = 1;

			switch ($action) {

				case  'activate':
					foreach ($users_sids as $user_sid => $value) {
						$username = SJB_UserManager::getUserNameByUserSID($user_sid);
						SJB_UserManager::activateUserByUserName($username);
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					break;

				case 'deactivate':
					foreach ($users_sids as $user_sid => $value) {
						$username = SJB_UserManager::getUserNameByUserSID($user_sid);
						SJB_UserManager::deactivateUserByUserName($username);
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					break;

				case 'delete':
						foreach (array_keys($users_sids) as $user_sid) {
							try {
								SJB_UserManager::deleteUserById($user_sid);
							} catch (Exception $e) {
								$errors[] = $e->getMessage();
							}
						}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
					break;

				default:
					unset($_REQUEST['restore']);
					break;
			}
			if (empty($errors)) {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI());
			}
		}

		/***************************************************************/

		$_REQUEST['action'] = 'search';

		$user = new SJB_User(array(), $userGroupSID);
		$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userGroupSID);

		$user->addProperty(array(
			'id' => 'user_group',
			'type' => 'list',
			'value' => '',
			'is_system' => true,
			'list_values' => SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions()
		));
		
		$user->addProperty(array(
			'id' => 'registration_date',
			'type' => 'date',
			'value' => '',
			'is_system' => true
		));

		// get array of accessible products
		$productsSIDs = SJB_ProductsManager::getProductsIDsByUserGroupSID($userGroupSID);
		$products = array();
		foreach ($productsSIDs as $key => $productSID) {
			$product = SJB_ProductsManager::getProductInfoBySID($productSID);
			$products[$key] = $product;
		}

		$user->addProperty(array
			(
				'id' => 'product',
				'type' => 'list',
				'value' => '',
				'list_values' => $products,
				'is_system' => true,
			)
		);

		$aliases = new SJB_PropertyAliases();

		$aliases->addAlias(array(
				'id' => 'user_group',
				'real_id' => 'user_group_sid',
				'transform_function' => 'SJB_UserGroupManager::getUserGroupSIDByID'
			)
		);

		$aliases->addAlias(array(
				'id' => 'product',
				'real_id' => 'product_sid',
			)
		);
		
		$_REQUEST['user_group']['equal'] = $userGroupSID;

		$search_form_builder = new SJB_SearchFormBuilder($user);
		$criteria_saver = new SJB_UserCriteriaSaver('Users' . $userGroupID);

		if (isset($_REQUEST['restore'])) {
			$_REQUEST = array_merge($_REQUEST, $criteria_saver->getCriteria());
		}

		$criteria = $search_form_builder->extractCriteriaFromRequestData($_REQUEST, $user);
		$search_form_builder->setCriteria($criteria);
		$search_form_builder->registerTags($tp);

		$tp->assign('userGroupInfo', $userGroupInfo);
		$tp->assign('products', $products);
		$tp->assign('selectedProduct', isset($_REQUEST['product']['simple_equal']) ? $_REQUEST['product']['simple_equal'] : '');
		$tp->display($searchTemplate);

		/********************** S O R T I N G *********************/
		$paginator = new SJB_UsersPagination($userGroupInfo, $template);

		$criteria = $search_form_builder->extractCriteriaFromRequestData($_REQUEST, $user);
		$inner_join = false;

		// if search by product field
		if (isset($_REQUEST['product']['simple_equal']) && $_REQUEST['product']['simple_equal'] != '') {
			$inner_join = array('contracts' => array('join_field' => 'user_sid', 'join_field2' => 'sid', 'join' => 'INNER JOIN'));
		}

		$searcher = new SJB_UserSearcher(array('limit' => ($paginator->currentPage - 1) * $paginator->itemsPerPage, 'num_rows' => $paginator->itemsPerPage), $paginator->sortingField, $paginator->sortingOrder, $inner_join);

		$found_users = array();
		$found_users_sids = array();

		if (SJB_Request::getVar('action', 'search') == 'search') {
			$found_users = $searcher->getObjectsSIDsByCriteria($criteria, $aliases);
			$criteria_saver->setSession($_REQUEST, $searcher->getFoundObjectSIDs());
		}
		elseif (isset($_REQUEST['restore'])) {
			$found_users = $criteria_saver->getObjectsFromSession();
		}
		foreach ($found_users as $id => $userID) {
			$found_users[$id] = SJB_UserManager::getUserInfoBySID($userID);
		}

		$paginator->setItemsCount($searcher->getAffectedRows());
		$sorted_found_users_sids = $found_users_sids;

		/****************************************************************/
		$tp->assign("userGroupInfo", $userGroupInfo);
		$tp->assign("found_users", $found_users);
		$searchFields = '';
		foreach ($_REQUEST as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $fieldName => $fieldValue) {
					if (is_array($fieldValue)) {
						foreach ($fieldValue as $fieldSubName => $fieldSubValue) {
							$searchFields .= "&{$key}[{$fieldSubName}]=" . array_pop($fieldSubValue);
						}
					} else {
						$searchFields .= "&{$key}[{$fieldName}]={$fieldValue}";
					}
				}
			}
		}
		$tp->assign('paginationInfo', $paginator->getPaginationInfo());
		$tp->assign("searchFields", $searchFields);
		$tp->assign("found_users_sids", $sorted_found_users_sids);
		$tp->assign('errors', $errors);
		$tp->display($template);
	}
}
