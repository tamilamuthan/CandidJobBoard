<?php

class SJB_Notifications
{
	const SEND_USER_PASS_CHANGE_LTR_SID 			= 36;
	const SEND_APPLY_NOW_SID 						= 33;
//	const SEND_USER_SOCIAL_REG_LETTER_SID 			= 6;
	const SEND_SUBSCRIPTION_ACTIVATION_LTR_SID 		= 24;

	public static function sendUserPasswordChangeLetter($user_sid)
	{
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$user_info = SJB_UserManager::createTemplateStructureForUser($user);
		$data = array('user' => $user_info);
		$email = SJB_EmailTemplateEditor::getEmail($user_info['username'], self::SEND_USER_PASS_CHANGE_LTR_SID, $data);
		return $email->send('User Password Change');
	}

	/**
	 * @static
	 * @param array $listing_info
	 * @return null
	 */
	public static function sendUserListingExpiredLetter($listing_info)
	{
		$userGroupSID 	= SJB_Array::getPath($listing_info, 'user/user_group_sid');
		$emailTplSID 	=  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID,'notify_on_listing_expiration');
		$user_info 		= SJB_Array::get($listing_info, 'user');
		$data 			= array('user' => $user_info, 'listing' => $listing_info);
		$email 			= SJB_EmailTemplateEditor::getEmail($user_info['username'], $emailTplSID, $data);

		return $email->send('User Listing Expired');
	}

	public static function sendUserContractExpiredLetter($userInfo, $contractInfo, $productInfo)
	{
		$user 			= SJB_UserManager::getObjectBySID($userInfo['sid']);
		if (!$user)
			return false;

		$userGroupSID 	= $user->getUserGroupSID();
		$emailTplSID 	=  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID,'notify_on_contract_expiration');

		$user_info = SJB_UserManager::createTemplateStructureForUser($user);
		$productInfo = array_merge($productInfo, SJB_ProductsManager::createTemplateStructureForProductForEmailTpl($productInfo));
		$data = array(
			'user' => $user_info,
			'product' => $productInfo,
			'contract' => $contractInfo
		);
		$email = SJB_EmailTemplateEditor::getEmail($userInfo['username'], $emailTplSID, $data);
		return $email->send('User Contract Expired');
	}

	/**
	 * @param SJB_Listing $listing
	 * @param $user_sid
	 * @return mixed
	 */
	public static function sendUserListingActivatedLetter(SJB_Listing $listing, $user_sid)
	{
		$user 			= SJB_UserManager::getObjectBySID($user_sid);
		$userGroupSID 	= $user->getUserGroupSID();
		$emailTplSID 	= SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID,'notify_on_listing_activation');

		$user_info = SJB_UserManager::createTemplateStructureForUser($user);
		$listing_info = SJB_ListingManager::createTemplateStructureForListing($listing);
		$data = array(
			'listing' => $listing_info,
			'user' => $user_info
		);
		$email = SJB_EmailTemplateEditor::getEmail($user_info['username'], $emailTplSID, $data);
		return $email->send('User Listing Activated');
	}

	public static function sendApplyNow($info, $file = '', $data_resume = array(), $userData = false)
	{
		$application_email = SJB_Applications::getApplicationEmailbyListingId($info['listing']['id']);
		$email_address = !empty($application_email) ? $application_email : $info['listing']['user']['username'];

		$data = array(
			'user'					=> SJB_Array::getPath($info, 'listing/user'),
			'listing' 				=> $info['listing'],
			'applicant_request' 	=> $info['submitted_data'],
			'data_resume' 			=> $data_resume,
		);

		$email = SJB_EmailTemplateEditor::getEmail($email_address, self::SEND_APPLY_NOW_SID, $data);
		$email->setFromName($info['submitted_data']['name'] . ' via ' . SJB_Settings::getValue('site_title'));
		$email->setReplyTo($userData['email']);
		$email->setFromEmail($userData['email']);
		if ($file != '') {
			$email->setFile($file);
		}
		return $email->send('Apply Now');
	}

	/**
	 * @param $userSID
	 * @param $productInfo
	 * @param SJB_Invoice $invoice
	 * @return mixed
	 */
	public static function sendSubscriptionActivationLetter($userSID, $productInfo, $invoice)
	{
		$emailTplSID = self::SEND_SUBSCRIPTION_ACTIVATION_LTR_SID;
		$user = SJB_UserManager::getObjectBySID($userSID);
		$user = SJB_UserManager::createTemplateStructureForUser($user);
		$productExtraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productInfo['sid']);
		$productInfo = array_merge($productInfo, $productExtraInfo);
		$fields = SJB_ProductsManager::createTemplateStructureForProductForEmailTpl($productInfo);
		$product = array_merge($fields, $productExtraInfo);
		$tax = 0;
		if ($invoice->getPropertyValue('tax_info')) {
			$taxInfo = $invoice->getPropertyValue('tax_info');
			$tax = $taxInfo['tax_amount'];
		}
		$data = array(
			'user' => $user,
			'product' => $product,
			'invoice' => array(
				'id' => $invoice->getSID(),
				'sub_total' => $invoice->getPropertyValue('sub_total'),
				'total' => $invoice->getPropertyValue('total'),
				'tax' => $tax,
				'date' => $invoice->getPropertyValue('date')
			)
		);

		$email = SJB_EmailTemplateEditor::getEmail($user['username'], $emailTplSID, $data);
		$result = $email->send('Subscription Activation');
		SJB_AdminNotifications::sendProductConfirmationLetter($data);
		return $result;
	}

	public static function sendUserWelcomeLetter($user_sid)
	{
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$userGroupSID = $user->getUserGroupSID();
		$emailTplSID 	=  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'welcome_email');

		$user = SJB_UserManager::createTemplateStructureForUser($user);
		$data = array('user' => $user);
		$email = SJB_EmailTemplateEditor::getEmail($user['username'], $emailTplSID, $data);
		return $email->send('Welcome email');
	}

//	public static function sendUserSocialRegistrationLetter(SJB_User $user, $network)
//	{
//		$user = SJB_UserManager::createTemplateStructureForUser($user);
//		$userEmail = SJB_Array::get($user, 'username');
//		if (is_array($userEmail)) {
//			$userEmail = array_pop($userEmail);
//			$user['username'] = $userEmail;
//		}
//		$data = array(
//			'user' => $user,
//			'network' => $network,
//		);
//		$email = SJB_EmailTemplateEditor::getEmail($userEmail, self::SEND_USER_SOCIAL_REG_LETTER_SID, $data);
//		return $email->send('Social Registration');
//	}

	/**
	 * @param array $listingsSIDs
	 * @param array $guestAlertInfo
	 * @param int $listingTypeSID
	 * @return array|bool|null
	 */
	public static function sendGuestAlertNewListingsFoundLetter(array $listingsSIDs, array $guestAlertInfo, $listingTypeSID)
	{
		$emailTplSID = SJB_ListingTypeManager::getListingTypeEmailTemplateForGuestAlert($listingTypeSID);

		$listings = array();
		foreach ($listingsSIDs as $listingSID) {
			$listing = SJB_ListingManager::getObjectBySID($listingSID);
			if ($listing instanceof SJB_Listing) {
				$listing = SJB_ListingManager::createTemplateStructureForListing($listing);
				array_push($listings, $listing);
			}
		}

		$data = array(
			'listings' => $listings,
			'key' => $guestAlertInfo['alert_key']
		);
		$email = SJB_EmailTemplateEditor::getEmail($guestAlertInfo['email'], $emailTplSID, $data);
		return $email->send('Guest Alert New Listings Found');
	}
}
