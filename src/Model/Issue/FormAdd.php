<?php

namespace App\Model\Issue;

use App\Model\Issue;
use CDate;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model;
use Sky4\Model\Utils;

class UserForm extends \Sky4\Model\Form {


	public function __construct(Model $model = null, $params = null) {
		if (!($this->model() instanceof Issue)) {
			$this->setModel(new Issue());
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
			'action' => '/issue/submit/',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
        $fields = $this->model()->getFields();

		$fields['name']['label'] = 'Название';
		$fields['name']['params']['rules'] = ['length' => ['max' => 255, 'min' => 2], 'required'];
        $fields['name']['attrs']['class'] = 'form__control form__control_modal';

		if (!isset($fields['name']['attrs'])) {
			$fields['name']['attrs'] = [];
		}
		$fields['name']['attrs']['data-validate-on-focus-out'] = 'true';

		$fields['number']['label'] = 'Номер выпуска';
		$fields['number']['params']['rules'] = ['required'];
        $fields['number']['attrs']['class'] = 'form__control form__control_modal';

		$fields['short_text']['label'] = 'Описание выпуска';
		$fields['short_text']['params']['rules'] = ['length' => ['max' => 2000, 'min' => 10], 'required'];
        $fields['short_text']['attrs']['class'] = 'form__control form__control_modal';
		if (!isset($fields['short_text']['attrs'])) {
			$fields['short_text']['attrs'] = [];
		}
		$fields['short_text']['attrs']['data-validate-on-focus-out'] = 'true';
        
        $fields['id_service']['elem'] = 'hidden_field';
        $fields['id_city']['elem'] = 'hidden_field';

		$result = [
            'name' => $fields['name'],
            'number' => $fields['number'],
            'short_text' => $fields['short_text'],
            'id_service' => $fields['id_service'],
            'id_city' => $fields['id_city'],
        ];
		return $result;
	}

	// -------------------------------------------------------------------------

	public function render() {
        if ($this->model()->exists()) {
			$images = Utils::getObjectsByIds($this->model()->val('image'));
			$full_images = Utils::getObjectsByIds($this->model()->val('full_image'));
			$edit_row = $this->model()->prepare($this->model(), isset($images[$this->model()->val('image')]) ? $images[$this->model()->val('image')]->iconLink('-thumb') : null, isset($full_images[$this->model()->val('full_image')]) ? $full_images[$this->model()->val('full_image')]->iconLink('-thumb') : null);
			$image_url = $edit_row['image'] ? $edit_row['image'] : '/img/no_img.png';
			$full_image_url = $edit_row['full_image'] ? $edit_row['full_image'] : '/img/no_img.png';
		} else {
			$edit_row = [];
			$image_url = '/img/no_img.png';
			$full_image_url = '/img/no_img.png';
		}
        
		return $this->view()
						->set('attrs', $this->getAttrs())
                        ->set('mode', $this->model()->exists() ? 'edit' : 'add')
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('heading', '')
						->set('sub_heading', '')
						->set('image_url', $image_url)
						->set('full_image_url', $full_image_url)
						->setTemplate('issue_add_form', 'forms')
						->render();
	}

}
