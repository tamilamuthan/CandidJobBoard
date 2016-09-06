<?php

/**
 * @package SystemClasses
 * @subpackage TemplateSupplier
 */
class SJB_TemplateSupplier
{
	/**
	 * Contents module name
	 *
	 * @var string $module_name
	 */
	var $module_name;

	private static $theme = null;
	private $accessType = false;

	private static $additionalContainerTemplate = null;
	
	public static function addContainerTemplate($name)
	{
		if (is_string($name))
			self::$additionalContainerTemplate = $name;
	}

	/**
	 * Constructor
	 *
	 * @param string $module_name Module name
	 */
	public function __construct($module_name)
	{
		if (null === self::$theme)
			self::$theme = ThemeManager::getCurrentTheme();
		$this->module_name = $module_name;
	}

	public static function getTheme()
	{
		return self::$theme;
	}
	
	public function getSystemAccessType()
	{
		return $this->accessType;
	}
	
	public function setSystemAccessType($at)
	{
		$this->accessType = $at;
		self::$theme = ThemeManager::getCurrentTheme($this->accessType);
	}

	/**
	 * Returns template path
	 *
	 * @param string $template_name Template name
	 * @return string Path to template
	 */
	function getTemplatePath($template_name)
	{
		if (file_exists($template_name) && is_readable($template_name)) {
			return realpath($template_name);
		}

		$theme = self::$theme;
		do {
			$path = SJB_TemplatePathManager::getAbsoluteTemplatePath($theme, $this->module_name, $template_name);

			$dir = dirname($path);
			if (!file_exists($dir)) {
				@mkdir($dir, 0777, true);
			}
			$path = realpath($path);
			if (file_exists($path)) {
				return $path;
			}
			$theme = ThemeManager::getThemeConfig($theme);
			$theme = $theme['template_fallback'];
		} while ($theme !== 'none');
		return $path;
	}

	function getTplName($tpl_name)
	{
		if (file_exists($this->getTemplatePath($tpl_name))) 
			return $this->getTemplatePath($tpl_name);
		
		// FIX FOR ajax_file_upload_handler function called from admin panel
		$httpReferrer = SJB_Request::getVar('HTTP_REFERER', '', 'SERVER');
		$requestUri   = SJB_Request::getVar('REQUEST_URI', '', 'SERVER');
		if (!empty($httpReferrer) && !empty($requestUri) && strpos($httpReferrer, '/admin/') !== false && strpos($requestUri, '/ajax_file_upload_handler/') !== false) {
			$currValue = SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE');
			SJB_System::setSystemSettings('SYSTEM_ACCESS_TYPE', 'admin');
			$systemTpl = SJB_System::getSystemSettings('SYSTEM_TEMPLATE_DIR').'/';
			// ok. Now - set previous value
			SJB_System::setSystemSettings('SYSTEM_ACCESS_TYPE', $currValue);
		} else {
			$systemTpl = SJB_System::getSystemSettings('SYSTEM_TEMPLATE_DIR').'/';
		}
		// END OF FIX
		
		$fileName = $this->normalizePath(SJB_TemplatePathManager::getAbsoluteTemplatesPath() . $systemTpl . $this->module_name . '/' . $tpl_name); // ?

		if (file_exists($fileName)) {
			return $fileName;
		}
		return null;
	}
	
	/**
	 * Return template content
	 *
	 * @param string $tpl_name Template name
	 * @return text Template content
	 */
	function fetchTemplateSource($tpl_name)
	{
		$fileName = $this->getTplName($tpl_name);
		if (empty($fileName)) {
			return '';
		}
		return file_get_contents($fileName);
	}

	/**
	 * Return template timestamp
	 *
	 * @param string $tpl_name Template name
	 * @return int Template timestamp
	 */
	function fetchTemplateTimestamp($tpl_name)
	{
		return filemtime($this->getTplName($tpl_name));
	}

	/**
	 * This is callback function that called by Smarty to complete following
	 * expressions {image src="image_name"}
	 *
	 * @param array $params Array of parameters
	 */
	/**
	 * Return image URL
	 *
	 * @param array $params
	 * @return string Image url
	 */
	function getImageURL($params)
	{
		$image_name = isset($params['src']) ? $params['src'] : '';
		if (empty($image_name)) {
			return $this->getPageImageURI();
		}
		return $this->getModuleImageURI($image_name);
	}

	function getCommonJsURL($params)
	{
		$filename = isset($params['src']) ? $params['src'] : '';
		if (empty($filename))
			return SJB_System::getSystemSettings('USER_SITE_URL') . '/' . 'templates/_system/common_js';
		// remove slashes from filename
		$filename = str_replace(array('/', "\\"), "_", $filename);
		return SJB_System::getSystemSettings('USER_SITE_URL') . '/' . 'templates/_system/common_js/' . $filename;
	}

	/**
	 * Private screwdriwer!
	 */
	function getPageImageURI() //***Screwdriver***
	{
		$module_name = SJB_System::getSystemSettings('PAGE_TEMPLATES_MODULE_NAME');
		return SJB_System::getSystemSettings('SITE_URL') ."/". SJB_TemplatePathManager::getAbsoluteImagePath (self::$theme, $module_name);
	}

	/**
	 * Returns image URI of specified module
	 *
	 * @param string $image_name Image name
	 * @return string Image URI
	 */
	function getModuleImageURI($image_name)
	{
		return SJB_System::getSystemSettings('SITE_URL') ."/". SJB_TemplatePathManager :: getAbsoluteImagePath (self::$theme, $this->module_name, $image_name);
	}

	/**
	 * 
	 * @param Smarty $template_processor
	 */
	function registerResources($template_processor)
	{
		$default_resource_name = strtolower('template_'. self::$theme . '_' . SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE'));
		$template_processor->registerResource($default_resource_name, new SJB\Smarty\Resource($this));
		$template_processor->default_resource_type = $default_resource_name;
		$template_processor->registerPlugin('function', "image", array($this, "getImageURL"));
		$template_processor->registerPlugin('function', "common_js", array($this, "getCommonJsURL"));
		$template_processor->registerPlugin('modifier', 'paymentTranslate', 'SJB\Smarty\Modifier\PaymentTranslator::translate');
		$template_processor->registerPlugin('function', "WYSIWYGEditor", array($this, "WYSIWYGEditor"));
		$template_processor->registerPlugin('block', "php", array($this, "php"));
	}

	function WYSIWYGEditor($params, &$smarty)
	{
		$type = SJB_Array::get($params, 'type', '');
		$currentEditor = new SJB_WYSIWYGEditorProvider($type);
		$width = SJB_Array::get($params, 'width', null);
		$height = SJB_Array::get($params, 'height', null);
		$conf = SJB_Array::get($params, 'conf', null);
		$content = SJB_Array::get($params, 'value', '');
		return $currentEditor->getEditorHTML($params['name'], $content, $height, $width, $conf, $params);
	}

	function php($params, $content, $smarty, &$repeat)
	{
		ob_start();
		eval($content);
		return ob_get_clean();
	}

	/**
	 * Fetching template
	 *
	 * @param $tpl_name
	 * @param $tpl_source
	 * @param $smarty
	 * @return bool true
	 */
	function template_source($tpl_name, &$tpl_source, &$smarty)
	{
		$tpl_source = $this->fetchTemplateSource($tpl_name);
		return true;
	}

	/**
	 * Fetching timestamp
	 * @param $tpl_name
	 * @param $tpl_timestamp
	 * @param $smarty
	 * @return bool true
	 */
	function template_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
	{
		$tpl_timestamp = $this->fetchTemplateTimestamp($tpl_name);
		return true;
	}

	function getModuleName()
	{
		return $this->module_name;
	}

	public static function getUserCurrentTheme()
	{
		return SJB_Settings::getValue('TEMPLATE_USER_THEME', 'default');
	}
	
	public static function normalizePath($path)
	{
		$parts = array();
		$path = str_replace('\\', '/', $path);
		$path = preg_replace('/\/+/u', '/', $path);
		$segments = explode('/', $path);
		foreach ($segments as $segment) {
			if ($segment != '.') {
				$part = array_pop($parts);
				if (empty($part)) {
					$parts[] = $segment;
				} 
				else if ($segment == '..') {
					if ($part == '..') {
						$parts[] = $part;
					}
					if ($part == '..' || $part == '') {
						$parts[] = $segment;
					}
				} else {
					$parts[] = $part;
					$parts[] = $segment;
				}
			}
		}
		return implode('/', $parts);
	}
}

class ThemeManager
{
	private static $configs = [];
	private static $settings = [];

	public static function reset()
	{
		self::$configs = [];
		self::$settings = [];
	}

	public static function getCurrentTheme($accessType = false)
	{
		$i18n = SJB_I18N::getInstance();
		$i18n->switchLang();
		$activeLanguagesData = $i18n->getActiveLanguagesData();
		$currentLanguage = $i18n->getCurrentLanguage();
		$currentLanguageData = null;
		foreach ($activeLanguagesData as $activeLanguageData) {
			if ($activeLanguageData['id'] == $currentLanguage) {
				$currentLanguageData = $activeLanguageData;
			}
		}
		SJB_System::setGlobalTemplateVariable('languages', $activeLanguagesData);
		SJB_System::setGlobalTemplateVariable('current_language', $currentLanguage);
		SJB_System::setGlobalTemplateVariable('current_language_data', $currentLanguageData);
			$arr = glob(SJB_BASE_DIR.'system/ext/jquery/bootstrap-datepicker/i18n/bootstrap-datepicker.*.min.js');

			if (is_array($arr) && !empty($arr)) {
				$available_datepicker_localizations = array();
				foreach ($arr as $item) {
					$matches = array();
					if (!preg_match('/bootstrap-datepicker.([a-zA-Z0-9-]+).min.js$/', $item, $matches) )
						continue;
					$available_datepicker_localizations[$matches[1]] = $matches[1];
				}
				SJB_System::setGlobalTemplateVariable('available_datepicker_localizations', $available_datepicker_localizations);
		}

		$theme = null;

		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == 'admin') { 
			if ($accessType === false || $accessType === 'admin')
				return SJB_System::getSystemSettings('SYSTEM_TEMPLATE_DIR').'/admin';
		}
		else {
			if (isset($_GET['theme'])) {
				$theme = $_GET['theme'];
				if (strtolower($theme) == 'mobile') {
					$theme = 'mobile';
				}
				SJB_Session::setValue('theme', $theme);
			}
		}
		
		if (!ThemeManager::isThemeExists($theme, $accessType)) {
			$theme = SJB_Session::getValue('theme');
			if (empty($theme) || !ThemeManager::isThemeExists($theme, $accessType)) {
				$theme = SJB_Settings::getSettingByName('TEMPLATE_USER_THEME');
				if ( !ThemeManager::isThemeExists($theme, $accessType) )
					$theme = SJB_System::getSystemSettings('DEFAULT_THEME');
			}
		}
		SJB_Event::dispatch('GetCurrentTheme', $theme, true);
		SJB_Session::setValue('TEMPLATE_USER_THEME', $theme);
		return $theme;
	}

	public static function isThemeExists($theme, $accessType = false)
	{
		if (empty($theme)) {
			return false;
		}

		if ($accessType === false) {
			$accessType = SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE');
		}
		$up_path = '';
		if ($accessType === 'admin') {
			$up_path = '../';
		}

		$themeExists = is_dir($up_path . SJB_PathManager::getAbsoluteThemesPath($accessType) . $theme);
		SJB_Event::dispatch('IsThemeExists', $themeExists, true);
		return $themeExists;
	}

	public static function getThemeConfig($theme)
	{
		if (!empty(self::$configs[$theme])) {
			return self::$configs[$theme];
		}
		if (self::isThemeExists($theme, 'user')) {
			$config = json_decode(file_get_contents(SJB_PathManager::getAbsoluteThemesPath('user') . $theme . '/config.json'), true);
			if (empty($config['template_fallback'])) {
				$config['template_fallback'] = '_system';
			}
			self::$configs[$theme] = $config;
			return $config;
		}
		return [];
	}

	public static function getThemeSettings($theme = false)
	{
		if (!$theme) {
			$theme = self::getCurrentTheme();
		}
		if (!empty(self::$settings[$theme])) {
			return self::$settings[$theme];
		}

		$config = self::getThemeConfig($theme);
		$settings = [];
		foreach ($config['config'] as $name => $value) {
			$settings[str_replace('theme_', '', $name)] = SJB_Settings::getValue($name . '_' . $theme, $value);
		}

		$banners = [
			'banner_top',
			'banner_bottom',
			'banner_right_side',
			'banner_inline',
		];
		foreach ($banners as $banner) {
			$settings[$banner . '_type'] = SJB_Settings::getValue($banner . '_type');
			$settings[$banner . '_code'] = SJB_Settings::getValue($banner . '_code');
			$settings[$banner . '_img'] = SJB_Settings::getValue($banner . '_img');
			$settings[$banner . '_link'] = SJB_Settings::getValue($banner . '_link');
		}
		self::$settings[$theme] = $settings;
		return $settings;
	}

	public static function compileStyles()
	{
		$theme = SJB_Settings::getValue('TEMPLATE_USER_THEME');
		$themeSettings = self::getThemeSettings($theme);
		$path = SJB_TemplatePathManager::getAbsoluteThemePath($theme) . 'assets/style';

		$variables = [
			'$font-family' => $themeSettings['font'],
			'$top-banner-image' => '"' . rawurlencode($themeSettings['main_banner']) . '"',
			'$secondary-banner-image' => '"' . rawurlencode($themeSettings['secondary_banner']) . '"'
		];
		if (isset($themeSettings['button_color_1'])) {
			$variables['$theme_button_color_1'] = $themeSettings['button_color_1'];
		}
		if (isset($themeSettings['button_color_2'])) {
			$variables['$theme_button_color_2'] = $themeSettings['button_color_2'];
		}
		if (isset($themeSettings['button_color_3'])) {
			$variables['$theme_button_color_3'] = $themeSettings['button_color_3'];
		}

		$override = '';
		foreach ($variables as $name => $value) {
			$override .= sprintf("%s: %s;\n", $name, $value);
		}

		$scss = new Leafo\ScssPhp\Compiler();
		$scss->setImportPaths($path);
		file_put_contents($path . '/styles.css', $scss->compile($override . file_get_contents($path . '/styles.scss')));
	}
}
