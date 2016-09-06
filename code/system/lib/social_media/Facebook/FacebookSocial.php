<?php

class SJB_FacebookSocial extends SJB_SocialMedia
{

	function SJB_FacebookSocial($info = array())
	{
		$this->common_fields = SJB_SocialMediaDetails::getCommonFields();
	}

	public static function getConnectSettings()
	{
		return array(
				array(
					'id'			=> 'fb_appID',
					'caption'		=> 'Facebook App ID',
					'type'			=> 'string',
					'length'		=> '255',
					'is_required'	=> true,
					'is_system'		=> true,
					'order'			=> -1,
					'comment'		=> 'To get these credentials you need to create an application in <a href="https://developers.facebook.com/" target="_blank">Facebook Developers Console</a>.<br /><br />Follow the <a target="_blank" href="http://wiki.smartjobboard.com/display/sjb42/Facebook#Facebook-GettingFacebookCredentials">User Manual instructions</a> on how to do this.'
				),
				array(
					'id'			=> 'fb_appSecret',
					'caption'		=> 'Facebook App Secret',
					'type'			=> 'string',
					'length'		=> '255',
					'is_required'	=> true,
					'is_system'		=> true,
					'order'			=> -0,
				),
			);
	}
}
