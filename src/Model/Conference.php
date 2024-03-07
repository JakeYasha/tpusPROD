<?php

namespace App\Model;

class Conference extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ActiveTrait,
	 Component\TextTrait,
	 Component\ExtendedNameTrait,
	 Component\MetadataTrait,
	 Component\OnIndexTrait,
	 Component\TimestampActionTrait,
	 Component\TimestampIntervalTrait,
	 Component\UserDataTrait;

	public function cols() {
		return [
			'name' => ['label' => 'Название'],
			'flag_is_active' => ['label' => 'На сайте', 'type' => 'flag'],
			'flag_show_answers' => ['label' => 'Показывать ответы', 'type' => 'flag'],
			'flag_is_on_index' => ['label' => 'На главной', 'type' => 'flag']
		];
	}

	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function fields() {
		return [
			'flag_show_answers' => [
				'elem' => 'single_check_box',
				'label' => 'Показывать ответы',
				'default_val' => 1
			],
			'participants' => [
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_2'
				],
				'elem' => 'tiny_mce',
				'label' => 'Участники',
				'params' => [
					'parser' => true
				]
			],
		];
	}

	public function formStructure() {
		return [
				['type' => 'tab', 'name' => 'article', 'label' => 'Статья'],
				['type' => 'tab', 'name' => 'meta', 'label' => 'Метаданные'],
				['type' => 'component', 'name' => 'ExtendedName'],
				['type' => 'component', 'name' => 'TimestampInterval'],
				['type' => 'field', 'name' => 'participants', 'inline' => true, 'with_label' => true],
				['type' => 'label', 'text' => 'Флаги'],
				['type' => 'field', 'name' => 'flag_is_active'],
				['type' => 'field', 'name' => 'flag_show_answers'],
				['type' => 'field', 'name' => 'flag_is_on_index'],
				['type' => 'component', 'name' => 'Text', 'tab_name' => 'article'],
				['type' => 'component', 'name' => 'Metadata', 'tab_name' => 'meta'],
		];
	}

	public function editableFieldsNames() {
		return $this->fieldsNames();
	}

	public function getFields() {
		$fields = parent::getFields();
		$fields['name']['label'] = 'Заголовок';
		$fields['timestamp_ending']['default_val'] = \Sky4\Helper\DeprecatedDateTime::shiftMonths(+1);
		$fields['text']['label'] = 'Текст статьи';
		$fields['text']['params']['rules'] = [];

		return $fields;
	}

	public function rels() {
		return [
			'conference-question' => [
				'keys' => ['id' => 'conference_id'],
				'model_alias' => 'conference-question',
				'title' => 'Вопросы'
			]
		];
	}

	public function title() {
		return 'Конференции';
	}

}
