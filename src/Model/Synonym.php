<?php

namespace App\Model;

class Synonym extends \Sky4\Model\Composite {

	use Component\IdTrait;

	public function cols() {
		return [
			'search' => ['label' => 'Поиск'],
			'replace' => ['label' => 'Замена']
		];
	}

	public function orderableFieldsNames() {
		return ['search', 'replace'];
	}

	public function fields() {
		return [
			'name' => [
				'elem' => 'hidden_field',
				'label' => 'Название',
				'params' => [
					'rules' => ['length' => ['max' => 255, 'min' => 1]]
				]
			],
			'search' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'search',
					'type' => 'string(255)',
				],
				'elem' => 'text_field',
				'label' => 'Поиск'
			],
			'replace' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'replace',
					'type' => 'string(255)',
				],
				'elem' => 'text_field',
				'label' => 'Замена'
			],
            'is_suggest' => [
				'elem' => 'single_check_box',
				'label' => 'Используется в подсказках для операторов онлайн справочной',
				'default_val' => 0
			],
		];
	}

	public function filterFields() {
		return [
			'search' => [
				'elem' => 'text_field',
				'field_name' => 'search',
				'label' => 'Название для поиска'
			]
		];
	}

	public function filterFormStructure() {
		return [
			['type' => 'field', 'name' => 'search']
		];
	}

	public function formStructure() {
		return [
			['type' => 'field', 'name' => 'search'],
			['type' => 'field', 'name' => 'replace'],
			['type' => 'field', 'name' => 'is_suggest']
		];
	}

	public function beforeInsert(&$vals, $parent_object = null) {
		if (!isset($vals['name'])) {
			$vals['name'] = isset($vals['search']) ? $vals['search'] : '';
		}
		return parent::beforeInsert($vals, $parent_object);
	}

	public function getList() {
		$res = [];

		$items = $this->reader()->objects();

		foreach ($items as $ob) {
			$res[$ob->val('search')][] = $ob->val('replace');
		}

		return $res;
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Синонимы';
	}

}
