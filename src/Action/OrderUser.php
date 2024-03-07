<?php

namespace App\Action;

use App\Classes\Action;
use App\Model\OrderUser as OrderUserModel;
use function app;

class OrderUser extends Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new OrderUserModel());
	}

	public function execute() {
		$this->model()->getByCookie();

		app()->frontController()->layout()->setTemplate('order');
		return $this->view()
						->setTemplate('step1', 'order')
						->set('name', $this->model()->val('name'))
						->set('email', $this->model()->val('email'))
						->set('phone', $this->model()->val('phone'))
						->save();
	}

}
