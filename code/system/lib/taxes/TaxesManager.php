<?php

class SJB_TaxesManager extends SJB_ObjectManager
{
	public static function getTaxAmount($invoice_sum, $tax_rate)
	{
		$i18n = SJB_I18N::getInstance();
		$lang_data = $i18n->getLanguageData($i18n->getCurrentLanguage());
		return round($invoice_sum * $tax_rate / 100, $lang_data['decimals']);
	}

	public static function getTaxInfoByPrice($price)
	{
		$tax = SJB_Settings::getValue('tax');
		if (empty($tax)) {
			return array();
		}
		$tax = floatval($tax);
		$tax_info = array(
			'tax_rate' => $tax,
		);
		$tax_info['tax_amount'] = SJB_TaxesManager::getTaxAmount($price, $tax_info['tax_rate']);
		return $tax_info;
	}
}
