<?php

namespace App\Model;

class PollAnswer extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait,
	 Component\StateTrait,
	 Component\TimestampActionTrait;

	public function editableFieldsNames() {
		return ['poll_id'];
	}

	public function fields() {
		return [
			'counter_votes' => [
				'elem' => 'text_field',
				'label' => 'Количество голосов',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'poll_id' => [
				'elem' => 'text_field',
				'label' => 'Опрос',
				'params' => [
					'rules' => ['int', 'required']
				]
			]
		];
	}

	public function formStructure() {
		return [
				['type' => 'field', 'name' => 'poll_id'],
				['type' => 'component', 'name' => 'Name'],
				['type' => 'component', 'name' => 'State'],
				['type' => 'component', 'name' => 'TimestampAction'],
				['type' => 'label', 'text' => 'Статистика'],
				['type' => 'field', 'name' => 'counter_votes']
		];
	}

	public function rels() {
		return [
			'poll' => [
				'keys' => ['poll_id' => 'id'],
				'model_alias' => 'poll',
				'title' => 'Опрос'
			],
			'poll-answer-vote' => [
				'keys' => [
					'id' => 'answer_id',
					'poll_id' => 'poll_id'
				],
				'model_alias' => 'poll-answer-vote',
				'title' => 'Голоса'
			]
		];
	}

	public function title() {
		return 'Ответы';
	}

}
