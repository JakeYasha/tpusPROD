<?php

namespace App\Model\FirmReview;

use App\Model\FirmReview;
use Sky4\Model\Form;

class FormAdd extends Form {

	public function __construct($model = null, $params = null) {
		parent::__construct($model, $params);
		$this->setModel(new FirmReview());
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Отправить отзыв',
				'attrs' => [
					'class' => 'send js-ajax-send btn btn_primary',
					'type' => 'submit',
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/firm-review/submit/',
			'enctype' => 'multipart/form-data',
			'method' => 'post',
			'class' => 'js-review-form'
		];
	}

	public function fields() {
		$model = new FirmReview();
		$result = [];
		$fields = $model->getFields();

		$fields['user_name']['label'] = 'Ваше имя';
		$fields['user_name']['params']['rules'] = ['length' => ['max' => 255, 'min' => 2], 'required'];
        $fields['user_name']['attrs']['class'] = 'form__control form__control_modal';

		if (!isset($fields['user_name']['attrs'])) {
			$fields['user_name']['attrs'] = [];
		}
		$fields['user_name']['attrs']['data-validate-on-focus-out'] = 'true';

		$fields['user_email']['label'] = 'E-mail для обратной связи';
		$fields['user_email']['params']['rules'] = ['required', 'email'];
        $fields['user_email']['attrs']['class'] = 'form__control form__control_modal';

		$fields['text']['label'] = 'Отзыв';
		$fields['text']['params']['rules'] = ['length' => ['max' => 2000, 'min' => 10], 'required'];
        $fields['text']['attrs']['class'] = 'form__control form__control_modal';
		if (!isset($fields['text']['attrs'])) {
			$fields['text']['attrs'] = [];
		}
		$fields['text']['attrs']['data-validate-on-focus-out'] = 'true';

		$result['user_name'] = $fields['user_name'];
		$result['user_email'] = $fields['user_email'];
		$result['text'] = $fields['text'];

		return $result;
	}

	// -------------------------------------------------------------------------

	public function render($heading, $firm) {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', $heading)
						->set('firm', $firm)
						->set('sub_heading', '')
						->setTemplate('firm_review_add_form', 'forms')
						->render();
	}

}
