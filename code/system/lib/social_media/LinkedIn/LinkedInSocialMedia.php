<?php
class SJB_LinkedInSocialMedia
{
	const NETWORK_ID = 'linkedin';

	/**
	 * @var SJB_LinkedIn
	 */
	private static $object;


	/**
	 * @param $callbackUrl
	 * @param null $accessToken
	 * @return SimpleXMLElement
	 * @throws Exception
	 */
	public function authorize($callbackUrl, $accessToken = null) {
		self::$object = new SJB_LinkedIn($callbackUrl);

		if (empty($accessToken)) {
			// check for response from LinkedIn
			if (!$this->isTokenRequested()) {
				self::$object->_getRequestToken();
			} else {
				if (SJB_Request::getVar(SJB_LinkedIn::OAUTH_PROBLEM)) {
					throw new Exception('oAuth Problem: ' . SJB_Request::getVar(SJB_LinkedIn::OAUTH_PROBLEM));
				}
			}
		}
		self::$object->_getAccessToken($accessToken);
		$response = self::$object->getProfileInfo(array('id', 'email-address'));
		return new SimpleXMLElement($response);
	}

	/**
	 * @return bool
	 */
	private function isTokenRequested()
	{
		return isset($_GET[SJB_LinkedIn::_GET_RESPONSE]);
	}

}
