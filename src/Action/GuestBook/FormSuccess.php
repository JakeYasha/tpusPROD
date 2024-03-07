<?php

namespace App\Action\GuestBook;

class FormSuccess extends \App\Action\GuestBook {

	public function execute() {
		$this->view()
				->setTemplate('form_send_success')
				->save();
	}

}
