<?php

namespace App\Action;

class Issue extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\Issue());
	}

	public function execute() {
		throw new \Sky4\Exception();
	}

}
