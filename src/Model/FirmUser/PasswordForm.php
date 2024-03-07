<?php

namespace App\Model\FirmUser;

class PasswordForm extends \Sky4\Model\Form {

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Восстановить',
				'attrs' => [
					'class' => 'send js-ajax-send btn btn_primary'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/restore-password/',
			'method' => 'post'
		];
	}

	public function editableFieldsNames() {
		return ['email'];
	}

	public function fields() {
		return array(
			'email' => [
                'attrs' => ['class' => 'form__control form__control_modal'],
				'elem' => 'text_field',
				'label' => 'Email',
				'params' => [
					'rules' => ['email', 'required']
				]
			]
		);
	}

	// -------------------------------------------------------------------------

	public function render() {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('height', 345)
						->set('heading', 'Восстановление пароля')
						->set('sub_heading', 'Введите ваш email')
						->setTemplate('firm_user_password_form', 'forms')
						->render();
	}

}
