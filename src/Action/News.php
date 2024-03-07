<?php

namespace App\Action;
use Sky4\Exception;

class News extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\Material());
	}

	public function execute() {
        if (!APP_IS_DEV_MODE) throw new Exception(Exception::TYPE_BAD_URL);
		app()->frontController()->layout()->setTemplate('news');
        
		$this->view()
                ->setTemplate('index')
				->save();
		return true;
	}

	/**
	 * 
	 * @return \App\Model\Material
	 */
	public function model() {
		return parent::model();
	}

}
