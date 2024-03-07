<?php

namespace App\Action\Consumer;

use App\Model\Consumer\FormAdd;

class Submit extends \App\Action\Consumer {

	public function execute() {
		$params = app()->request()->processPostParams([
			'g-recaptcha-response' => ['type' => 'string']
		]);
		$form = new FormAdd($this->model());
		$form->setInputVals($_POST);
		if (!$form->validate()) {
			$form->errorHandler()->setError('', 'Пожалуйста проверьте правильность ввода данных в форму.');
		} else if (!app()->capcha()->isValid($params['g-recaptcha-response'])) {
			$form->errorHandler()->setError('', 'Вы робот?');
		} else if (!$this->model()->insert($form->getVals())) {
			$form->errorHandler()->setError('', 'Форма не отправлена, свяжитесь с администратором.');
		}

		if ($form->errorHandler()->hasErrors()) {
			$form->errorHandler()->saveErrorsInSession()
					->saveValsInSession($form->getVals());
			app()->response()->redirect('/consumer/');
		}

		app()->email()
				->setSubject('Новый вопрос по защите прав потребителей')
				->setTo(app()->config()->get('app.email.editor'))
				->setModel($this->model())
				->setTemplate('email_to_admin', 'consumer')
				->sendToQuery();

		app()->response()->redirect('/consumer/form-success/');
	}

}
