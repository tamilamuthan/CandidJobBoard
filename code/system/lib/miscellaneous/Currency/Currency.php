<?php

class SJB_CurrencyManager
{
    private static $currencies = [
        'AUD' => ['caption' => 'Australian Dollar', 'sign' => '$'],
        'BRL' => ['caption' => 'Brazilian Real', 'sign' => 'R$'],
        'GBP' => ['caption' => 'British Pound', 'sign' => '£'],
        'CAD' => ['caption' => 'Canadian Dollar', 'sign' => '$'],
        'CZK' => ['caption' => 'Czech Koruna', 'sign' => 'Kč'],
        'DKK' => ['caption' => 'Danish Krone', 'sign' => 'kr'],
        'EUR' => ['caption' => 'Euro', 'sign' => '€'],
        'HKD' => ['caption' => 'Hong Kong Dollar', 'sign' => '$'],
        'HUF' => ['caption' => 'Hungarian Forint', 'sign' => 'Ft'],
        'ILS' => ['caption' => 'Israeli New Shekel', 'sign' => '₪'],
        'INR' => ['caption' => 'Indian Rupee', 'sign' => '₹'],
        'JPY' => ['caption' => 'Japanese Yen', 'sign' => '¥'],
        'MYR' => ['caption' => 'Malaysian Ringgit', 'sign' => 'RM'],
        'MVR' => ['caption' => 'Maldivian rufiyaa', 'sign' => 'Rf'],
        'MXN' => ['caption' => 'Mexican Peso', 'sign' => '$'],
        'TWD' => ['caption' => 'New Taiwan Dollar', 'sign' => '$'],
        'NZD' => ['caption' => 'New Zealand Dollar', 'sign' => '$'],
        'NOK' => ['caption' => 'Norwegian Krone', 'sign' => 'kr'],
        'PHP' => ['caption' => 'Philippine Peso', 'sign' => '₱'],
        'PLN' => ['caption' => 'Polish Złoty', 'sign' => 'zł'],
        'RUB' => ['caption' => 'Russian Ruble', 'sign' => 'р.'],
        'SGD' => ['caption' => 'Singapore Dollar', 'sign' => '$'],
        'ZAR' => ['caption' => 'South African rand', 'sign' => 'R'],
        'SEK' => ['caption' => 'Swedish Krona', 'sign' => 'kr'],
        'CHF' => ['caption' => 'Swiss Franc', 'sign' => 'Fr'],
        'THB' => ['caption' => 'Thai Baht', 'sign' => '฿'],
        'TRY' => ['caption' => 'Turkish Lira', 'sign' => 'TRY'],
        'USD' => ['caption' => 'United States Dollar', 'sign' => '$'],
    ];

    public static function getCurrencySign()
    {
        return self::$currencies[SJB_Settings::getSettingByName('transaction_currency')]['sign'];
    }

    public static function getCurrencyCode()
    {
        return SJB_Settings::getSettingByName('transaction_currency');
    }

    public static function getCurrencies()
    {
        return self::$currencies;
    }
}
