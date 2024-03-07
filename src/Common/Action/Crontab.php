<?php

namespace App\Action;

class Crontab extends \Sky4\Controller\Action {

	protected $start_log = true;
	protected $class_name = null;
	protected $db = null;
	protected $file_name = null;
	protected $lock_file = null;
	protected $update_images_dir = null;
	protected $update_work_dir = null;

	public function __construct($internal_call = false) {
		parent::__construct();
		if (isset($_SERVER['REMOTE_ADDR']) && $internal_call !== true) {
			throw new \Sky4\Exception();
		}

		$this->db = app()->db();
		$this->update_work_dir = APP_DIR_PATH.'/update/utf8';
		$this->update_images_dir = APP_DIR_PATH.'/update/ratiss_image';
		$this->class_name = get_class($this);
		$this->lock_file = new \Sky4\FileSystem\File(APP_DIR_PATH.'/src/Cron/locks/'.md5($this->class_name).'.lock');
	}

	public function execute() {
		throw new \Sky4\Exception();
	}

	/**
	 * 
	 * @return \Sky4\Db\Connection
	 */
	protected function db() {
		if ($this->db === null) {
			$this->db = app()->db();
		}

		return $this->db;
	}

	protected function dir() {
		return $this->update_work_dir;
	}

	protected function imagesDir() {
		return $this->update_images_dir;
	}

	protected function fileName() {
		return $this->file_name;
	}

	protected function startAction() {
		if ($this->start_log) {
			$this->startLog($this->class_name);
		}

		if (!$this->isLocked()) {
			$this->lock();
		} else {
			app()->log(get_class($this).' заблокирован (cron/locks/)');
		}

		return $this;
	}

	protected function endAction() {
		self::unlock();
		$this->log('===');
		return $this;
	}

	public function startLog($className) {
		app()->log($className);
		return $this;
	}

	protected function lock() {
		//file_put_contents($this->lock_file->path(), 1);
		return $this;
	}

	protected function unlock() {
//		if ($this->lock_file->exists()) {
//			$this->lock_file->remove();
//		}
		return $this;
	}

	protected function isLocked() {
		return $this->lock_file->exists();
	}

	protected function setLogFile($log_file_name) {
		$this->log_file = $log_file_name;
		return $this;
	}

	protected function setStartLog($bool = true) {
		$this->start_log = $bool;
		return $this;
	}

	protected function log($message, $level = 0) {
		app()->log($message, $level);
		return $this;
	}

	protected function flipTable($table_name) {
		$this->db->query()->renameTable($table_name, 'del_'.$table_name);
		$this->db->query()->renameTable('tmp_'.$table_name, $table_name);
		$this->db->query()->dropTable('del_'.$table_name);

		return $this;
	}

	protected function createTempTable($table_name) {
		try {
			app()->db()->query()->dropTable('tmp_'.$table_name);
		} catch (\Sky4\Exception $exc) {
			;
		}

		app()->db()->query()->copyTable($table_name, 'tmp_'.$table_name);

		return $this;
	}

	protected function loadData($file_name, $table_name = null) {
		if ($table_name === null) {
			$table_name = $file_name;
		}
		$this->log('загрузка '.$file_name.' в '.$table_name);

		$table_fields = app()->db()->query()->showCols($table_name);
		$fields = [];
		foreach ($table_fields as $field) {
			$fields[] = $field['Field'];
		}
		$field_str = implode(',', $fields);
		$dump_file = new \Sky4\FileSystem\File($this->dir().'/'.$file_name.'.txt');

		$i = 0;
		if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
			while ($data = fgetcsv($handle, 50000, "	")) {
				if (count($data) !== count($fields)) {
					$this->log($file_name.'.txt строка '.$i.' не соответствие полей');
				} else {
					$j = 0;
					$data_string = [];
					foreach ($fields as $f) {
						$data_string[] = ':'.$f;
						$data_params[':'.$f] = $data[$j];
						$j++;
					}
					$data_string = implode(",", $data_string);
					$query = "REPLACE INTO $table_name($field_str) VALUES($data_string)";
					if ($field_str && count($data)) {
						try {
							app()->db()->query()->setText($query)->execute($data_params);
						} catch (PDOException $exc) {
							$this->log($exc->getMessage());
						}
					}
				}

				$i++;
			}
			fclose($handle);
		}

		$this->log('обработано записей: '.$i);

		return $this;
	}
    
    protected function loadDataExt($file_name, $table_name = null) {
		if ($table_name === null) {
			$table_name = $file_name;
		}
        $bad_fields = [];
        if ($table_name == 'sts_hist_calls') {
            $bad_fields = ['asterisk_id','dispatcher','number'];
        }
		$this->log('загрузка '.$file_name.' в '.$table_name);

		$table_fields = app()->db()->query()->showCols($table_name);
		$fields = [];
		foreach ($table_fields as $field) {
            if (!in_array($field['Field'], $bad_fields)) {
                $fields[] = $field['Field'];
            }
		}
		$field_str = implode(',', $fields);
		$dump_file = new \Sky4\FileSystem\File($this->dir().'/'.$file_name.'.txt');

		$i = 0;
		if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
			while ($data = fgetcsv($handle, 50000, "	")) {
				if (count($data) !== count($fields)) {
					$this->log($file_name.'.txt строка '.$i.' не соответствие полей');
				} else {
					$j = 0;
					$data_string = [];
					foreach ($fields as $f) {
						$data_string[] = ':'.$f;
						$data_params[':'.$f] = $data[$j];
						$j++;
					}
					$data_string = implode(",", $data_string);
					$query = "REPLACE INTO $table_name($field_str) VALUES($data_string)";
					if ($field_str && count($data)) {
						try {
							app()->db()->query()->setText($query)->execute($data_params);
						} catch (PDOException $exc) {
							$this->log($exc->getMessage());
						}
					}
				}

				$i++;
			}
			fclose($handle);
		}

		$this->log('обработано записей: '.$i);

		return $this;
	}

}
