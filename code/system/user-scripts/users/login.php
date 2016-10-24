<?php

class SJB_Users_Login extends SJB_Function
{
	public function execute()
	{
		$logged_in = false;
		$tp = SJB_System::getTemplateProcessor();
		$shoppingCart = SJB_Request::getVar('shopping_cart', false);
		$proceedToPosting = SJB_Request::getVar('proceed_to_posting', false);
		$productSID = SJB_Request::getVar('productSID', false);
		$listingTypeID = SJB_Request::getVar('listing_type_id', false);
		$errors = array();

		if (SJB_Authorization::isUserLoggedIn() && !isset($_REQUEST['as_user'])) {
			SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . '/my-account/');
		} else {
			$template = SJB_Request::getVar('template', 'login.tpl');
			$page_config = SJB_System::getPageConfig(SJB_System::getURI());

			if (SJB_Request::getVar('action', false) == 'login') {
				$username = SJB_Request::getVar('username');
				$password = SJB_Request::getVar('password');
				$keep_signed = SJB_Request::getVar('keep', false);

				$login_as_user = false;
				if (isset($_REQUEST['as_user'])) {
					$login_as_user = true;
					if (SJB_UserManager::getCurrentUserSID()) {
						SJB_Authorization::logout();
					}
				}
				// redirect user to the home page if it's login page or to the same page otherwise

				if (SJB_UserManager::getCurrentUserSID()) {
					$logged_in = true;
				} else {
					SJB_UserManager::login($username, $password, $errors, false, $login_as_user);
					if (empty($errors)) {
						$logged_in = SJB_Authorization::login($username, $password, $keep_signed, $errors, $login_as_user);
					}
				}

				if ($logged_in && !$shoppingCart) {
					if (SJB_Request::getVar('return_url', false) != false) {
						$redirect_url = base64_decode(SJB_Request::getVar('return_url'));
						if (strpos($redirect_url,'/change-password/') !== false) {
							$redirect_url = SJB_System::getSystemSettings("SITE_URL") . "/my-account/";
						}
						if (!empty($proceedToPosting)) {
							$redirect_url .= '&proceed_to_posting=1&productSID=' . $productSID;
						}
					} else {
						if ($page_config->module == 'users' && $page_config->function == 'login') {
                            switch(SJB_UserGroupManager::getUserGroupIDByUserSID(SJB_UserManager::getCurrentUserSID())) {
                                case 'Employer': $redirect_url = SJB_System::getSystemSettings("SITE_URL") . "/my-listings/job/"; break;
                                case 'JobSeeker': $redirect_url = SJB_System::getSystemSettings("SITE_URL") . "/my-listings/resume/"; break;
                                case 'Investor': $redirect_url = SJB_System::getSystemSettings("SITE_URL") . "/my-listings/opportunity/"; break;
                                case 'Entrepreneur': $redirect_url = SJB_System::getSystemSettings("SITE_URL") . "/my-listings/idea/"; break;
                                default: $redirect_url = SJB_System::getSystemSettings("SITE_URL") . "/my-listings/resume/";
                                    
                            }
						} else {
							$redirect_url = SJB_System::getSystemSettings("SITE_URL") . SJB_System::getURI();
						}
					}
					SJB_HelperFunctions::redirect($redirect_url);
				}
				$tp->assign('logged_in', $logged_in);
			}

			$return_url = SJB_Request::getVar('return_url', ($page_config->function != 'login' && $page_config->uri != '/') ? base64_encode(SJB_Navigator::getURIThis()) : false);
			if (!filter_var(SJB_System::getSystemSettings("SITE_URL") . base64_decode($return_url), FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
				$return_url = '';
			}

			$tp->assign('shopping_cart', $shoppingCart);
			$tp->assign('proceedToPosting', $proceedToPosting);
			$tp->assign('productSID', $productSID);
			$tp->assign('listingTypeID', $listingTypeID);
			$tp->assign('return_url', $return_url);
			$tp->assign('ajaxRelocate', SJB_Request::getVar('ajaxRelocate', false));
			$tp->assign('errors', $errors);
			$tp->assign('adminEmail', SJB_System::getSettingByName('system_email'));
			$tp->display($template);
		}
	}
}
