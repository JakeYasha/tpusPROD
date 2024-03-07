<?php

namespace App\Action\FirmUser;

class Logout extends \App\Action\FirmUser {

	public function execute() {
		app()->firmUser()->userComponent()->removeFromSession();
		app()->response()->redirect('/');
	}

}
