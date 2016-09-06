<?php

class SJB_Admin_ListingImport_DeleteImport extends SJB_Function
{
	public function execute()
	{
		$id = SJB_Request::getVar('id', false);
		if ($id) {
			SJB_DB::query("DELETE FROM `parsers` WHERE id = ?n", $id);
		}
		SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . "/show-import/");
	}
}

