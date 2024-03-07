<?php

namespace App\Model\FirmCoords;

use App\Model\Firm;
use App\Model\FirmCoords;
use Sky4\Model;

class UserForm extends \Sky4\Model\Form {

	public function __construct(Model $model = null, $params = null) {
		if (!($this->model() instanceof FirmCoords)) {
			$this->setModel(new FirmCoords());
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
			'action' => '/firm-user/submit/' . $this->model()->alias() . '/?redirect=/firm-user/info/?mode=map&success',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		return [
			'id' => ['elem' => 'hidden_field'],
			'coords_latitude' => ['elem' => 'hidden_field'],
			'coords_longitude' => ['elem' => 'hidden_field'],
			'hash' => ['elem' => 'hidden_field']
		];
	}

	// -------------------------------------------------------------------------

	public function render(Firm $firm, FirmCoords $firm_coords) {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('firm_coords', $firm_coords)
						->set('firm', $firm)
						->setTemplate('firm_coords_form_user', 'forms')
						->render();
	}

}
