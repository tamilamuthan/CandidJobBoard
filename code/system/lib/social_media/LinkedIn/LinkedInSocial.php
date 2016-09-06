<?php

class SJB_LinkedInSocial extends SJB_SocialMedia
{

	/**
	 * @param array $info
	 * @param bool $isGroupsExist
	 * @param bool $isAuthorized
	 */
	function __construct($info = array(), $isGroupsExist = false, $isAuthorized = false)
	{
	}

	/**
	 * @return array
	 */
	public static function getConnectSettings() {
		return array(
			array(
				'id'            => 'li_signin',
				'caption'       => 'Enable login with Linkedin',
				'type'          => 'boolean',
				'length'        => '255',
				'is_required'   => false,
				'is_system'     => true,
				'order'         => 0,
				'comment'       => '',
			),
			array(
				'id'            => 'li_apiKey',
				'caption'       => 'Client ID',
				'type'          => 'string',
				'length'        => '255',
				'is_required'   => true,
				'is_system'     => true,
				'order'         => 1,
				'comment'       => 'To get these credentials you need to create an application in <a href="https://www.linkedin.com/secure/developer" target="_blank">Linkedin Developer Network</a>.',
			),
			array(
				'id'            => 'li_secKey',
				'caption'       => 'Client Secret',
				'type'          => 'string',
				'length'        => '255',
				'is_required'   => true,
				'is_system'     => true,
				'order'         => 2,
			),
		);
	}

}
