<?php

namespace App\Model\Component;

class MetadataPriceMode extends \Sky4\Model\Component {

	public function editableFieldsNames() {
		return $this->fieldsNames();
	}

	public function fields() {
		return [
			'metadata_price_mode_description' => [
				'attrs' => ['rows' => '5'],
				'elem' => 'text_area',
				'label' => 'Описание',
				'params' => [
					'parser' => true
				]
			],
			'metadata_price_mode_key_words' => [
				'elem' => 'text_field',
				'label' => 'Ключевые слова',
				'params' => [
					'rules' => ['length' => ['max' => 255]]
				]
			],
			'metadata_price_mode_robots' => [
				'elem' => 'text_field',
				'label' => 'Robots',
				'params' => [
					'rules' => ['length' => ['max' => 255]]
				]
			],
			'metadata_price_mode_title' => [
				'elem' => 'text_field',
				'label' => 'Заголовок',
				'params' => [
					'rules' => ['length' => ['max' => 255]]
				]
			]
		];
	}

	public function formStructure() {
		return [
			['type' => 'label', 'text' => $this->title()],
			['type' => 'field', 'name' => 'metadata_price_mode_title'],
			['type' => 'field', 'name' => 'metadata_price_mode_key_words'],
			['type' => 'field', 'name' => 'metadata_price_mode_description'],
			['type' => 'field', 'name' => 'metadata_price_mode_robots']
		];
	}

	public function title() {
		return 'Метаданные для товаров';
	}

}
