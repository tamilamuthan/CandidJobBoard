<?php

class SJB_XmlImport
{
	/**
	 * Storage for posted listings ids for each parser to realize 'snapshot' mode
	 * @var array
	 */
	static $postedByParser = array();
	
	static $addListingErrors = array();
	

	public static function cleanXmlFromImport($xml)
	{
		$xml = str_replace("\r", '', $xml ); // cut new line
		$xml = str_replace("\n", '', $xml ); // cut new line
		$xml = preg_replace('/&(?!amp;)/u', '&amp;', $xml ); // CUT comment
		$xml = preg_replace('#<([-a-z]*)\/>#siu', '<$1>.</$1>', $xml ); // make empty readible
		$xml = preg_replace('#(\<\!\-\-.*?\>)#siu', '', $xml ); // CUT comment
		return $xml;
	}
	
	public static function saveImport($parserInfo, $addNewUser = 0) {
		// make map
		$map1 = array();
		$map2 = array();
		$serUserMap = '';

		foreach ($parserInfo['mapped'] as $one) {
			$tmp = explode(':', $one);
			$map1[] = $tmp[0];
			$map2[] = $tmp[1];
		}
		$mapped_user = !empty($parserInfo['mapped_user']) ? $parserInfo['mapped_user'] : '';
		if ($addNewUser == 1 && !empty($mapped_user) && is_array($mapped_user)) {
			// make map
			$mapUser1 = array();
			$mapUser2 = array();
			foreach ($mapped_user as $one) {
				$tmp = explode(':', $one);
				$mapUser1[] = str_replace('user_', '', $tmp[0]);
				$mapUser2[] = $tmp[1];
			}
			foreach ($mapUser1 as $key => $val) {
				$val = SJB_XmlImport::decodeSpecialEntities($val);
				$mapUser[$val] = $mapUser2[$key];
			}
			$serUserMap = serialize($mapUser);
		}
		//$map = array_combine($map1, $map2); // PHP5
		foreach ($map1 as $key => $val) {
			$val = SJB_XmlImport::decodeSpecialEntities($val);
			$map[$val] = $map2[$key];
		}

		if (isset($parserInfo['default_value'])) {
			foreach ($parserInfo['default_value'] as $key => $val) {
				if ($val == '') {
					unset($parserInfo['default_value'][$key]);
				}
			}
			$parserInfo['default_value'] = serialize($parserInfo['default_value']);
		} else {
			$parserInfo['default_value'] = '';
		}
		if (isset($parserInfo['user_default_value'])) {
			foreach ($parserInfo['user_default_value'] as $keyuser => $valuser) {
				if ($valuser == '') {
					unset($parserInfo['user_default_value'][$keyuser]);
				}
			}
			$parserInfo['user_default_value'] = serialize($parserInfo['user_default_value']);
		} else {
			$parserInfo['user_default_value'] = '';
		}
		
		$queryId = intval($parserInfo['id']);
		$query = "SET
					`type_id` = ?n,
					`name` = ?s,
					`description` = ?s,
					`url` = ?s,
					`usr_id` = ?n,
					`usr_name` = ?s,
					`maper_user` = ?s,
					`xml` = ?s,
					`add_new_user` = ?n,
					`username` = ?s,
					`external_id` = ?s,
					`product_sid` = ?n,
					`import_type` = ?s,
					`default_value` = ?s,
					`default_value_user` = ?s,
					`maper` = ?s";

		$serMap = serialize($map);
		if ($parserInfo['id'] > 0) {
			SJB_DB::query("UPDATE `parsers` {$query} WHERE id = ?n", $parserInfo['type_id'], $parserInfo['parser_name'], $parserInfo['form_description'], $parserInfo['parser_url'], $parserInfo['usr_id'], $parserInfo['usr_name'], $serUserMap, $parserInfo['xml'], $addNewUser, $parserInfo['username'], $parserInfo['external_id'], $parserInfo['postUnderProduct'], $parserInfo['import_type'], $parserInfo['default_value'], $parserInfo['user_default_value'], $serMap, $queryId);
		} else {
			$parserInfo['id'] = SJB_DB::query("INSERT INTO `parsers` {$query}", $parserInfo['type_id'], $parserInfo['parser_name'], $parserInfo['form_description'], $parserInfo['parser_url'], $parserInfo['usr_id'], $parserInfo['usr_name'], $serUserMap, $parserInfo['xml'], $addNewUser, $parserInfo['username'], $parserInfo['external_id'], $parserInfo['postUnderProduct'], $parserInfo['import_type'], $parserInfo['default_value'], $parserInfo['user_default_value'], $serMap);
		}
		return array('id' => $parserInfo['id'], 'errors' => array());
	}

	public static function activate($id)
	{
		SJB_DB::query("UPDATE parsers SET active='1' WHERE id='{$id}'");
	}

	public static function deactivate($id)
	{
		SJB_DB::query("UPDATE parsers SET active='0' WHERE id='{$id}'");
	}

	public static function addListings($data, $usr_id, $parser_id)
	{
		self::$addListingErrors = array();
		$parser = SJB_XmlImport::getSystemParsers($parser_id);
		if (!isset($parser[0])) {
			return;
		}

		$parser      = $parser[0];
		$currentUser = SJB_UserManager::getObjectBySID($usr_id );
		$listingFields = SJB_ListingFieldManager::getListingFieldsInfoByListingType(0);
		$listingFields = array_merge($listingFields, SJB_ListingFieldManager::getListingFieldsInfoByListingType($parser['type_id']));
		foreach ($data as $listing) {
			if (!$listing) {
				continue;
			}

			if (isset($listing['userSID'])) {
				$user = SJB_UserManager::getObjectBySID($listing['userSID']);
			} else {
				$user = $currentUser;
			}
			
			$listing['access_type'] = 'everyone';
			$listing['active']      = 1;

			if (empty($user)) {
				$listing['user_sid'] = '';
			} else {
				$listing['user_sid'] = $user->getSID();
			}
			
			$external_id = isset ($listing['external_id']) ? $listing['external_id'] : '';

			// fix for new format of ApplicationSettings
			if (!empty($listing['ApplicationSettings'])) {
				if (preg_match("^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,}\$^iu", $listing['ApplicationSettings'])) {
					$listing['ApplicationSettings'] = array( 'value' => $listing['ApplicationSettings'], 'add_parameter' => 1);
				} elseif(preg_match("^(https?:\/\/)^iu", $listing['ApplicationSettings'])) {
					$listing['ApplicationSettings'] = array( 'value' => $listing['ApplicationSettings'], 'add_parameter' => 2);
				} else {
					//put empty if not valid email or url
					$listing['ApplicationSettings'] = array( 'value' => '', 'add_parameter' => '');
				}
			}

			foreach ($listingFields as $listingField) {
				if ($listingField['type'] == 'location') {
					foreach ($listingField['fields'] as $fields) {
						if (isset($listing[$listingField['id'].'_'.$fields['id']])) {
							$listing[$listingField['id']][$fields['id']] = $listing[$listingField['id'].'_'.$fields['id']];
						}
					}
				}
			}

			$listingObj = new SJB_Listing($listing, $parser['type_id']);
			$listingObj->deleteProperty('featured');
			$listingObj->deleteProperty('status');
			$listingObj->addDataSourceProperty($parser_id);
			if ($parser['product_sid'] && $user) {
				$contractsInfo = SJB_ContractManager::getAllContractsInfoByUserSID($user->getSID());
				$extraInfo     = array();
				$contractId    = null;
				foreach ($contractsInfo as $contractInfo) {
					if ($contractInfo['product_sid'] == $parser['product_sid']) {
						$extraInfo  = unserialize($contractInfo['serialized_extra_info']);
						$contractId = $contractInfo['id'];
						break;
					}
				}
				if (!$extraInfo && $parser['product_sid']) {
					$extraInfo = SJB_ProductsManager::getProductExtraInfoBySID($parser['product_sid']);
				}

				$listingObj->setProductInfo($extraInfo);
				$listingObj->addProperty(array(
					'id'        => 'contract_id',
					'type'      => 'id',
					'value'     => $contractId,
					'is_system' => true
				));
			} elseif (!$parser['product_sid']) {
				self::$addListingErrors[] = 'Listings cannot be posted without a product. Please select an appropriate product in the settings of XML Import.';
				continue;
			}else {
				self::$addListingErrors[] = 'Required user profile fields are not mapped';
				continue;
			}
			
			$listingSid = null;
			if (!empty($external_id)) {
				$listingObj->addExternalIdproperty($listing['external_id']);
				$existingSid = SJB_ListingManager::getListingSidByExternalId($external_id);
				if (is_numeric($existingSid)) {
					$listingSid = $existingSid;
				}
			}
			$properties = $listingObj->getProperties();
			foreach ($properties as $property) {
				$propertyType = $property->type->property_info['type'];
				if ($propertyType === 'complex') {
					$complexProperties = $property->type->complex->getProperties();
					if (is_array($complexProperties)) {
						foreach ($complexProperties as $complexProperty) {
							if (!$complexProperty->value)
							$listingObj->details->properties[$property->id]->type->complex->setPropertyValue($complexProperty->id, array(1=>''));
						}
					}
				}
				if ($propertyType === 'list' || $propertyType === 'multilist') {
					$ignoreProps = array('data_source', 'access_type');
					if (!in_array($property->id, $ignoreProps)) {
						if ($propertyType === 'multilist' && strpos($property->value, ',') !== false) {
							$propertyValues = explode(',', $property->value);
							$listValues = array();
							$valuesCollection = array();
							foreach ($propertyValues as $propertyValue) {
								$listValues[] = "'$propertyValue'";
							}
							$listValues = is_array($listValues) ? implode(',', $listValues) : '';
							$matchedValues = SJB_DB::query("SELECT `sid` FROM `listing_field_list` WHERE `value` IN (?w) AND `field_sid` = ?n", $listValues, $property->type->property_info['sid']);
							if (is_array($matchedValues)) {
								foreach($matchedValues as $matchedValue) {
									$valuesCollection[] = $matchedValue['sid'];
								}
								$value = implode(',', $valuesCollection);
							}
						} else {
							$value = SJB_DB::queryValue("SELECT `sid` FROM `listing_field_list` WHERE `value`= ?s AND `field_sid` = ?n", $property->value, $property->type->property_info['sid']);
						}
						if (empty($value)) {
							$value = '';
						}
						$listingObj->setPropertyValue($property->id, $value);
						$listingObj->getProperty($property->id)->type->property_info['value'] = $value;
					}
				}
			}
			
			// set listing sid if listing already exists
			$updatedListing = false;
			if (is_numeric($listingSid)) {
				// todo: нужно отказаться от listingInfo для activation даты и соответственно переписать сохранение листинга
				$listingInfo = SJB_ListingManager::getListingInfoBySID($listingSid);
				$listingObj->setSID($listingSid);
				$listingObj->setActivationDate($listingInfo['activation_date']);
				$listingObj->deleteProperty('expiration_date');
				$updatedListing = true;
			}

			SJB_ListingManager::saveListing($listingObj);
			SJB_ProductsManager::incrementPostingsNumber($parser['product_sid']);
			$listingSid = $listingObj->getSID();

			if (!$updatedListing) {
				SJB_ListingManager::activateListingBySID($listingSid, false, false, $listingObj->getActivationDate());
				if (!empty($listing['expiration_date'])) {
					$i18n = SJB_I18N::getInstance();
					if ($i18n->isValidDate($listing['expiration_date'])) {
						SJB_ListingDBManager::setListingExpirationDate($listingSid, $i18n->getInput('date', $listing['expiration_date']));
					}
				}
				if ($extraInfo['featured']) {
					SJB_ListingManager::makeFeaturedBySID($listingSid);
				}
			}
			// and save listing sid to self::$postedByParser storage
			self::$postedByParser[] = $listingSid;
		}
		SJB_BrowseDBManager::addListings(self::$postedByParser);
	}

	public static function getRootNode($xml)
	{
		preg_match('/<(.*?)>/i', $xml, $mathc );
		if (isset($mathc[1]) && strlen($mathc[1]) > 0)
			return $mathc[1];
		return false;
	}

	public static function megaReader($root, $array)
	{
		$tmp_arr = array();
		foreach ($array as $key => $val) {
			if ($key == $root) {
				$tmp_arr = array_merge($tmp_arr, $val);
			} elseif (is_array($val)) {
				$tmp_arr = array_merge($tmp_arr, self::megaReader($root, $val));
			}
		}
		return $tmp_arr;
	}

	public static function getListingArray($root, $tree)
	{
		return SJB_XmlImport::megaReader($root, $tree);
	}

	public static function parseData($found, $map, $defaultValues = array())
	{
        $data = array();
        $external_id = '';
        foreach ($found as $one) {
            $tmp = array();
            foreach ($map as $remote => $local) {
                if(strpos($remote, 'external_id') !== false) {
                    $external_id = str_replace("_external_id", "", $remote);
                    $remote = str_replace("_external_id", "", $remote);
                    $external_id = $one[$external_id];
                }
                if (isset($one[$remote])) {
                    // fix convert of &nbsp; to non-ASCII character
                    $one[$remote] = str_replace("&nbsp;", " ", $one[$remote]);
                    if (is_array($local)) {
                        foreach ($local as $arr) {
                            $tmp[$arr] = stripslashes(html_entity_decode($one[$remote], ENT_COMPAT,'UTF-8'));
                        }
                    }
                    else {
                        $tmp[$local] = stripslashes(html_entity_decode($one[$remote], ENT_COMPAT,'UTF-8'));
                        $tmp['external_id'] = $external_id;
                    }
                }
            }
            $data[] = array_merge($tmp, $defaultValues);
        }
        return $data;
	}

	public static function convertArray($array, $parent = '')
	{
		$tmp = array();
		foreach ($array as $key => $val) {
			if (is_array($val))
				$tmp = array_merge($tmp, self::convertArray($val, (!is_numeric($key) ? $key : '')));
			else
				$tmp[(! empty($parent) ? $parent . '_' : '') . $key] = $val;
		}

		return $tmp;
	}


	public static function is_multy($array)
	{
		foreach ($array as $ar) {
			if (!is_array($ar)) {
				return false;
			}
		}
		return true;
	}

	public static function runImport($id_pars = '')
	{
		$work_id = SJB_XmlImport::getSystemParsers($id_pars);
		$result = array('total' => 0, 'errors');

		foreach ($work_id as $pars) {
			$result['total']++;
			$map               = unserialize($pars['maper']);
			$defaultValues     = ($pars['default_value'] != '')?unserialize($pars['default_value']):array();
			$defaultValuesUser = ($pars['default_value_user'] != '')?unserialize($pars['default_value_user']):array();
			// MAP (REMOTE >> LOCAL)
			$usr_id            = $pars['usr_id'];

			if ($root = SJB_XmlImport::getRootNode($pars['xml'])) {
				$sxml      = new simplexml();
	            $xmlString = SJB_HelperFunctions::getUrlContentByCurl($pars['url']);
	            if ($xmlString === false) {
					$result['errors'][] = 'Failed to open data URL, data source - '.$pars['name'];
					continue;
	            }

				@$tree = $sxml->xml_load_file($xmlString, 'array');
				if (!$tree || ! is_array($tree)){
					$result['errors'][] = 'Failed to open data URL, data source - '.$pars['name'];
					continue;
				}

				if (isset($tree['@content']))
					$tree = $tree[0];

				$found = SJB_XmlImport::getListingArray($root, $tree);
				if (!SJB_XmlImport::is_multy($found)) {
					$tmp     = $found;
					$found   = array();
					$found[] = $tmp;
				}

				foreach ($found as $key => $val) {
					$found[$key] = SJB_XmlImport::convertArray($val);
				}

				// field in username to mapping it, and default mapping(incomingFieldName -> username)
				$parsUsername = $pars['username'];
				$mapUser[$parsUsername] = 'username';

				// check for non default mapping
				if ($pars['add_new_user'] == 1 && !empty($pars['maper_user'])) {
					$mapUser = unserialize($pars['maper_user']);
					if (array_key_exists($parsUsername, $mapUser)) {
						$mapUser[$parsUsername] = array($mapUser[$parsUsername], 'username');
					} else {
						$mapUser[$parsUsername] = 'username';
					}
				}

				$data = SJB_XmlImport::parseData($found, $map, $defaultValues);
				if ($pars['add_new_user'] == 1) {
					$dataUser = SJB_XmlImport::parseData($found, $mapUser, $defaultValuesUser);
					$user_group_sid = $pars['usr_id'];
					$userProfileFields = SJB_UserProfileFieldManager::getFieldsInfoByUserGroupSID($user_group_sid);
					foreach ($dataUser as $key => $user){
						if (isset($user['username']) && $user['username'] != '') {
							$username = preg_replace('/[\\/\\\:*?\"<>|%#$\s\'-]/u', '_',html_entity_decode($user['username']));
							$username = str_replace('&', 'And', $username);
							// If user_email_as_username set to TRUE

							$user['username'] = $username;
							if (!empty($user['password'])) {
								unset($user['password']);
							}
							$user['password']['confirmed'] = $user['password']['original'] = $username;

							$userSID = SJB_UserManager::getUserSIDbyUsername($user['username']);
							if (empty($userSID)) {
								foreach ($userProfileFields as $userProfileField) {
									if ($userProfileField['type'] == 'location') {
										foreach ($userProfileField['fields'] as $fields) {
											if (isset($user[$userProfileField['id'].'_'.$fields['id']])) {
												$user[$userProfileField['id']][$fields['id']] = $user[$userProfileField['id'].'_'.$fields['id']];
											}
										}
									}
								}
								$userObj = SJB_ObjectMother::createUser($user, $user_group_sid);
								$userObj->deleteProperty('active');
								$userObj->deleteProperty('featured');
								SJB_UserManager::saveUser($userObj);
								SJB_UserManager::activateUserByUserName($userObj->getUserName());
								$contract = new SJB_Contract(array('product_sid' => $pars['product_sid']));
								$contract->setUserSID($userObj->getSID());
								$contract->saveInDB();
								$data[$key]['userSID'] = $userObj->getSID();
							}
							else {
								$data[$key]['userSID'] = $userSID;
							}
						}
					}
				}
				
				// set start value for current parser
				self::$postedByParser = array();
				
				if (count($data) > 0) {
					SJB_XmlImport::addListings($data, $usr_id, $pars['id']);
				}
				
				// clear listings, not saved or updated by current snapshot import
				if ($pars['import_type'] == 'snapshot') {
					if (sizeof(self::$postedByParser)) {
						SJB_DB::queryExec("DELETE FROM `listings` WHERE `data_source` = ?n AND `sid` NOT IN (?l)", $pars['id'], self::$postedByParser);
					} else {
						SJB_DB::queryExec("DELETE FROM `listings` WHERE `data_source` = ?n", $pars['id']);
					}
				}
				
			} else {
				$result['errors'][] = 'Not correct XML in parser - '.$pars['name'];
				continue;
			}

		}
		if (!empty(self::$addListingErrors)) {
			if (!isset($result['errors'])) {
				$result['errors'] = self::$addListingErrors;
			} else {
				$result['errors'] = array_merge($result['errors'], self::$addListingErrors);
			}
		}
		return $result;
	}

	public static function getSystemParsers($id = '', $all = false)
	{
		return SJB_DB::query("SELECT * FROM parsers WHERE " . (!empty($id)?"id='{$id}'":(!$all?"active='1'":"active='0' OR active='1'")));
	}

	public static function getProducts($userType, $userName, &$errors)
	{
		$products = array();
		if ($userType == 'group') {
			$products = self::getProductsByUserGroup($userName);
		} else {
			try {
				$products = self::getProductsByUserName($userName);
			} catch (Exception $e) {
				$errors[] = SJB_I18N::getInstance()->gettext('Backend', $e->getMessage());
			}
			
		}

		return $products;
	}
	
	public static function decodeSpecialEntities($val)
	{
		$val = str_replace('_dog_', '@', $val);
		$val = str_replace('_col_', ':', $val);
		return $val;
	}
	
	public static function encodeSpecialEntities($val)
	{
		$val = str_replace('@', '_dog_', $val);
		$val = str_replace(':', '_col_', $val);
		return $val;
	}
	
	public static function translateProductsName($products)
	{
		foreach ($products as &$product) {
			$product['name'] = SJB_I18N::getInstance()->gettext('Backend', $product['name']);
		}
		
		return $products;
	}	
	
	private static function getProductsByUserGroup($userGroupSID)
	{
		return SJB_ProductsManager::getUserGroupProducts($userGroupSID);
	}


	/**
	 * @param $userName
	 * @return array
	 * @throws Exception
	 */
	private static function getProductsByUserName($userName)
	{
		$products = array();
		$userSid = SJB_UserManager::getUserSIDbyUsername($userName);
		if (empty($userSid)) {
			throw new Exception("User not exists. Please enter user name of existing user to the 'User Name' field.");
		}
		$contractsInfo = SJB_ContractManager::getAllContractsInfoByUserSID($userSid);
		if (empty($contractsInfo)) {
			throw new Exception("User doesn't have any product. Please select another user or add at least one posting product to the current user.");
		}
		foreach ($contractsInfo as $contractInfo) {
			$products[] = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
		}
		
		return $products;
	}
	
} // END of SJB_XmlImport


