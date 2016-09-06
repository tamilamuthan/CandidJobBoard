<?php

class JobG8_IncomingActions extends JobG8
{
	const LOGO_FIELD_ID = 'Logo';
	private $mapper;
	private $error = '';
	private $processedUsers = array();


	public function __construct()
	{
		$this->mapper = new JobG8_Mapper();
	}
	
	/**
	 * @param string $xml
	 * @return string
	 */
	public function getIncomingLogData($xml)
	{
		$ip   = SJB_Request::getVar('REMOTE_ADDR', '', 'SERVER');
		$requestMethod =  SJB_Request::getVar('REQUEST_METHOD', '', 'SERVER');
		$requestMethod = empty($requestMethod) ? '' : "\nRequest method: ". $requestMethod;
		$logData = "************************* ". date("Y-m-d H:i") ." *************************\n";
		$logData .= "IP-address: {$ip}{$requestMethod}";
		$logData .= "\n------------- RAW POST -----------\n\n";
		$logData .= $xml;
		
		return $logData ."\n\n";
	}
	
	/**
	 * @param  string $xml
	 * @return string
	 */
	public function getXMLResponse($xml)
	{
		$xmlResponse = '<?xml version="1.0" encoding="utf-8"?><Body>';
		$doc = new DOMDocument();
		$doc->loadXML($xml);
		$actions = array('Post', 'Amend', 'Delete');
		foreach ($actions as $action) {
			$xmlResponse .= $this->getXMLResponseByAction($doc, $action);
		}
		$xmlResponse .= '</Body>';
		
		return $xmlResponse;
	}

	/**
	 * @param string $action
	 * @return string
	 */
	public function getTestData($action)
	{
		$xmlData = '<?xml version="1.0" encoding="utf-8"?>
			<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
			<s:Body s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">';
		switch (strtolower($action)) {
			case 'post':
				$xmlData .=  $this->getPostTestData();
				break;
			case 'amend':
				$xmlData .=  $this->getAmendTestData();
				break;
			case 'delete':
				$xmlData .=  $this->getDeleteTestData();
				break;
		}
		$xmlData .= '</s:Body></s:Envelope>';
		
		return $xmlData;
	}
	
	/**
	 * @param DOM object $job
	 * @param string $action
	 * @return bool
	 */
	private function isActionExecute($job, $action)
	{
		$currentUserSID = SJB_UserManager::getCurrentUserSID();
		$userContractsSIDs = SJB_ContractManager::getAllContractsSIDsByUserSID($currentUserSID);
		try {
			if ($this->isAllowedToCreateThisJob($userContractsSIDs, $job)) {
				$contractID = array_pop($userContractsSIDs);
				switch (strtolower($action)) {
					case 'post':
						$this->postAdverts($job, $contractID);
						break;
					case 'amend':
						$this->amendAdverts($job, $contractID);
						break;
					case 'delete':
						$this->deleteAdverts($job);
						break;
				}
			}
		} catch (Exception $e) {
			$this->error = $e->getMessage();
			return false;
		}
		
		return true;
	}

	/**
	 * @param DOM object $job
	 * @param int $contractID
	 * @throws Exception
	 */
	private function postAdverts($job, $contractID)
	{
		if ($this->getListingSIDByJobReference((string) $job->JobReference)) {
			throw new Exception('JobReference Already Exists');
		}
		$listingTypeSid = SJB_ListingTypeManager::getListingTypeSIDByID('Job');
		$listing = new SJB_Listing(array(), $listingTypeSid);
		$listingData = $this->getListingData($listing, $job, $contractID);
		$listingUserSid = $this->getUserSIDByCompanyName((string) $job->AdvertiserName);
		if (isset($job->LogoURL) && (string) $job->LogoURL != '') {
			$this->addOrUpdateLogo($listingUserSid, (string)$job->LogoURL);
		}
		$this->createListing($listingData, $listingTypeSid, $contractID, true, $listingUserSid);
	}

	/**
	 * @param DOM object $job
	 * @param int $contractID
	 */
	private function amendAdverts($job, $contractID)
	{
		$listingTypeSid = SJB_ListingTypeManager::getListingTypeSIDByID ('Job');
		$jobSID = $this->getListingSIDByJobReference((string) $job->JobReference);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($jobSID);
		if ($listingInfo) {
			$listingInfo['expiration_date'] = SJB_I18N::getInstance()->getDate($listingInfo['expiration_date']);
			$listing = new SJB_Listing($listingInfo, $listingTypeSid);
			$listingData = $this->getListingData($listing, $job, $contractID);
			$listingData = array_merge($listingInfo, $listingData);
			if (isset($job->LogoURL) && (string) $job->LogoURL != '' && $listing->getUserSID()) {
				$this->addOrUpdateLogo($listing->getUserSID(), (string)$job->LogoURL);
			}
			$this->createListing($listingData, $listingTypeSid, $contractID);
		}
	}
	
	/**
	 * @param DOM object $job
	 * @throws Exception
	 */
	private function deleteAdverts($job)
	{
		$jobReference  = (string) $job->JobReference;
		if (empty($jobReference)) {
			throw new Exception('Empty Job Reference');
		}
		$jobSID = $this->getListingSIDByJobReference($jobReference);
		if (empty($jobSID)) {
			throw new Exception('Job Reference Not Exists');
		}
		SJB_Event::addToIgnoreList('beforeListingDelete');
		SJB_ListingManager::deleteListingBySID($jobSID);
		Jobg8::deleteJobProperties($jobSID);
		SJB_Event::removeFromIgnoreList('beforeListingDelete');
	}

	/**
	 * Updates user's Logo image or adds new one if not exists
	 * @param $userSid int|string
	 * @param $logoUrl string
	 */
	private function addOrUpdateLogo($userSid, $logoUrl)
	{
		if (empty($logoUrl) || in_array($userSid, $this->processedUsers)) {
			return;
		}
		$tempImageName = $userSid . '_' . substr(md5((string)microtime(true)), 0, 10);
		$tempImagePath = SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY') . '/' . $tempImageName;
		$imageContent = file_get_contents($logoUrl);
		if ($imageContent === false) {
			return;
		}
		file_put_contents($tempImagePath, $imageContent);
		unset($imageContent);

		if (file_exists($tempImagePath)) {
			$fieldInfo = SJB_UserProfileFieldManager::getUserProfileFieldInfoByID(self::LOGO_FIELD_ID);

			$_FILES[$fieldInfo['id']] = array(
				'tmp_name' => $tempImagePath,
				'size'     => filesize($tempImagePath),
				'name'     => "jobg8_logo_{$tempImageName}.png",
				'type'     => '',
			);

			$uploader = new SJB_UploadPictureManager();
			$uploader->setUploadedFileID('Logo_' . $userSid);
			$uploader->setHeight($fieldInfo['height']);
			$uploader->setWidth($fieldInfo['width']);
			$uploader->uploadPicture($fieldInfo['id'], $fieldInfo);

			$user = SJB_UserManager::getObjectBySID($userSid);
			$user->setPropertyValue($fieldInfo['id'], 'Logo_' . $userSid);
			SJB_UserManager::saveObject('users', $user);
			unset($user);
			unlink($tempImagePath);
			$this->processedUsers[] = $userSid;
		}
	}

	/**
	 * @param string $companyName
	 * @return int
	 */
	private function getUserSIDByCompanyName($companyName)
	{
		if (empty($companyName)) {
			return null;
		}
		$username = preg_replace('/[\\/\\\:*?\"<>|%#$\s\'-]/u', '_', html_entity_decode($companyName));
		$username = str_replace('&', 'And', $username);
		$user = array(
			'username'    => 'jobg8_' . $username,
			'CompanyName' => $companyName,
		);
		$userSID = null;
		if (!empty($user['username'])) {
			$user['password']['confirmed'] = $user['password']['original'] = $username;
			$userSID = SJB_UserManager::getUserSIDbyUsername($user['username']);
			if (empty($userSID)) {
				$userObj = SJB_ObjectMother::createUser($user, SJB_UserGroup::EMPLOYER);
				$userObj->deleteProperty('active');
				$userObj->deleteProperty('featured');
				SJB_UserManager::saveUser($userObj);
				SJB_UserManager::activateUserByUserName($userObj->getUserName());
				$userSID = $userObj->getSID();
			}
		}
		
		return $userSID;
	}

	/**
	 * @param array $contractsSIDs
	 * @param DOM object $job
	 * @return bool
	 * @throws Exception
	 */
	private function isAllowedToCreateThisJob($contractsSIDs, $job)
	{
		if (empty($contractsSIDs)) {
			throw new Exception('User do not have contract');
		}
		if (count($contractsSIDs) > 1) {
			throw new Exception('User have more than one contract');
		}
		$mapper = new JobG8_Mapper();
		$ignoreFields = array(
			$mapper->categoryMappingFieldID   => array('id' => '', 'name' => ''),
			$mapper->employmentMappingFieldID => array('id' => '', 'name' => ''),
		);
		if (isset($job->Classification)) {
			$ignoreFields[$mapper->categoryMappingFieldID] = array(
				'id'   => (int) $job->Classification['ValueID'],
				'name' => (string) $job->Classification
			);
		}
		if (isset($job->EmploymentType)) {
			$ignoreFields[$mapper->employmentMappingFieldID] = array(
				'id'   => (int) $job->EmploymentType['ValueID'],
				'name' => (string) $job->EmploymentType
			);
		} else if (isset($job->WorkHours)) {
			$ignoreFields[$mapper->employmentMappingFieldID] = array(
				'id'   => (int) $job->WorkHours['ValueID'],
				'name' => (string) $job->WorkHours
			);
		}
		foreach ($ignoreFields as $ignoreFieldName => $ignoreFieldValue) {
			if ($this->mapper->isJobg8FieldIgnored($ignoreFieldValue['id'])) {
				throw new Exception("{$ignoreFieldName} '{$ignoreFieldValue['name']}' has not passed filter");
			}
		}
		
		return true;
	}

	/**
	 * @param $jobReference
	 * @return int
	 */
	private function getListingSIDByJobReference($jobReference)
	{
		return SJB_DB::queryValue("SELECT `listingSid` FROM `?w` WHERE `jobReference` = ?s", JobG8::JOBG8_LISTING_PROPERTIES_TABLE, $jobReference);
	}

	/**
	 * @param Listing object $listing
	 * @param DOM object $job
	 * @param int $contractID
	 * @return array
	 */
	private function getListingData($listing, $job, $contractID)
	{
		$classification = isset($job->Classification) ? $this->mapper->getJobCategoryMappingValue((int) $job->Classification['ValueID']) : array();
		$subClassification = isset($job->SubClassification) && (string) $job->SubClassification != 'Other' ? $this->mapper->getJobCategoryMappingValue((string) $job->SubClassification, true) : array();
		$jobCategory = array_unique(array_merge($classification, $subClassification));
		$location = $listing->getPropertyValue('Location');
		$locationFields = array(
			'Country' 	=> isset($job->Country) ? $job->Country : (isset($location['Country']) ? $location['Country'] : ''),
			'ZipCode' 	=> isset($job->PostCode) ? $job->PostCode : (isset($location['ZipCode']) ? $location['ZipCode'] : '')
		);
		$cityAndState = array(
			'State' 	=> isset($job->Location) ? $job->Location : (isset($location['State']) ? $location['State'] : ''),
			'City'		=> isset($job->Area) ? (string) $job->Area == 'Not Specified' ? '' : (string) $job->Area : (isset($location['City']) ? $location['City'] : ''),
		);
		$locationFields = array_merge($locationFields, $cityAndState);
		$employmentType = isset($job->EmploymentType) ? (int) $job->EmploymentType['ValueID'] : (isset($job->WorkHours) ? (int) $job->WorkHours['ValueID'] : '');
		$applicationSettings = $listing->getPropertyValue('ApplicationSettings');
		$listingData = array(
			'listing_type_id' 		=> 'Job',
			'listing_contract_id' 	=> $contractID,
			'Title' 				=> isset($job->Position) ? (string) $job->Position : $listing->getPropertyValue('Title'),
			'jobReference'          => (string) $job->JobReference,
			'jobType'               => (string) $job->JobType,
			'JobCategory' 			=> empty($jobCategory) ? $listing->getPropertyValue('JobCategory') : $jobCategory,
			'Location' 				=> $locationFields,
			'EmploymentType' 		=> empty($employmentType) ? $listing->getPropertyValue('EmploymentType') : $this->mapper->getEmploymentTypeMappingValue($employmentType),
			'JobDescription'  		=> isset($job->Description) ? (string) $job->Description : $listing->getPropertyValue('JobDescription'),
			'ApplicationSettings' 	=> array('value' => isset($job->ApplicationURL) ? (string) $job->ApplicationURL : $applicationSettings['value'], 'add_parameter' => 2),
		);
		
		return $listingData;
	}

	/**
	 * @param $listingSid
	 * @param array $properties
	 * @param bool $amend
	 * @return bool
	 */
	private function setJobProperties($listingSid, array $properties, $amend = false)
	{
		if (!isset($properties['jobReference']) || !isset($properties['jobType'])) {
			return false;
		}
		if (!$amend) {
			SJB_DB::queryExec("INSERT INTO ?w (`listingSid`, `jobReference`, `jobType`) VALUES (?n, ?s, ?s)", JobG8::JOBG8_LISTING_PROPERTIES_TABLE, $listingSid, $properties['jobReference'], $properties['jobType']);
		} else {
			SJB_DB::queryExec("UPDATE ?w SET `jobType` = ?s WHERE `listingSid` = ?s", JobG8::JOBG8_LISTING_PROPERTIES_TABLE, $properties['jobType'], $listingSid);
		}
	}

	/**
	 * @param $listingData
	 * @param $listingTypeSid
	 * @param $contractID
	 * @param bool $activate
	 * @param null $userSID
	 */
	private function createListing($listingData, $listingTypeSid, $contractID, $activate = false, $userSID = null)
	{
		$contract = new SJB_Contract(array('contract_id' => $contractID));
		$extraInfo = $contract->extra_info;
		$listing = new SJB_Listing($listingData, $listingTypeSid);
		$listing->deleteProperty('featured');
		$listing->deleteProperty('status');
		$allProperties = $listing->getProperties();
		foreach ($allProperties as $key => $value) {
			if ($key == 'Location') {
				$child = $listing->getChild($key);
				foreach ($child->getProperties() as $field) {
					$field->makeNotRequired();
				}
			} else {
				$listing->makePropertyNotRequired($key);
			}
		}
		$accessType = $listing->getProperty('access_type');
		if (empty($accessType->value)) {
			$listing->setPropertyValue('access_type', 'everyone');
		}
		$fieldErrors = array();
		$addListingForm = new SJB_Form($listing);
		if (!$addListingForm->isDataValid($fieldErrors)) {
			foreach ($fieldErrors as $property => $fieldError) {
				$listing->deleteProperty($property);
			}
		}
		if (!empty($userSID)) {
			$listing->setUserSID($userSID);
		} 
		if (!empty($listingData['sid'])) {
			$listing->setSID($listingData['sid']);
		}
		$listing->setProductInfo($extraInfo);
		SJB_ListingManager::saveListing($listing);
		$this->setJobProperties($listing->getSID(), $listingData, !$activate);
		if (!empty($extraInfo['featured'])) {
			SJB_ListingManager::makeFeaturedBySID($listing->getSID());
		}
		//SJB-2210 to make browse-by rebuild only once this row must go before listing activation
		if ($activate) {
			SJB_ListingManager::activateListingBySID($listing->getSID(), true, false);
		}
	}

	/**
	 * @param DOMDocument $domDocument
	 * @param string $action
	 * @return string
	 */
	private function getXMLResponseByAction($domDocument, $action)
	{
		$xmlResponse = '';
		$tagNames = $action .'Adverts';
		$tagName = $action .'Advert';
		$nodes = $domDocument->getElementsByTagNameNS("urn:jobg8", $tagNames);
		foreach ($nodes as $node) {
			foreach ($node->getElementsByTagName($tagName) as $job) {
				$simpleEXMLJob = simplexml_import_dom($job);
				$jobReference = (string) $simpleEXMLJob->JobReference;
				$xmlResponse .= '<'. $action .'AdvertResponse><JobReference>'. $jobReference .'</JobReference>';
				if ($this->isActionExecute($simpleEXMLJob, $action)) {
					$xmlResponse .= '<Success>'. $jobReference .'</Success>';
				} else {
					$xmlResponse .= '<Error>'. $this->error .'</Error>';
				}
				$xmlResponse .= '</'. $action .'AdvertResponse>';
			}
		}
		
		return $xmlResponse;
	}

	/**
	 * @return string
	 */
	private function getPostTestData()
	{
		return '
			<q1:PostAdverts xmlns:q1="urn:jobg8">
				<user xsi:type="xsd:string" />
				<pass xsi:type="xsd:string" />
				<PostAdvert xmlns="http://jobg8.com/">
					<JobReference>7597381/45608000</JobReference>
					<ClientReference>7597381000</ClientReference>
					<Classification ValueID="6200">Accounting</Classification>
					<SubClassification ValueID="6463">Other</SubClassification>
					<Position>TEST Accounts Receivable Collection Analyst</Position>
					<Description><![CDATA[<P>We are recruiting for an<STRONG> Senior Accounts Receivable Analyst </STRONG>with an international company located in the Atlanta area. The individual will manage accounts receivable and collections for a large portfolio of assigned customer accounts. </P> <P><STRONG>Responsibilities: </STRONG></P> <P>Provide single point of contact for assigned customers. Investigate and respond timely to customer queries. </P> <P>Accountable for maintaining past dues consistent with department objectives </P> <P>Clarifies nature of customer issue and takes responsibility for resolution. Collaborates with various departments and management levels (ie Contracts, Legal, Sales, Order Management). </P> <P>Research and resolve payment discrepancies. Prepare debit/credit adjustments as appropriate </P> <P>Update customer master file </P> <P>Reconcile customer accounts </P> <P><STRONG>Qualifications: </STRONG></P> <P>Bachelor\'s Degree in Accounting, Business Administration or a related field </P> <P>Five to seven years\' experience in collections. Lead or Supervisory experience. </P> <P>Must be flexible, organized and able to work under time constraints and deadlines </P> <P>Experience collaborating with internal departments to resolve account issues </P> <P>Team and customer oriented </P> <P>Excellent math, verbal and written communication skills </P> <P>Advanced Excel required. Knowledge of Sales Force a plus </P>]]></Description>
					<Location ValueID="15331">Georgia</Location>
					<Area ValueID="15504">Atlanta</Area>
					<PostCode>30346</PostCode>
					<Country ValueID="247">United States</Country>
					<EmploymentType ValueID="2163">Permanent</EmploymentType>
					<WorkHours ValueID="2188">Not Specified</WorkHours>
					<VisaRequired>Applicants must be eligible to work in the specified location</VisaRequired>
					<PayPeriod ValueID="2178">Annual</PayPeriod>
					<PayMinimum>3300.40</PayMinimum>
					<PayMaximum>3500.40</PayMaximum>
					<Currency ValueID="1228">US Dollar . USD</Currency>
					<Contact>Susan Saye</Contact>
					<ApplicationURL>http://www.jobg8.com/Application.aspx?UQqVaBfSJacV8SVjJp4MWwo</ApplicationURL>
					<AdvertiserName>iStaff</AdvertiserName>
					<LogoURL>https://www.smartjobboard.com/ca/jobg8/logo.png</LogoURL>
					<JobType>APPLICATION</JobType>
				</PostAdvert>
			</q1:PostAdverts>';
	}

	/**
	 * @return string
	 */
	private function getAmendTestData()
	{
		return '
			<q1:AmendAdverts xmlns:q1="urn:jobg8">
				<user xsi:type="xsd:string" />
				<pass xsi:type="xsd:string" />
				<AmendAdvert xmlns="http://jobg8.com/">
					<JobReference>7597381/45608000</JobReference>
					<Position>TEST AMEND Senior Producer / Project manager</Position>
					<Description><![CDATA[A digital advertising agency based in Central London is currently looking for a Senior Producer/Project Manager to join them. You will be working in the digital department across a number of account for leading UK ad international brands. You will have<br/>solid digital agency background and will be required to show examples of successful projects. You will successfully manage budgets and timelines, manage third party supplier relationships, coordination between design and production/technical teams.<br/>Experience in MS Project is also necessary.]]></Description>
					<Location ValueID="15331">Georgia</Location>
					<Area ValueID="15504">Atlanta</Area>
					<EmploymentType ValueID="2163">Permanent</EmploymentType>
					<WorkHours ValueID="2188">Not Specified</WorkHours>
					<VisaRequired>Applicants must be eligible to work in the specified location</VisaRequired>
					<PayPeriod ValueID="2178">Annual</PayPeriod>
					<PayMinimum>2000.40</PayMinimum>
					<PayMaximum>3000.40</PayMaximum>
					<Currency ValueID="1228">US Dollar . USD</Currency>
					<ApplicationURL>http://training.jobg8.com/Application.aspx?CbHaj2P2jteDFHa4CPaonQe</ApplicationURL>
					<JobSource>The IT Job Board</JobSource>
					<AdvertiserName>iStaff</AdvertiserName>
					<LogoURL>https://www.smartjobboard.com/ca/jobg8/logo.png</LogoURL>
					<JobType>ATS</JobType>
				</AmendAdvert>
			</q1:AmendAdverts>';
	}

	/**
	 * @return string
	 */
	private function getDeleteTestData()
	{
		return'
			<q1:DeleteAdverts xmlns:q1="urn:jobg8">
				<user xsi:type="xsd:string" />
				<pass xsi:type="xsd:string" />
				<DeleteAdvert xmlns="http://jobg8.com/">
					<JobReference>7597381/45608000</JobReference>
				</DeleteAdvert>
			</q1:DeleteAdverts>';
	}	

}