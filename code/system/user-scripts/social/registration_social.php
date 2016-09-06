<?php

class SJB_Social_RegistrationSocial extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$user_group_id = SJB_Request::getVar('user_group_id', null);

		if ($user_group_id) {
			$user_group_sid = SJB_UserGroupManager::getUserGroupSIDByID($user_group_id);

			$user = SJB_ObjectMother::createUser($_REQUEST, $user_group_sid);
			$user->deleteProperty('active');
			$user->deleteProperty('featured');

			$errors = array();

			// social plugin
            SJB_Event::dispatch('SocialPlugin_AddListingFieldsIntoRegistration', $user, true);
            SJB_Event::dispatch('FillRegistrationData_Plugin', $user, true);
            SJB_Event::dispatch('AddReferencePluginDetails', $user, true);

            $user->deleteProperty('active');
            $user->deleteProperty('featured');
            SJB_UserManager::saveUser($user);

			// subscribe user on default product
			$defaultProduct = SJB_UserGroupManager::getDefaultProduct($user_group_sid);
			$availableProductIDs = SJB_ProductsManager::getProductsIDsByUserGroupSID($user_group_sid);

			if ($defaultProduct && in_array($defaultProduct, $availableProductIDs)) {
				$contract = new SJB_Contract(array('product_sid' => $defaultProduct));
				$contract->setUserSID($user->getSID());
				$contract->saveInDB();
			}

			SJB_UserManager::activateUserByUserName($user->getUserName());
			SJB_Authorization::login($user->getUserName(), $user->getPropertyValue('password'), false, $errors, false);
			// save access token, profile info for synchronization
			SJB_SocialPlugin::postRegistration();
			SJB_Notifications::sendUserWelcomeLetter($user->getSID());

			if ($user->getUserGroupSID() == SJB_UserGroup::JOBSEEKER) {
				SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . '/add-listing/?listing_type_id=Resume&autofill=1');
			}
			SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . '/edit-profile/');
		} else {
			$tp->assign('user_groups_info', SJB_UserGroupManager::getAllUserGroupsInfo());
			$tp->display('registration_choose_user_group_social.tpl');
		}
	}
}
