<?php

namespace App\Model\FirmReview;

use App\Model\Firm;
use App\Model\FirmReview;
use App\Model\PriceCatalog;
use Sky4\Model\Utils;

class FirmUserForm extends \Sky4\Model\Form {

	public function __construct(\Sky4\Model $model = null, $params = null) {
		if (!($this->model() instanceof FirmReview)) {
			$this->setModel(new FirmReview());
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
				'label' => $this->model()->exists() ? 'Добавить ответ' : 'Добавить',
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
			'action' => '/firm-user/submit/' . $this->model()->alias() . '/?redirect=/firm-user/review/?success',
			'enctype' => 'multipart/form-data',
			'method' => 'post'
		];
	}

	public function fields() {
		$fields = $this->model()->getFields();
		$fields['reply_text']['attrs']['class'] = 'js-tiny-mce-no-link';

		return [
			'id' => ['elem' => 'hidden_field'],
			'id_firm' => ['elem' => 'hidden_field'],
			'flag_is_reply_send' => ['elem' => 'hidden_field'],
			//
			'reply_user_name' => $fields['reply_user_name'],
			'reply_text' => $fields['reply_text'],
		];
	}

	// -------------------------------------------------------------------------

	public function render() {
		return $this->view()
						->set('mode', $this->model()->exists() ? 'edit' : 'add')
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						//
						->set('item', FirmReview::prepare($this->model()))
						->setTemplate('firm_review_form_user', 'forms')
						->render();
	}

}
