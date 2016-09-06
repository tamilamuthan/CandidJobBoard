<?php

class SJB_Admin_Classifieds_EditListingType extends SJB_Function
{
	public function execute()
	{
		$listingTypeSID = SJB_Request::getVar('sid');
		if ($listingTypeSID == '6') {
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/posting-pages/job/edit/11');
		}
		if ($listingTypeSID == '7') {
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/posting-pages/resume/edit/19');
		}
	}
}
