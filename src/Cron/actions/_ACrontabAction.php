<?php

class ACrontabAction {

	/**
	 *
	 * @var CDbConnection $db 
	 */
	protected $db;
	protected $crontab;

	function __construct() {
		$this->db = App::db();
	}

	public function run() {
		return $this;
	}

	public function log($message) {
		$this->crontab->log($message);
		return $this;
	}

	public function setCrontabInstance(ACrontab $crontab) {
		$this->crontab = $crontab;
	}

	protected function flipTable($table_name) {
		$this->db->query()->renameTable($table_name, 'del_' . $table_name);
		$this->db->query()->renameTable('tmp_' . $table_name, $table_name);
		$this->db->query()->dropTable('del_' . $table_name);

		return $this;
	}

	protected function createTempTable($table_name) {
		try {
			App::db()->query()->dropTable('tmp_' . $table_name);
		} catch (\Sky4\Exception $exc) {
			;
		}

		App::db()->query()->copyTable($table_name, 'tmp_' . $table_name);

		return $this;
	}
}