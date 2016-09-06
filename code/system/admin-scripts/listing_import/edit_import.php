<?php

class SJB_Admin_ListingImport_EditImport extends SJB_Function
{
	public function execute()
	{
		$errors = array();
		if (SJB_Request::isAjax()) {
			$response = null;
			if ($userName = SJB_Request::getVar('parser_user')) {
				$userType = SJB_Request::getVar('user_type');
				$products = SJB_XmlImport::getProducts($userType, $userName, $errors);
				$response = array(
					'products' => empty($products) ? '' : SJB_XmlImport::translateProductsName($products),
					'error' => empty($errors) ? '' : array_pop($errors)
				);
				$response = json_encode($response);
			}
			die($response);
		}

		$tp = SJB_System::getTemplateProcessor();
		$original_xml = (!empty ($_REQUEST ['xml']) ? $_REQUEST ['xml'] : '');
		$xml = $original_xml;

		$usr_id = 0;
		$importType = '';
		$tree = '';
		$listing_fields = array();

		$parsing_name        = SJB_Request::getVar('parser_name', '');
		$usr_name            = SJB_Request::getVar('parser_user', '');
		$pars_url            = SJB_Request::getVar('parser_url', '');
		$form_description    = SJB_Request::getVar('form_description', '');
		$type_id             = SJB_Request::getVar('type_id', '', 'POST');
		$add_new_user        = SJB_Request::getVar('add_new_user', 0);
		$username            = SJB_Request::getVar('username', '');
		$external_id         = SJB_Request::getVar('external_id', '');
		$defaultValue        = array();

		$id                 = SJB_Request::getVar('id', 0, 'GET');
		$selected           = array();
		$a_selected         = array();
		$selectedProduct    = SJB_Request::getVar('postUnderProduct');
		$formIsSubmitted = SJB_Request::getVar('form_action', false);
		
		if (!empty ($_REQUEST ['xml']) || $id > 0) {
			if ($id > 0) { // load exist parser
				
				$parserInfo = SJB_XmlImport::getSystemParsers($id);
				if (isset($parserInfo [0] ['name'])) {
					$parserInfo = $parserInfo[0];
				}
				if ($formIsSubmitted) {
					$mergeFields = array('parser_name' => 'name', 'form_description' => 'description', 'parser_url' => 'url', 'postUnderProduct' => 'product_sid');
					foreach ($mergeFields as $formField => $dbField) {
						if (empty($parserInfo[$formField])) {
							$parserInfo[$formField] = $parserInfo[$dbField];
						}
						if (empty($_REQUEST[$dbField])) {
							$_REQUEST[$dbField] = $_REQUEST[$formField];
						}
					}
					$parserInfo = array_merge($parserInfo, $_REQUEST);
					$selectUserType = SJB_Request::getVar('selectUserType');
					$parsing_name = SJB_Request::getVar('parser_name', '');
					$selectedProduct = SJB_Request::getVar('postUnderProduct');
					$addNewUser = 0;
					if ($selectUserType == 'username') {
						$usr_name = (isset($parserInfo['parser_user']) ? SJB_DB::quote($parserInfo['parser_user']) : '');
						$usr_id = SJB_UserManager::getUserSIDbyUsername($usr_name);
						if (empty($usr_name)) {
							$errors[] = 'Please enter user name of existing user to the "User Name" field';
							$usr_name = '';
						} else {
							$user_sid_exists = SJB_UserManager::getUserSIDbyUsername($usr_name);
							if (empty($user_sid_exists)) {
								$errors[] = 'User "' . $usr_name . '" not exists. Please enter user name of existing user to the "User Name" field';
								$usr_name = '';
							}
						}
					}
					elseif ($selectUserType == 'group') {
						$userGroupSid = (isset($parserInfo['parser_user']) ? $parserInfo['parser_user'] : 0);
						$usr_id = $userGroupSid;
						$usr_name = SJB_UserGroupManager::getUserGroupIDBySID($usr_id);
						$addNewUser = 1;
					}
					if (preg_match('/[^_,\-\p{L}\d\s]/u', $parsing_name)) {
						$errors['Data Source Name'] = 'NOT_VALID_VALUE';
					}
					if (!filter_var($parserInfo['parser_url'], FILTER_VALIDATE_URL)) {
						$errors[] = 'Please input correct URL';
					}
					$productInfo = SJB_ProductsManager::getProductInfoBySID($selectedProduct);

					$id = SJB_Request::getVar('id', 0, 'GET');
					$parserInfo['id'] = $id;
					$parserInfo['usr_id'] = $usr_id;
					$parserInfo['usr_name'] = $usr_name;
					$parserInfo['import_type'] = !empty($parserInfo['import_type']) ? $parserInfo['import_type'] : 'increment';
					$original_xml = SJB_Request::getVar('xml', '');
					$mapped = SJB_Request::getVar('mapped', '');
					
					$parserInfo['username'] = SJB_XmlImport::decodeSpecialEntities($parserInfo['username']);
					$parserInfo['external_id'] = str_replace('_dog_', '@', $parserInfo['external_id']);
					if (!empty($mapped) && is_array($mapped) && !empty($original_xml) && empty($errors)) {
						$result = SJB_XmlImport::saveImport($parserInfo, $addNewUser);
						if (empty($result['errors'])) {
							if ($formIsSubmitted == 'save_info') {
								SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/show-import/');
							} else {
								$parserInfo = SJB_XmlImport::getSystemParsers($id);
								$parserInfo = array_pop($parserInfo);
								$formIsSubmitted = false;
							}
						} else {
							$errors = array_merge($errors, $result['errors']);
						}
					}
				}

				$parsing_name        = $parserInfo ['name'];
				$usr_id              = $parserInfo ['usr_id'];
				$usr_name            = $parserInfo ['usr_name'];
				$form_description    = $parserInfo ['description'];
				$pars_url            = $parserInfo ['url'];
				$type_id             = $parserInfo ['type_id'];
				$add_new_user        = $parserInfo ['add_new_user'];
				$importType          = $parserInfo ['import_type'];
				$xml                 = $parserInfo ['xml'];
				$defaultValue        = $parserInfo ['default_value'];
				$username            = $parserInfo ['username'];
				$map                 = unserialize($parserInfo ['maper']);
				$external_id         = str_replace('@', '_dog_', $parserInfo['external_id']);

				if (!$formIsSubmitted) {
					$xml = SJB_XmlImport::cleanXmlFromImport($xml);
					$defaultValue = ($defaultValue != '') ? unserialize($defaultValue) : array();
				}
				
				foreach ($map as $key => $val) {
					unset($map[$key]);
					$key = SJB_XmlImport::encodeSpecialEntities($key);
					$map[$key] = $val;
				}
				$selected   = array_values($map);
				$a_selected = array_keys($map);
				$selectedProduct = $parserInfo['product_sid'];
			} else {
				$xml = SJB_XmlImport::cleanXmlFromImport(SJB_Request::getVar('xml'));
			}

			$sxml = new simplexml ();
			$xml  = stripslashes($xml);
			$tree = $sxml->xml_load_file($xml, 'array');
			if (isset($tree['@content']))
				$tree = $tree[0];

			if (is_array($tree)) {

				$tree = SJB_XmlImport::convertArray($tree);
				foreach ($tree as $key => $val) {
					unset($tree[$key]);
					$key = SJB_XmlImport::encodeSpecialEntities($key);
					$tree[$key]['val'] = $val;
					$tree[$key]['key'] = $key;
				}
				$field_types = array(0, $type_id);
				$listing_fields = array();
				$i = 0;
				foreach ($field_types as $type) {
					$listing_fields_info = SJB_ListingFieldManager::getListingFieldsInfoByListingType($type);
					foreach ($listing_fields_info as $listing_field_info) {
						if ($listing_field_info['type'] == 'location') {
							foreach ($listing_field_info['fields'] as $fieldInfo) {
								$listing_field = new SJB_ListingField ($fieldInfo);
								$listing_field->setSID($fieldInfo['sid']);
								$listing_fields[$i]['id'] = $listing_field_info['id'].'_'.$listing_field->details->properties['id']->value;
								$listing_fields[$i]['caption'] = $listing_field->details->properties['caption']->value;
								$i++;
							}
						}
						else {
							$listing_field = new SJB_ListingField ($listing_field_info);
							$listing_field->setSID($listing_field_info ['sid']);
							$listing_fields[$i]['id'] = $listing_field->details->properties['id']->value;
							$listing_fields[$i]['caption'] = $listing_field->details->properties['caption']->value;
							$i++;
						}
					}

				}
				$listing_fields[$i]['id'] = $listing_fields[$i]['caption'] = 'external_id';
			} else {
				$errors [] = 'XML parsing error.';
			}

		} else {
			$errors [] = 'Please input correct xml';
		}
		if(empty($selectedProduct)) {
			$errors[] = 'Please select a product';
		}

		if (!filter_var($pars_url, FILTER_VALIDATE_URL)) {
			$errors[] = 'Please input correct URL';
		}

		$error = SJB_Request::getVar('error', false, 'GET');
		if ($error) {
			$errors[$error] = true;
		}
		
		$userType = empty($add_new_user) ? 'username' : 'group';
		if ($userType == 'group') {
			$userName = SJB_UserGroupManager::getUserGroupSIDByID($usr_name);
		} else {
			$userName = $usr_name;
		}
		$products = SJB_XmlImport::getProducts($userType, $userName, $errors);
		
		$tp->assign('id', $id);
		$tp->assign('selected', $selected);
		$tp->assign('a_selected', $a_selected);
		$tp->assign('xml', htmlspecialchars($xml));
		$tp->assign('xmlToUser', $xml);
		$tp->assign('default_value', $defaultValue);

		$tp->assign('form_name', $parsing_name);
		$tp->assign('form_user', $usr_name);
		$tp->assign('form_user_sid', $usr_id);
		$tp->assign('form_url', $pars_url);
		$tp->assign('form_description', $form_description);
		$tp->assign('username', $username);
		$tp->assign('external_id', $external_id);
		$tp->assign('import_type', $importType);

		$type_name = SJB_ListingTypeManager::getListingTypeIDBySID($type_id);

		$tp->assign('add_new_user', $add_new_user);
		$tp->assign('type_id', $type_id);
		$tp->assign('type_name', $type_name);
		$tp->assign('errors', $errors);
		$tp->assign('tree', $tree);
		$tp->assign('fields', $listing_fields);
		$tp->assign('selectedProduct', $selectedProduct);
		$tp->assign('products', $products);
		$tp->assign('editImport', 1);

		$tp->display('add_step_two.tpl');

	}
}
