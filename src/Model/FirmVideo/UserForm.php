<?php

namespace App\Model\FirmVideo;

use App\Model\FirmVideo;
use Sky4\Model;
use Sky4\Model\Form;
use Sky4\Model\Utils;

class UserForm extends Form {

	public function __construct(Model $model = null, $params = null) {
		if (!($this->model() instanceof FirmVideo)) {
			$this->setModel(new FirmVideo());
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
				'label' => $this->model()->exists() ? 'Сохранить' : 'Добавить',
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
			'action' => '/firm-user/submit/' . $this->model()->alias() . '/?redirect=/firm-user/video/?success',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$fields = $this->model()->getFields();
		$fields['name']['label'] = 'Название видеоролика';
		$fields['text']['label'] = 'Описание видеороилка';
		$fields['text']['params']['rules'] = ['required'];

		return [
			'id' => ['elem' => 'hidden_field'],
			'id_firm' => ['elem' => 'hidden_field'],
			//'id_service' => ['elem' => 'hidden_field'],
			'id_city' => ['elem' => 'hidden_field'],
			//
			'name' => $fields['name'],
			'text' => $fields['text'],
			'video_youtube' => $fields['video_youtube'],
		];
	}

	// -------------------------------------------------------------------------

	public function render() {
		if ($this->model()->exists()) {
			$images = Utils::getObjectsByIds($this->model()->val('image'));
			$edit_row = $this->model()->prepare($this->model(), isset($images[$this->model()->val('image')]) ? $images[$this->model()->val('image')]->iconLink('-150x150') : null);
			$image_url = $edit_row['image'] ? $edit_row['image'] : '/img/no_img.png';
		} else {
			$edit_row = [];
			$image_url = '/img/no_img.png';
		}

		return $this->view()
						->set('mode', $this->model()->exists() ? 'edit' : 'add')
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						//
						->setTemplate('firm_video_form_user', 'forms')
						->render();
	}

}
