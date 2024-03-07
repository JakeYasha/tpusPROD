<?php

namespace App\Action;

class Request extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\Request());
	}

	public function execute() {
		throw new \Sky4\Exception();
	}

}
