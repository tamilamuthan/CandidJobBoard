<?php


class I18NPhraseSearcher
{
	/**
	 * @var I18NDataSource
	 */
	var $dataSource;
	var $matcher;

	/**
	 * @param I18NDataSource $dataSource
	 */
	function setDataSource(I18NDataSource &$dataSource)
	{
		$this->dataSource =& $dataSource;
	}

	function setMatcher(&$matcher)
	{
		$this->matcher =& $matcher;
	}
	
	function &search(&$criteria)
	{
		$domainsData =& $this->getDomainsData($criteria->getDomainID());
		$phrasesData =& $this->getAllPhrases($domainsData);
		
		$query = $criteria->getPhraseID();
		if(!empty($query))
		{
			$phrasesData =& $this->filterPhrases($query, $phrasesData);
		}
		return $phrasesData;
	}
	
	function &getDomainsData($domain_id)
	{
		if (empty($domain_id))
		{
			$domainsData = $this->dataSource->getDomainsData();
		}
		else
		{
			$domainData =& $this->dataSource->getDomainData($domain_id);
			$domainsData = array(&$domainData);
		}
		return $domainsData;
	}

	function &getAllPhrases(&$domainsData)
	{	
		$phrasesData = array();
		foreach (array_keys($domainsData) as $i)
		{
			$domainData =& $domainsData[$i];
			$domainPhrases =& $this->dataSource->getDomainPhrases($domainData->getID());
			$phrasesData = array_merge($phrasesData, $domainPhrases);
		}
		return $phrasesData;
	}

	/**
	 * @param $query
	 * @param PhraseData[] $phrasesData
	 * @return array
	 */
	function &filterPhrases($query, &$phrasesData)
	{
		$this->matcher->setQuery($query);
		$currentLang = SJB_Settings::getValue('i18n_default_language');
		$filteredPhrasesData = array();
		foreach ($phrasesData as $key => $phraseData) {
			if ($this->matcher->match($phraseData->getID())) {
				$filteredPhrasesData[] =& $phrasesData[$key];
			} else {
				foreach ($phraseData->translations as $translation) {
					/** @var TranslationData $translation */
					if ($translation->getLanguageID() == $currentLang) {
						if ($this->matcher->match($translation->getTranslation())) {
							$filteredPhrasesData[] =& $phrasesData[$key];
						}
					}
				}
			}
		}
		return $filteredPhrasesData;
	}
}
