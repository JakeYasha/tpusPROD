<?php

namespace App\Action;

class StsService extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\StsService());
	}

	public function execute() {
		throw new \Sky4\Exception();
	}

}
