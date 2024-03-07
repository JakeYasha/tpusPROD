<?php

namespace App\Classes;

use App\Model\AppConfig;
use const APP_SUB_SYSTEM_NAME;
use function str;

class Config extends \Sky4\Config {

	public function get($key, $default_val = null) {
		$result = $default_val;
		if (is_array($this->params) && !empty($this->params)) {
			$app = defined('APP_SUB_SYSTEM_NAME') ? str()->toLower(APP_SUB_SYSTEM_NAME) : '';
			$key = str()->toLower(str()->trim($key));
			if (isset($this->params[$app])) {
				if (isset($this->params[$app][$key])) {
					$result = $this->params[$app][$key]['val'];
				}
			} elseif (($app === 'app') && isset($this->params[''])) {
				if (isset($this->params[''][$key])) {
					$result = $this->params[''][$key]['val'];
				}
			}
		}
		return $result;
	}

	public function getFrontConfig($key, $default_val = null) {
		$result = $default_val;
		if (is_array($this->params) && !empty($this->params)) {
			$app = 'app';
			$key = str()->toLower(str()->trim($key));
			if (isset($this->params[$app])) {
				if (isset($this->params[$app][$key])) {
					$result = $this->params[$app][$key]['val'];
				}
			} elseif (($app === 'app') && isset($this->params[''])) {
				if (isset($this->params[''][$key])) {
					$result = $this->params[''][$key]['val'];
				}
			}
		}
		return $result;
	}

	public function load() {
		$app_config = new AppConfig();
		$this->params = $app_config->getItems();
		return $this;
	}

}
