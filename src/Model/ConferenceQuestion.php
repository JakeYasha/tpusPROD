<?php

namespace App\Model;

class ConferenceQuestion extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ActiveTrait,
	 Component\TimestampActionTrait,
	 Component\UserDataTrait;

	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function cols() {
		return [
			'user_name' => [
				'label' => 'Пользователь'
			],
			'timestamp_inserting' => [
				'label' => 'Дата вопроса',
				'style_class' => 'date-time'
			],
			'flag_show_answer' => [
				'label' => 'Показывать ответ',
				'type' => 'flag'
			],
			'flag_is_active' => [
				'label' => 'На сайте',
				'type' => 'flag'
			],
		];
	}

	public function fields() {
		return [
			'answer' => [
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_2'
				],
				'elem' => 'tiny_mce',
				'label' => 'Ответ',
				'params' => [
					'parser' => true
				]
			],
			'conference_id' => [
				'elem' => 'hidden_field',
				'label' => 'Конференция',
				'params' => [
					'rules' => ['int']
				]
			],
			'question' => [
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_2'
				],
				'elem' => 'tiny_mce',
				'label' => 'Вопрос',
				'params' => [
					'parser' => true
				]
			],
			'author_name' => [
				'elem' => 'text_field',
				'label' => 'Автор ответа',
				'params' => [
					'rules' => ['length' => ['max' => 255]]
				]
			],
			'flag_show_answer' => [
				'elem' => 'single_check_box',
				'label' => 'Показывать ответ',
				'default_val' => 0
			],
		];
	}

	public function formStructure() {
		return [
				['type' => 'field', 'name' => 'conference_id'],
				['type' => 'label', 'text' => 'Вопрос'],
				['type' => 'field', 'name' => 'user_name'],
				['type' => 'field', 'name' => 'user_email'],
				['type' => 'field', 'name' => 'question'],
			//
			['type' => 'label', 'text' => 'Ответ'],
				['type' => 'field', 'name' => 'author_name'],
				['type' => 'field', 'name' => 'answer'],
			//
			['type' => 'label', 'text' => 'Флаги'],
				['type' => 'field', 'name' => 'flag_show_answer'],
				['type' => 'field', 'name' => 'flag_is_active'],
			//
			['type' => 'component', 'name' => 'TimestampAction'],
		];
	}

	public function getFields() {
		$fields = parent::getFields();

		$fields['user_name']['label'] = 'Автор вопроса';

		return $fields;
	}

	public function editableFieldsNames() {
		return $this->fieldsNames();
	}

	public function relWithParentModel() {
		return [
			'keys' => ['conference_id' => 'id'],
			'model_alias' => 'conference'
		];
	}

	public function rels() {
		return [
			'conference' => [
				'keys' => ['conference_id' => 'id'],
				'model_alias' => 'conference',
				'title' => 'Конференция'
			]
		];
	}

	public function title() {
		return 'Вопрос конференции';
	}

	public function alias() {
		return 'conference-question';
	}

	public function table() {
		return 'conferencequestion';
	}

}
