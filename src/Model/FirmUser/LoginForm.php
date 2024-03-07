<?php

namespace App\Model\FirmUser;

class LoginForm extends \Sky4\Model\Form {

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Войти',
				'attrs' => [
					'class' => 'send js-ajax-send btn btn_primary'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/firm-user/login/',
			'method' => 'post',
			'class' => 'clean-submit'
		];
	}

	public function editableFieldsNames() {
		return ['login', 'password'];
	}

	public function fields() {
		return array(
			'email' => [
				'elem' => 'text_field',
				'label' => 'Email',
				'params' => ['rules' => ['email']],
                'attrs' => ['class' => 'form__control form__control_modal']
			],
			'password' => [
				'elem' => 'password_field',
				'label' => 'Пароль',
				'params' => ['rules' => ['required']],
                'attrs' => ['class' => 'form__control form__control_modal']
			]
		);
	}

	// -------------------------------------------------------------------------

	public function render() {
        $view = $this->view();
        return $view
                        ->set('attrs', $this->getAttrs())
                        ->set('controls', $this->renderControls())
                        ->set('fields', $this->renderFields())
                        ->set('height', 395)
                        ->set('heading', 'Вход на сайт')
                        ->set('sub_heading', 'Введите ваш логин и пароль')
                        ->setTemplate('firm_user_login_form', 'forms')
                        ->render();
	}

}
