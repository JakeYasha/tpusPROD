<?php

namespace App\Action\FirmUser;

class GetLoginForm extends \App\Classes\Action {

	public function execute() {
		$form = new \App\Model\FirmUser\LoginForm();
		die($form->render());
	}

}
