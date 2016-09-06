<?php

class SJB_Admin_Dashboard_View extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('usersInfo', SJB_UserManager::getUsersInfo());
		$tp->assign('groupsInfo', SJB_UserManager::getGroupsInfo());
		$tp->assign('listingsInfo', SJB_ListingManager::getListingsInfo());
		$tp->assign('listingTypesInfo', SJB_ListingTypeManager::getAllListingTypesInfo());
		$tp->assign('invoicesInfo', SJB_InvoiceManager::getInvoicesInfo());
		$tp->assign('applicationsInfo', SJB_Applications::getApplicationsInfo());
		$tp->assign('jobAlertsInfo', SJB_GuestAlertManager::getAlertsInfo());
		$tp->display('index.tpl');
	}
}
