<?php

class SJB_Miscellaneous_SitemapGenerator extends SJB_Function
{
	public function execute()
	{
		$list_of_pages = SJB_PageManager::get_pages(true);
		$scriptPath = explode(SJB_System::getSystemSettings("SYSTEM_URL_BASE"), __FILE__);
		$scriptPath = array_shift($scriptPath);
		SJB_System::setSystemSettings('SITE_URL', SJB_H::getCustomDomainUrl());

		$handle = fopen($scriptPath . "sitemap.xml", "w");
		$text = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$text .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		fwrite($handle, $text);

		fwrite($handle, $this->makeMapItem(SJB_System::getSystemSettings("SITE_URL") . '/'));

		// pages
		foreach ($list_of_pages as $page) {
			fwrite($handle, $this->makeMapItem(SJB_System::getSystemSettings("SITE_URL") . $page['uri']));
		}

		// jobs
		$request['action'] = 'search';
		$request['listing_type']['equal'] = 'Job';
		$found_listings_sids = $this->searchListings($request, 'Job');
		foreach ($found_listings_sids as $sid) {
			$listing_info = SJB_ListingManager::getListingInfoBySID($sid);
			fwrite($handle, $this->makeMapItem(SJB_System::getSystemSettings('SITE_URL') . SJB_TemplateProcessor::listing_url($listing_info)));
		}

		// resumes
		if (SJB_Settings::getValue('public_resume_access')) {
			$request['action'] = 'search';
			$request['listing_type']['equal'] = 'Resume';
			$found_listings_sids = $this->searchListings($request, 'Resume');
			foreach ($found_listings_sids as $sid) {
				$listing_info = SJB_ListingManager::getListingInfoBySID($sid);
				fwrite($handle, $this->makeMapItem(SJB_System::getSystemSettings('SITE_URL') . SJB_TemplateProcessor::listing_url($listing_info)));
			}
		}

		// browse by category
		$categoriesInfo = SJB_ListingFieldManager::getFieldInfoBySID(198);
		foreach ($categoriesInfo['list_values'] as $category) {
			fwrite($handle, $this->makeMapItem(SJB_System::getSystemSettings("SITE_URL") . '/categories/' . $category['id'] . '/' . SJB_TemplateProcessor::pretty_url($category['caption']) . '-jobs/'));
		}

		// search results
		fwrite($handle, $this->makeMapItem(SJB_System::getSystemSettings("SITE_URL") . '/jobs/'));
		if (SJB_Settings::getValue('public_resume_access')) {
			fwrite($handle, $this->makeMapItem(SJB_System::getSystemSettings("SITE_URL") . '/resumes/'));
		}

		// companies
		fwrite($handle, $this->makeMapItem(SJB_System::getSystemSettings("SITE_URL") . '/companies/'));

		$searcher = new SJB_UserSearcher();
		$user = new SJB_User([], SJB_UserGroup::EMPLOYER);
		$search_form_builder = new SJB_SearchFormBuilder($user);
		$criteria = $search_form_builder->extractCriteriaFromRequestData(
			[
				'username' => ['not_equal' => 'jobg8'],
				'active' => ['equal' => 1],
				'CompanyName' => ['not_empty' => true],
			], $user);

		$employers = $searcher->getObjectsSIDsByCriteria($criteria);
		foreach ($employers as $employer) {
			$employer = SJB_UserManager::getUserInfoBySID($employer);
			fwrite($handle, $this->makeMapItem(SJB_System::getSystemSettings('SITE_URL') . "/company/{$employer['sid']}/" . SJB_TemplateProcessor::pretty_url($employer['CompanyName']) . '/'));
		}

		$text = "\n" . '</urlset>';
		fwrite($handle, $text);
		fclose($handle);
	}

	private function makeMapItem($url)
	{
		$text   = [''];
		$text[] = '    <url>';
		$text[] = '        <loc>' . htmlspecialchars($url, ENT_XML1) . '</loc>';
		$text[] = '        <lastmod>' . date('Y-m-d') . '</lastmod>';
		$text[] = '        <changefreq>daily</changefreq>';
		$text[] = '        <priority>1</priority>';
		$text[] = '    </url>';
		return implode("\n", $text);
	}

	private function searchListings($requested_data, $listing_type_id)
	{
		$criteria_saver = new SJB_ListingCriteriaSaver(microtime(true));
		$listing_type_sid = !empty($listing_type_id) ? SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id) : 0;
		$requested_data['active']['equal'] = '1';
		$criteria_saver->setSessionForCriteria(array_merge($criteria_saver->getCriteria(), $requested_data));
		return $this->getListingSidCollectionFromRequest($requested_data, $listing_type_sid, $criteria_saver);
	}

	private function getListingSidCollectionFromRequest($requested_data, $listing_type_sid, $criteria_saver)
	{
		$listing = new SJB_Listing([], $listing_type_sid);
		$id_alias_info = $listing->addIDProperty();
		$listing->addActivationDateProperty();
		$username_alias_info = $listing->addUsernameProperty();
		$listing_type_id_info = $listing->addListingTypeIDProperty();
		$listing->addCompanyNameProperty();

		// select only accessible listings by user sid
		// see SearchCriterion.php, AccessibleCriterion class
		$requested_data['access_type'] = ['accessible' => SJB_UserManager::getCurrentUserSID()];

		$criteria = $criteria_saver->getCriteria();
		$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData(array_merge($criteria, $requested_data), $listing);

		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias($id_alias_info);
		$aliases->addAlias($username_alias_info);
		$aliases->addAlias($listing_type_id_info);

		$searcher = new SJB_ListingSearcher();
		return $searcher->getObjectsSIDsByCriteria($criteria, $aliases);
	}
}
