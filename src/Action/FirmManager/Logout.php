<?php

namespace App\Action\FirmManager;

class Logout extends \App\Action\FirmManager {

	public function execute() {
		app()->firmUser()->userComponent()->removeFromSession();
		app()->firmManager()->userComponent()->removeFromSession();
		setcookie('theme_name', app()->stsService()->val('theme_name'), time()+24*60*60*1000, '/');
		app()->response()->redirect('/');
	}

}
