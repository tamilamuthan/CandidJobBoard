<?php
class SJB_Miscellaneous_TaskScheduler extends SJB_Function
{
	/** @var SJB_TemplateProcessor*/
	public $tp;

	private $lang;
	private $currentDate;
	private $notifiedJobAlerts = array();

	public function execute()
	{
		set_time_limit(0);
		$i18n = SJB_I18N::getInstance();
		$this->lang = $i18n->getLanguageData($i18n->getCurrentLanguage());
		$this->currentDate = strftime($this->lang['date_format'], time());

		$this->tp = SJB_System::getTemplateProcessor();

		if ((time() - SJB_Settings::getSettingByName('task_scheduler_last_executed_time_hourly')) > 3600) {
			$this->runHourlyTaskScheduler();
			SJB_Settings::updateSetting('task_scheduler_last_executed_time_hourly', time());
		}
		if ((time() - SJB_Settings::getSettingByName('task_scheduler_last_executed_time_daily')) > 86400) {
			$this->runDailyTaskScheduler();
			SJB_Settings::updateSetting('task_scheduler_last_executed_time_daily', time());
		}
		$this->runTaskScheduler();
	}

	private function runDailyTaskScheduler()
	{
		$guestsNotifiedEmails = $this->sendGuestsAlerts();
		$this->tp->assign('notified_guests_emails', $guestsNotifiedEmails);
	}

	private function runHourlyTaskScheduler()
	{
	}

	private function runTaskScheduler()
	{
		// Deactivate Expired Listings & Send Notifications
		$listingsExpiredID = SJB_ListingManager::getExpiredListingsSID();
		foreach ($listingsExpiredID as $listingExpiredID) {
			SJB_ListingManager::deactivateListingBySID($listingExpiredID, true);
			$listing = SJB_ListingManager::getObjectBySID($listingExpiredID);
			$listingInfo = SJB_ListingManager::createTemplateStructureForListing($listing);
			SJB_Notifications::sendUserListingExpiredLetter($listingInfo);
		}
		$listingsDeactivatedID = array();
		if (SJB_Settings::getSettingByName('automatically_delete_expired_listings')) {
			$listingsDeactivatedID = SJB_ListingManager::getDeactivatedListingsSID();
			foreach ($listingsDeactivatedID as $listingID) {
				SJB_ListingManager::deleteListingBySID($listingID);
			}
		}

		SJB_ListingManager::unFeaturedListings();
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));

		// Send Notifications for Expired Contracts
		$contractsExpiredID = SJB_ContractManager::getExpiredContractsID();
		foreach ($contractsExpiredID as $contractExpiredID) {
			$contractInfo = SJB_ContractManager::getInfo($contractExpiredID);
			$productInfo = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
			$userInfo = SJB_UserManager::getUserInfoBySID($contractInfo['user_sid']);
			$serializedExtraInfo = unserialize($contractInfo['serialized_extra_info']);

			if (! empty($serializedExtraInfo['featured_profile']) && ! empty($userInfo['featured'])) {
				$contracts = SJB_ContractManager::getAllContractsInfoByUserSID($userInfo['sid']);
				$isFeatured = 0;
				foreach ($contracts as $contract) {
					if ($contract['id'] != $contractExpiredID) {
						$serializedExtraInfo = unserialize($contract['serialized_extra_info']);
						if (! empty($serializedExtraInfo['featured'])) {
							$isFeatured = 1;
						}
					}
				}
				if (! $isFeatured) {
					SJB_UserManager::removeFromFeaturedBySID($userInfo['sid']);
				}
			}
			SJB_Notifications::sendUserContractExpiredLetter($userInfo, $contractInfo, $productInfo);
			SJB_ContractManager::deleteContract($contractExpiredID, $contractInfo['user_sid']);
		}

		// LISTING XML IMPORT
		SJB_XmlImport::runImport();

		// UPDATE PAGES WITH FUNCTION EQUAL BROWSE(e.g. /browse-by-city/)
		SJB_BrowseDBManager::rebuildBrowses();

		//-------------------sitemap generator--------------------//
		SJB_System::executeFunction('miscellaneous', 'sitemap_generator');

		SJB_Settings::updateSetting('task_scheduler_last_executed_date', $this->currentDate);
		$this->tp->assign('expired_listings_id', $listingsExpiredID);
		$this->tp->assign('deactivated_listings_id', $listingsDeactivatedID);
		$this->tp->assign('expired_contracts_id', $contractsExpiredID);
		$this->tp->assign('notifiedJobAlerts', $this->notifiedJobAlerts);

		$schedulerLog = $this->tp->fetch('task_scheduler_log.tpl');

		SJB_DB::query('INSERT INTO `task_scheduler_log`
			(`last_executed_date`, `notifieds_sent`, `expired_listings`, `expired_contracts`, `log_text`)
			VALUES ( NOW(), ?n, ?n, ?n, ?s)',
			count($this->notifiedJobAlerts), count($listingsExpiredID), count($contractsExpiredID), $schedulerLog);

//		SJB_System::getModuleManager()->executeFunction('social', 'linkedin');
//		SJB_System::getModuleManager()->executeFunction('social', 'facebook');
		SJB_System::getModuleManager()->executeFunction('classifieds', 'linkedin');
		SJB_System::getModuleManager()->executeFunction('classifieds', 'facebook');

		SJB_Event::dispatch('task_scheduler_run');
	}

	public function sendGuestsAlerts()
	{
		$guestEmailsNotified = array();
		$notificationsLimit = (int)SJB_Settings::getSettingByName('num_of_listings_sent_in_email_alerts');

		$listing = new SJB_Listing();
		$listing->addActivationDateProperty();
		$aliasInfoID = $listing->addIDProperty();
		$userNameAliasInfo = $listing->addUsernameProperty();
		$listingTypeIDInfo = $listing->addListingTypeIDProperty();
		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias($aliasInfoID);
		$aliases->addAlias($userNameAliasInfo);
		$aliases->addAlias($listingTypeIDInfo);

		$guestAlertsToNotify = SJB_GuestAlertManager::getGuestAlertsToNotify();

		foreach ($guestAlertsToNotify as $guestAlertInfo) {
			$dataSearch = unserialize($guestAlertInfo['data']);
			$dataSearch['active']['equal'] = 1;
			if (! empty($guestAlertInfo['last_send'])) {
				$dateArr = explode(' ', $guestAlertInfo['last_send']);
				$dateArr = explode('-', $dateArr[0]);
				$guestAlertInfo['last_send'] = strftime($this->lang['date_format'], mktime(0, 0, 0, $dateArr[1], $dateArr[2], $dateArr[0]));
				$dataSearch['activation_date']['not_less'] = $guestAlertInfo['last_send'];
			}
			$dataSearch['activation_date']['not_more'] = $this->currentDate;
			$listingTypeSID = 0;
			if ($dataSearch['listing_type']['equal']) {
				$listingTypeID = $dataSearch['listing_type']['equal'];
				$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
			}

			$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($dataSearch, $listing);
			$searcher = new SJB_ListingSearcher();
			$searcher->found_object_sids = array();
			$searcher->setLimit($notificationsLimit);
			$listingsIDsFound = $searcher->getObjectsSIDsByCriteria($criteria, $aliases);

			if (count($listingsIDsFound)) {
				$sentGuestAlertNewListingsFoundLetter = SJB_Notifications::sendGuestAlertNewListingsFoundLetter($listingsIDsFound, $guestAlertInfo, $listingTypeSID);
				if ($sentGuestAlertNewListingsFoundLetter) {
					SJB_GuestAlertManager::markGuestAlertAsSentBySID($guestAlertInfo['sid']);
					array_push($guestEmailsNotified, $guestAlertInfo['email']);
					$this->notifiedJobAlerts[] = $guestAlertInfo['sid'];
				}
			}
		}
		return $guestEmailsNotified;
	}
}
