<?php

namespace App\Action\FirmUser;

use App\Model\FirmUser\FormPasswordChanging;
use function app;

class Profile extends \App\Action\FirmUser {

	public function execute($mode = null) {
		app()->metadata()->setTitle('Личный кабинет - профиль');
		app()->breadCrumbs()
				->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
				->setElem('Профиль', '/firm-user/profile/');

		if ($mode === 'success') {
			$this->view()
					->set('form', app()->chunk()->set('message', 'Данные успешно сохранены!')->render('forms.common_form_success'))
					->setTemplate('profile')
					->save();
		} else {
			$errors = [
				'Пароли не совпадают',
				'Вы ввели не правильный текущий пароль'
			];

			$error_code = app()->request()->processGetParams([
						'error_code' => ['type' => 'int']
					])['error_code'];

			$form = new FormPasswordChanging();
			if ($error_code !== null) {
				$form->errorHandler()->setError('submit', $errors[$error_code], $error_code);
			}

			$this->view()
					->set('bread_crumbs', app()->breadCrumbs()->render(true))
					->set('form', $form->render())
					->setTemplate('profile')
					->save();
		}
	}

}
