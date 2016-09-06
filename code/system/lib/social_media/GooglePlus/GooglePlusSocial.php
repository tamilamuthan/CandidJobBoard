<?php

class SJB_GooglePlusSocial extends SJB_SocialMedia
{

	public static function getConnectSettings()
	{
		return array(
			array(
				'id'			=> 'oauth2_client_id',
				'caption'		=> 'Client ID for Web Application',
				'type'			=> 'string',
				'length'		=> '25',
				'is_required'	=> true,
				'order'			=> null,
				'comment'		=> 'To get these credentials you need to register an application in <a href="https://cloud.google.com/console" target="_blank">Google API Console</a>.<br/><br/>Follow the <a href="http://wiki.smartjobboard.com/display/sjb42/Google+Plus#GooglePlus-GettingGoogle+Credentials" target="_blank">User Manual instructions</a> on how to do this.'
			),
			array(
				'id' 			=> 'client_secret',
				'caption' 		=> 'Client Secret for Web Application',
				'type'			=> 'string',
				'length'		=> '25',
				'is_required'	=> true,
				'order'			=> null,
				'comment'		=> ''
			),
			array(
				'id' 			=> 'developer_key',
				'caption' 		=> 'API Key for Browser Applications',
				'type'			=> 'string',
				'length'		=> '25',
				'is_required'	=> true,
				'order'			=> null,
				'comment'		=> ''
			),
		);
	}
}
