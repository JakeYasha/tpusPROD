<?php

namespace App\Action\FirmUser;

class GetRestoreForm extends \App\Classes\Action {

	public function execute() {
		$form = new \App\Model\FirmUser\PasswordForm();
		die($form->render());
	}

}
