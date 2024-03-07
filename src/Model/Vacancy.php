<?php

namespace App\Model;
class Vacancy extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ActiveTrait,
	 Component\NameTrait,
	 Component\TimestampIntervalTrait,
	 Component\TimestampActionTrait;

	public function cols() {
		return [
			'name' => [
				'label' => 'Должность'
			],
			'flag_is_active' => [
				'label' => 'На сайте',
				'type' => 'flag'
			]
		];
	}

	public function formStructure() {
		return [
			['type' => 'tab', 'name' => 'conditions', 'label' => 'Требования/условия'],
			['type' => 'tab', 'name' => 'contacts', 'label' => 'Контакты'],
			['type' => 'component', 'name' => 'Name'],
			['type' => 'component', 'name' => 'TimestampInterval'],
			['type' => 'component', 'name' => 'TimestampAction'],
			['type' => 'component', 'name' => 'Active'],
			['type' => 'field', 'name' => 'responsibility', 'inline' => true, 'with_label' => true],
			['type' => 'field', 'name' => 'requirements', 'tab_name' => 'conditions', 'inline' => true, 'with_label' => true],
			['type' => 'field', 'name' => 'desirable', 'tab_name' => 'conditions', 'inline' => true, 'with_label' => true],
			['type' => 'field', 'name' => 'terms', 'tab_name' => 'conditions', 'inline' => true, 'with_label' => true],
			['type' => 'field', 'name' => 'contact', 'tab_name' => 'contacts', 'inline' => true, 'with_label' => true],
		];
	}

	public function getFields() {
		$fields = parent::getFields();
		$fields['name']['label'] = 'Должность';
		$fields['timestamp_ending']['default_val'] = \Sky4\Helper\DeprecatedDateTime::shiftMonths(+1);

		return $fields;
	}

	public function editableFieldsNames() {
		return $this->fieldsNames();
	}

	public function fields() {
		$fields['responsibility'] = ['label' => 'Обязанности', 'elem' => 'tiny_mce', 'col' => ['flags' => 'not_null', 'type' => 'text_2'], 'params' => ['parser' => true]];
		$fields['requirements'] = ['label' => 'Требования', 'elem' => 'tiny_mce', 'col' => ['flags' => 'not_null', 'type' => 'text_2'], 'params' => ['parser' => true]];
		$fields['desirable'] = ['label' => 'Желательно', 'elem' => 'tiny_mce', 'col' => ['flags' => 'not_null', 'type' => 'text_2'], 'params' => ['parser' => true]];
		$fields['terms'] = ['label' => 'Условия', 'elem' => 'tiny_mce', 'col' => ['flags' => 'not_null', 'type' => 'text_2'], 'params' => ['parser' => true]];
		$fields['contact'] = ['label' => 'Контактная информация', 'elem' => 'tiny_mce', 'col' => ['flags' => 'not_null', 'type' => 'text_2'], 'attrs' => ['style' => 'height: 200px;'], 'params' => ['parser' => true]];

		return $fields;
	}

	public function title() {
		return 'Вакансии';
	}

}
