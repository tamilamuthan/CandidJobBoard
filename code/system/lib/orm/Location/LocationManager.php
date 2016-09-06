<?php

class SJB_LocationManager
{
	public static function getRadiuses()
	{
		return [
			'values' => [10, 20, 50, 100, 200],
			'default' => 50
		];
	}

	/**
	 * @param SJB_GeoLocation $location
	 * @param $distance
	 * @return string
	 */
	public static function findPlacesWithinDistance($location, $distance, $table = '')
	{
		$radius = 6371.01;
		$boundingCoordinates = $location->boundingCoordinates($distance, $radius);
		$meridian180WithinDistance = $boundingCoordinates[0]->getLongitudeInRadians() > $boundingCoordinates[1]->getLongitudeInRadians();
		$angularRadius = $distance / $radius;
		if ($table) {
			$table .= '.';
		}

		// Distance calculation using Nautical Miles :)
		$sql = " (({$table}`Location_Latitude` >= {$boundingCoordinates[0]->getLatitudeInDegrees()} AND {$table}`Location_Latitude` <= {$boundingCoordinates[1]->getLatitudeInDegrees()}) AND ({$table}`Location_Longitude` >= {$boundingCoordinates[0]->getLongitudeInDegrees()}".
			($meridian180WithinDistance ? " OR" : " AND") . " {$table}`Location_Longitude` <= {$boundingCoordinates[1]->getLongitudeInDegrees()}) AND
			acos(sin({$location->getLatitudeInRadians()}) * sin(RADIANS({$table}`Location_Latitude`)) + cos({$location->getLatitudeInRadians()}) * cos(RADIANS({$table}`Location_Latitude`)) * cos(RADIANS({$table}`Location_Longitude`) - {$location->getLongitudeInRadians()})) <= {$angularRadius}) ";
		return $sql;
	}
}

