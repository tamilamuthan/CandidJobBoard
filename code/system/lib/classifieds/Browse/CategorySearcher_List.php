<?php

class SJB_CategorySearcher_List extends SJB_AbstractCategorySearcher
{
	public function SJB_CategorySearcher_List($field)
	{
		$this->field = $field;
		parent::SJB_AbstractCategorySearcher($field);
	}

	protected function _decorateItems($items)
	{
		$listingFieldListItemManager = new SJB_ListingFieldListItemManager();
		$values = $listingFieldListItemManager->getHashedListItemsByFieldSID($this->field['sid']);
		$values = $this->getSortedValues($values);
		$listData = array();
		foreach ($values as $id => $value) {
			$listData[$id] = array(
				'caption' => $value,
				'count' => isset($items[$id]) ? $items[$id] : 0
			);
		}
		return $listData;
	}

	protected function _get_Captions_with_Counts_Grouped_by_Captions($request_data, array $listingSids = array())
	{
		return parent::_get_Captions_with_Counts_Grouped_by_Captions($request_data, $listingSids);
	}

	/**
	 * Check 'sort_by_alphabet' flag for field, and sort values if needed
	 * 
	 * @param $values
	 */
	private function getSortedValues($values)
	{
//		$fieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($this->field['sid']);
//		if (SJB_Array::get($fieldInfo, 'sort_by_alphabet') > 0) {
//			$i18n = SJB_I18N::getInstance();
//
//			// translate captions to current language
//			$translates = array();
//			foreach ($values as $value) {
//				$translates[] = $i18n->gettext('', $value);
//			}
//
//			// we need to recover keys for $values after array_multisort
//			$keys = array_keys($values);
//
//			// sort $keys and $values order by $translates sort
//			array_multisort($translates, SORT_STRING, $keys, $values);
//			// restore keys for $values
//			$values = array_combine($keys, $values);
//		}
		return $values;
	}
}
