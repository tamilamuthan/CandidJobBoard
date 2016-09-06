<?php

class SJB_Admin_Classifieds_ImportListings extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('import_listings');
		return parent::isAccessible();
	}

	public function execute()
	{
		ini_set('max_execution_time', 0);
		$tp = SJB_System::getTemplateProcessor();
		$file_info = isset($_FILES['import_file']) ? $_FILES['import_file'] : null;
		$encodingFromCharset = SJB_Request::getVar('encodingFromCharset', 'UTF-8');
		$listingTypeID = SJB_Request::getVar('listing_type_id', null);
		$productSID = SJB_Request::getVar('product_sid', 0);
		$errors = array();

		if ($listingTypeID && $productSID) {
			$acl = SJB_Acl::getInstance();
			$resource = 'post_' . strtolower($listingTypeID);
			if (!$acl->isAllowed($resource, $productSID, 'product'))
				$errors[] = 'You cannot import listings of this type under the selected product';
		}
		$listingType = SJB_ListingTypeManager::getListingTypeInfoBySID(SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID));
		$tp->assign('listingType', $listingType);
		if (!empty($file_info)) {
			$extension = SJB_Request::getVar('file_type');
			if (!SJB_ImportFile::isValidFileExtensionByFormat($extension, $file_info)) {
				$errors['DO_NOT_MATCH_SELECTED_FILE_FORMAT'] = true;
			}
		}

		if (empty($file_info) || SJB_UploadFileManager::getErrorId('import_file') || $errors) {
			if (SJB_UploadFileManager::getErrorId('import_file')) {
				$errors[SJB_UploadFileManager::getErrorId('import_file')] = 1;
			}
			$products = SJB_ProductsManager::getAllProductsInfo();
			foreach ($products as $key => $product) {
				if (empty($product['post_' . strtolower($listingTypeID)])) {
					unset($products[$key]);
				}
			}
			$tp->assign('products', $products);
			$tp->assign('errors', $errors);
			$tp->assign('charSets', SJB_HelperFunctions::getCharSets());
			$tp->display('import_listings.tpl');
		}
		else {
			$i18n = SJB_I18N::getInstance();
			$csv_delimiter = SJB_Request::getVar('csv_delimiter', null);
			$activeStatus = SJB_Request::getVar('active', 0);
			$activationDate = SJB_Request::getVar('activation_date');
			if ($activationDate) {
				$activationDate = $i18n->getInput('date', $activationDate);
			}
			$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);

			$extension = $_REQUEST['file_type'];

			if ($extension == 'xls') {
				$import_file = new SJB_ImportFileXLS($file_info);
			} elseif ($extension == 'csv') {
				$import_file = new SJB_ImportFileCSV($file_info, $csv_delimiter);
			}

			$import_file->parse($encodingFromCharset);

			$listing = $this->CreateListing(array(), $listingTypeID);
			$imported_data = $import_file->getData();

			$count = 0;
			$addedListingsSids = array();
			$nonExistentUsers = array();
			foreach ($imported_data as $key => $importedColumn) {
				if ($key == 1) {
					$importedDataProcessor = new SJB_ImportedDataProcessor($importedColumn, $listing);
					continue;
				}
				if (!$importedColumn)
					continue;
				$count++;
				$listingInfo = $importedDataProcessor->getData($importedColumn);
				$doc = new DOMDocument();
				foreach ($listing->getProperties() as $property) {
					if ($property->getType() == 'complex' && !empty($listingInfo[$property->id])) {
						$childFields = SJB_ListingComplexFieldManager::getListingFieldsInfoByParentSID($property->sid);
						$doc->loadXML($listingInfo[$property->id]);
						$results = $doc->getElementsByTagName($property->id . 's');
						$listingInfo[$property->id] = array();
						foreach ($results as $complexparent) {
							$i = 1;
							foreach ($complexparent->getElementsByTagName($property->id) as $result) {
								$resultXML = simplexml_import_dom($result);
								foreach ($childFields as $childField) {
									if (isset($resultXML->$childField['id']))
										$listingInfo[$property->id][$childField['id']][$i] = XML_Util::reverseEntities((string)$resultXML->$childField['id']);
								}
								$i++;
							}
						}
					} elseif ($property->getType() == 'location') {
						$locationFields = array($property->id.'.Country', $property->id.'.State', $property->id.'.City', $property->id.'.ZipCode', $property->id.'.Latitude', $property->id.'.Longitude');
						$locationFieldAdded = array();
						foreach ($locationFields as $locationField) {
							if (array_key_exists($locationField, $listingInfo)) {
								$listingInfo[$property->id][str_replace($property->id.'.', '', $locationField)] = $listingInfo[$locationField];
								$locationFieldAdded[] = str_replace($property->id.'.', '', $locationField);
							}
						}
						if ($property->id == 'Location') {
							$locationFields = array('Country', 'State', 'City', 'ZipCode', 'Latitude', 'Longitude');
							foreach ($locationFields as $locationField) {
								if (array_key_exists($locationField, $listingInfo) && !in_array($locationField, $locationFieldAdded) && !$listing->getProperty($locationField)) {
									$listingInfo[$property->id][$locationField] = $listingInfo[$locationField];
								}
							}
						}
					}
				}

				$listing = $this->CreateListing($listingInfo, $listingTypeID);
				$date = $activationDate;
				if (empty($date)) {
					$date = !empty($listingInfo['activation_date']) ? $listingInfo['activation_date'] : date('Y-m-d');
				}
				$listing->addActivationDateProperty($date);
				$listing->setActivationDate($date);
				if (empty($productInfo['listing_duration'])) {
					$expirationDate = '';
				} else {
					$expirationDate = date('Y-m-d', strtotime($date . ' + ' . $productInfo['listing_duration'] . ' days'));
				}
				$listing->addExpirationDateProperty($expirationDate);
				$listing->addActiveProperty($activeStatus || !empty($listingInfo['active']));
				SJB_ListingDBManager::setListingExpirationDateBySid($listing->sid);
				$listing->setProductInfo(SJB_ProductsManager::getProductExtraInfoBySID($productSID));
				$listing->setPropertyValue('access_type', 'everyone');

				foreach ($listing->getProperties() as $property) {
					if ($property->id == 'ApplicationSettings' && !empty($listingInfo['ApplicationSettings'])) {
						if (preg_match("^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,}\$^iu", $listingInfo['ApplicationSettings']))
							$listingInfo['ApplicationSettings'] = array('value' => $listingInfo['ApplicationSettings'], 'add_parameter' => 1);
						elseif (preg_match("^(https?:\/\/)^", $listingInfo['ApplicationSettings']))
							$listingInfo['ApplicationSettings'] = array('value' => $listingInfo['ApplicationSettings'], 'add_parameter' => 2);
						else
							$listingInfo['ApplicationSettings'] = array('value' => '', 'add_parameter' => ''); //put empty if not valid email or url
						$listing->details->properties[$property->id]->type->property_info['value'] = $listingInfo['ApplicationSettings'];
					}
					elseif ($property->getType() == 'complex') {
						$childFields = SJB_ListingComplexFieldManager::getListingFieldsInfoByParentSID($property->sid);
						foreach ($childFields as $childField) {
							if ($property->type->complex->details->properties[$childField['id']]->value == null) {
								$property->type->complex->details->properties[$childField['id']]->value = array(1 => '');
								$property->type->complex->details->properties[$childField['id']]->type->property_info['value'] = array(1 => '');
							}
						}
					}
					// The import of files at import of listings
					if (in_array($property->getType(), array('file', 'logo', 'picture')) && $property->value !== '') {
						$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($property->id);
						SJB_UploadFileManager::fileImport($listingInfo, $fieldInfo);
					}
				}

				if ($listing->getUserSID()) {
					SJB_ListingManager::saveListing($listing);
					$listingSid = $listing->getSID();
					if ($listing->isActive()) {
						SJB_ListingManager::activateListingBySID($listingSid, false, false, $listing->getActivationDate());
					}
					$addedListingsSids[] = $listingSid;
				} else {
					$nonExistentUsers[] = SJB_Array::get($listingInfo, 'username', '');
					$count--;
				}
				$_FILES = array(); // cleanup files after import
			}
			
			SJB_BrowseDBManager::addListings($addedListingsSids);
			SJB_ProductsManager::incrementPostingsNumber($productSID, count($addedListingsSids));

			$tp->assign('imported_listings_count', $count);
			$tp->assign('nonExistentUsers', $nonExistentUsers);
			$tp->display('import_listings_result.tpl');
			if (file_exists(SJB_System::getSystemSettings("IMPORT_FILES_DIRECTORY"))) {
				SJB_Filesystem::delete(SJB_System::getSystemSettings("IMPORT_FILES_DIRECTORY"));
			}
		}
	}

	private function CreateListing($listing_info, $listing_type_id)
	{
		$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
		$listing = new SJB_Listing($listing_info, $listing_type_sid);
		$userInfo = array();
		if (!empty($listing_info['extUserID'])) {
			$userInfo = SJB_UserManager::getUserInfoByExtUserID($listing_info['extUserID'], $listing_type_id);
			if ($userInfo) {
				$listing->setUserSID($userInfo['sid']);
			}
		}
		if (!$userInfo && !empty($listing_info['username'])) {
			$userInfo = SJB_UserManager::getUserInfoByUserName($listing_info['username']);
			$listing->setUserSID($userInfo['sid']);
		}
		return $listing;
	}
}
