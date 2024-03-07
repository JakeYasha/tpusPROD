<?php

namespace App\Model\PriceRequest;

class FormAdd extends \Sky4\Model\Form {

	public function __construct($model = null, $params = null) {
		$this->setModel($model);
		parent::__construct($model, $params);
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Отправить',
				'attrs' => [
					'class' => 'send js-send js-ajax-send btn btn_primary',
					'type' => 'submit',
					'style' => 'margin-top: -3px;'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/price/request/submit/',
			'enctype' => 'multipart/form-data',
			'method' => 'post',
			'style' => 'height: 772px'
		];
	}

	public function fields() {
		$result = [];
		$this->setFieldProp('brief_text', 'val', $this->model()->val('brief_text'));
		$fields = $this->model()->getFields();

		$fields['user_name']['label'] = 'Ваше имя';
		$fields['user_name']['params']['rules'] = ['length' => ['max' => 255], 'required'];
        $fields['user_name']['attrs']['class'] = 'form__control form__control_modal';
		$fields['user_email']['label'] = 'Контактный e-mail';
		$fields['user_email']['params']['rules'] = ['length' => ['max' => 50], 'email'];
        $fields['user_email']['attrs']['class'] = 'form__control form__control_modal';
		$fields['user_phone']['label'] = 'Контактный телефон';
		$fields['user_phone']['params']['rules'] = ['length' => ['max' => 18, 'min' => 6]];
		$fields['user_phone']['attrs'] = ['class' => 'js-masked-phone form__control form__control_modal'];
		$fields['text']['label'] = 'Пожелания и комментарий к заказу';
		$fields['text']['attrs']['style'] = 'height: 150px;';
		$fields['text']['attrs']['rows'] = '5';
        $fields['text']['attrs']['class'] = 'form__control form__control_modal';
		$fields['brief_text']['label'] = 'Тема сообщения';
		$fields['brief_text']['val'] = $this->model()->val('brief_text');
        $fields['brief_text']['attrs']['class'] = 'form__control form__control_modal';

		$result['brief_text'] = $fields['brief_text'];
		$result['user_name'] = $fields['user_name'];
		$result['user_email'] = $fields['user_email'];
		$result['user_phone'] = $fields['user_phone'];
		$result['text'] = $fields['text'];

		if ($this->getParam('sms_mode') === true) {
			$fields['user_phone']['params']['rules'][] = 'required';
		}

		return $result;
	}

	// -------------------------------------------------------------------------

	public function render($heading = null, $sms_mode = null) {
		$hidden_fields = [
			'id_firm' => $this->model()->id_firm(),
			//'id_service' => $this->model()->val('id_service'),
			'id_price' => $this->model()->val('id_price')
		];

		if ($sms_mode !== null && $sms_mode) {
			$this->fields['user_phone']['params']['rules'][] = 'required';
		}

		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('hidden_fields', $hidden_fields)
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', $heading)
						->set('sub_heading', '')
						->setTemplate('price_request_add_form', 'forms')
						->render();
	}

}
