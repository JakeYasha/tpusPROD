<?php

namespace App\Classes;

use App\Model\Banner;
use App\Model\Firm;
use App\Model\GeoIp;
use App\Model\StatBannerClick;
use App\Model\StatBannerShow;
use App\Model\StatObject;
use App\Model\StatRequest;
use App\Model\StatUser;
use App\Model\Price;
use App\Classes\App as CApp;
use Sky4\Helper\DeprecatedDateTime as CDateTime;
use Sky4\Model as CModel;
use const APP_IS_DEV_MODE;
use function str;

class Statistics {

	protected static $_session_var = '__app_statistics';
	protected static $_lifetime = 3600;

	/* var StatUser $stat_user */
	protected $stat_user = null;
	protected $stat_objects = [];

	public function __construct() {
		$this->setUserFromSession();
	}

	public function addObject($type, CModel $model) {
		$hash = md5($type.$model->alias().$model->id());
		if (!isset($this->stat_objects[$hash])) {
			$this->stat_objects[$hash] = [
				'type' => $type,
				'model' => $model
			];
		}

		return $this;
	}

	public function fixBannerShow(Banner $banner) {
		$action_string = 'banner-'.$banner->id().'-show';
		if (!$this->exist($action_string) && !$this->isBot() && !$this->isHackingAttemp() && !$this->isDeveloper() && !$this->isEmptyReferer()) {
			$bs = new StatBannerShow();
			$bs->insert([
				'id_stat_user' => $this->getStatUserId(),
				'id_firm' => $banner->id_firm(),
				'id_banner' => $banner->id(),
				'id_city' => $banner->val('id_city')
			]);

			if ((int)$banner->val('max_count') !== 0) {
				$flag_is_active = 1;
				$total_count = (int)$banner->val('current_count') + 1;
				if ((int)$banner->val('current_count') >= (int)$banner->val('max_count')) {
					$flag_is_active = 0;
				}
				$banner->update(['current_count' => $total_count, 'flag_is_active' => $flag_is_active]);
			}
		}

		return $this;
	}

	public function fixBannerClick(Banner $banner) {
		$action_string = 'banner-'.$banner->id().'-click';
		if (!$this->exist($action_string) && !$this->isBot() && !$this->isHackingAttemp() && !$this->isDeveloper() && !$this->isEmptyReferer()) {
			$bc = new StatBannerClick();
			$sr = new StatRequest($this->getRequestId());
			$bc->insert([
				'id_stat_user' => $this->getStatUserId(),
				'id_stat_request' => $sr->id(),
				'id_firm' => $banner->id_firm(),
				'id_banner' => $banner->id(),
				'id_city' => $banner->val('id_city'),
				'response_url' => $sr->val('request_refferer'),
				'banner_url' => $banner->val('url')
			]);
		}

		return $this;
	}

	public static function getBotSignatures() {
		return [
            'AhrefsBot', 
            'Apache-HttpClient', 
            'B2BContext', 
            'Baiduspider', 
            'Begun', 
            'bingbot', 
            'CCBot', 
            'DeuSu', 
			'DotBot', 
            'DuckDuckGo-Favicons-Bot', 
            'eSyndiCat',
            'Exabot', 
            'facebookexternalhit', 
			'Google favicon', 
            'Googlebot', 
            'GrapeshotCrawler', 
            'ia_archiver', 
            'Linguee Bot',
            'linkdex',
            'LinkpadBot',
			'Mail.RU_Bot', 
            'Mediapartners-Google', 
			'MegaIndex', 
            'MJ12bot',
			'MSNBot', 
            'Nutch', 
			'openstat.ru/Bot', 
            'python-requests',
			'Riddler', 
            'Slurp', 
            'Sogou',
            'Spider',
            'SputnikBot', 
            'StackRambler', 
            'statdom.ru/Bot', 
            'trendictionbot', 
            'Twitterbot', 
            'VelenPublicWebCrawler', 
            'vkShare', 
            'Yahoo! Slurp',
			'Yandex', 
		];
	}

	public static function isBot() {
		$bots = self::getBotSignatures();
		foreach ($bots as $bot) {
			if (stristr(app()->request()->getUserAgent(), $bot) !== false) {
				return true;
			}
		}
		return false;
	}

	public static function isEmptyReferer() {
		if (app()->request()->getReferer('') == '') {
			return true;
		}
		return false;
	}
    
    public static function isHackingAttemp() {
        $request_data = [
            app()->request()->getReferer(''),
            app()->request()->getRequestUri(''),
            app()->request()->getUserAgent('')
        ];
        foreach($request_data as $string) {
            if (!empty($string) && strpos($string,'\'') !== FALSE) {
                return true;
            }
        }
		return false;
	}


	public static function isDeveloper() {
        //08.11.2019 ALWAYS FALSE
		return false;//defined('APP_IS_DEV_MODE') && APP_IS_DEV_MODE ? true : false;
	}

	public function fixRequest($action_string, $filters = []) {
		if (!$this->isExceptionRequest($action_string, $filters) && !$this->exist($action_string, $filters) && !$this->isBot() && !$this->isHackingAttemp() && !$this->isDeveloper() && !$this->isEmptyReferer()) {
			$sr = new StatRequest();
			$sr->insert([
				'id_stat_user' => $this->getStatUserId(''),
				'request_url' => app()->request()->getRequestUri(''),
				'request_refferer' => app()->request()->getReferer(''),
				'request_text' => isset($filters['query']) ? app()->request()->processParams($filters, ['query' => 'string'])['query'] : ''
			]);

			$this->setRequestId($sr->id());
		}

		return $this;
	}
    
    public function isExceptionRequest($action_string, $filters) {
        $exceptions = ['App\\Controller\\FirmFeedback', 'App\\Controller\\Utils'];
		$exception_request = false;
		foreach ($exceptions as $string) {
			if (str()->pos($action_string, $string) !== false) {
				$exception_request = true;
			}
		}
        
        $ajax_exceptions = ['App\\Controller\\AppAjax'];
		foreach ($ajax_exceptions as $string) {
			if (str()->pos($action_string, $string) !== false 
                    && in_array($filters['url'], ['/app-ajax/fix-stat/24/', '/app-ajax/fix-stat/26/'])) {
                $exception_request = false;
			}
		}

        return $exception_request;
    }

	public function fixResponse($rewrite_prev_response = true) {
		$current_request_id = $this->getRequestId();
        
		if ($current_request_id !== null && !$this->isBot() && !$this->isHackingAttemp() && !$this->isDeveloper() && !$this->isEmptyReferer()) {
			$sr = new StatRequest($current_request_id);
			if ($rewrite_prev_response) {
				$sr->update([
					'response_id_city' => App::location()->currentId(),
					'response_title' => App::metadata()->getTitle(),
					'response_url' => app()->request()->getRequestUri(),
					'response_code' => http_response_code()
				]);
			}

			foreach ($this->stat_objects as $object) {
				$so = new StatObject();
				$model_info = self::getModelInfo($object['model']);
				$so->insert([
					'id_city' => $model_info['id_city'],
					'id_stat_user' => $this->getStatUserId(),
					'id_stat_request' => $current_request_id,
					'model_alias' => $model_info['alias'],
					'model_id' => $model_info['id'],
					'id_firm' => $model_info['id_firm'],
					'type' => $object['type'],
					'name' => $model_info['name']
				]);
			}

			$current_request_id = null;
		}

		return $this;
	}

	public function getRequestHash($action_string, $filters = []) {
		return md5($action_string.$this->getStatUserId().implode(',', $filters));
	}

	public function exist($action_string, $filters = []) {
		$hash = $this->getRequestHash($action_string, $filters);
		$res = false;

		if (isset($_SESSION[self::$_session_var][$hash]) || $this->isBot() || $this->isHackingAttemp() || $this->isDeveloper() || $this->isEmptyReferer()) {
			$res = true;
		} else {
			$_SESSION[self::$_session_var][$hash] = ['datetime' => time()];
		}

		return $res;
	}

	public function getStatUserId() {
		if ($this->stat_user === null && !$this->isBot() && !$this->isHackingAttemp() && !$this->isDeveloper() && !$this->isEmptyReferer()) {
			$cookie_stat_user_hash = app()->cookie()->get('app.stat.user');
			if ($cookie_stat_user_hash !== null) {
				$user = new StatUser();
				$user->reader()
						->setWhere(['AND', 'cookie_hash = :cookie_hash'], [':cookie_hash' => $cookie_stat_user_hash])
						->objectByConds();

				if ($user->exists()) {
					if ($user->isValid()) {
						$this->stat_user = $user;
					} else {
						$user->update(['timestamp_ending' => CDateTime::now()]);
					}
				}
			}

			if ($this->stat_user === null) {
				$user = new StatUser();
				$ip = new GeoIp();
				$ipgb = $ip->getFromCache();

				$user->insert([
					'id_city' => App::location()->currentId(),
					'user_city_name' => $ipgb['town'],
					'id_user' => 0,
					'cookie_hash' => $this->genCookieHash(),
					'referer' => app()->request()->getReferer(''),
					'user_agent' => app()->request()->getUserAgent(''),
					'timestamp_beginning' => CDateTime::now(),
					'timestamp_ending' => CDateTime::now(),
				]);

				$this->stat_user = $user;

				$this
						->storeUserInSession()
						->setCookieHash();
			}
		}

		return $this->isBot() || $this->isHackingAttemp() || $this->isDeveloper() || $this->isEmptyReferer() ? null : $this->stat_user->id();
	}

	protected function genCookieHash() {
		return md5(app()->request()->getRemoteAddr().app()->request()->getUserAgent());
	}

	protected function setCookieHash() {
		app()->cookie()
				->setExpireDay()
				->set('app.stat.user', $this->genCookieHash());

		return $this;
	}

	protected function setRequestId($current_request_id) {
		$_SESSION[self::$_session_var]['requests'][md5(app()->request()->getRequestUri())] = $current_request_id;
	}

	protected function storeUserInSession() {
		if ($this->stat_user !== null) {
			$_SESSION[self::$_session_var]['user_stat'] = $this->stat_user;
		}

		return $this;
	}

	protected function setUserFromSession() {
		if (isset($_SESSION[self::$_session_var]['user_stat']) && $_SESSION[self::$_session_var]['user_stat'] instanceof StatUser && $_SESSION[self::$_session_var]['user_stat']->exists()) {
			$this->stat_user = $_SESSION[self::$_session_var]['user_stat'];
			$this->stat_user->update(['timestamp_ending' => CDateTime::now()]);
		}

		return $this;
	}

	public function getRequests() {
		return isset($_SESSION[self::$_session_var]['requests']) ? $_SESSION[self::$_session_var]['requests'] : [];
	}

	public function getRequestId() {
		$requests = $this->getRequests();
		$request_hash = md5(app()->request()->getRequestUri());
		$result = null;
		foreach ($requests as $hash => $request_id) {
			if ($hash === $request_hash) {
				$result = $request_id;
			}
		}

		return $result;
	}

	public static function getModelInfo(CModel $model) {
		$result = [
			'alias' => $model->alias(),
			'id_firm' => $model instanceof Firm ? $model->id() : $model->val('id_firm', 0),
			'id_city' => $model->val('id_city', App::location()->currentId()),
			'name' => $model instanceof Price ? ($model->val('name').' '.$model->val('unit').' '.$model->val('vendor')) : $model->val('name', '')
		];

		$result['id'] = $model->id();
		return $result;
	}

	public function actionClear() {
		App::db()->query()->truncateTable('stat_request');
		App::db()->query()->truncateTable('stat_object');
		$_SESSION[self::$_session_var] = [];
		exit();
	}

}
