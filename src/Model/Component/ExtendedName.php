<?php

namespace App\Model\Component;

class ExtendedName extends \Sky4\Model\Component {

	public function editableFieldsNames() {
		return $this->fieldsNames();
	}

	public function fields() {
		$c = $this->fieldPropCreator();
		return [
			'name' => $c->stringField('Название', 255, ['rules' => ['required']]),
			'name_in_url' => $c->stringField('Название в URL'),
		];
	}

	public function filterFields() {
		return [
			'name' => [
				'elem' => 'text_field',
				'field_name' => 'name',
				'label' => 'Название'
			]
		];
	}

	public function filterFormStructure() {
		return [
				['type' => 'field', 'name' => 'name']
		];
	}

	public function formStructure() {
		return [
			$this->formStructureCreator()->label($this->title()),
			'name',
			'name_in_url'
		];
	}

	public function orderableFieldsNames() {
		return ['name'];
	}

	public function title() {
		return 'Название';
	}

}
