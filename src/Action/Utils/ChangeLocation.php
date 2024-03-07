<?php

namespace App\Action\Utils;

class ChangeLocation extends \App\Action\Utils {

	public function execute($id) {
		app()->location()->clear();
		app()->location()->set($id);
		setcookie('theme_name', app()->stsService()->val('theme_name'), time()+24*60*60*1000, '/');
		app()->response()->redirect(app()->location()->linkPrefix(), 301);
	}

}
