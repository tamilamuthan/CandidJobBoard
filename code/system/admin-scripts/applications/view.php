<?php
class SJB_Admin_Applications_View extends SJB_Function
{
	public function isAccessible()
	{
		$userSid = SJB_Request::getVar('user_sid', null, 'GET');
		$userGroupID = SJB_UserGroupManager::getUserGroupIDByUserSID($userSid);
		$this->setPermissionLabel('manage_' . strtolower($userGroupID));
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$displayTemplate = 'view.tpl';
		$errors = array();

		$filename = SJB_Request::getVar('filename', false);

		if ($filename) {
			$appsID = SJB_Request::getVar('appsID', false);
			if ($appsID) {
				$file = SJB_UploadFileManager::openApplicationFile($filename, $appsID);
				if (!$file)
					$errors['NO_SUCH_FILE'] = true;
			}
			else
				$errors['NO_SUCH_APPS'] = true;
		}

		$cu = SJB_UserManager::getUserInfoBySID(SJB_Request::getVar('user_sid', null));
		$tp->assign('user_sid', $cu['sid']);
		$tp->assign('username', $cu['username']);

		$appJobId = SJB_Request::getVar('appJobId', false);

		// посортируем чего-нибуть
		$orderBy = SJB_Request::getVar('orderBy', 'date');
		$order = SJB_Request::getVar('order', 'desc');

		$tp->assign('orderBy', $orderBy);
		$tp->assign('order', $order);

		if (!empty($orderBy) && isset($order)) {
			switch ($orderBy) {
				case 'date':
					$orderInfo = array('sorting_field' => 'date', 'sorting_order' => $order);
					break;
				case 'title':
					$orderInfo = array('sorting_field' => 'Title', 'sorting_order' => $order, 'inner_join' => array('table' => 'listings', 'field1' => 'sid', 'field2' => 'listing_id'));
					break;
				case 'applicant':
					$orderInfo = false;
					$sortByUsername = true;
					break;
				case 'company':
					$orderInfo = array('sorting_field' => 'CompanyName', 'sorting_order' => $order, 'inner_join' => array('table' => 'listings', 'field1' => 'sid', 'field2' => 'listing_id'), 'inner_join2' => array('table1' => 'users', 'table2' => 'listings', 'field1' => 'sid', 'field2' => 'user_sid'));
					break;
			}
		}

		if ($cu['user_group_sid'] == SJB_UserGroup::EMPLOYER) { // Работадатель
			if ($appJobId) {
				$apps = SJB_Applications::getByJob($appJobId, $orderInfo);
				$jobInfo = SJB_ListingManager::getListingInfoBySID($appJobId);
				$tp->assign('jobInfo', $jobInfo);
				$tp->assign('listingType', SJB_ListingTypeManager::getListingTypeInfoBySID($jobInfo['listing_type_sid']));
			}
			else {
				$apps = SJB_Applications::getByEmployer($cu['sid'], $orderInfo);
			}

			for ($i = 0; $i < count($apps); ++$i) {
				$apps[$i]['job'] = SJB_ListingManager::getListingInfoBySID($apps[$i]['listing_id']);
				if (isset($apps[$i]['resume']) && !empty($apps[$i]['resume']))
					$apps[$i]['resumeInfo'] = SJB_ListingManager::getListingInfoBySID($apps[$i]['resume']);
				if ($apps[$i]['jobseeker_id'] == 0)
					$apps[$i]['user']['FirstName'] = $apps[$i]['username'];
				else
					$apps[$i]['user'] = SJB_UserManager::getUserInfoBySID($apps[$i]['jobseeker_id']);
			}

			$jobs = SJB_ListingManager::getListingsByUserSID($cu['sid']);
			$appJobs = array();
			foreach ($jobs as $job)
				$appJobs[] = array('title' => $job->details->properties['Title']->value, 'id' => $job->sid);

			$tp->assign('appJobs', $appJobs);
			$tp->assign('current_filter', $appJobId);

		}
		else { // Соискатель

			$apps = SJB_Applications::getByJobseeker($cu['sid'], $orderInfo);
			for ($i = 0; $i < count($apps); ++$i) {
				$apps[$i]['job'] = SJB_ListingManager::getListingInfoBySID($apps[$i]['listing_id']);
				$apps[$i]['company'] = SJB_UserManager::getUserInfoBySID($apps[$i]['job']['user_sid']);
			}

			$displayTemplate = 'view_seeker.tpl';
		}

		if (isset($sortByUsername)) {
			$order = ($order == 'desc') ? SORT_DESC : SORT_ASC;
			foreach ($apps as $key => $value) {
				if (!isset($apps[$key]['user']['FirstName']))
					$apps[$key]['user']['FirstName'] = '';
				if (!isset($apps[$key]['user']['LastName']))
					$apps[$key]['user']['LastName'] = '';
				$sortKeys[$key] = $apps[$key]['user']['FirstName'] . ' ' . $apps[$key]['user']['LastName'];
			}
			if ($apps)
				array_multisort($sortKeys, $order, SORT_REGULAR, $apps);
		}
		$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($cu['user_group_sid']);
		SJB_System::setGlobalTemplateVariable('wikiExtraParam', $userGroupInfo['id']);
		$tp->assign('METADATA', SJB_Applications::getApplicationMeta());
		$tp->assign("user_group_info", $userGroupInfo);
		$tp->assign('applications', $apps);
		$tp->assign('errors', $errors);
		$tp->display($displayTemplate);

	}
}