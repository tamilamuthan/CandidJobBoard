<?php

class SJB_FontsManager
{
    private static $fonts = [
        '"Arvo", sans-serif' => [
            'caption' => 'Arvo',
            'link' => 'https://fonts.googleapis.com/css?family=Arvo:400,700'
        ],
        'Arial, Helvetica, sans-serif' => [
            'caption' => 'Arial',
            'link' => ''
        ],
        'ChaparralPro-LightIt' => [
            'caption' => 'Chaparral Pro Light It',
            'link' => ''
        ],
        'Comic Sans MS, cursive' => [
            'caption' => 'Comic Sans MS',
            'link' => ''
        ],
        'Courier New, Courier, monospace' => [
            'caption' => 'Courier New',
            'link' => ''
        ],
        '"Fira Sans", sans-serif' => [
            'caption' => 'Fira Sans',
            'link' => 'https://code.cdn.mozilla.net/fonts/fira.css'
        ],
        '"Forum"' => [
            'caption' => 'Forum',
            'link' => 'https://fonts.googleapis.com/css?family=Forum'
        ],
        'Georgia, serif' => [
            'caption' => 'Georgia',
            'link' => ''
        ],
        '"Helvetica Neue", Helvetica, Arial, sans-serif' => [
            'caption' => 'Helvetica Neue',
            'link' => ''
        ],
        'Impact,Charcoal, sans-serif' => [
            'caption' => 'Impact',
            'link' => ''
        ],
        'Laila, sans-serif' => [
            'caption' => 'Laila', 'link' => 'https://fonts.googleapis.com/css?family=Laila:400,300,500,700'
        ],
        '"Ledger"' => [
            'caption' => 'Ledger',
            'link' => 'https://fonts.googleapis.com/css?family=Ledger'
        ],
        'Lucida Console, Monaco, monospace' => [
            'caption' => 'Lucida Console',
            'link' => ''
        ],
        '"Neuton", sans-serif' => [
            'caption' => 'Neuton', 'link' => 'https://fonts.googleapis.com/css?family=Neuton:400,300,700'
        ],
        '"Noto Sans", sans-serif' => [
            'caption' => 'Noto Sans', 'link' => 'https://fonts.googleapis.com/css?family=Noto+Sans:400,700'
        ],
        '"Noto Serif", sans-serif' => [
            'caption' => 'Noto Serif', 'link' => 'https://fonts.googleapis.com/css?family=Noto+Serif:400,700'
        ],
        '"Open Sans", sans-serif' => [
            'caption' => 'Open Sans', 'link' => 'https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700'
        ],
        '"PT Sans", sans-serif' => [
            'caption' => 'PT Sans', 'link' => 'https://fonts.googleapis.com/css?family=PT+Sans:400,700'
        ],
        '"PT Serif", sans-serif' => [
            'caption' => 'PT Serif', 'link' => 'https://fonts.googleapis.com/css?family=PT+Serif:400,700'
        ],
        '"Quicksand", sans-serif' => [
            'caption' => 'Quicksand', 'link' => 'https://fonts.googleapis.com/css?family=Quicksand:400,700,300'
        ],
        '"Roboto", sans-serif' => [
            'caption' => 'Roboto', 'link' => 'https://fonts.googleapis.com/css?family=Roboto:400,300,500,700'
        ],
        'Tahoma, Geneva, sans-serif' => [
            'caption' => 'Tahoma',
            'link' => ''
        ],
        'Times New Roman, Times, serif' => [
            'caption' => 'Times New Roman',
            'link' => ''
        ],
        'Trebuchet MS, Helvetica, sans-serif' => [
            'caption' => 'Trebuchet MS',
            'link' => ''
        ],
        '"Ubuntu", sans-serif' => [
            'caption' => 'Ubuntu', 'link' => 'https://fonts.googleapis.com/css?family=Ubuntu:400,300,500,700'
        ],
        'Verdana, Geneva, sans-serif' => [
            'caption' => 'Verdana',
            'link' => ''
        ],
        'Work Sans, sans-serif' => [
            'caption' => 'Work Sans', 'link' => 'https://fonts.googleapis.com/css?family=Work+Sans:400,300,500,700'
        ],
    ];

    public static function getFonts()
    {
        return self::$fonts;
    }

    public static function getFontLink()
    {
        $settings = ThemeManager::getThemeSettings();
        $font = $settings['font'];
        if (!empty(self::$fonts[$font]['link'])) {
            return '<link href="' . self::$fonts[$font]['link'] . '" rel="stylesheet" type="text/css">';
        }
        return '';
    }
}
