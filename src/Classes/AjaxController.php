<?php

namespace App\Classes;

class AjaxController extends \CController {

	protected $result = [];

	public function __construct() {
		parent::__construct();
		$this->result = [
			'error_code' => 0,
			'error_message' => ''
		];
	}

	protected function hasErrors() {
		return $this->result['error_code'] ? true : false;
	}

	protected function renderResult() {
		echo json_encode($this->result);
		exit();
	}

	protected function setError($message, $code = 1) {
		$this->result['error_code'] = (int) $code;
		$this->result['error_message'] = (string) $message;
		return $this;
	}

}
