<?php

class SJB_Error extends \Monolog\Logger
{
	/**
	 * @var \Monolog\Logger
	 */
	private static $instance;

	public static function getInstance()
	{
		if (self::$instance) {
			return self::$instance;
		}

		register_shutdown_function(array('SJB_Error', 'fatalErrorHandler'));
		set_exception_handler(array('SJB_Error', 'fatalErrorHandler'));

		self::$instance = new SJB_Error('logger');

		if (SJB_System::getSystemSettings('sentry')) {
			$client = new Raven_Client(SJB_System::getSystemSettings('sentry'));
			self::$instance->pushHandler(new \Monolog\Handler\RavenHandler($client));
		}
		self::$instance->pushHandler(new \Monolog\Handler\ErrorLogHandler());
		$handler = new \Monolog\ErrorHandler(self::$instance);
		$handler->registerErrorHandler();
		$handler->registerExceptionHandler();
		$handler->registerFatalHandler();
		return self::$instance;
	}

	public static function fatalErrorHandler()
	{
		$lastError = error_get_last();
		$args = func_get_args();
		$isError = false;
		if ($args && $args[0] instanceof Exception) {
			self::getInstance()->addError( $args[0]->getMessage(), array(
				'exception' => $args[0]
			));
			$isError = true;
		}

		if (in_array($lastError['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR))) {
			self::getInstance()->addError($lastError['message'], array(
				'file' => $lastError['file'],
				'line' => $lastError['line']
			));
			$isError = true;
		}
		if ($isError) {
			$siteURL   = SJB_System::getSystemSettings('SITE_URL');
			echo "
				<html>
					<head>
						<link rel=\"stylesheet\" href=\"{$siteURL}/templates/_system/errors/errors.css\" type=\"text/css\">
					</head>
					<body>
						<p class=\"error\">Fatal error! Your request can not be executed!</p>
					</body>
				</html>";
			exit();
		}

	}
}
