<?php

namespace App\Model\Request;

use App\Model\Request;

class FormAdd extends \Sky4\Model\Form {

	public function __construct($model = null, $params = null) {
		parent::__construct($model, $params);
		$this->setModel(new Request());
	}

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'Отправить',
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
			'action' => '/request/submit/',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$model = new Request();
		$result = [];
		$fields = $model->getFields();

		$fields['user_name']['label'] = 'Ф.И.О. контактного лица';
		$fields['user_name']['attrs']['class'] = 'form__control form__control_modal';
		$fields['user_name']['params']['rules'] = ['length' => ['max' => 255, 'min' => 2], 'required'];

		$fields['company_name']['label'] = 'Название организации';
		$fields['company_name']['attrs']['class'] = 'form__control form__control_modal';
		$fields['company_name']['params']['rules'] = ['length' => ['max' => 255, 'min' => 2], 'required'];
		$fields['company_email']['label'] = 'E-mail';
		$fields['company_email']['attrs']['class'] = 'form__control form__control_modal';
		$fields['company_email']['params']['rules'] = ['length' => ['max' => 255, 'min' => 2], 'required'];
		$fields['company_web_site_url']['label'] = 'Web-сайт';
		$fields['company_web_site_url']['attrs']['class'] = 'form__control form__control_modal';
		$fields['company_phone']['label'] = 'Тел./факс (с кодом города)';
		$fields['company_phone']['attrs']['class'] = 'form__control form__control_modal';
		$fields['company_phone']['params']['rules'] = ['length' => ['min' => 5], 'required'];
        
		$fields['company_activity']['attrs']['class'] = 'form__control form__control_modal';
        
		$fields['appointment']['attrs']['class'] = 'form__control form__control_modal';

		$fields['town']['params']['rules'] = ['length' => ['max' => 255, 'min' => 2], 'required'];
		$fields['town']['attrs']['class'] = 'form__control form__control_modal';

		$fields['user_email']['label'] = 'E-mail';
		$fields['user_email']['attrs']['class'] = 'form__control form__control_modal';
		$fields['company_activity']['label'] = 'Вид деятельности вашей организации';
		$fields['services']['label'] = 'Выберите услуги заинтересовавшие Вас:';
		$result['id_region_country'] = $fields['id_region_country'];
		$result['services'] = $fields['services'];
		$result['user_name'] = $fields['user_name'];
		$result['appointment'] = $fields['appointment'];
		$result['company_name'] = $fields['company_name'];
		$result['town'] = $fields['town'];
		$result['company_phone'] = $fields['company_phone'];
		$result['company_email'] = $fields['company_email'];
		$result['company_web_site_url'] = $fields['company_web_site_url'];
		$result['company_activity'] = $fields['company_activity'];
		$result['files'] = $fields['files'];

		return $result;
	}

	// -------------------------------------------------------------------------

	public function render($heading = null) {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', $heading)
						->set('sub_heading', '')
						->setTemplate('request_add_form', 'forms')
						->render();
	}

}
