<?php

namespace App\Model\GuestBook;

use App\Model\GuestBook;

class FormAdd extends \Sky4\Model\Form {

	public function __construct($model = null, $params = null) {
		parent::__construct($model, $params);
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Отправить отзыв',
				'attrs' => [
					'class' => 'send js-send btn btn_primary',
					'type' => 'submit',
					'style' => 'margin-right: 119px; margin-bottom: 20px;padding: 0;width: 193px;'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/guest-book/submit/',
			'enctype' => 'multipart/form-data',
			'method' => 'post',
			'class' => 'js-review-form'
		];
	}

	public function fields() {
		$result = [];
		$model = new GuestBook();
		$fields = $model->getFields();

		$fields['user_name']['label'] = 'Ваше имя';
		$fields['user_name']['params']['rules'] = ['length' => ['max' => 255, 'min' => 2], 'required'];

		if (!isset($fields['user_name']['attrs'])) {
			$fields['user_name']['attrs'] = [];
		}
		//$fields['user_name']['attrs']['data-validate-on-focus-out'] = 'true';

		$fields['user_email']['label'] = 'Ваш e-mail';
		$fields['user_email']['params']['rules'] = ['required', 'email'];

		$fields['subject']['label'] = 'Тема сообщения';
		$fields['subject']['params']['rules'] = ['required'];

		$fields['text']['label'] = 'Сообщение';
		$fields['text']['params']['rules'] = ['length' => ['max' => 2000, 'min' => 10], 'required'];
		if (!isset($fields['text']['attrs'])) {
			$fields['text']['attrs'] = [];
		}
		//$fields['text']['attrs']['data-validate-on-focus-out'] = 'true';

		$result['user_name'] = $fields['user_name'];
		$result['user_email'] = $fields['user_email'];
		$result['subject'] = $fields['subject'];
		$result['text'] = $fields['text'];

		return $result;
	}

	// -------------------------------------------------------------------------

	public function render() {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', 'Добавьте отзыв')
						->set('sub_heading', '')
						->setTemplate('common_form', 'forms')
						->render();
	}

}
