<?php

namespace App\Action\FirmUser;

class SetUser extends \App\Action\FirmUser {

	public function execute($id) {
		$this->model()->reader()->object($id);
		if ($this->model()->exists() && app()->firmUser()->val('email') === $this->model()->val('email')) {
			$this->model()->userComponent()->saveInSession();
		}

		app()->response()->redirect(self::link('/'));
	}

}
