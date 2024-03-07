<?php

namespace App\Model;
class StsSubgroup extends \Sky4\Model\Composite {

	public function idFieldsNames() {
		return ['id_group', 'id_subgroup'];
	}
	
	public function id() {
		return $this->val('id_subgroup');
	}
	
	public function name() {
		return str()->firstCharToUpper(str()->toLower($this->val('name')));
	}

	public function fields() {
		return [
			'id_subgroup' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null primary_key',
					'name' => 'id_subgroup',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_subgroup'
			],
			'id_group' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null primary_key',
					'name' => 'id_group',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'id_group'
			],
			'name' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'name',
					'type' => 'string(512)',
				],
				'elem' => 'text_field',
				'label' => 'name'
			],
			'datetime' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'datetime',
					'type' => 'date_time',
				],
				'elem' => 'text_field',
				'label' => 'datetime'
			],
			'name_web' => [
				'col' => [
					'default_val' => '',
					'flags' => '',
					'name' => 'name_web',
					'type' => 'string(512)',
				],
				'elem' => 'text_field',
				'label' => 'name_web'
			],
			'info' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'info',
					'type' => 'string(3000)',
				],
				'elem' => 'text_field',
				'label' => 'info'
			],
			'text_title' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'text_title',
					'type' => 'string(500)',
				],
				'elem' => 'text_field',
				'label' => 'text_title'
			],
			'text_keywords' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'text_keywords',
					'type' => 'text_2',
				],
				'elem' => 'text_field',
				'label' => 'text_keywords'
			],
			'text_description' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'text_description',
					'type' => 'text_2',
				],
				'elem' => 'text_field',
				'label' => 'text_description'
			],
		];
	}

}
