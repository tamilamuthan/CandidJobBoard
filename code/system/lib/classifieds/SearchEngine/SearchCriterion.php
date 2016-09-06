<?php

class SJB_SearchCriterion
{
	var $value			= null;
	var $field_value 	= null;
	var $property_name 	= null;
	var $property 		= null;
	var $type			= null;


	function SJB_SearchCriterion($criterion_type)
	{
		$this->type = $criterion_type;
	}

	function setPropertyName($property_name)
	{
		$this->property_name = $property_name;
	}

	function getPropertyName()
	{
		return $this->property_name;
	}

	function setProperty($property)
	{
		$this->property = $property;
	}

	function getProperty()
	{
		return $this->property;
	}

	function setValue($value)
	{
		$this->value=$value;
	}

	function getValue()
	{
		return array($this->type => $this->value);
	}

	function getRawValue()
	{
		return $this->value;
	}

	function setFieldValue($value)
	{
		$this->field_value = $value;
	}

	function getFieldValue()
	{
		return $this->field_value;
	}

	function getType()
	{
		return $this->type;
	}

	function getSQL()
	{
		return null;
	}

	function getSystemSQL($table = '')
	{
		return null;
	}

	function setSQLValue()
	{
		if (!empty($this->property)) {
			$this->property->setValue($this->value);
			$this->value = $this->property->getSQLValue($this);
		}
	}

	public static function getCriterionByType($type)
	{
		$types = [
			'equal' => 'SJB_EqualCriterion',
			'not_equal' => 'SJB_NotEqualCriterion',
			'like' => 'SJB_LikeCriterion',
			'not_like' => 'SJB_NotLikeCriterion',
			'multi_like' => 'SJB_MultiLikeCriterion',
			'multi_like_and' => 'SJB_MultiLikeAndCriterion',
			'in' => 'SJB_InCriterion',
			'more' => 'SJB_MoreCriterion',
			'less' => 'SJB_LessCriterion',
			'not_more' => 'SJB_LessEqualCriterion',
			'not_less' => 'SJB_MoreEqualCriterion',
			'not_empty' => 'SJB_NotEmptyCriterion',
			'geo_coord' => 'SJB_GeoCoordCriterion',
			'is_null' => 'SJB_NullCriterion',
			'simple_equal' => 'SJB_SimpleEqual',
			'all_words' => 'SJB_AllWordsCriterion',
			'accessible' => 'SJB_AccessibleCriterion',
			'company_like' => 'SJB_CompanyLikeCriterion',
			'relevance' => 'SJB_RelevanceCriterion',
			'location' => 'SJB_LocationCriterion',
		];

		$type = strtolower($type);
		if (!isset($types[$type]))
			return null;
		return new $types[$type]($type);
	}
}

class SJB_NullCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		return "(`id` = '{$this->property_name}' AND isnull(`value`))";
	}

	function getSystemSQL($table = '')
	{
		return "isnull(`{$this->property_name}`)";
	}

	function isValid()
	{
		return true;
	}
}

class SJB_NotEqualCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` != '{$value}')";
	}

	function getSystemSQL($table = '')
	{
		$value = SJB_DB::quote($this->value);
		return "`{$this->property_name}` != '{$value}'";
	}

	function isValid()
	{
		return true;
	}
}

class SJB_EqualCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` = '{$value}')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		return "`{$this->property_name}` = '{$value}'";
	}

	function isValid()
	{
		return $this->value !== '';
	}
}

class SJB_MultiLikeCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$res = '';
		$id = SJB_DB::quote($this->property_name);
		if (is_array($this->value)) {
			foreach ($this->value as $value) {
				if ($value === "0" || $value === "")
					continue;
				$val = SJB_DB::quote($value);
				if ($res == "") {
					$res .= " FIND_IN_SET('{$val}', `value`) ";
				} else {
					$res .= " OR FIND_IN_SET('{$val}', `value`) ";
				}
			}
		}
		else {
			$value = SJB_DB::quote($this->value);
			if ($value !== "0") {
				$res = " FIND_IN_SET('{$value}', `value`) ";
			}
		}
		if ($res === '')
			$res = 'true';
		return "(`id` = '{$id}' AND ({$res}))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		
		$tablePrefix = '';
		if ($table != '') {
			$tablePrefix = "`{$table}`.";
		}
		$value = $this->value;
		if (is_array($value))
			$value = implode(',', $value);
		$vals = explode(',', SJB_DB::quote($value));
		$res = '';
		foreach ($vals as $val) {
			if ($res == '') {
				$res .= " FIND_IN_SET('{$val}', {$tablePrefix}`{$this->property_name}`) ";
			} else {
				$res .= " OR FIND_IN_SET('{$val}', {$tablePrefix}`{$this->property_name}`) ";
			}
		}
		return "($res)";
	}

	function isValid()
	{
		$valid = true;
		if (is_array($this->value)) {
			$valid = false;
			foreach ($this->value as $val) {
				if (!empty($val)) {
					$valid = true;
					break;
				}
			}
		}
		return !empty($this->value) && $valid;
	}
}

class SJB_MultiLikeAndCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$res = "";
		$id = SJB_DB::quote($this->property_name);
		$search = array('%', '_');
		$replace = array('\%', '\_');
		if (is_array($this->value)) {
			foreach ($this->value as $value) {
				if ($value === '0' || $value === '')
					continue;
				$val = SJB_DB::quote($value);
				$val = str_replace($search, $replace, $val);
				if ($res == '')
					$res .= " `value` LIKE '{$val}'";
				else
					$res .= " AND `value` LIKE '{$val}'";
			}
		} else {
			$value = SJB_DB::quote($this->value);
			$value = str_replace($search, $replace, $value);
			if ($value !== '0')
				$res = "`value` LIKE '{$value}'";
		}
		if ($res === '')
			$res = 'true';

		return "(`id` = '{$id}' AND ($res))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$tablePrefix = '';
		if ($table != '') {
			$tablePrefix = "`{$table}`.";
		}
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = $this->value;
		if (is_array($value))
			$value = implode(',', $value);
		$vals = explode(',', SJB_DB::quote($value));
		$res = '';
		foreach ($vals as $val) {
			$val = str_replace($search, $replace, $val);
			if ($res == '')
				$res .= "{$tablePrefix}`{$this->property_name}` LIKE '{$val}'";
			else 
				$res .= " OR {$tablePrefix}`{$this->property_name}` LIKE '{$val}'";
		}
		return "($res)";
	}

	function isValid()
	{
		$valid = true;
		if (is_array($this->value)) {
			$valid = false;
			foreach ($this->value as $val)
				if (!empty($val)) {
					$valid = true;
					break;
				}
		}
		return !empty($this->value) && $valid;
	}
}

class SJB_LikeCriterion extends SJB_SearchCriterion
{
	function getSQL( $table_name = '' )
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		$id = SJB_DB::quote($this->property_name);
		if($table_name)
			return "(`{$table_name}`.`id` = '".$id . "' AND `{$table_name}`.`value` LIKE '%{$value}%')";
		else
			return "(`id` = '".$id . "' AND `value` LIKE '%{$value}%')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');

		$tablePrefix = '';
		if ($table != '') {
			$tablePrefix = "`{$table}`.";
		}
		if (is_array($this->value)) {
			$sql = '';
			foreach ($this->value as $value) {
				$value = SJB_DB::quote($value);
				$value = str_replace($search, $replace, $value);
				if (!empty($sql))
					$sql .= ' OR ';
				$sql .= "{$tablePrefix}`{$this->property_name}` LIKE '%{$value}%'";
			}
			return "({$sql})";
		}

		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		return "{$tablePrefix}`{$this->property_name}` LIKE '%{$value}%'";
	}

	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_NotLikeCriterion extends SJB_SearchCriterion
{
	function getSQL( $table_name = '' )
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		$id = SJB_DB::quote($this->property_name);
		if($table_name)
			return "(`{$table_name}`.`id` = '".$id . "' AND `{$table_name}`.`value` not LIKE '%{$value}%')";
		else
			return "(`id` = '".$id . "' AND `value` not LIKE '%{$value}%')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');

		$tablePrefix = '';
		if ($table != '') {
			$tablePrefix = "`{$table}`.";
		}
		if (is_array($this->value)) {
			$sql = '';
			foreach ($this->value as $value) {
				$value = SJB_DB::quote($value);
				$value = str_replace($search, $replace, $value);
				if (!empty($sql))
					$sql .= ' OR ';
				$sql .= "{$tablePrefix}`{$this->property_name}` not LIKE '%{$value}%'";
			}
			return $sql;
		}

		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		return "{$tablePrefix}`{$this->property_name}` not LIKE '%{$value}%'";
	}

	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_InCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;

		$value = $this->getSQLValue();
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` IN ({$value}))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;

		$value = $this->getSQLValue();
		return "`{$this->property_name}` IN ({$value})";
	}

	function isValid()
	{
		return !empty($this->value);
	}

	function _wrapValueWithApostrof($value)
	{
		return "'" . SJB_DB::quote($value) . "'";
	}
	
	function _wrapArrayWithApostrof($array)
	{
		return array_map(array($this,"_wrapValueWithApostrof"), $array);
	}
	
	function getSQLValue()
	{
		$value 		= '';
		if (is_array($this->value))
			$value = join($this->_wrapArrayWithApostrof($this->value), ', ');
		if (empty($value))
			$value = 'NULL';
		return $value;
	}
}

class SJB_MoreCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
	 	if (!$this->isValid())
	 		return null;

		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` > {$this->value})";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		return "`{$this->property_name}` > {$this->value}";
	}

	function isValid()
	{
		return is_numeric($this->value);
	}
}

class SJB_LessCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;

		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` < {$this->value})";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		return "`{$this->property_name}` < {$this->value}";
	}

	function isValid()
	{
		return is_numeric($this->value);
	}
}

class SJB_MoreEqualCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		
		$this->setSQLValue();
		$value = preg_replace("/^'+([^'\"]+)'+$/u", '$1', $this->value);
		$value = is_numeric($value) ? $value : "'" . SJB_DB::quote($value) . "'";
		
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` >= {$value})";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		
		$this->setSQLValue();
		
		$value = preg_replace("/^'+([^'\"]+)'+$/u", '$1', $this->value);
		$value = is_numeric($value) ? $value : "'" . SJB_DB::quote($value) . "'";
		
		return "`{$this->property_name}` >= {$value}";
	}

	function isValid()
	{
		if (!empty($this->property)) {
			$this->property->setValue($this->value);
			$is_valid = $this->property->isSearchValueValid();
			$this->setValue($this->property->getValue());
		}
		else {
			$value = trim($this->value);
			$is_valid = !empty($value);
		}

		return $is_valid;
	}
}

class SJB_LessEqualCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		
		$this->setSQLValue();
		$value = preg_replace("/^'+([^'\"]+)'+$/u", '$1', $this->value);
		$value = is_numeric($value) ? $value : "'" . SJB_DB::quote($value) . "'";
		
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` <= {$value})";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		
		$this->setSQLValue();
		$value = preg_replace("/^'+([^'\"]+)'+$/u", '$1', $this->value);
		$value = is_numeric($value) ? $value : "'" . SJB_DB::quote($value) . "'";
		
		return "`{$this->property_name}` <= {$value}";
	}

	function isValid()
	{
		if (!empty($this->property)) {
			$this->property->setValue($this->value);
			$is_valid = $this->property->isSearchValueValid();
			$this->setValue($this->property->getValue());
		}
		else {
			$value = trim($this->value);
			$is_valid = !empty($value);
		}

		return $is_valid;
	}
}

/**
 * Special GeoCriterion.
 * Used in iPhone API.
 * @author janson
 *
 */
class SJB_GeoCoordCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid()) {
			return null;
		}

		$latitude  = $this->value['latitude'];
		$longitude = $this->value['longitude'];
		$distance  = $this->value['distance'] * 1.60934;

		$geoLocation = new SJB_GeoLocation();
		$myLocation = $geoLocation->fromDegrees($latitude, $longitude);
		$sql = SJB_LocationManager::findPlacesWithinDistance($myLocation, $distance);

		return "({$sql})";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}

		$latitude  = SJB_DB::quote($this->value['latitude']);
		$longitude = SJB_DB::quote($this->value['longitude']);
		$distance  = SJB_DB::quote($this->value['distance'] * 1.60934);

		$geoLocation = new SJB_GeoLocation();
		$myLocation = $geoLocation->fromDegrees($latitude, $longitude);
		$sql = SJB_LocationManager::findPlacesWithinDistance($myLocation, $distance);

		return "{$sql}) ";
	}

	function isValid()
	{
		return (!empty($this->value['distance']) && is_numeric($this->value['distance']) && !empty($this->value['latitude']) && is_numeric($this->value['latitude']) && !empty($this->value['longitude']) && is_numeric($this->value['longitude']));
	}

	function getValue()
	{
		return $this->value;
	}
}


class SJB_NotEmptyCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;

		if (empty($this->value))
			return null;

		return "(`id` = '{$this->property_name}' AND `value` != '')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;

		if (empty($this->value))
			return null;

		return "`{$this->property_name}` != ''";
	}

	function isValid()
	{
		return true;
	}
}

class SJB_SimpleEqual extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		return "(`{$id}` = '{$value}')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		return "`{$this->property_name}` = '{$value}'";
	}

	function isValid()
	{
		return $this->value !== '';
	}
}

class SJB_AllWordsCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid()) {
			return null;
		}
		$res = '';
		$id = SJB_DB::quote($this->property_name);
		$this->value = trim($this->value);
		$values = explode(' ', SJB_DB::quote($this->value));
		$values = array_map(array('SJB_HelperFunctions','trimValue'), $values);
		$search = array('%', '_');
		$replace = array('\%', '\_');

		if (is_array($values)) {
			foreach ($values as $value) {
				$val = SJB_DB::quote($value);
				$val = str_replace($search, $replace, $val);
				if ($res == '') {
					$res .= "`value` like '%{$val}%'";
				} else {
					$res .= " AND `value` like '%{$val}%'";
				}
			}
		}
		else {
			$value = SJB_DB::quote($this->value);
			$value = str_replace($search, $replace, $value);
			if ($value != '0') {
				$res = "`value` like '%{$value}%'";
			}
		}
		if ($res == '') {
			$res = 'true';
		}
		return "(`id` = '{$id}' AND ({$res}))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}
		$this->value = trim($this->value);
		$values = explode(' ', SJB_DB::quote($this->value));
		$values = array_map(array('SJB_HelperFunctions','trimValue'), $values);
		$id = SJB_DB::quote($this->property_name);
		$res = '';
		$search = array('%', '_');
		$replace = array('\%', '\_');

		$userId = SJB_UserManager::getUserIdByKeywords($this->value);
		$userCriteria = '';
		if ($userId && $table == 'listings') {
			$userCriteria = " OR `listings`.`user_sid` = " . $userId;
		}
		if ($table) {
			$table = "`{$table}`.";
		}
		foreach ($values as $val) {
			$val = str_replace($search, $replace, $val);
			if ($res == '') {
				$res .= "{$table}`{$id}` like '%{$val}%'";
			}
			else {
				$res .= " AND {$table}`{$id}` like '%{$val}%'";
			}
		}
		if ($userCriteria) {
			$res .= $userCriteria;
		}
		return "({$res})";
	}

	function isValid()
	{
		$values = explode(' ', $this->value);
		$valid = true;

		if (is_array($values)) {
			$valid = false;
			foreach ($values as $val) {
				if (!empty($val)) {
					$valid = true;
					break;
				}
            }
		}
		return !empty($values) && $valid;
	}
}

class SJB_AccessibleCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` = '{$value}')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}
		
		return "(`{$table}`.`{$this->property_name}` = 'everyone')";
	}

	function isValid()
	{
		return $this->value !== '';
	}
}

class SJB_CompanyLikeCriterion extends SJB_SearchCriterion
{
	function getSQL( $table_name = 'users_properties' )
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		$id = SJB_DB::quote($this->property_name);

		return "(`{$table_name}`.`id` = '".$id . "' AND `{$table_name}`.`value` LIKE '%{$value}%')";
	}

	function getSystemSQL($table = 'users')
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		$id = SJB_DB::quote($this->property_name);

		return "(`{$table}`.`{$id}` LIKE '%{$value}%')";
	}

	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_RelevanceCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		return " MATCH(`{$id}`) AGAINST ('{$value}')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		return " MATCH(`{$this->property_name}`) AGAINST ('{$value}') ";
	}

	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_LocationCriterion extends SJB_SearchCriterion
{
	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}

		$latlong = \SJB\Location\Helper::getLocationFromGoogle($this->value['value']);

		$id = SJB_DB::quote($this->property_name);

		if ($latlong) {
			$geoLocation = new SJB_GeoLocation();
			$radiusSearchUnit = SJB_System::getSettingByName('radius_search_unit');
			$query = array();
			if (empty($this->value['radius']) || !is_numeric($this->value['radius'])) {
				$this->value['radius'] = 50;
			}
			$distance = $radiusSearchUnit == 'kilometers' ? $this->value['radius'] : $this->value['radius'] * 1.60934;
			$query[] = SJB_LocationManager::findPlacesWithinDistance($geoLocation->fromDegrees($latlong['Latitude'], $latlong['Longitude']), $distance, $table);
			$query = implode(' OR ', $query);
			return $query;
		}
		$listValues = $this->getListValues();
		$value = SJB_DB::quote(implode(' ', $listValues));
		return " (MATCH(`{$table}`.`{$id}`) AGAINST ('{$value}' IN BOOLEAN MODE))";
	}

	function isValid()
	{
		return !empty($this->value['value']);
	}

	public function getListValues()
	{
		$listValues = str_replace(',', ' ', $this->value['value']);
		$listValues = explode(' ', $listValues);
		$listValues = array_diff($listValues, array(''));
		$correctedValues = array();
		foreach ($listValues as $key => $value) {
			$value = trim($value);
			while(preg_match('/^[+\-><()~*"]/u', $value)) {
				$value = preg_replace('/^[+\-><()~*"]/u', '', $value);
			}
			$listValues[$key] = $value;
			$len = strlen($value);
			if ($len < 4) {
				for ($i = $len; $i < 4; $i++) {
					$value .= '_';
				}
				$correctedValues[] = $value;
			}
		}
		$listValues = array_merge($listValues, $correctedValues);
		$listValues = array_diff($listValues, array(''));
		return $listValues;
	}
}
