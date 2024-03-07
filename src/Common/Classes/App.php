<?php

/**
 * @created Apr 26, 2017
 * @author Dmitriy Mitrofanov <d.i.mitrofanov@gmail.com>
 */
namespace App\Classes;
require_once APP_DIR_PATH.'/protected/punycode/idna_convert.class.php';

class App extends \Sky4\App {

	private static $_geo_data = null;
	private static $_use_map = null;
	private static $_use_agreement = null;
	private static $_sphinx = null;
	private static $_sphinx_conn = null;
	private static $_message_queue = null;
	private static $_user = null;
	private static $_user_timestamp = null;
	private static $time = null;

	/**
	 *
	 * @var Logger 
	 */
	private static $logger = null;

	public static function log($message, $level = 0) {
		if (self::$logger === null) {
			self::$logger = \Sky4\Container::getClass('\\App\\Classes\\Logger');
		}

		self::$logger->add($message, $level);
	}

	/**
	 * 
	 * @return Logger
	 */
	public function getLogger() {
		if (self::$logger === null) {
			self::$logger = \Sky4\Container::getClass('\\App\\Classes\\Logger');
		}

		return self::$logger;
	}
    
    public static function init() {
		return parent::init();
	}

	public static function run() {
		// if (preg_match('~^/print/.*~', $_SERVER['REQUEST_URI'])) {
		// 	$_SERVER['REQUEST_URI'] = \Sky4\Helper\StringHelper::replace($_SERVER['REQUEST_URI'], '/print/', '/print-action/');
		// }/// а не тут ли яндекс отваливается?
		return parent::run();
	}

	public static function assert($vars, $types, $args = [], $args_count = 0) {
		$result = true;
		foreach ($vars as $k => $var) {
			if ($types[$k] === 'intnull') {
				if (!($var === null || is_numeric($var))) {
					$result = false;
				}
			} elseif ($types[$k] === 'int') {
				if (!is_numeric($var)) {
					$result = false;
				}
			}
		}

		if (count($args) !== $args_count) {
			$result = false;
		}

		if (!$result) {
			throw new \Sky4\Exception(\Sky4\Exception::TYPE_BAD_URL.' ожидаем другие параметры');
		}
	}

	public static function away($url, $id_firm = null, $mode = null) {
		$result_url = '';
		$idn = new \idna_convert(['idn_version' => 2008]);
		$url = $idn->encode($url);

		if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
			$url = 'http://'.$url;
		}
		$params = [];

		if ($id_firm !== null) {
			$params[] = 'id_firm='.$id_firm;
		}

		if ($mode !== null) {
			$params[] = 'mode='.$mode;
		}

		$result_url = $params ? '/page/away/?url='.urlencode(trim($url)).'&'.implode('&', $params) : '/page/away/?url='.urlencode(trim($url));

		if ($mode === 'yml') {
			$result_url .= '" target="_blank" rel="nofollow"';
		}


		return $result_url;
	}

	/**
	 * 
	 * @return \App\Classes\BreadCrumbs
	 */
	public static function breadCrumbs() {
		return \Sky4\Container::getClass('\\App\\Classes\\BreadCrumbs');
	}

	public static function getSphinxConnection() {
		if (self::$_sphinx_conn === null) {
			$conn = new \Foolz\SphinxQL\Connection();
			$conn->setParams(['host' => '127.0.0.1', 'port' => APP_SPHINX_CONNECTION_PORT]);

			self::$_sphinx_conn = $conn;
		}

		return self::$_sphinx_conn;
	}
	
	public static function resetSphinxConnection() {
		self::$_sphinx_conn = null;
	}

	public static function prepareSphinxMatchStatement($query, $operator = '&', $mask = '*') {
		if (!is_array($query)) {
			$query_words = preg_split('/[\s,-]+/', $query, 10);
		} else {
			$query_words = $query;
		}

		$keywords = [];
		$res = $query;
		if ($query_words) {
			foreach ($query_words as $word) {
				//if (str()->length($word) > 3) {
				if ($mask === '*') {
					$keywords[] = "(".$word." | *".$word."*)";
				} elseif ($mask === 'any' && $operator === '|') {
					$keywords[] = implode($operator, preg_split('/[\s,-]+/', $word, 10));
				} elseif ($mask === 'any') {
					$keywords[] = $word; // implode($operator, preg_split('/[\s,-]+/', $word, 10));
				} else {
					$keywords[] = '"'.$word.'"';
				}
				//}
			}
			$res = implode(' '.$operator.' ', $keywords);
		}

		return $res;
	}

	/**
	 * 
	 * @return Tabs
	 */
	public static function tabs() {
		return \Sky4\Container::getClass('\\App\\Classes\\Tabs');
	}

	/**
	 * 
	 * @return Sidebar
	 */
	public static function sidebar() {
		return \Sky4\Container::getClass('\\App\\Classes\\Sidebar');
	}

	/**
	 * 
	 * @return Statistics
	 */
	public static function stat() {
		return \Sky4\Container::getClass('\\App\\Classes\\Statistics');
	}

	/**
	 * 
	 * @return \App\Classes\Capcha
	 */
	public static function capcha() {
		return \Sky4\Container::getClass('\\App\\Classes\\Capcha');
	}

	/**
	 * 
	 * @return \App\Classes\Config
	 */
	public static function config() {
		return \Sky4\Container::getClass('\\App\\Classes\\Config');
	}

	/**
	 * 
	 * @return \App\Classes\Location
	 */
	public static function location() {
		return \Sky4\Container::getClass('\\App\\Classes\\Location');
	}

	/**
	 * 
	 * @return \App\Classes\Metadata
	 */
	public static function metadata() {
		return \Sky4\Container::getClass('\\App\\Classes\\Metadata');
	}

	public static function uri() {
		return str_replace(self::location()->currentId(), '', self::request()->getRequestUri());
	}

	public static function url() {
		return self::request()->getRequestUri();
	}
    public static function getPathUrl() {
        $_request_parse = parse_url(self::request()->getRequestUri());
        $_arr_path = explode('/', $_request_parse['path']);
        $_arr_path = array_values(array_diff($_arr_path,array("null","")));
		return $_arr_path;
	}
    
	public static function link($url, $fixed_prefix = null) {
		return self::location()->link($url, $fixed_prefix);
	}

	public static function linkFilter($url, $filters = [], $current = []) {
		$base_url = preg_replace('~([^?]*)(\?.*)~', '$1', $url);
		ksort($filters);
		if ($filters) {
			$res_filters = [];
			foreach ($filters as $key => $val) {
				if (($val !== null || isset($current[$key]))) {
					if (isset($current[$key]) && $current[$key] === false) continue;

					$res_filters[] = $key.'='.((isset($current[$key])) ? $current[$key] : $val);
				}
			}
			return $res_filters ? $base_url.'?'.implode('&', $res_filters) : $base_url;
		}

		return $base_url;
	}

	public static function getGeoData() {
		if (self::$_geo_data === null) {
			$geo_ip = new GeoIp();
			self::$_geo_data = $geo_ip->getFromCache();
		}
		return self::$_geo_data;
	}

	public static function useMap() {
		return self::$_use_map !== null;
	}
    
    public static function useAgreement() {
		return self::$_use_agreement !== null;
	}

	/**
	 * @return \App\Model\FirmUser
	 */
	public static function firmUser() {
		$firm_user = new \App\Model\FirmUser();
		$firm_user->userComponent()->getFromSession();

		if (!$firm_user->exists() && self::firmManager()->exists()) {
			return self::firmManager();
		}

		return $firm_user;
	}

	/**
	 * @return \App\Model\FirmManager
	 */
	public static function firmManager() {
		$firm_manager = new \App\Model\FirmManager();
		$firm_manager->userComponent()->getFromSession();

		return $firm_manager;
	}

	/**
	 * @return \App\Model\FirmUserTimestamp
	 */
	public static function firmUserTimestamp() {
		if (self::$_user_timestamp === null) {
			if (self::firmUser()->exists()) {
				$firm_user_timestamp = new \App\Model\FirmUserTimestamp();
				$firm_user_timestamp->getByUser(self::firmUser());
				self::$_user_timestamp = $firm_user_timestamp;
			}
		}

		return self::$_user_timestamp;
	}

	/**
	 * @return StsService
	 */
	public static function stsService() {
		$location = self::location()->currentId();
		$services = new \App\Model\StsService();
		$service = $services->reader()
				->setWhere(['AND', '`id_city` = :id_city', '`exist` = :exist'], [':id_city' => $location, ':exist' => 1])
				->objectByConds();

		if (!$service->exists()) {
			$location = self::location()->getRegionId();
			$service = $services->reader()
					->setWhere(['AND', '`id_region_country` = :id_region_country', '`exist` = :exist'], [':id_region_country' => $location, ':exist' => 1])
					->objectByConds();
		}

		return $service;
	}

	public static function useCaptcha() {
		self::metadata()->setJsFile('https://www.google.com/recaptcha/api.js');
	}

	public static function setUseMap($param) {
		self::$_use_map = $param;
	}
    
    public static function setUseAgreement($param) {
		self::$_use_agreement = $param;
	}

	/**
	 * 
	 * @return EmailSender
	 */
	public static function email() {
		return \Sky4\Container::getClass('\\App\\Classes\\EmailSender');
	}

	/**
	 * 
	 * @return MessageQueue
	 */
	public static function sender() {
		return \Sky4\Container::getClass('MessageQueue');
	}

	/**
	 * 
	 * @return \Sky4\Db\Connection
	 */
	public static function db() {
		return \Sky4\Container::getClass('\\Sky4\\Db\\Connection');
	}

	/**
	 * @return Adv
	 */
	public static function adv() {
		return \Sky4\Container::getClass('\\App\\Classes\\Adv');
	}

	/**
	 * @return ACrontab
	 * @todo
	 */
	public static function crontab() {
		return \Sky4\Container::getClass('ACrontab');
	}

	/**
	 * @return System
	 */
	public static function system() {
		return \Sky4\Container::getClass('\\App\\Classes\\System');
	}

	public static function startTimer() {
		self::$time = microtime(true);
	}

	public static function endTimer($time = 0, $die = true) {
		if ($die) {
			die(microtime(true) - self::$time - $time);
		}

		return microtime(true) - self::$time - $time;
	}

    public static function sendMessage($message) {
        if (APP_IS_DEV_MODE && isset($_REQUEST['debug'])) {
            $token = file_get_contents("https://xlorspace.ru/bot/?format=plain&action=auth&auth_key=f0b2774b-fb77-474d-b547-d29b39492654");
            file_get_contents("https://xlorspace.ru/bot/?bot=TovaryplusOnlineLog&action=send&token={$token}&message=" . urlencode($message));
        }
    }
    
    public static function inDebugMode() {
        return isset($_REQUEST['debug']);
    }

    /**
	 * @return bool
    */
    public static function isNewTheme() {
        if (app()->cookie()->get('theme_name') !== NULL) {
            return app()->cookie()->get('theme_name') === 'telemagic';
        }
        
        return \App\Classes\App::stsService()->val('theme_name') === 'telemagic';
    }
}
