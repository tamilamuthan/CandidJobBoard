<?php

class SJB_DateFormatter
{
    private $format;

    private static $months = [

        'es' => [
            'ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.',
        ],

        'fr' => [
            'janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.',
        ],

        'en' => [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
        ],

        'de' => [
            'Jan', 'Feb', 'Mrz', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez',
        ],

        'ru' => [
            'янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек',
        ],

    ];

    public static function localizeFormat($date)
    {
        $lang = SJB_I18N::getInstance()->getCurrentLanguage();
        foreach (self::$months['en'] as $id => $month) {
            $date = str_replace($month, self::$months[$lang][$id], $date);
        }
        return $date;
    }

    public static function getFormats()
    {
        return [
            '%b %d, %Y' => '%b %d, %Y',
            '%b %d' => '%b %d',
            '%d %b, %Y' => '%d %b, %Y',
            '%d %b' => '%d %b',
            '%m/%d/%Y (mm/dd/yyyy)' => '%m/%d/%Y',
            '%d/%m/%Y (dd/mm/yyyy)' => '%d/%m/%Y',
        ];
    }

    function getOutput($date)
    {
        return strftime($this->format, strtotime($date));
    }

    function getInput($date)
    {
        $date = trim($date);
        if (preg_match('/\-/', $date) == 1) {
            $format = preg_replace('/[\%]/u', '', $this->format);
            $date = date($format, strtotime($date));
        }
        if (empty($date))
            return '';
        $parsedDate = strptime($date, $this->format);
        return sprintf("%s-%02s-%02s", $parsedDate['tm_year'] + 1900, $parsedDate['tm_mon'] + 1, $parsedDate['tm_mday']);
    }

    function isValid($date)
    {
        $parsedDate = strptime($date, $this->format);
        if ($parsedDate === false)
            return false;
        $parsedDate['tm_year'] += 1900;
        $parsedDate['tm_mon'] += 1;
        $timestamp = mktime(0, 0, 0, $parsedDate['tm_mon'], $parsedDate['tm_mday'], $parsedDate['tm_year']);
        $dateToCompare = strftime($this->format, $timestamp);
        return isset($parsedDate['tm_year'], $parsedDate['tm_mon'], $parsedDate['tm_mday']) && $date == $dateToCompare;
    }

	function setDateFormat($format)
	{
		$this->format = $format;
	}
}
