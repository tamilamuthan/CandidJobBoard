<?php

class SJB_Applications_View extends SJB_Function
{
	private $pages;
	private $totalPages;
	private $currentPage;
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$appsPerPage = SJB_Request::getVar('appsPerPage', 10);
		$this->currentPage = SJB_Request::getVar('page', 1);
		$currentUser = SJB_UserManager::getCurrentUser();
		$appJobId = SJB_Request::getVar('appJobId', false, null, 'int');
		$orderBy = SJB_Request::getVar('orderBy', 'date');
		$order = SJB_Request::getVar('order', 'desc');
		$displayTemplate = "view.tpl";
		$errors = array();

		// не бум пускать незарегенных
		if (SJB_UserManager::isUserLoggedIn() === false) {
			$tp->assign("ERROR", "NOT_LOGIN");
			$tp->display("../miscellaneous/error.tpl");
			return;
		}

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

		if (!is_numeric($this->currentPage) || $this->currentPage < 1) {
			$this->currentPage = 1;
		}

		if (!is_numeric($appsPerPage) || $appsPerPage < 1) {
			$appsPerPage = 10;
		}

		if ($order != 'asc' && $order != 'desc') {
			$order = 'desc';
		}

		$tp->assign("orderBy", $orderBy);
		$tp->assign("order", $order);
		if (isset($orderBy) && isset($order) && $orderBy != "") {
			switch ($orderBy) {
				case "date":
					$orderInfo = array('sorting_field' => 'date', 'sorting_order' => $order);
					break;
				case "title":
					$orderInfo = array('sorting_field' => 'Title', 'sorting_order' => $order, 'inner_join' => array('table' => 'listings', 'field1' => 'sid', 'field2' => 'listing_id'));
					break;
				case "applicant":
					$orderInfo = false;
					$sortByUsername = true;
					break;
				case "company":
					$orderInfo = array('sorting_field' => 'CompanyName', 'sorting_order' => $order, 'inner_join' => array('table' => 'listings', 'field1' => 'sid', 'field2' => 'listing_id'), 'inner_join2' => array('table1' => 'users', 'table2' => 'listings', 'field1' => 'sid', 'field2' => 'user_sid'), );
					break;
				default:
					$orderInfo = array('sorting_field' => 'date', 'sorting_order' => $order);
			}
		}
		if ($currentUser->getUserGroupSID() == SJB_UserGroup::EMPLOYER) {
			$jobs = SJB_DB::query('select `Title` as `title`, `sid` as `id` from `listings` where `user_sid` = ?n', $currentUser->sid);

			$listingTitle = null;
			foreach ($jobs as $job) {
				if ($job['id'] == $appJobId)
					$listingTitle = $job['title'];
			}
			$apps = $this->executeApplicationsForEmployer($appsPerPage, $appJobId, $currentUser, $orderInfo, $listingTitle);

			if (empty($apps) && $this->currentPage > 1) {
				$this->currentPage = 1;
				$apps = $this->executeApplicationsForEmployer($appsPerPage, $appJobId, $currentUser, $orderInfo, $listingTitle);
			}

			foreach ($apps as $i => $app) {
				$apps[$i]["job"] = SJB_ListingManager::getListingInfoBySID($apps[$i]["listing_id"]);
				if (isset($apps[$i]["resume"]) && !empty($apps[$i]["resume"])) {
					$resume = SJB_ListingManager::getObjectBySID($apps[$i]["resume"]);
					if ($resume) {
						$apps[$i]["resumeInfo"] = $apps[$i]["resumeInfo"] = SJB_ListingManager::createTemplateStructureForListing($resume);
					}
				}
				// если это анонимный соискатель - то возьмем имя из пришедшего поля 'username'
				if ($apps[$i]['jobseeker_id'] == 0) {
					$apps[$i]["user"]["FirstName"] = $apps[$i]['username'];
				} else {
					$apps[$i]["user"] = SJB_UserManager::getUserInfoBySID($apps[$i]["jobseeker_id"]);
				}
			}

			$tp->assign("appsPerPage", $appsPerPage);
			$tp->assign("currentPage", $this->currentPage);
			$tp->assign("pages", $this->pages);
			$tp->assign("totalPages", $this->totalPages);
			$tp->assign("appJobs", $jobs);
			$tp->assign("current_filter", $appJobId);
			$tp->assign("listing_title", $listingTitle);
		} else { // jobseeker

			$apps = SJB_Applications::getByJobseeker($currentUser->sid, $orderInfo);
			for ($i = 0; $i < count($apps); ++$i) {
				$apps[$i]["job"] = SJB_ListingManager::getListingInfoBySID($apps[$i]["listing_id"]);
				$apps[$i]["company"] = SJB_UserManager::getUserInfoBySID($apps[$i]["job"]["user_sid"]);
			}

			$displayTemplate = "view_seeker.tpl";
		}

		if (isset($sortByUsername)) {
			$sortKeys = array();
			$order = ($order == "desc") ? SORT_DESC : SORT_ASC;
			foreach ($apps as $key => $value) {
				if (!isset($apps[$key]["user"]["FirstName"])) $apps[$key]["user"]["FirstName"] = '';
				if (!isset($apps[$key]["user"]["LastName"])) $apps[$key]["user"]["LastName"] = '';
				$sortKeys[$key] = $apps[$key]["user"]["FirstName"] . " " . $apps[$key]["user"]["LastName"];
			}
			array_multisort($sortKeys, $order, SORT_REGULAR, $apps);
		}

		if (empty($apps)) {
			$errors['APPLICATIONS_NOT_FOUND'] = true;
		}

		$tp->assign("METADATA", SJB_Applications::getApplicationMeta());
		$tp->assign("applications", $apps);
		$tp->assign("errors", $errors);
		$tp->display($displayTemplate);
	}

	private function executeApplicationsForEmployer($appsPerPage, $appJobId, SJB_User $currentUser, $orderInfo, $listingTitle)
	{
		$limit['countRows'] = $appsPerPage;
		$limit['startRow'] = $this->currentPage * $appsPerPage - ($appsPerPage);
		$apps = array();
		if ($appJobId) {
			if (SJB_Applications::isUserOwnsAppsByAppJobId($currentUser->getID(), $appJobId)) {
				$allAppsCountByJobID = SJB_Applications::getCountAppsByJob($appJobId);
				$this->setPaginationInfo($appsPerPage, $allAppsCountByJobID);
				$apps = SJB_Applications::getByJob($appJobId, $orderInfo, $limit);
			}
		} else {
			$allAppsCount = SJB_Applications::getCountApplicationsByEmployer($currentUser->getSID());
			$this->setPaginationInfo($appsPerPage, $allAppsCount);
			$apps = SJB_Applications::getByEmployer($currentUser->getSID(), $orderInfo, $limit);
		}
		return $apps;
	}

	/**
	 * @param $appsPerPage
	 * @param $appsCount
	 */
	private function setPaginationInfo($appsPerPage, $appsCount)
	{
		$this->totalPages = ceil($appsCount / $appsPerPage);
		if (empty($this->totalPages)) {
			$this->totalPages = 1;
		}

		$this->pages = array();
		for ($i = $this->currentPage - 2; $i < $this->currentPage + 3; $i++) {
			if ($i == $this->totalPages) {
				break;
			} else {
				if ($i > 0) {
					$this->pages[] = $i;
				}
				if ($i * $appsPerPage > $appsCount) {
					break;
				}
			}
		}

		if (array_search(1, $this->pages) === false) {
			array_unshift($this->pages, 1);
		}
		if (array_search($this->totalPages, $this->pages) === false) {
			array_push($this->pages, $this->totalPages);
		}
	}

}
