<?php

class JobG8_OutgoingActions extends JobG8
{
	const APPLICATION_SETTINGS_TYPE_EMAIL = 1;
	const APPLICATION_SETTINGS_TYPE_URL = 2;
	
	public $errors = array();
	public $listingSIDsNotPassFilters = array();

	
	/**
	 * @param array $listingSIDs
	 * @return string
	 */
	public function getOutgoingXML($listingSIDs)
	{
		$domDocument = new DOMDocument('1.0', 'UTF-8');
		$domElement = $domDocument->createElement('content');
		$node = $domDocument->appendChild($domElement);
		foreach ($listingSIDs as $action => $sids) {
			$data = $this->getDataForXMLElements($action, $sids);
			foreach ($data as $elements) {
				$this->createXMLElements($elements, $domDocument, $action, $node);
			}
		}
		if (!$node->hasChildNodes()) {
			return '';
		}
		$domDocument->formatOutput = true;
		
		return SJB_HelperFunctions::clearNonPrintedCharacters($domDocument->saveXML());
	}

	/**
	 * @param array $elements
	 * @param DOMDocument $domDocument
	 * @param string $action
	 * @param DOMNode $node
	 */
	private function createXMLElements($elements, $domDocument, $action, $node)
	{
		if ($node->tagName == 'content') {
			$domElement = $domDocument->createElement('job');
			$domAttribute = $domDocument->createAttribute('action');
			$domAttribute->value = $action;
			$domElement->appendChild($domAttribute);
			$node = $node->appendChild($domElement);
		}
		$number = 1;
		foreach($elements as $element => $value) {
			$element = is_numeric($element) ? 'elem' : $element;
			$child = $domDocument->createElement($element, '');
			if ($element == 'elem') {
				$childAttribute = $domDocument->createAttribute('name');
				$childAttribute->value = $element .'_'. $number;
				$child->appendChild($childAttribute);
				$number++;
			}
			if (is_array($value)) {
				$node->appendChild($child);
				$this->createXMLElements($value, $domDocument, $action, $child);
			} else {
				$childNode = $node->appendChild($child);
				if ($value) {
					$cdata = $domDocument->createCDATASection($value);
					$childNode->appendChild($cdata);
				}
			}
		}
	}

	/**
	 * @param string $action
	 * @param array $listingSIDs
	 * @return array
	 * @throws Exception
	 */
	private function getDataForXMLElements($action, $listingSIDs)
	{
		$data = array();
		foreach ($listingSIDs as $listingSID) {
			try {
				if ($action != 'delete') {
					$listing = SJB_ListingManager::getObjectBySID($listingSID);
					if (empty($listing)) {
						throw new Exception('Cannot get listing object by SID');
					}
					$applicationSettings = $listing->getPropertyValue('ApplicationSettings');
					if ($this->postingType != 'traffic' && empty($applicationSettings['value'])) {
						$this->listingSIDsNotPassFilters[] = $listingSID;
						throw new Exception('ApplicationSettings value is empty');
					}
					$jobCategory = $listing->getPropertyValue('JobCategory');
					if (empty($jobCategory)) {
						$this->listingSIDsNotPassFilters[] = $listingSID;
						throw new Exception('JobCategory is empty');
					}
					if (!$this->isAllowedToSend($listingSID)) {
						$this->listingSIDsNotPassFilters[] = $listingSID;
						throw new Exception('Not passed by filters');
					}
				}
				$data[] = $this->getElements($action, $listingSID);
			} catch (Exception $e) {
				$this->errors[$action][$listingSID] = $e->getMessage();
				continue;
			}
		}
		
		return $data;
	}

	/**
	 * @param string $action
	 * @param int $listingSID
	 * @return array
	 */
	private function getElements($action, $listingSID)
	{
		if ($action == 'delete') {
			$elements = array(
				'id' => $listingSID
			);
		} else {
			$listing = SJB_ListingManager::getObjectBySID($listingSID);
			$listingStructure = SJB_ListingManager::createTemplateStructureForListing($listing);
			$userEmail = $listingStructure['user']['username'];
			if ($listingStructure['ApplicationSettings']['add_parameter'] == self::APPLICATION_SETTINGS_TYPE_EMAIL) {
				$userEmail = $listingStructure['ApplicationSettings']['value'];
			}
			$mapper = new JobG8_Mapper();
			$elements = array(
				'id'             => $listingStructure['id'],
				'title'          => $listingStructure['Title'],
				'date'           => $listingStructure['activation_date'],
				'url'            => SJB_System::getSystemSettings('SITE_URL') . SJB_TemplateProcessor::listing_url($listingStructure),
				'company'        => $listingStructure['user']['CompanyName'],
				'URLATS'         => $listingStructure['ApplicationSettings']['add_parameter'] == self::APPLICATION_SETTINGS_TYPE_URL ? $listingStructure['ApplicationSettings']['value'] : '',
				'email'          => $listingStructure['ApplicationSettings']['add_parameter'] == self::APPLICATION_SETTINGS_TYPE_EMAIL ? $listingStructure['ApplicationSettings']['value'] : '',
				'description'    => $listingStructure['JobDescription'],
				'city'           => $listingStructure['Location']['City'],
				'state'          => $listingStructure['Location']['State'],
				'country'        => $listingStructure['Location']['Country'],
				'zipcode'        => $listingStructure['Location']['ZipCode'],
				'jobtype'        => empty($listingStructure['EmploymentType']) ? '' : $listingStructure['EmploymentType'],
				'jobCategory'    => array_pop($listingStructure['JobCategory']),
				'Language'       => $mapper->getLanguageByMapping(),
				'user'           => array(
					'id'      => $listingStructure['user']['id'],
					'name'    => $listingStructure['user']['username'],
					'email'   => $userEmail,
					'website' => $listingStructure['user']['WebSite']
				),
				'LogoURL'        => isset($listingStructure['user']['Logo']['file_url']) ? $listingStructure['user']['Logo']['file_url'] : '',
			);
		}
		
		return $elements;
	}
	
	/**
	 * @param int $listingSID
	 * @return bool
	 */
	private function isAllowedToSend($listingSID)
	{
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingSID);
		$filter = new JobG8_Filter($this->postingType);
		
		return $filter->isPassedByFilters($listingInfo);
	}

	/**
	 * @param array $listingSIDs
	 * @param string $uploadedFileID
	 * @param array $errors
	 * @return string
	 */
	public function getOutgoingLogData($listingSIDs, $uploadedFileID, $errors)
	{
		$logData = "************************* ". date("Y-m-d H:i") ." *************************\n";
		$logData.= "Posting Method: ". $this->postingType . "\n\n";
		if (empty($listingSIDs)) {
			$logData .= "no listings to send";
		} else {
			$errorLogData = 'No errors';
			if (!empty($errors)) {
				$errorLogData = '';
				foreach ($errors as $action => $error) {
					$errorLogData .= "\n". $action ." Errors:\n";
					foreach ($error as $sid => $value) {
						$key = array_search($sid, $listingSIDs[$action]);
						unset($listingSIDs[$action][$key]);
						$errorLogData .= "\tListing ID: {$sid} with error: {$value}\n";
					}
				}
			}
			$logData .= "Listings Sent to Jobg8:\n";
			foreach ($listingSIDs as $action => $sids) {
				$logData .= "\t". $action .": ";
				foreach ($sids as $sid) {
					$logData .= $sid .", ";
				}
				$logData = substr($logData, 0, -2) ."\n";
			}
			$logData .= "\nUploaded File ID: ". $uploadedFileID ."\n";
			$logData .= $errorLogData;
		}
		
		return $logData ."\n\n";
	}

	/**
	 * @param $postingType
	 */
	public function setPostingType($postingType) {
		parent::setPostingType($postingType);
		$this->errors = array();
		$this->listingSIDsNotPassFilters = array();
	}
}