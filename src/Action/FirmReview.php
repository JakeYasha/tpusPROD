<?php

namespace App\Action;

use App\Classes\Action;
use Sky4\Exception;

class FirmReview extends Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\FirmReview());
	}

	public function execute() {
		throw new Exception();
	}

	/**
	 * 
	 * @return FirmReview
	 */
	public function model() {
		return parent::model();
	}

}
