<?php

namespace App\Model\Consumer;

use App\Model\Consumer;

class FormAdd extends \Sky4\Model\Form {

	public function __construct($model = null, $params = null) {
		parent::__construct($model, $params);
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Отправить вопрос',
				'attrs' => [
					'class' => 'send js-send btn btn_primary',
					'type' => 'submit'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/consumer/submit/',
			'enctype' => 'multipart/form-data',
			'method' => 'post',
			'class' => 'js-review-form'
		];
	}

	public function fields() {
		$result = [];
		$model = new Consumer();
		$fields = $model->getFields();

		$fields['user_name']['label'] = 'Ваше имя';
		$fields['user_name']['params']['rules'] = ['length' => ['max' => 255, 'min' => 2], 'required'];
        $fields['user_name']['attrs']['class'] = 'form__control form__control_modal';

		if (!isset($fields['user_name']['attrs'])) {
			$fields['user_name']['attrs'] = [];
		}

		$fields['user_email']['label'] = 'Ваш e-mail';
		$fields['user_email']['params']['rules'] = ['required', 'email'];
        $fields['user_email']['attrs']['class'] = 'form__control form__control_modal';

		$fields['user_phone']['label'] = 'Ваш телефон <span class="label-clarify">При указании контактного телефона, в последний четверг текущего месяца вам может перезвонить для ответа специалист отдела по защите прав потребителей</span>';
		$fields['user_phone']['params']['rules'] = ['length' => ['max' => 18, 'min' => 6]];
		$fields['user_phone']['attrs'] = ['class' => 'js-masked-phone form__control form__control_modal'];

		$fields['question']['label'] = 'Вопрос';
		$fields['question']['params']['rules'] = ['length' => ['max' => 2000, 'min' => 1], 'required'];
        $fields['question']['attrs']['class'] = 'form__control form__control_modal';
        
		if (!isset($fields['text']['attrs'])) {
			$fields['text']['attrs'] = [];
		}

		$result['user_name'] = $fields['user_name'];
		$result['user_email'] = $fields['user_email'];
		$result['user_phone'] = $fields['user_phone'];
		$result['question'] = $fields['question'];

		return $result;
	}

	// -------------------------------------------------------------------------

	public function render() {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', 'Ваш вопрос')
						->set('sub_heading', '')
						->setTemplate('common_form', 'forms')
						->render();
	}

}
