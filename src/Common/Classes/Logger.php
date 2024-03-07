<?php

namespace App\Classes;

class Logger {

	private $log_file = null;

	public function add($message, $level = 0) {
		if ($this->log_file === null) {
			$log_file = date("d-m");
		} else {
			$log_file = $this->log_file;
		}

		$log_file = new \Sky4\FileSystem\File(APP_DIR_PATH . '/src/Cron/log/' . $log_file . '.log');
		if (!$log_file->exists()) {
			file_put_contents($log_file->path(), PHP_EOL . 'log started at ' . date('d.m.Y H:i:s'));
			$files = glob(APP_DIR_PATH . '/cron/log/*');
			foreach ($files as $f) {
				if (filemtime($f) < (time() - 60 * 60 * 24 * 10)) {
					unlink($f);
				}
			}
		}

		file_put_contents($log_file->path(), PHP_EOL . str_repeat(' ', $level) . date("H:i:s ") . '[' . $message . ']', FILE_APPEND);
		echo PHP_EOL . str_repeat(' ', $level) . date("H:i:s ") . '[' . $message . ']';
	}

	public function setLogFileName($file_name) {
		$this->log_file = (string) $file_name;
	}

}
