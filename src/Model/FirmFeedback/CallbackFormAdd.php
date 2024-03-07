<?php

namespace App\Model\FirmFeedback;

class CallbackFormAdd extends \Sky4\Model\Form {

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Отправить',
				'attrs' => [
					'class' => 'send js-ajax-send btn btn_primary',
					'type' => 'submit'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/firm-feedback/submit-callback/',
			'enctype' => 'application/x-www-form-urlencoded',
			'method' => 'post'
		];
	}

	public function fields() {
		$fields = [];

		$fields['user_name'] = ['elem' => 'text_field', 'label' => 'Ваше имя', 'params' => ['rules' => ['required', 'length' => ['max' => 25]]]];
        $fields['user_name']['attrs']['class'] = 'form__control form__control_modal';
		$fields['user_phone'] = ['elem' => 'text_field', 'label' => 'Ваш контактный телефон', 'params' => ['rules' => ['required']], 'attrs' => ['class' => 'js-masked-phone form__control form__control_modal']];

		return $fields;
	}

	// -------------------------------------------------------------------------

	public function render(\App\Model\Firm $firm, $heading = 'Заказать обратный звонок') {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', $heading)
						->set('referer', app()->request()->getReferer())
						->set('firm', $firm)
						->set('sub_heading', '')
						->setTemplate('firm_callback_add_form', 'forms')
						->render();
	}

}
