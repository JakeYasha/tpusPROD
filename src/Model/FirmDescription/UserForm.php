<?php

namespace App\Model\FirmDescription;

use App\Model\Firm;
use App\Model\FirmDescription;
use App\Model\FirmFile;
use App\Model\Image;
use Sky4\Model;

class UserForm extends \Sky4\Model\Form {

	public function __construct(Model $model = null, $params = null) {
		if (!($this->model() instanceof FirmDescription)) {
			$this->setModel(new FirmDescription());
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
			'action' => '/firm-user/submit/' . $this->model()->alias() . '/?redirect=/firm-user/info/?mode=description&success',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		return [
			'id' => ['elem' => 'hidden_field'],
			'id_firm' => ['elem' => 'hidden_field'],
			'text' => [
				'attrs' => ['style' => 'height: 150px'],
				'elem' => 'tiny_mce',
				'label' => 'Описание',
				'params' => [
					'parser' => true
				]
			],
		];
	}

	// -------------------------------------------------------------------------

	public function render(Firm $firm) {
		$image_client = new Image();
		$image_ratiss = new Image();
		$where = ['AND', '`id_firm` = :id_firm', 'id_price = :nil', 'source = :source'];
		$params_client = [':id_firm' => $firm->id(), ':source' => 'client', ':nil' => 0];
		$params_ratiss = [':id_firm' => $firm->id(), ':source' => 'ratiss', ':nil' => 0];
		$image_client->reader()->setWhere($where, $params_client)->objectByConds();
		$image_ratiss->reader()->setWhere($where, $params_ratiss)->objectByConds();

		$ff = new FirmFile();
		$images = $ff->reader()
				->setWhere(['AND', '`id_firm` = :id_firm', 'type = :type'], [':id_firm' => $firm->id(), ':type' => 'image'])
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('has_default_logo', ($image_client->exists() && $image_ratiss->exists()))
						->set('fields', $this->renderFields())
						->set('logo_path', $firm->val('file_logo') ? $firm->val('file_logo') : '/img/no_img.png')
						->set('id_firm', $firm->id())
						->set('firm_images', $images)
						->setTemplate('description_form_user', 'forms')
						->render();
	}

}
