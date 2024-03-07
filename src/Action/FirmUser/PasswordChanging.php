<?php

namespace App\Action\FirmUser;

use App\Action\FirmUser as FirmUserAction;
use App\Model\FirmUser;
use function app;

class PasswordChanging extends FirmUserAction {

	public function execute() {
		$vals = app()->request()->processPostParams([
			'password' => ['type' => 'string'],
			'new_password' => ['type' => 'string'],
			'new_password_repeat' => ['type' => 'string']
		]);

		$result = false;
		if (app()->firmUser()->exists()) {
			$firm_user = new FirmUser();
			$firm_user->userComponent()->findByEmail(app()->firmUser()->val('email'));

			if ($firm_user->userComponent()->comparePassword($vals['password'])) {
				if ($vals['new_password'] === $vals['new_password_repeat']) {
					$firm_user->userComponent()->saveNewPasswordWithoutChecking($vals['new_password']);
					$result = true;
				} else {
					$error_code = 0;
				}
			} else {
				$error_code = 1;
			}
		} else {
			app()->response()->redirect('/#login');
		}

		if ($result) {
			app()->response()->redirect('/firm-user/profile/success/');
		}

		app()->response()->redirect('/firm-user/profile/fail/?error_code=' . $error_code);
	}

}
