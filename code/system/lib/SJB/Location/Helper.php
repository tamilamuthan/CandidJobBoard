<?php

namespace SJB\Location;

use SJB_I18N;
use SJB_Session;
use SJB_Settings;

class Helper
{
	public static function getLocationFromGoogle($location)
	{
		$key = 'goole_location_' . md5($location) . SJB_I18N::getInstance()->getCurrentLanguage();
		if (SJB_Session::getValue($key)) {
			return SJB_Session::getValue($key);
		}
		$query = [
			'l' => $location,
			'lang' => SJB_I18N::getInstance()->getCurrentLanguage(),
		];
		$locationLimit = SJB_Settings::getValue('location_limit');
		if ($locationLimit) {
			$query['country'] = $locationLimit;
		}
		$url = 'http://geo.mysmartjobboard.com?' . http_build_query($query);
		$response = file_get_contents($url);
		$result = @json_decode($response, true);
		if ($result) {
			SJB_Session::setValue($key, $result);
			return $result;
		}
		return false;
	}

	public static function fixLocation(\SJB_Object $object)
	{
		$l = $object->getProperty('Location');
		if (empty($l)) {
			return;
		}
		$val = $object->getPropertyValue('Location');
		if (is_string($val) || empty($val)) { // can be empty string in import
			$val = [
				'Country' => '',
				'State' => '',
				'City' => '',
				'ZipCode' => '',
				'Latitude' => '',
				'Longitude' => '',
			];
		}
		if (trim(join('', $val), " \t\n\r\0\x0B0") == '' && $object->getPropertyValue('GooglePlace')) {
			$latLong = self::getLocationFromGoogle($object->getPropertyValue('GooglePlace'));
			foreach ($l->type->child->getProperties() as $prop) {
				if (!empty($latLong[$prop->getId()])) {
					$val[$prop->getId()] = $latLong[$prop->getId()];
					$prop->setValue($latLong[$prop->getId()]);
				}
			}
		}
		if (!empty($val['Latitude'])) {
			return;
		}

		if (empty($val['City'])) $val['City'] = '';
		if (empty($val['State'])) $val['State'] = '';
		if (empty($val['Country'])) $val['Country'] = '';
		$locs = [
			join(', ', [$val['City'], $val['State'], $val['Country']]),
			join(', ', [$val['City'], $val['Country']]),
		];
		foreach ($locs as $key => $loc) {
			$locs[$key] = str_replace(', , ', ', ', trim($loc, ', '));
		}

		foreach (array_unique($locs) as $loc) {
			if (empty($loc)) {
				continue;
			}
			$latLong = self::getLocationFromGoogle($loc);
			if (empty($latLong)) {
				continue;
			}

			foreach ($l->type->child->getProperties() as $prop) {
				if (!empty($latLong[$prop->getId()])) {
					$val[$prop->getId()] = $latLong[$prop->getId()];
					$prop->setValue($latLong[$prop->getId()]);
				}
			}
			$object->setPropertyValue('Location', $val);
			$object->setPropertyValue('GooglePlace', $latLong['Location']);
			return;
		}
	}
}
