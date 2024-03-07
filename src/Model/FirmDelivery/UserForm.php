<?php

namespace App\Model\FirmDelivery;

use App\Model\Firm;
use App\Model\FirmDelivery;
use Sky4\Model;
use Sky4\Model\Form;

class UserForm extends Form {

	public function __construct(Model $model = null, $params = null) {
		if (!($this->model() instanceof FirmDelivery)) {
			$this->setModel(new FirmDelivery());
		}
		parent::__construct($model, $params);
	}

	public function editableFieldsNames() {
		return array_keys($this->fields());
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

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/firm-user/submit/' . $this->model()->alias() . '/?redirect=/firm-user/info/?mode=delivery&success',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		return [
			'id' => ['elem' => 'hidden_field'],
			'id_firm' => ['elem' => 'hidden_field'],
			'type' => [
				'elem' => 'check_boxes',
				'label' => 'Отметьте способы доставки, с которыми вы можете работать',
				'options' => FirmDelivery::types()
			],
			'text' => [
				'attrs' => ['style' => 'height: 150px'],
				'elem' => 'tiny_mce',
				'label' => 'Дополнительная информация по доставке и оплате',
				'params' => [
					'parser' => true
				]
			],
		];
	}

	// -------------------------------------------------------------------------

	public function render(Firm $firm) {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->setTemplate('delivery_form_user', 'forms')
						->render();
	}

}
