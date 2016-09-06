<?php

class SJB_PluginAbstract
{
	function pluginSettings()
	{
		return array();
	}
	
	public static function init()
	{
	}

	/**
	 * @param array  $criteria
	 * @param string $settingName
	 * @return string
	 */
	protected static function getLocation(array $criteria, $settingName = '')
	{
		return !empty($criteria['Location']['location']['value']) ? $criteria['Location']['location']['value'] : SJB_Settings::getValue($settingName);
	}
}