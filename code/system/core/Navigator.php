<?php

class SJB_Navigator
{
	public static function getURI()
	{
		$url = parse_url(SJB_System::getSystemSettings('SITE_URL'));
		if (isset($url['path'])) {
			$requestUri = parse_url($_SERVER['REQUEST_URI']);
			$return = substr($requestUri['path'], strlen($url['path']));
			return $return ? $return : '/';
		}

		$url = parse_url(SJB_System::getSystemSettings('SITE_URL') . $_SERVER['REQUEST_URI']);
		return $url['path'];
	}
	
	public static function getURIThis()
	{
		return $_SERVER['REQUEST_URI'];
	}

	public static function isRequestedUnderLegalURI()
	{
		$siteUrl = parse_url(SJB_System::getSystemSettings('SITE_URL'));
		$requestUri = parse_url($_SERVER['REQUEST_URI']);
		$isUnderOurHost = $siteUrl['host'] === $_SERVER['HTTP_HOST'];
		$isInOurPath = isset($siteUrl['path']) ? strpos($requestUri['path'], $siteUrl['path']) === 0 : true;
		return $isUnderOurHost && $isInOurPath;
	}
}
