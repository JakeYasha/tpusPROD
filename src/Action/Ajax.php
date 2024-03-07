<?php

namespace App\Action;

use Sky4\App,
	Sky4\Exception;

class Ajax extends \App\Classes\Action {

	use \App\Classes\Traits\ControllerExtension,
	 \App\Classes\Traits\Ajax;

	public function execute() {
		throw new Exception(Exception::TYPE_BAD_URL);
	}

}
