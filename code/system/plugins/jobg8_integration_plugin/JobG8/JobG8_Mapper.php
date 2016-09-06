<?php

class JobG8_Mapper 
{
	const CATEGORY_MAPPING_TYPE = 'category';
	const EMPLOYMENT_MAPPING_TYPE = 'employment';
	
	public $categoryMappingFieldID = 'JobCategory';
	public $employmentMappingFieldID = 'EmploymentType';
	public $errors = array();
	private $tableName = 'jobg8_mapping';
	
	private $jobg8Categories = array(
		6200 => "Accounting",
		6201 => "Administration",
		6202 => "Advert / Media / Entertainment",
		6203 => "Banking & Financial Services",
		6204 => "Call Centre / CustomerService",
		6205 => "Community & Sport",
		6206 => "Construction",
		6207 => "Consulting & Corporate Strategy",
		6208 => "Education",
		6209 => "Engineering",
		5251 => "Executive Positions",
		6210 => "Government & Defence",
		6211 => "Healthcare & Medical",
		6212 => "Hospitality & Tourism",
		6213 => "HR / Recruitment",
		6215 => "I.T. & Communications",
		6214 => "Insurance & Superannuation",
		6216 => "Legal",
		6217 => "Manufacturing Operations",
		6218 => "Mining / Oil / Gas",
		6219 => "Primary Industry",
		6220 => "Real Estate & Property",
		6221 => "Retail & Consumer Products",
		6222 => "Sales & Marketing",
		6223 => "Science & Technology",
		6224 => "Self Employment",
		6225 => "Trades & Services",
		6226 => "Transport & Logistics"
	);
	
	private $jobg8EmploymentTypes = array(
		2164 => "Contract",
		2163 => "Permanent",
		2165 => "Temporary",
		2189 => "Full Time",
		2190 => "Part Time"
	);
	
	private $categoryMappingValues = array(
		6200 => array("Accounting"),
		6201 => array("Admin-Clerical"),
		6202 => array("Media-Journalism"),
		6203 => array("Banking", "Finance"),
		6204 => array("Customer Service"),
		6205 => array("Other"),
		6206 => array("Construction"),
		6207 => array("Consultant"),
		6208 => array("Education"),
		6209 => array("Engineering"),
		5251 => array("Executive"),
		6210 => array("Government"),
		6211 => array("Health Care"),
		6212 => array("Hospitality-Hotel"),
		6213 => array("Human Resources"),
		6215 => array("Information Technology", "Telecommunications"),
		6214 => array("Insurance"),
		6216 => array("Legal"),
		6217 => array("Manufacturing"),
		6218 => array("Other"),
		6219 => array("Other"),
		6220 => array("Real Estate"),
		6221 => array("Retail"),
		6222 => array("Sales", "Marketing"),
		6223 => array("Biotech", "Science"),
		6224 => array("Other"),
		6225 => array("Distribution-Shipping", "Warehouse"),
		6226 => array("Transportation", "Automotive"),
	);

	private $employmentTypeMappingValues = array(
		2164 => "Contractor",
		2163 => "Full time",
		2165 => "Seasonal",
		2189 => "Full time",
		2190 => "Part time",
	);

	private $languageMapping = array (
		"Afrikaans - South Africa (af-ZA)"         => "1078",
		"Arabic - United Arab Emirates (ar-AE)"    => "14337",
		"Arabic - Bahrain (ar-BH)"                 => "15361",
		"Arabic - Algeria (ar-DZ)"                 => "5121",
		"Arabic - Egypt (ar-EG)"                   => "3073",
		"Arabic - Iraq (ar-IQ)"                    => "2049",
		"Arabic - Jordan (ar-JO)"                  => "11265",
		"Arabic - Kuwait (ar-KW)"                  => "13313",
		"Arabic - Lebanon (ar-LB)"                 => "12289",
		"Arabic - Libya (ar-LY)"                   => "4097",
		"Arabic - Morocco (ar-MA)"                 => "6145",
		"Arabic - Oman (ar-OM)"                    => "8193",
		"Arabic - Qatar (ar-QA)"                   => "16385",
		"Arabic - Saudi Arabia (ar-SA)"            => "1025",
		"Arabic - Syria (ar-SY)"                   => "10241",
		"Arabic - Tunisia (ar-TN)"                 => "7169",
		"Arabic - Yemen (ar-YE)"                   => "9217",
		"Belarusian - Belarus (be-BY)"             => "1059",
		"Bulgarian - Bulgaria (bg-BG)"             => "1026",
		"Catalan - Catalan (ca-ES)"                => "1027",
		"Czech - Czech Republic (cs-CZ)"           => "1029",
		"Azeri (Cyrillic) - Azerbaijan (Cy-az-AZ)" => "2092",
		"Serbian (Cyrillic) - Serbia (Cy-sr-SP)"   => "3098",
		"Uzbek (Cyrillic) - Uzbekistan (Cy-uz-UZ)" => "2115",
		"Danish - Denmark (da-DK)"                 => "1030",
		"German - Austria (de-AT)"                 => "3079",
		"German - Switzerland (de-CH)"             => "2055",
		"German - Germany (de-DE)"                 => "1031",
		"German - Liechtenstein (de-LI)"           => "5127",
		"German - Luxembourg (de-LU)"              => "4103",
		"Dhivehi - Maldives (div-MV)"              => "1125",
		"Greek - Greece (el-GR)"                   => "1032",
		"English - Australia (en-AU)"              => "3081",
		"English - Belize (en-BZ)"                 => "10249",
		"English - Canada (en-CA)"                 => "4105",
		"English - Caribbean (en-CB)"              => "9225",
		"English - United Kingdom (en-GB)"         => "2057",
		"English - Ireland (en-IE)"                => "6153",
		"English - Jamaica (en-JM)"                => "8201",
		"English - New Zealand (en-NZ)"            => "5129",
		"English - Philippines (en-PH)"            => "13321",
		"English - Trinidad and Tobago (en-TT)"    => "11273",
		"English - United States (en-US)"          => "1033",
		"English - South Africa (en-ZA)"           => "7177",
		"English - Zimbabwe (en-ZW)"               => "12297",
		"Spanish - Argentina (es-AR)"              => "11274",
		"Spanish - Bolivia (es-BO)"                => "16394",
		"Spanish - Chile (es-CL)"                  => "13322",
		"Spanish - Colombia (es-CO)"               => "9226",
		"Spanish - Costa Rica (es-CR)"             => "5130",
		"Spanish - Dominican Republic (es-DO)"     => "7178",
		"Spanish - Ecuador (es-EC)"                => "12298",
		"Spanish - Spain (es-ES)"                  => "3082",
		"Spanish - Guatemala (es-GT)"              => "4106",
		"Spanish - Honduras (es-HN)"               => "18442",
		"Spanish - Mexico (es-MX)"                 => "2058",
		"Spanish - Nicaragua (es-NI)"              => "19466",
		"Spanish - Panama (es-PA)"                 => "6154",
		"Spanish - Peru (es-PE)"                   => "10250",
		"Spanish - Puerto Rico (es-PR)"            => "20490",
		"Spanish - Paraguay (es-PY)"               => "15370",
		"Spanish - El Salvador (es-SV)"            => "17418",
		"Spanish - Uruguay (es-UY)"                => "14346",
		"Spanish - Venezuela (es-VE)"              => "8202",
		"Estonian - Estonia (et-EE)"               => "1061",
		"Basque - Basque (eu-ES)"                  => "1069",
		"Farsi - Iran (fa-IR)"                     => "1065",
		"Finnish - Finland (fi-FI)"                => "1035",
		"Faroese - Faroe Islands (fo-FO)"          => "1080",
		"French - Belgium (fr-BE)"                 => "2060",
		"French - Canada (fr-CA)"                  => "3084",
		"French - Switzerland (fr-CH)"             => "4108",
		"French - France (fr-FR)"                  => "1036",
		"French - Luxembourg (fr-LU)"              => "5132",
		"French - Monaco (fr-MC)"                  => "6156",
		"Galician - Galician (gl-ES)"              => "1110",
		"Gujarati - India (gu-IN)"                 => "1095",
		"Hebrew - Israel (he-IL)"                  => "1037",
		"Hindi - India (hi-IN)"                    => "1081",
		"Croatian - Croatia (hr-HR)"               => "1050",
		"Hungarian - Hungary (hu-HU)"              => "1038",
		"Armenian - Armenia (hy-AM)"               => "1067",
		"Indonesian - Indonesia (id-ID)"           => "1057",
		"Icelandic - Iceland (is-IS)"              => "1039",
		"Italian - Switzerland (it-CH)"            => "2064",
		"Italian - Italy (it-IT)"                  => "1040",
		"Japanese - Japan (ja-JP)"                 => "1041",
		"Georgian - Georgia (ka-GE)"               => "1079",
		"Kazakh - Kazakhstan (kk-KZ)"              => "1087",
		"Kannada - India (kn-IN)"                  => "1099",
		"Konkani - India (kok-IN)"                 => "1111",
		"Korean - Korea (ko-KR)"                   => "1042",
		"Kyrgyz - Kazakhstan (ky-KZ)"              => "1088",
		"Azeri (Latin) - Azerbaijan (Lt-az-AZ)"    => "1068",
		"Lithuanian - Lithuania (lt-LT)"           => "1063",
		"Serbian (Latin) - Serbia (Lt-sr-SP)"      => "2074",
		"Uzbek (Latin) - Uzbekistan (Lt-uz-UZ)"    => "1091",
		"Latvian - Latvia (lv-LV)"                 => "1062",
		"Macedonian (FYROM (mk-MK)"                => "1071",
		"Mongolian - Mongolia (mn-MN)"             => "1104",
		"Marathi - India (mr-IN)"                  => "1102",
		"Malay - Brunei (ms-BN)"                   => "2110",
		"Malay - Malaysia (ms-MY)"                 => "1086",
		"Norwegian (Bokmal) - Norway (nb-NO)"      => "1044",
		"Dutch - Belgium (nl-BE)"                  => "2067",
		"Dutch - The Netherlands (nl-NL)"          => "1043",
		"Norwegian (Nynorsk) - Norway (nn-NO)"     => "2068",
		"Punjabi - India (pa-IN)"                  => "1094",
		"Polish - Poland (pl-PL)"                  => "1045",
		"Portuguese - Brazil (pt-BR)"              => "1046",
		"Portuguese - Portugal (pt-PT)"            => "2070",
		"Romanian - Romania (ro-RO)"               => "1048",
		"Russian - Russia (ru-RU)"                 => "1049",
		"Sanskrit - India (sa-IN)"                 => "1103",
		"Slovak - Slovakia (sk-SK)"                => "1051",
		"Slovenian - Slovenia (sl-SI)"             => "1060",
		"Albanian - Albania (sq-AL)"               => "1052",
		"Swedish - Finland (sv-FI)"                => "2077",
		"Swedish - Sweden (sv-SE)"                 => "1053",
		"Swahili - Kenya (sw-KE)"                  => "1089",
		"Syriac - Syria (syr-SY)"                  => "1114",
		"Tamil - India (ta-IN)"                    => "1097",
		"Telugu - India (te-IN)"                   => "1098",
		"Thai - Thailand (th-TH)"                  => "1054",
		"Turkish - Turkey (tr-TR)"                 => "1055",
		"Tatar - Russia (tt-RU)"                   => "1092",
		"Ukrainian - Ukraine (uk-UA)"              => "1058",
		"Urdu - Pakistan (ur-PK)"                  => "1056",
		"Vietnamese - Vietnam (vi-VN)"             => "1066",
		"Chinese (Simplified) (zh-CHS)"            => "4",
		"Chinese (Traditional) (zh-CHT)"           => "31748",
		"Chinese - China (zh-CN)"                  => "2052",
		"Chinese - Hong Kong SAR (zh-HK)"          => "3076",
		"Chinese - Macau SAR (zh-MO)"              => "5124",
		"Chinese - Singapore (zh-SG)"              => "4100",
		"Chinese - Taiwan (zh-TW)"                 => "1028",
	);
	
	
	public function __construct()
	{
		$categoryMappingFieldID = SJB_Settings::getSettingByName('categoryMappingFieldID');
		$this->categoryMappingFieldID = empty($categoryMappingFieldID) ? $this->categoryMappingFieldID : $categoryMappingFieldID;
		$employmentMappingFieldID = SJB_Settings::getSettingByName('employmentMappingFieldID');
		$this->employmentMappingFieldID = empty($employmentMappingFieldID) ? $this->employmentMappingFieldID : $employmentMappingFieldID;;
	}
	
	/**
	 * @param string $lang
	 * @return string
	 */
	public function getLanguageByMapping($lang = null)
	{
		if ($lang === null) {
			return '1033';
		}
		$langValue = isset($this->languageMapping[$lang]) ? $this->languageMapping[$lang] : '1033';
		
		return $langValue;
	}
	
	/**
	 * @param string|int $jobg8CategoryValue
	 * @param bool $subCategory
	 * @return array
	 */
	public function getJobCategoryMappingValue($jobg8CategoryValue, $subCategory = false)
	{
		if (empty($jobg8CategoryValue)) {
			return array();
		}
		if (!$subCategory) {
			$jobg8CategoryValue = $this->jobg8Categories[$jobg8CategoryValue];
		}
		$jobg8CategoryValue = html_entity_decode($jobg8CategoryValue);
		$mappedValue = $this->getSJBFieldValueByJobg8FieldValue($jobg8CategoryValue);
		if (!empty($mappedValue)) {
			$mappedValue = explode(',', $mappedValue);
		} else {
			$fieldSid = SJB_ListingFieldManager::getListingFieldSIDByID($this->categoryMappingFieldID);
			$itemSid = SJB_ListingFieldManager::getListItemSIDByValue($jobg8CategoryValue, $fieldSid);
			if ($itemSid) {
				$mappedValue = array($itemSid);
			} else {
				$mappedValue = [];
			}
		}
		
		return $mappedValue;
	}

	/**
	 * @param int $jobg8EmploymentTypeSID
	 * @return array
	 */
	public function getEmploymentTypeMappingValue($jobg8EmploymentTypeSID)
	{
		if (empty($jobg8EmploymentTypeSID) || !isset($this->jobg8EmploymentTypes[$jobg8EmploymentTypeSID])) {
			return '';
		}
		$jobg8FieldValue = $this->jobg8EmploymentTypes[$jobg8EmploymentTypeSID];
		$mappedValue = $this->getSJBFieldValueByJobg8FieldValue($jobg8FieldValue);
		if (empty($mappedValue)) {
			$fieldSid = SJB_ListingFieldManager::getListingFieldSIDByID($this->employmentMappingFieldID);
			$itemSid = SJB_ListingFieldManager::getListItemSIDByValue($jobg8FieldValue, $fieldSid);
			if (!empty($itemSid)) {
				$mappedValue = $itemSid;
			}
		}
		
		return $mappedValue;
	}

	/**
	 * @param string $fieldId
	 * @param string $itemName
	 * @return string
	 */
	public function getValueOfListFieldForItemName($fieldId, $itemName)
	{
		if (empty($fieldId) || empty($itemName)) {
			return '';
		}
		$itemName = html_entity_decode($itemName);
		$fieldSid = SJB_ListingFieldManager::getListingFieldSIDByID($fieldId);
		$itemSid  = SJB_ListingFieldManager::getListItemSIDByValue($itemName, $fieldSid);
		$mappedValue = '';
		if (!empty($itemSid)) {
			$mappedValue = $itemSid;
		}
		
		return $mappedValue;
	}

	/**
	 * @param int $jobg8FieldSID
	 * @return bool
	 */
	public function isJobg8FieldIgnored($jobg8FieldSID)
	{
		$ignoreFieldSIDs = $this->getJobg8IgnoreFieldSID();
		$validIgnoreFieldSIDs = array();
		foreach ($ignoreFieldSIDs as $ignoreFieldSID) {
			$validIgnoreFieldSIDs[] = array_shift($ignoreFieldSID);
		}

		return in_array($jobg8FieldSID, $validIgnoreFieldSIDs);
	}
	
	public function setDefaultCategoryMapping()
	{
		foreach ($this->categoryMappingValues as $jobg8CategorySID => $sjbCategoryMappingValue) {
			$fieldSid = SJB_ListingFieldManager::getListingFieldSIDByID($this->categoryMappingFieldID);
			$mappedValue = array();
			foreach ($sjbCategoryMappingValue as $itemName) {
				$itemSid = SJB_ListingFieldManager::getListItemSIDByValue($itemName, $fieldSid);
				if (!empty($itemSid)) {
					$mappedValue[] = $itemSid;
				}
			}
			
			SJB_DB::queryExec('UPDATE ?w SET `sjb_field_value` = ?s WHERE `sid` = ?n', $this->tableName, implode(',', $mappedValue), $jobg8CategorySID);
		}
	}
	
	public function setDefaultEmploymentMapping()
	{
		foreach ($this->employmentTypeMappingValues as $jobg8EmploymentTypeSID => $sjbEmploymentTypeMappingValue) {
			$fieldSid = SJB_ListingFieldManager::getListingFieldSIDByID($this->employmentMappingFieldID);
			$mappedValue = '';
			$itemSid = SJB_ListingFieldManager::getListItemSIDByValue($sjbEmploymentTypeMappingValue, $fieldSid);
			if (!empty($itemSid)) {
				$mappedValue = $itemSid;
			}
			SJB_DB::queryExec('UPDATE ?w SET `sjb_field_value` = ?s WHERE `sid` = ?n', $this->tableName, $mappedValue, $jobg8EmploymentTypeSID);
		}
	}
	
	public function setCategoryMappingType()
	{
		if (!$this->isExistMappingType(self::CATEGORY_MAPPING_TYPE)) {
			$mapper = new JobG8_Mapper();
			foreach ($mapper->jobg8Categories as $jobg8CategorySID => $jobg8CategoryValue) {
				SJB_DB::queryExec('INSERT INTO ?w SET `sid` = ?n, `type` = ?s, `jobg8_field_value` = ?s', $this->tableName, $jobg8CategorySID, self::CATEGORY_MAPPING_TYPE, $jobg8CategoryValue);
			}
		}
	}
	
	public function setEmploymentMappingType()
	{
		if (!$this->isExistMappingType(self::EMPLOYMENT_MAPPING_TYPE)) {
			$mapper = new JobG8_Mapper();
			foreach ($mapper->jobg8EmploymentTypes as $jobg8EmploymentTypeSID => $jobg8EmploymentTypeValue) {
				SJB_DB::queryExec('INSERT INTO ?w SET `sid` = ?n, `type` = ?s, `jobg8_field_value` = ?s', $this->tableName, $jobg8EmploymentTypeSID, self::EMPLOYMENT_MAPPING_TYPE, $jobg8EmploymentTypeValue);
			}
		}
	}

	/**
	 * @param int $sid
	 * @param string $value
	 * @param int $allow
	 */
	public function setMappingValue($sid, $value, $allow)
	{
		SJB_DB::query('UPDATE ?w SET `sjb_field_value` = ?s, `allow` = ?n WHERE `sid` = ?n', $this->tableName, $value, $allow, $sid);
	}

	/**
	 * @param string $mappingType
	 * @return array
	 */
	public function getMappingInfoByType($mappingType)
	{
		return SJB_DB::query('SELECT * FROM ?w WHERE `type` = ?s', $this->tableName, $mappingType);
	}

	/**
	 * @param string $mappingType
	 * @return bool
	 */
	public function isExistMappingType($mappingType)
	{
		return (bool) SJB_DB::queryValue('SELECT `sid` FROM ?w WHERE `type` = ?s', $this->tableName, $mappingType);
	}

	/**
	 * @param string $mappingType
	 * @param string $mappingField
	 */
	public function saveMappingField($mappingType, $mappingField)
	{
		if (SJB_ListingFieldManager::getListingFieldSIDByID($mappingField)) {
			SJB_Settings::saveSetting($mappingType .'MappingFieldID', $mappingField);
			switch ($mappingType) {
				case self::CATEGORY_MAPPING_TYPE: $this->categoryMappingFieldID = $mappingField;
					break;
				case self::EMPLOYMENT_MAPPING_TYPE: $this->employmentMappingFieldID = $mappingField;
					break;
			}
		} else {
			$this->errors[] = sprintf(SJB_I18N::getInstance()->gettext('Backend', 'The %s field does not exist'), $mappingField);
		}
	}

	/**
	 * @param array $mappedFieldValues
	 * @param string $mappingType
	 * @param array $allowedMappingFields
	 * @return bool
	 */
	public function isAllFieldMappedByType($mappedFieldValues, $mappingType, $allowedMappingFields)
	{
		$jobg8MappingFields = array();
		switch ($mappingType) {
			case self::CATEGORY_MAPPING_TYPE: $jobg8MappingFields = $this->jobg8Categories;
				break;
			case self::EMPLOYMENT_MAPPING_TYPE: $jobg8MappingFields = $this->jobg8EmploymentTypes;
				break;
		}
		foreach ($jobg8MappingFields as $jobg8MappingFieldSID => $jobg8MappingFieldValue) {
			$mappedFieldValue = $mappedFieldValues[$jobg8MappingFieldSID];
			if (is_array($mappedFieldValue)) {
				$mappedFieldValue = array_pop($mappedFieldValue);
			}
			if (empty($mappedFieldValue) && !empty($allowedMappingFields[$jobg8MappingFieldSID])) {
				$this->errors[] = sprintf(SJB_I18N::getInstance()->gettext('Backend', 'The %s field does not mapped'), $jobg8MappingFieldValue);
			}
		}

		return !empty($this->errors);
	}
	
	public function createMappingTable()
	{
		SJB_DB::queryExec("
			CREATE TABLE IF NOT EXISTS ?w (
				`sid` int(11) NOT NULL,
				`type` varchar(255) NOT NULL,
				`jobg8_field_value` varchar(255) NOT NULL,
				`sjb_field_value` varchar(255) DEFAULT NULL,
				`allow` tinyint(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`sid`),
				KEY `type` (`type`),
				KEY `jobg8_field_value` (`jobg8_field_value`),
				KEY `sjb_field_value` (`sjb_field_value`)
			) DEFAULT CHARSET=utf8",
			$this->tableName
		);
	}

	/**
	 * @param string $jobg8FieldValue
	 * @return string
	 */
	private function getSJBFieldValueByJobg8FieldValue($jobg8FieldValue)
	{
		return SJB_DB::queryValue('SELECT `sjb_field_value` FROM ?w WHERE `jobg8_field_value` = ?s', $this->tableName, $jobg8FieldValue);
	}

	/**
	 * @return array
	 */
	private function getJobg8IgnoreFieldSID()
	{
		return SJB_DB::query('SELECT `sid` FROM ?w WHERE `allow` = 0', $this->tableName);
	}
}