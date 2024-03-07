<?php

namespace App\Action;

class Page extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\Page());
	}

	public function execute() {
		throw new \Sky4\Exception();
	}

	/**
	 * 
	 * @return \App\Model\Page
	 */
	public function model() {
		return parent::model();
	}

}
