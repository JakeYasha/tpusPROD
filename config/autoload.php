<?php

require_once FRAMEWORK_DIR_PATH . '/src/Autoloader.php';

$autoloader = new \Sky4\Autoloader();
if (defined('APP_SUB_SYSTEM_NAME') && (APP_SUB_SYSTEM_NAME === 'CMS')) {
	$autoloader->addNamespace('App', CMS_DIR_PATH . '/src/');
} else {
	$autoloader->addNamespace('App', APP_DIR_PATH . '/src/');
}
$autoloader->addNamespace('App', APP_DIR_PATH . '/src/Common/')
		->addNamespace('App', CMS_DIR_PATH . '/src/Common/')
		// ----
		->addNamespace('App\\Model\\Component', APP_DIR_PATH . '/src/Model/Component/')
		->addNamespace('App\\Model\\Component', FRAMEWORK_DIR_PATH . '/repository-src/models-components/')
		->addNamespace('App\\Model', APP_DIR_PATH . '/src/Model/')
		->addNamespace('App\\Model', CMS_DIR_PATH . '/src/Model/')
		//----
		->addNamespace('Sky4', APP_DIR_PATH . '/src/Sky4/')
		->addNamespace('Sky4', FRAMEWORK_DIR_PATH . '/src/')
		// ----
		->addNamespace('Libs', LIBS_DIR_PATH . '/')
		->addNamespace('Mailgun', LIBS_DIR_PATH . '/composer/vendor/mailgun/mailgun-php/src/Mailgun/')
		->addNamespace('Guzzle', LIBS_DIR_PATH . '/composer/vendor/guzzle/guzzle/src/Guzzle/')
		->addNamespace('Symfony\\Component\\EventDispatcher', LIBS_DIR_PATH . '/composer/vendor/symfony/event-dispatcher/')
		->addNamespace('Foolz', LIBS_DIR_PATH . '/Foolz/')
		->addNamespace('Mpdf', APP_DIR_PATH . '/protected/mpdf/src/')
		->addNamespace('Psr', APP_DIR_PATH . '/protected/psr/log/Psr')
		// ----
		->addNamespace('Framework', FRAMEWORK_DIR_PATH . '/deprecated/')
		->addDirPath(APP_DIR_PATH . '/src/Sky4/deprecated/')
		->addDirPath(FRAMEWORK_DIR_PATH . '/deprecated/')
		// ----
		->register();

if (!function_exists('classExists')) {

	function classExists($class_name) {
		return \Sky4\Autoloader::getInstance()->loadClass($class_name);
	}

}

