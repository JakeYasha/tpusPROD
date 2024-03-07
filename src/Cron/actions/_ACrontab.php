<?php

class ACrontab {

	private $verbose_log = false;
	private $log_file = null;
	private $start_log = true;

	public function run(ACrontabAction $object) {
		$object->setCrontabInstance($this);
		if ($this->start_log) {
			$this->startLog(get_class($object));
		}
		$className = get_class($object);
		if (!self::isLocked($className)) {
			self::lock($className);
			try {
				$object->run();
				self::unlock($className);
			} catch (\Sky4\Exception $exc) {
				$error = $exc->getTraceAsString();
				self::log($className . ':' . $exc->getMessage() . PHP_EOL . $error);
				self::unlock($className);
			}
		}
		return $this;
	}

	public function log($message) {
		if ($this->log_file === null) {
			$log_file = date("d-m");
		} else {
			$log_file = $this->log_file;
		}
		$log_file = APP_DIR_PATH . '/app/cron/log/' . $log_file . '.log';
		if (!file_exists($log_file)) {
			file_put_contents($log_file, date('d.m.Y H:i:s'));
			$files = glob(APP_DIR_PATH . '/cron/log/*');
			foreach ($files as $f) {
				if (filemtime($f) < (time() - 60 * 60 * 24 * 10)) {
					unlink($f);
				}
			}
		}

		if ($this->verbose_log) {
			echo "\n" . $message;
		} else {
			file_put_contents($log_file, PHP_EOL . date("H:i:s ") . $message, FILE_APPEND);
		}

		return $this;
	}

	public function startLog($className) {
		$this->log('== ' . $className . ' ==');
		return $this;
	}

	private static function lock($className) {
		file_put_contents(APP_DIR_PATH . '/app/cron/locks/' . md5($className) . '.lock', 1);
	}

	private static function unlock($className) {
		unlink(APP_DIR_PATH . '/app/cron/locks/' . md5($className) . '.lock');
	}

	private static function isLocked($className) {
		return file_exists($className);
	}

	public function setLogFile($log_file_name) {
		$this->log_file = $log_file_name;
		return $this;
	}

	public function setStartLog($bool = true) {
		$this->start_log = $bool;
		return $this;
	}

	public function setVerboseLog($bool = true) {
		$this->verbose_log = $bool;
		return $this;
	}

}
