<?php

namespace App\Model;
class StsGroups extends \Sky4\Model\Composite {
	
	public function idFieldsNames() {
		return ['id_group'];
	}

	public function fields() {
		return [
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
					'type' => 'string(256)',
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
	
	public function name() {
		return str()->firstCharToUpper(str()->toLower($this->val('name')));
	}

}
