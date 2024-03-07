<?php

namespace App\Model\PhotoContestItem;

class FormAdd extends \Sky4\Model\Form {

	public function controls() {
		return [
			'submit' => [
				'elem' => 'button',
				'label' => 'ГОТОВО',
				'attrs' => [
					'class' => 'send js-send btn btn_primary'
				]
			]
		];
	}

	public function attrs() {
		return [
			'accept-charset' => 'utf-8',
			'action' => '/photo-contest/submit-photo/',
			'method' => 'post'
		];
	}

	public function editableFieldsNames() {
		return ['user_name', 'user_email', 'nomination_id'];
	}

	public function fields() {
		return array(
			'user_name' => [
				'elem' => 'text_field',
				'label' => '',
				'params' => [
					'rules' => ['required', 'length' => ['max' => 255]]
				]
			],
			'user_phone' => [
				'attrs' => [
					'class' => 'js-masked-phone'
				],
				'elem' => 'text_field',
				'label' => '',
				'params' => [
					'rules' => ['required']
				]
			]
		);
	}

	// -------------------------------------------------------------------------

	public function render($nominations, $item, $filters) {
		return $this->view()
						->set('attrs', $this->getAttrs())
						->set('controls', $this->renderControls())
						->set('fields', $this->renderFields())
						->set('filters', $filters)
						->set('nominations', $nominations)
						->set('item', $item)
						->setTemplate('photo_contest_item_form', 'forms')
						->render();
	}

}
