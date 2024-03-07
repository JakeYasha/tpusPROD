<?php

namespace App\Action\Consumer;

class FormSuccess extends \App\Action\Consumer {

	public function execute() {
		app()->metadata()
				->setTitle('Вопрос успешно отправлен!')
				->noIndex();
		$this->view()->setTemplate('form_send_success')->save();
	}

}
