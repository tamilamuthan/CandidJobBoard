<?php

class IndeedPlugin extends SJB_PluginAbstract
{
    public static $indeedListings = [];
    
    private static $countries = [
        'us' => [
            'caption' => 'United States',
            'domain' => 'indeed.com',
        ],
        'ar' => [
            'caption' => 'Argentina',
            'domain' => 'ar.indeed.com',
        ],
        'au' => [
            'caption' => 'Australia',
            'domain' => 'au.indeed.com',
        ],
        'at' => [
            'caption' => 'Austria',
            'domain' => 'at.indeed.com',
        ],
        'bh' => [
            'caption' => 'Bahrain',
            'domain' => 'bh.indeed.com',
        ],
        'be' => [
            'caption' => 'Belgium',
            'domain' => 'be.indeed.com',
        ],
        'br' => [
            'caption' => 'Brazil',
            'domain' => 'indeed.com.br',
        ],
        'ca' => [
            'caption' => 'Canada',
            'domain' => 'ca.indeed.com',
        ],
        'cl' => [
            'caption' => 'Chile',
            'domain' => 'indeed.cl',
        ],
        'cn' => [
            'caption' => 'China',
            'domain' => 'cn.indeed.com',
        ],
        'co' => [
            'caption' => 'Colombia',
            'domain' => 'co.indeed.com',
        ],
        'cz' => [
            'caption' => 'Czech Republic',
            'domain' => 'cz.indeed.com',
        ],
        'dk' => [
            'caption' => 'Denmark',
            'domain' => 'dk.indeed.com',
        ],
        'fi' => [
            'caption' => 'Finland',
            'domain' => 'indeed.fi',
        ],
        'fr' => [
            'caption' => 'France',
            'domain' => 'indeed.fr',
        ],
        'de' => [
            'caption' => 'Germany',
            'domain' => 'de.indeed.com',
        ],
        'gr' => [
            'caption' => 'Greece',
            'domain' => 'gr.indeed.com',
        ],
        'hk' => [
            'caption' => 'Hong Kong',
            'domain' => 'indeed.hk',
        ],
        'hu' => [
            'caption' => 'Hungary',
            'domain' => 'hu.indeed.com',
        ],
        'in' => [
            'caption' => 'India',
            'domain' => 'indeed.co.in',
        ],
        'id' => [
            'caption' => 'Indonesia',
            'domain' => 'id.indeed.com',
        ],
        'ie' => [
            'caption' => 'Ireland',
            'domain' => 'ie.indeed.com',
        ],
        'il' => [
            'caption' => 'Israel',
            'domain' => 'il.indeed.com',
        ],
        'it' => [
            'caption' => 'Italy',
            'domain' => 'it.indeed.com',
        ],
        'jp' => [
            'caption' => 'Japan',
            'domain' => 'jp.indeed.com',
        ],
        'kr' => [
            'caption' => 'Korea',
            'domain' => 'kr.indeed.com',
        ],
        'kw' => [
            'caption' => 'Kuwait',
            'domain' => 'kw.indeed.com',
        ],
        'lu' => [
            'caption' => 'Luxembourg',
            'domain' => 'indeed.lu',
        ],
        'my' => [
            'caption' => 'Malaysia',
            'domain' => 'indeed.com.my',
        ],
        'mx' => [
            'caption' => 'Mexico',
            'domain' => 'indeed.com.mx',
        ],
        'nl' => [
            'caption' => 'Netherlands',
            'domain' => 'indeed.nl',
        ],
        'nz' => [
            'caption' => 'New Zealand',
            'domain' => 'nz.indeed.com',
        ],
        'no' => [
            'caption' => 'Norway',
            'domain' => 'no.indeed.com',
        ],
        'om' => [
            'caption' => 'Oman',
            'domain' => 'om.indeed.com',
        ],
        'pk' => [
            'caption' => 'Pakistan',
            'domain' => 'indeed.com.pk',
        ],
        'pe' => [
            'caption' => 'Peru',
            'domain' => 'indeed.com.pe',
        ],
        'ph' => [
            'caption' => 'Philippines',
            'domain' => 'indeed.com.ph',
        ],
        'pl' => [
            'caption' => 'Poland',
            'domain' => 'pl.indeed.com',
        ],
        'pt' => [
            'caption' => 'Portugal',
            'domain' => 'indeed.pt',
        ],
        'qa' => [
            'caption' => 'Qatar',
            'domain' => 'qa.indeed.com',
        ],
        'ro' => [
            'caption' => 'Romania',
            'domain' => 'ro.indeed.com',
        ],
        'ru' => [
            'caption' => 'Russia',
            'domain' => 'ru.indeed.com',
        ],
        'sa' => [
            'caption' => 'Saudi Arabia',
            'domain' => 'sa.indeed.com',
        ],
        'sg' => [
            'caption' => 'Singapore',
            'domain' => 'indeed.com.sg',
        ],
        'za' => [
            'caption' => 'South Africa',
            'domain' => 'indeed.co.za',
        ],
        'es' => [
            'caption' => 'Spain',
            'domain' => 'indeed.es',
        ],
        'se' => [
            'caption' => 'Sweden',
            'domain' => 'se.indeed.com',
        ],
        'ch' => [
            'caption' => 'Switzerland',
            'domain' => 'indeed.ch',
        ],
        'tw' => [
            'caption' => 'Taiwan',
            'domain' => 'tw.indeed.com',
        ],
        'tr' => [
            'caption' => 'Turkey',
            'domain' => 'tr.indeed.com',
        ],
        'ae' => [
            'caption' => 'United Arab Emirates',
            'domain' => 'indeed.ae',
        ],
        'gb' => [
            'caption' => 'United Kingdom',
            'domain' => 'indeed.co.uk',
        ],
        've' => [
            'caption' => 'Venezuela',
            'domain' => 've.indeed.com',
        ]
    ];

    function pluginSettings()
    {
        $countryList = [];
        foreach (self::$countries as $id => $country) {
            $countryList[] = [
                'id' => $id,
                'caption' => $country['caption']
            ];
        }
        return [
            [
                'id' => 'IndeedPublisherID',
                'caption' => 'Publisher ID',
                'type' => 'string',
                'comment' => 'To get the Publisher ID, go to <a href="https://indeed.com" target="_blank">https://indeed.com</a> , sign in/register, then go to Publishers menu (<a href="https://ads.indeed.com/jobroll/" target="_blank">https://ads.indeed.com/jobroll/</a>) and Create an Account.<br/>Once you created an account, go to XML Feed tab (<a href="https://ads.indeed.com/jobroll/xmlfeed" target="_blank">https://ads.indeed.com/jobroll/xmlfeed</a>) and find your Publisher ID in the table below. ',
                'length' => '50',
                'order' => null,
            ],
            [
                'id' => 'IndeedKeywords',
                'caption' => 'Keyword Query',
                'type' => 'string',
                'comment' => 'Search query to filter your backfill jobs. You can enter keywords, job categories or roles.<br/> By default terms are ANDed. To see what is possible, use <a href="http://www.indeed.com/advanced_search" target="_blank">Indeed advanced search page</a> to perform a search and then check the url for the q value.',
                'length' => '50',
                'order' => null,
            ],
            [
                'id' => 'IndeedCountry',
                'caption' => 'Country',
                'type' => 'list',
                'list_values' => $countryList,
                'comment' => 'Search within country specified',
                'length' => '50',
                'order' => null,
            ],
            [
                'id' => 'IndeedLocation',
                'caption' => 'Location',
                'type' => 'string',
                'comment' => 'Limits your backfill jobs to a specific location. Use a postal code or a "city, state/province/region" combination.',
                'length' => '50',
                'order' => null,
            ],
            [
                'id' => 'IndeedRadius',
                'caption' => 'Radius',
                'type' => 'list',
                'list_values' => [
                    [
                        'id' => '10',
                        'caption' => '10',
                    ],
                    [
                        'id' => '20',
                        'caption' => '20'
                    ],
                    [
                        'id' => '50',
                        'caption' => '50'
                    ],
                    [
                        'id' => '100',
                        'caption' => '100'
                    ],
                    [
                        'id' => '200',
                        'caption' => '200'
                    ],
                ],
                'comment' => 'Distance from search location. Default is 25.',
                'length' => '50',
                'order' => null,
            ],
            [
                'id' => 'IndeedJobType',
                'caption' => 'Job Type',
                'type' => 'list',
                'list_values' => [
                    [
                        'id' => 'fulltime',
                        'caption' => 'fulltime',
                    ],
                    [
                        'id' => 'parttime',
                        'caption' => 'parttime',
                    ],
                    [
                        'id' => 'contract',
                        'caption' => 'contract',
                    ],
                    [
                        'id' => 'internship',
                        'caption' => 'internship',
                    ],
                    [
                        'id' => 'temporary',
                        'caption' => 'temporary',
                    ],
                ],
                'length' => '50',
                'order' => null,
            ],
            [
                'id' => 'IndeedSiteType',
                'caption' => 'Site Type',
                'type' => 'list',
                'list_values' => [
                    [
                        'id' => 'jobsite',
                        'caption' => 'jobsite',
                    ],
                    [
                        'id' => 'employer',
                        'caption' => 'employer'
                    ],
                ],
                'comment' => "To show only jobs from job boards use 'jobsite'. For jobs from direct employer websites use 'employer'",
                'length' => '50',
                'order' => null,
            ],
        ];
    }

    /**
     * Register this plugin as listings provider for ajax requests
     *
     * @static
     * @param array $arrayOfProviders
     * @return array
     */
    public static function registerAsListingsProvider($arrayOfProviders = [])
    {
        $arrayOfProviders[] = 'indeed';
        return $arrayOfProviders;
    }

    /**
     * @param SJB_SearchResultsTP $params
     * @return mixed
     */
    public static function getListingsFromIndeed($params)
    {
        $listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($params->listing_type_sid);
        if ($listingTypeID == 'Job' && $GLOBALS['uri'] == '/jobs/' || $GLOBALS['uri'] == '/ajax/') {
            $page = intval(SJB_Request::getVar('page', $params->listing_search_structure['current_page']));
            $publisherID = SJB_Settings::getSettingByName('IndeedPublisherID');
            $limit = $params->getCriteriaSaver()->getListingsPerPage();
            if (!$limit) {
                $limit = 10;
            }
            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = SJB_Request::getUserAgent();
            $start = $limit * ($page - 1);

            $stateIndexes = [
                'AL' => 'Alabama',
                'AK' => 'Alaska',
                'AZ' => 'Arizona',
                'AR' => 'Arkansas',
                'CA' => 'California',
                'CO' => 'Colorado',
                'CT' => 'Connecticut',
                'DE' => 'Delaware',
                'FL' => 'Florida',
                'GA' => 'Georgia',
                'HI' => 'Hawaii',
                'ID' => 'Idaho',
                'IL' => 'Illinois',
                'IN' => 'Indiana',
                'IA' => 'Iowa',
                'KS' => 'Kansas',
                'KY' => 'Kentucky',
                'LA' => 'Louisiana',
                'ME' => 'Maine',
                'MD' => 'Maryland',
                'MA' => 'Massachusetts',
                'MI' => 'Michigan',
                'MN' => 'Minnesota',
                'MS' => 'Mississippi',
                'MO' => 'Missouri',
                'MT' => 'Montana',
                'NE' => 'Nebraska',
                'NV' => 'Nevada',
                'NH' => 'New Hampshire',
                'NJ' => 'New Jersey',
                'NM' => 'New Mexico',
                'NY' => 'New York',
                'NC' => 'North Carolina',
                'ND' => 'North Dakota',
                'OH' => 'Ohio',
                'OK' => 'Oklahoma',
                'OR' => 'Oregon',
                'PA' => 'Pennsylvania',
                'RI' => 'Rhode Island',
                'SC' => 'South Carolina',
                'SD' => 'South Dakota',
                'TN' => 'Tennessee',
                'TX' => 'Texas',
                'UT' => 'Utah',
                'VT' => 'Vermont',
                'VA' => 'Virginia',
                'WA' => 'Washington',
                'WV' => 'West Virginia',
                'WI' => 'Wisconsin',
                'WY' => 'Wyoming',
                'DC' => 'District of Columbia',
                'AS' => 'American Samoa',
                'GU' => 'Guam',
                'MP' => 'Northern Mariana Islands',
                'PR' => 'Puerto Rico',
                'UM' => "United's Minor Outlying Islands",
                'VI' => 'Virgin Islands'
            ];

            // SET PARAMS FOR REQUEST
            $keywords = SJB_Settings::getValue('IndeedKeywords');
            $criteria = $params->criteria_saver->criteria;
            $fieldSID = SJB_ListingFieldManager::getListingFieldSIDByID('JobCategory');
            $fieldInfo = SJB_ListingFieldDBManager::getListValuesBySID($fieldSID);
            $fieldList = [];
            foreach ($fieldInfo as $val)
                $fieldList[$val['id']] = $val['caption'];

            $categoryCriteria = isset($criteria['JobCategory']['multi_like']) ? $criteria['JobCategory']['multi_like'] : '';

            if (!empty($categoryCriteria)) {
                $categoryKeywords = [];
                foreach ($categoryCriteria as $category) {
                    if (!empty($category) && !empty($fieldList[$category]))
                        $categoryKeywords[] = $fieldList[$category];
                }
                if ($categoryKeywords) {
                    $keywords .= ' (' . join(' or ', $categoryKeywords) . ')';
                }
            }
            foreach ($criteria as $fieldName => $field) {
                if (is_array($field)) {
                    foreach ($field as $fieldType => $values) {
                        if ($fieldType === 'multi_like_and') {
                            foreach ($values as $val) {
                                $keywords .= ' "' . $val . '"';
                            }
                        }
                    }
                }
            }
            if (isset($criteria['keywords']) && !empty($criteria['keywords'])) {
                foreach ($criteria['keywords'] as $key => $item) {
                    if ($key == 'all_words') {
                        $keywords .= ' ' . $item;
                    }
                }
            }
            if (substr($keywords, -4) == ' or ') {
                $keywords = substr($keywords, 0, strlen($keywords) - 4);
            }
            $keywords = trim($keywords);

            $location = SJB_Settings::getValue('IndeedLocation');
            $radius = SJB_Settings::getValue('IndeedRadius');
            if (!empty($criteria['GooglePlace']['location']['value'])) {
                $locationInfo = \SJB\Location\Helper::getLocationFromGoogle($criteria['GooglePlace']['location']['value'], false);
                if ($locationInfo) {
                    if (!empty($criteria['GooglePlace']['location']['radius'])) {
                        $radius = $criteria['GooglePlace']['location']['radius'];
                    }
                    $location = trim($locationInfo['City'] . ', ' . $locationInfo['State'], ' ,');
                }
            } else {
                $locationFields = ['City', 'State'];
                $fieldVals = [];
                foreach ($locationFields as $locationField) {
                    if (!empty($criteria['Location_' . $locationField])) {
                        $fieldVals[] = current($criteria['Location_' . $locationField]);
                    }
                }
                if ($fieldVals) {
                    $location = join(', ', $fieldVals);
                }
            }

            $indeedCountry = SJB_Settings::getValue('IndeedCountry', 'us');
            $country = !empty($criteria['Location_Country']['multi_like'][0]) ? $criteria['Location_Country']['multi_like'][0] : $indeedCountry;

            $jobType = SJB_Settings::getSettingByName('IndeedJobType');
            $siteType = SJB_Settings::getSettingByName('IndeedSiteType');
            $query = [
                'publisher' => $publisherID,
                'q' => $keywords,
                'l' => $location,
                'radius' => $radius,
                'st' => $siteType,
                'jt' => $jobType,
                'start' => $start,
                'limit' => $limit,
                'fromage' => '',
                'filter' => '',
                'latlong' => 1,
                'co' => $country,
                'chnl' => '',
                'userip' => $ip,
                'useragent' => $userAgent,
                'v' => 2,
            ];
            $url = "http://api.indeed.com/ads/apisearch?" . http_build_query($query);
            $indeedListings = [];
            try {
                $xml = SJB_H::getUrlContentByCurl($url);
                $doc = new DOMDocument();
                $doc->loadXML($xml, LIBXML_NOERROR);
                $results = $doc->getElementsByTagName('results');
                if (!($results instanceof DOMNodeList)) {
                    throw new ErrorException('CANT GET INDEED XML RESULTS');
                }
                $totalResults = $doc->getElementsByTagName('totalresults')->item(0)->nodeValue;
                if ($totalResults) {
                    $totalPages = ceil(((integer)$totalResults) / $limit);
                    $pageNumber = $doc->getElementsByTagName('pageNumber')->item(0)->nodeValue + 1;
                    $indeedDomain = !empty($indeedCountry) && isset(self::$countries[$indeedCountry]['domain']) ? self::$countries[$indeedCountry]['domain'] : self::$countries['us']['domain'];
                    if (strpos($indeedDomain, '.') !== 2) {
                        $indeedDomain = 'www.' . $indeedDomain;
                    }

                    foreach ($results as $node) {
                        foreach ($node->getElementsByTagName('result') as $result) {
                            $resultXML = simplexml_import_dom($result);
                            $jobKey = (string)$resultXML->jobkey;
                            $state = (string)$resultXML->state;
                            $country = (string)$resultXML->country;

                            $indeedListings [$jobKey] = [
                                'Title' => (string)$resultXML->jobtitle,
                                'CompanyName' => (string)$resultXML->company,
                                'JobDescription' => (string)$resultXML->snippet,
                                'Location' => [
                                    'Country' => empty($country) ? '' : self::$countries[strtolower($country)]['caption'],
                                    'State' => empty($state) ? '' : isset($stateIndexes [strtoupper($state)]) ? $stateIndexes [strtoupper($state)] : $state,
                                    'City' => (string)$resultXML->city,
                                ],
                                'url' => (string)$resultXML->url,
                                'onmousedown' => ' onMouseDown="' . (string)$resultXML->onmousedown . '" ',
                                'target' => ' target="_blank" ',
                                'jobkey' => $jobKey,
                                'activation_date' => (string)$resultXML->date,
                                'api' => 'indeed',
                                'code' => '<span id="indeed_at"><a href="' . SJB_Request::getProtocol() . '://' . $indeedDomain . '/">jobs</a> by <a href="' . SJB_Request::getProtocol() . '://' . $indeedDomain . '/" title="Job Search"><img src="' . SJB_Request::getProtocol() . '://www.indeed.com/p/jobsearch.gif" style="border: 0; vertical-align: middle;" alt="Indeed job search"></a></span>',
                                'pageNumber' => $pageNumber,
                                'totalPages' => $totalPages,
                            ];
                        }
                    }
                }
            } catch (ErrorException $e) {
                SJB_Logger::error($e->getMessage());
            }
            self::$indeedListings = $indeedListings;
        }
        return $params;
    }

    public static function addIndeedListingsToListingStructure($listings_structure)
    {
        foreach (self::$indeedListings as $indeedListing)
            $listings_structure[$indeedListing['jobkey']] = $indeedListing;
        return $listings_structure;
    }
}
