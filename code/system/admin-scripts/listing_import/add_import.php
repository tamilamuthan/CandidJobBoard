<?php

class SJB_Admin_ListingImport_AddImport extends SJB_Function
{
	public function execute()
	{
		$errors = array();
		if (SJB_Request::isAjax()) {
			$response = null;
			$user_type = SJB_Request::getVar('user_type');
			$user_name = SJB_Request::getVar('parser_user');
			$products = SJB_XmlImport::getProducts($user_type, $user_name, $errors);
			$response = array(
				'products' => empty($products) ? '' : SJB_XmlImport::translateProductsName($products),
				'error' => empty($errors) ? '' : array_pop($errors)
			);
			die(json_encode($response));
		}

		$tp = SJB_System::getTemplateProcessor();
		$add_level = SJB_Request::getVar('add_level', 1);
		$selectUserType = SJB_Request::getVar('selectUserType');
		$add_new_user = $selectUserType == 'group' ? 1 : 0 ;
		$parsing_name = SJB_Request::getVar('parser_name', '');
		$usr_id = '';

		switch ($add_level) {

			case '1':
				$tp->display('add_step_one.tpl');
				break;

			case '3':
				$parserInfo = $_REQUEST;
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

				$id = SJB_Request::getVar('id', 0, 'GET');
				$parserInfo['id'] = $id;
				$parserInfo['usr_id'] = $usr_id;
				$parserInfo['usr_name'] = $usr_name;
				$parserInfo['import_type'] = !empty($parserInfo['import_type']) ? $parserInfo['import_type'] : 'increment';
				$original_xml = SJB_Request::getVar('xml', '');
				$mapped = SJB_Request::getVar('mapped', '');

				$parserInfo['username'] = !empty($parserInfo['username']) ? $parserInfo['username'] : '';
				$parserInfo['username'] = SJB_XmlImport::decodeSpecialEntities($parserInfo['username']);
				$parserInfo['external_id'] = str_replace('_dog_', '@', $parserInfo['external_id']);
				if (!empty($mapped) && is_array($mapped) && !empty($original_xml) && empty($errors)) {
					$result = SJB_XmlImport::saveImport($parserInfo, $addNewUser);
					$form_submitted = SJB_Request::getVar('form_action');
					if (empty($result['errors'])) {
						if ($form_submitted == 'save_info') {
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/show-import/');
						} elseif ($form_submitted == 'apply_info') {
							$getterParameters = '?id=' . $result['id'];
							if (!empty($result['errors'])) {
								$getterParameters .= '&error=' . $result['errors'];
							}
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-import/' . $getterParameters );
						}
						break;
					} else {
						$errors = array_merge($errors, $result['errors']);
					}
				} else {
					if (empty($errors)) {
						$errors[] = 'No data to save';
					}
				}

			case '2':
				$template = 'add_step_two.tpl';

				$original_xml = SJB_Request::getVar('xml');
				$xml = $original_xml;

				$tree = '';
				$listing_fields = array();

				$parsing_name       = SJB_Request::getVar('parser_name');
				$usr_name           = SJB_Request::getVar('parser_user');
				$pars_url           = SJB_Request::getVar('parser_url');
				$form_description   = SJB_Request::getVar('form_description', '', 'POST');
				$type_id            = SJB_ListingTypeManager::getListingTypeSIDByID('Job');
				$selectedProduct    = SJB_Request::getVar('postUnderProduct');
				$id                 = SJB_Request::getVar('id', 0, 'GET');
				$import_type        = SJB_Request::getVar('import_type', 'increment');
				$selected           = array();
				$a_selected         = array();

				if (!empty($_REQUEST['xml']) || $id > 0) {
					// step 2 OR edit exist

					if ($id > 0) { // load exist parser

						$parser_from_id = SJB_XmlImport::getSystemParsers($id);

						if (isset($parser_from_id[0]['name'])) {
							$parser_from_id = $parser_from_id[0];
						}

						$parsing_name = $parser_from_id['name'];
						$usr_id = $parser_from_id['usr_id'];
						$usr_name = $parser_from_id['usr_name'];
						$form_description = $parser_from_id['description'];
						$pars_url = $parser_from_id['url'];
						$type_id = $parser_from_id['type_id'];
						$selectedProduct = $parser_from_id['product_sid'];
						$xml = $parser_from_id['xml'];
						$xml = SJB_XmlImport::cleanXmlFromImport($xml);

						$map = unserialize($parser_from_id['maper']);
						$selected = array_values($map);
						$a_selected = array_keys($map);

					} else {
						$xml = SJB_XmlImport::cleanXmlFromImport($_REQUEST['xml']);
					}

					$sxml = new simplexml();
					$tree = @$sxml->xml_load_file($xml, 'array');
					if (isset($tree['@content'])) {
						$tree = $tree[0];
					}
					
					if (is_array($tree)) {

						$tree = SJB_XmlImport::convertArray($tree);
						foreach ($tree as $key => $val) {
							unset($tree[$key]);
							// replace '@' and ':'
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
								} else {
									$listing_field = new SJB_ListingField($listing_field_info);
									$listing_field->setSID($listing_field_info['sid']);
									$listing_fields[$i]['id'] = $listing_field->details->properties['id']->value;
									$listing_fields[$i]['caption'] = $listing_field->details->properties['caption']->value;
									$i++;
								}
							}
						}
						$listing_fields[$i]['id'] = $listing_fields[$i]['caption'] = 'external_id';
					} else {
						$errors[] = 'XML parsing error.';
						$template = 'add_step_one.tpl';
					}

				} else {
					$errors[] = 'Please input correct xml';
					$template = 'add_step_one.tpl';
				}

				$tp->assign('id', $id);
				$tp->assign('selected', $selected);
				$tp->assign('a_selected', $a_selected);
				$tp->assign('xml', htmlspecialchars($xml));
				$tp->assign('xmlToUser', $xml);
				$tp->assign('form_name', $parsing_name);
				$tp->assign('form_user', $usr_name);
				$tp->assign('form_url', $pars_url);
				$tp->assign('form_description', $form_description);
				$type_name = SJB_ListingTypeManager::getListingTypeIDBySID($type_id);
				$tp->assign('type_id', $type_id);
				$tp->assign('type_name', $type_name);
				$tp->assign('errors', $errors);
				$tp->assign('tree', $tree);
				$tp->assign('fields', $listing_fields);
				$tp->assign('selectedProduct', $selectedProduct);
				$tp->assign('add_new_user', $add_new_user);
				$tp->assign('form_user_sid', $usr_id);
				$tp->assign('import_type', $import_type);
				$tp->display($template);
				break;
		}
	}
}
