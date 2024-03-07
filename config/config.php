<?php

if (PHP_MAJOR_VERSION >= 7) {
	set_error_handler(function ($errno, $errstr) {
		return (strpos($errstr, 'Declaration of') === 0);
	}, E_WARNING);
}

header('Content-Type: text/html; charset=utf-8');

define('APPS_DIR_PATH', '/var/www/html');
define('APP_DIR_PATH', '/var/www/html/tovaryplus.ru');
define('CMS_DIR_PATH', '/var/www/html/sky/cms');
define('FRAMEWORK_DIR_PATH', '/var/www/html/sky/framework');
define('LIBS_DIR_PATH', '/var/www/html/sky/libs');

define('APP_CONFIG_DIR_PATH', APP_DIR_PATH.'/config');


define('APP_VIEWS_DIR_PATH', APP_DIR_PATH.'/src/views2');

/*if (isset($_REQUEST['kcenter']) && $_REQUEST['kcenter'] == 1) {
    define('APP_VIEWS_DIR_PATH', APP_DIR_PATH.'/src/views3');
} else {
    define('APP_VIEWS_DIR_PATH', APP_DIR_PATH.'/src/views2');
}*/
 

define('CMS_VIEWS_DIR_PATH', CMS_DIR_PATH.'/src/views');

// define('APP_URL', 'https://www.tovaryplus.ru');
// define('CMS_URL', 'https://www.tovaryplus.ru/cms');
define('APP_URL', 'http://46.148.230.145/tovaryplus.ru/');
define('CMS_URL', 'http://46.148.230.145/tovaryplus.ru/cms');
// define('STATIC_FILES_URL', '//static.sky-cms.ru');;
define('STATIC_FILES_URL', '/public/sky');


date_default_timezone_set('Europe/Moscow');
error_reporting(E_ALL ^ E_STRICT);
//session_set_cookie_params(0, '/', 'tp43.sky-cms.ru');
setlocale(LC_CTYPE, ['ru_RU.UTF-8']);

ini_set('display_errors', (defined('APP_IS_DEV_MODE') && APP_IS_DEV_MODE) ? 1 : 0);
ini_set("log_errors", 1);
ini_set("error_log", '/var/www/html/logs/tovaryplus.ru_error.log');
ini_set('session.name', 'sid');

/**
 * 
 * @return \App\Classes\App
 */
function app() {
	return \Sky4\Container::getClass('\\App\\Classes\\App');
}

/**
 * 
 * @return Sky4\Helper\Html
 */
function html() {
	return \Sky4\Container::getClass('\\Sky4\\Helper\\Html');
}

function encode($string) {
	return html()->encode($string);
}

/**
 * 
 * @return Sky4\Helper\StringHelper
 */
function str() {
	return \Sky4\Container::getClass('\\Sky4\Helper\StringHelper');
}
