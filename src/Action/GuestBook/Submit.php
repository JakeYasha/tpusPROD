<?php

namespace App\Action\GuestBook;

class Submit extends \App\Action\GuestBook {

	public function execute() {
		$params = app()->request()->processPostParams([
			'g-recaptcha-response' => ['type' => 'string']
		]);
		$form = new \App\Model\GuestBook\FormAdd($this->model());
		$form->setEditableFieldsNames(['user_name', 'user_email', 'subject', 'text'])
				->setInputVals($_POST);

		if (!$form->validate()) {
			$form->errorHandler()->setError('', 'Проверьте правильность ввода данных в форму.');
		} else if (!app()->capcha()->isValid($params['g-recaptcha-response'])) {
			$form->errorHandler()->setError('', 'Вы робот?');
		} else if (($form->getVals() === null) || !is_array($form->getVals())) {
			$form->errorHandler()->setError('', 'Форма не отправлена, свяжитесь с администратором.');
		} else if (!$this->model()->insert($form->getVals())) {
			$form->errorHandler()->setError('', 'Форма не отправлена, свяжитесь с администратором.');
		}

		if ($form->errorHandler()->hasErrors()) {
			$form->errorHandler()->saveErrorsInSession()
					->saveValsInSession($form->getVals());
			app()->response()->redirect('/guest-book/');
		}

		app()->email()
				->setSubject('Добавлен новый отзыв на сайте Tovaryplus.ru')
				->setTo(app()->config()->get('app.email.administrator'))
				->setModel($form->model())
				->setTemplate('email_to_admin', 'guestbook')
				->sendToQuery();

		app()->response()->redirect('/guest-book/form-success/');
	}

}
