<?php

namespace App\Model\FirmUser;

class FormPasswordChanging extends \Sky4\Model\Form {

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/firm-user/password-changing/',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Сохранить',
				'attrs' => [
					'class' => 'send js-send btn btn_primary',
					'type' => 'submit'
				]
			]
		];
	}

	public function editableFieldsNames() {
		return array_keys($this->fields());
	}

	public function fields() {
		return [
			'password' => [
				'attrs' => ['class' => 'grey form__control form__control_modal'],
				'elem' => 'password_field',
				'label' => 'Текущий пароль',
				'params' => [
					'rules' => ['required']
				]
			],
			'new_password' => [
				'attrs' => ['class' => 'grey form__control form__control_modal'],
				'elem' => 'password_field',
				'label' => 'Новый пароль',
				'params' => [
					'rules' => ['length' => ['min' => 4], 'required']
				]
			],
			'new_password_repeat' => [
				'attrs' => ['class' => 'grey form__control form__control_modal'],
				'elem' => 'password_field',
				'label' => 'Повторите пароль',
				'params' => [
					'rules' => ['length' => ['min' => 4], 'required']
				]
			]
		];
	}

	public function structure() {
		return [
			['type' => 'field', 'name' => 'password'],
			['type' => 'field', 'name' => 'new_password'],
			['type' => 'field', 'name' => 'new_password_repeat']
		];
	}

	public function render() {
		return $this->view()
						->set('errors', $this->errorHandler()->getErrors())
						->set('heading', 'Изменение пароля')
						->set('mode', $this->model()->exists() ? 'edit' : 'add')
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->setTemplate('common_form_no_captcha', 'forms')
						//
						->render();
	}

}
