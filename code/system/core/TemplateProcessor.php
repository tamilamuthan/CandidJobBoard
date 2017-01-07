<?php

/**
 * TemplateProcessor - Template processing
 * @package SystemClasses
 * @subpackage TemplateProcessor
 */
class SJB_TemplateProcessor extends Smarty
{
	var $module_name;

	/**
	 * @var SJB_StructureExplorer
	 */
	private $htmlTagConverter;

	/**
	 * @var SJB_I18N
	 */
	private $i18n = null;
	
	/**
	 * 
	 * @var SJB_TemplateSupplier
	 */
	var $templateSupplier;

	/**
	 * Constructor explains our requirements to Smarty
	 *
	 * @param SJB_TemplateSupplier $templatesupplier instatance of SJB_TemplateSupplier class
	 * @return SJB_TemplateProcessor
	 */
	function __construct($templatesupplier)
	{
		$this->htmlTagConverter = SJB_ObjectMother::createHTMLTagConverterInArray();
		$this->compile_check = true;
		$this->module_name = $templatesupplier->getModuleName();

		parent::__construct();
		$this->error_reporting = E_ALL ^ E_NOTICE;
		$this->setCompileDir(SJB_System::getSystemSettings('COMPILED_TEMPLATES_DIR')
			. SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') . '/'
			. $templatesupplier->getTheme());
		if (!@is_dir($this->getCompileDir())) {
			@mkdir($this->getCompileDir(), 0777, true);
		}
		$this->setCacheDir(SJB_System::getSystemSettings('COMPILED_TEMPLATES_DIR') . '/smarty_cache');
		if (!@is_dir($this->getCacheDir())) {
			@mkdir($this->getCacheDir(), 0777, true);
		}

		/////////////////////////
		$this->registerPlugin('function', 'module', array(&$this, 'module'));
		$this->registerPlugin('function', 'hidden_form_fields', array(&$this, 'hidden_form_fields'));
		$this->registerPlugin('function', 'url', array(&$this, 'get_module_function_url'));
		$this->registerPlugin('function', 'event', array(&$this, 'dispatch_event'));
		$this->registerPlugin('function', 'js', array(&$this, '_tpl_javascript_get'));
		$currencyFormatter = new SJB_CurrencyFormatter();
		$this->registerPlugin('function', 'currencyFormat', array($currencyFormatter, 'currencyFormat'));
		$this->registerPlugin('function', 'currencySign', array($currencyFormatter, 'currencySign'));
		/////////////////////////

		$this->registerPlugin('block', 'title', array(&$this, '_tpl_title') );
		$this->registerPlugin('block', 'keywords', array(&$this, '_tpl_keywords') );
		$this->registerPlugin('block', 'javascript', array(&$this, '_tpl_javascript') );
		$this->registerPlugin('block', 'description', array(&$this, '_tpl_description') );
		$this->registerPlugin('block', 'head', array(&$this, '_tpl_head') );
		$this->registerPlugin('block', 'breadcrumbs', array(&$this, '_tpl_breadcrumbs') );

		$this->registerFilter('pre', array(&$this, '_replace_translation_alias'));
		$this->registerPlugin('block', 'tr', array(&$this, 'translate'));
		$this->registerPlugin('modifier', 'clrNonPrintedChars', 'SJB_HelperFunctions::clearNonPrintedCharacters');
		$this->registerPlugin('modifier', 'location', array(&$this, 'location'));
		$this->registerPlugin('modifier', 'badges', array(&$this, 'badges'));
		$this->registerPlugin('modifier', 'pretty_url', array(&$this, 'pretty_url'));
		$this->registerPlugin('modifier', 'listing_url', array(&$this, 'listing_url'));
		$this->registerPlugin('modifier', 'whmcs_encode', 'SJB_HelperFunctions::whmcsEncode');
		$this->registerPlugin('modifier', 'tr', array(&$this, 'tr'));
		$this->registerPlugin('modifier', 'banner', array(&$this, 'banner'));
		$this->registerPlugin('modifier', 'date', array(&$this, 'date'));

		$templatesupplier->registerResources($this);
		$this->templateSupplier = $templatesupplier;

		$this->registerPlugin('function', 'set_token_field', array(&$this, 'tpl_set_token_field'));

		$this->registerGlobalVariables();
	}

	function date($date, $format = null, $withTime = false)
	{
		require_once 'Smarty/libs/plugins/modifier.date_format.php';
		if (!$format) {
			$format = SJB_Settings::getValue('date_format');
			if ($withTime) {
				$format .= ' %H:%M';
			}
		}
		$date = smarty_modifier_date_format($date, $format);
		if ($withTime && strpos($date, ' 00:00') !== false) {
			$date = str_replace(' 00:00', '', $date);
		}
		return SJB_DateFormatter::localizeFormat($date);
	}

	function banner($name)
	{
		$settings = ThemeManager::getThemeSettings();
		if (empty($settings[$name . '_type'])) {
			return '';
		}

		if ($settings[$name . '_type'] == 'img') {
			if (empty($settings[$name . '_img'])) {
				return '';
			}

			if (!preg_match("~^(?:f|ht)tps?://~i", $settings[$name . '_link'])) {
				$settings[$name . '_link'] = "http://" . $settings[$name . '_link'];
			}

			return sprintf('<a href="%s" target="_blank"><img src="%s/files/banners/%s" /></a>', $settings[$name . '_link'], SJB_H::getSiteUrl(), rawurlencode($settings[$name . '_img']));
		}
		return $settings[$name . '_code'];
	}

	function tr($text, $escape = false)
	{
		$text = SJB_I18N::getInstance()->gettext('', $text);
		if ($escape) {
			return smarty_modifier_escape($text);
		}
		return $text;
	}

	/**
	 * @param array $listingOrUser
	 * @return bool|mixed|string
	 */
	function location($listingOrUser)
	{
		if ($listingOrUser) {
			if (!empty($listingOrUser['GooglePlace'])) {
				return $listingOrUser['GooglePlace'];
			}
			if (!empty($listingOrUser['Location']['City']) && !empty($listingOrUser['Location']['State'])) {
				return $listingOrUser['Location']['City'] . ', ' . $listingOrUser['Location']['State'];
			}
		}
		return '';
	}

    function badges($listingOrUser) 
    {
        $html = '';
        $achievements = array();

        if (isset($listingOrUser['user'])) {
            //print '<pre>'; print_r($listingOrUser); print '</pre>'; exit;
            $achievements = SJB_AchievementManager::getAllAchievementsInfoByUserSID($listingOrUser['user']['sid']);
            foreach ($achievements as $key => $achievementInfo) {
                 $achievements[$key] = $achievementInfo;
                 $achievements[$key]['badge'] =  SJB_BadgesManager::getBadgeInfoBySID($achievementInfo['badge_sid']);
            }
        }

        foreach($achievements as $ach) {
            if ($ach['badge']['file']) {
                $html .=   '<span style="padding:10px"> '
                          .'<img src="http://localhost/gradlead/code/files/files/'.$ach['badge']['file'].'" '
                          .'title="'.$ach['badge']['detailed_description'].'" border="0"/>'
                          .'</span>';
            }
        }

        return $html;
    }

	/**
	 * @param string $urlPart
	 * @param bool $strict
	 * @return string
	 */
	public static function pretty_url($urlPart, $strict = true)
	{
		$urlPart = mb_strtolower($urlPart);
		if (!$strict && preg_match('/[^\\w\\s]/u', $urlPart)) {
			return $urlPart;
		}
		$urlPart = preg_replace('/[^\\w]/u', '-', $urlPart);
		return trim(preg_replace('/-+/u', '-', $urlPart), '-');
	}

	/**
	 * @param array|object $listing
	 * @return string
	 */
	public static function listing_url($listing)
	{
		$url = '';
		// todo: remove createTemplateStructureForListing as we don't use CompanyName any more
		if (is_array($listing) && empty($listing['user'])) {
			$listingObject = new SJB_Listing($listing, $listing['listing_type_sid']);
			$listingObject->setSID($listing['sid']);
			$listing = SJB_ListingManager::createTemplateStructureForListing($listingObject);
		} elseif ($listing instanceof SJB_Listing) {
			$listing = SJB_ListingManager::createTemplateStructureForListing($listing);
		}
		if ($listing && is_array($listing)) {
			if ($listing['type']['id'] == 'Job') {
                $url = '/job/' . $listing['id'] . '/' . self::pretty_url($listing['Title']) . '/';
            } else if ($listing['type']['id'] == 'Opportunity') {
                $url = '/opportunity/' . $listing['id'] . '/' . self::pretty_url($listing['Title']) . '/';
            } else if ($listing['type']['id'] == 'Idea') {
                $url = '/idea/' . $listing['id'] . '/' . self::pretty_url($listing['Title']) . '/';
			} else {
				$url = '/resume/' . $listing['id'] . '/' . self::pretty_url($listing['Title']) . '/';
			}
		}
		return $url;
	}

	function getSystemAccessType()
	{
		return $this->templateSupplier->getSystemAccessType();
	}
	
	function setSystemAccessType($at)
	{
		$this->templateSupplier->setSystemAccessType($at);
	}

	function _tpl_title($params, $content)
	{
		SJB_System::setPageTitle($content);
	}

	function _tpl_keywords($params, $content)
	{
		SJB_System::setPageKeywords($content);
	}

	function _tpl_javascript($params, $content)
	{
		if (empty($content)) {
			return false;
		}

		$js = empty($GLOBALS['javascript']) ? '' : $GLOBALS['javascript'];
		$GLOBALS['javascript'] = $js . ' ' . $content;
		SJB_System::setGlobalTemplateVariable('javascript', $GLOBALS['javascript'], false);
	}

	function _tpl_javascript_get()
	{
		return $GLOBALS['javascript'];
	}

	function _tpl_description($params, $content)
	{
		SJB_System::setPageDescription($content);
	}

	function _tpl_head($params, $content)
	{
		if (empty($content)) {
			return false;
		}
		SJB_System::setPageHead($content);
	}

	function _tpl_breadcrumbs($params, $content)
	{
		SJB_System::setGlobalTemplateVariable('ADMIN_BREADCRUMBS', $content, false);
	}

	function hidden_form_fields($params)
	{
		$result = "\n";
		foreach($params as $key => $value) {
			$result .= '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">'."\n";
		}
		return $result;
	}

	/**
	 * This is callback function that called by Smarty to complete following
	 * expressions {module name="module_name" function="function name"}
	 *
	 * @param array $params Array of parameters
	 * @return string
	 */
	function xload_module($params)
	{
		$name = isset($params['name']) ? $params['name'] : '';
		$function = isset($params['function']) ? $params['function'] : '';
		unset($params['name']);
		unset($params['function']);
		return SJB_System::executeFunction($name, $function, $params);
	}

	function module($params)
	{
		$name = isset($params['name']) ? $params['name'] : '';
		$function = isset($params['function']) ? $params['function'] : '';
		unset($params['name']);
		unset($params['function']);

		if (empty($name) || empty($function)) {
			return '<!-- Either module or function is not specified in call to {module ..} -->';
		}

		return SJB_System::executeFunction($name, $function, $params);
	}

	public function dispatch_event($params)
	{
		$eventName = isset($params['name']) ? $params['name'] : '';
		$eventData = isset($params['data']) ? $params['data'] : null;

		if (empty($eventName)) {
			return;
		}

		SJB_Event::dispatch($eventName, $eventData);
		return $eventData;
	}

	function image()
	{
		return null;
	}

	function print_image_path()
	{
		return null;
	}

	/**
	 * Getting url of module function
	 * @param array $params  Array of parameters
	 * @return string
	 */
	function get_module_function_url($params)
	{
		if (count($params) == 0) {
			return SJB_System::getSystemSettings('SITE_URL');
		}
		return 'There is no such function or module.';
	}

	function registerGlobalVariables()
	{
		$variables = SJB_System::getGlobalTemplateVariables();
		foreach ($variables as $name => $value) {
			$this->assign($name, $value);
		}
		parse_str($_SERVER['QUERY_STRING'], $queryString);
		$params = array();
		$uri = SJB_System::getURI();
		if (!empty($_POST) && !in_array($uri, array('/paypal-pro-fill-payment-card/', '/view-invoice/', '/edit-product/', '/add-product/'))) {
			$queryString = array_merge($queryString, $_POST);
		}

		foreach ($queryString as $key => $val) {
			if (!in_array($key, array('lang', 'theme'))) {
				$params[$key] = $val;
			}
		}
		$this->assign('url', $uri);
		$this->assign('acl', SJB_Acl::getInstance());
		$this->assign('params', http_build_query($params, '', '&amp;'));
		if (SJB_System::getSystemSettings('isSaas')) {
			$this->assign('isSaas', SJB_System::getSystemSettings('isSaas'));
			$this->assign('intercom_app', SJB_System::getSystemSettings('env')['Intercom']['app']);
		}
		$billingUrl = 'https://www.smartjobboard.com/ca';
		if (SJB_System::getSystemSettings('whmcs_url')) {
			$billingUrl = SJB_System::getSystemSettings('whmcs_url');
		}
		$this->assign('billingUrl', $billingUrl);
	}

	public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
	{
		try {
			return parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
		}
		catch (SmartyCompilerException $ex) {
			$error = 'Template syntax error';
			if ($display) {
				echo $error;
				return;
			}
			return $error;
		}
	}
	
	public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
	{
		if (SJB_FlashMessages::getInstance()->isErrors()) {
			return;
		}
		
		$compile_id = $this->module_name;
		if (SJB_HelperFunctions::debugModeIsTurnedOn()) {
			SJB_HelperFunctions::debugInfoPush(array($compile_id => $template), 'TEMPLATE_PROCESSOR');
		}
		parent::display($this->templateSupplier->getTplName($template), $cache_id, $compile_id, $parent);
	}

	function filterThenAssign($tpl_var, $value = null)
	{
		if (!empty($value)) {
			$this->htmlTagConverter->explore($value);
		}
		$this->assign($tpl_var, $value);
	}

	
	function translate($params, $phrase_id, &$smarty, $repeat)
	{
		if ($repeat) {
			return null; // see Smarty manual
		}
		$trimmedPhraseId = trim($phrase_id);
		if ((empty($phrase_id) || empty($trimmedPhraseId)) && !is_numeric($trimmedPhraseId)) {
			return '';
		}
		$this->i18n = SJB_I18N::getInstance();
		$mode = isset($params['mode']) ? $params['mode'] : null;

		if (isset($params['metadata']) && gettype($params['metadata']) === 'array') {
			$res = $this->_translateMetadata($params['metadata'], $phrase_id, $mode);
			$res = $this->replace_with_template_vars($res, $smarty);
			return $res;
		} 

		if (isset($params['type'])) {
			return $this->_translateByType($params['type'], $phrase_id);
		} 
		$domain = isset($params['domain']) ? $params['domain'] : null;
		$res = $this->i18n->gettext($domain, trim($phrase_id), $mode);
		return $this->replace_with_template_vars($res, $smarty);
	}

	/**
	 * @param $res
	 * @param Smarty $smarty
	 * @return mixed
	 */
	function replace_with_template_vars($res, &$smarty)
	{
		if (preg_match_all('/{[$]([a-zA-Z0-9_.]+)}/', $res, $matches)) {
			foreach($matches[1] as $varName) {
				$varNameArray = explode('.', $varName);
				$value = $smarty->getTemplateVars(is_array($varNameArray) ? $varNameArray[0] : $varName);
				if (is_array($value)) {
					if (is_array($varNameArray)) {
						$varNameArraySize = sizeof($varNameArray);
						for ($i = 1; $i < $varNameArraySize; $i++) {
							if (isset($value[$varNameArray[$i]])) {
								$value = $value[$varNameArray[$i]];
							} else {
								$value = '';
								break;
							}
						}
					} else {
						$value = '';
					}
				}
				
				$value = str_replace(array('\\', '$'), array('\\\\', '\$'), $value);
				$res = preg_replace('/{[$]'.$varName.'}/u',$value,$res);
			}
		}
		return $res;
	}

	function _translateMetadata($metadata, $phrase_id, $mode)
	{
		if (isset($metadata['domain'])) {
			return $this->i18n->gettext($metadata['domain'], $phrase_id, $mode);
		}
		if (isset($metadata['type'])) {
			return $this->_translateByType($metadata['type'], $phrase_id);
		}
		return null;
	}
	
	function _translateByType($type, $value)
	{
		switch ($type) {
			case 'int':
			case 'integer':
				return $this->i18n->getInt($value);
				break;
			case 'float':
				return $this->i18n->getFloat($value);
				break;
			case 'date':
				return $this->i18n->getDate($value);
				break;
			default: return $value;
				break;
		}
	}

	function _replace_translation_alias($tpl_source)
	{
		return preg_replace_callback(
			'/\[\[(?:([\w-_]+)!)?(.*?)(?::([\w-_]+))?\]\]/msu',
			array (&$this, '_replace_alias_with_block_function_tr'), $tpl_source);
	}

	function _replace_alias_with_block_function_tr($matches)
	{
		$domain = $matches[1];
		$phrase_id = $matches[2];
		$mode = isset($matches[3]) ? ' mode="'.$matches[3].'"' : null;
		$metadata = null;
		if (preg_match("/^[$]([a-zA-Z0-9._]+)$/",$phrase_id, $m)) {
			$metadata = ' metadata=$METADATA.'.$m[1];
			$phrase_id = "{".$phrase_id."}";
		}
		else {
			if ($domain) {
				$domain = ' domain="'.$domain.'"';
			}
			else if (preg_match("/^(\\w+\\\\!)/", $phrase_id)) {
				$phrase_id = preg_replace("/^(\\w+)\\\\!/u", '$1!', $phrase_id);
			}
		}
		if ($phrase_id) {
			return sprintf('{tr%s%s%s}%s{/tr}', $metadata, $domain, $mode, $phrase_id);
		}
	}

	function deleteCacheBySpecifiedPath($path)
	{
		static $error = '';
		static $result = '';
		if (file_exists($path)) {
			$dh = opendir($path);
			while ($file = readdir($dh)) {
				if ($file != '.' && $file != '..' && $file != '' && strlen($file) > 4) {
					if (preg_match("/.php$/", $file)) {
						if (!@unlink($path . "/" . $file) ) {
							$error['CANT_DELETE_FILES'][] = $path . "/" . $file;
	 					} else {
							$result = "Smarty Cache was successfully cleared";
						}
					}
				}
			}
		}
		return !empty($error) ? $error : $result;
	}
	
	/**
	 * Set token hidden field
	 * @param $params
	 * @return string
	 */
	public function tpl_set_token_field($params)
	{
		// check $_REQUEST for incoming token value and check it for NON-WORD characters for security reason
		$token = SJB_Request::getVar('form_token');
		if (empty($token) || (preg_match('|[\W]+|', $token))) {
			$token = (string) microtime(true);
			$token = md5($token);
		}
		$this->assign('form_token', $token);

		return "<input type=\"hidden\" name=\"form_token\" value=\"{$token}\" />";
	}

}
