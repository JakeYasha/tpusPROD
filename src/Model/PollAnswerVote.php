<?php

namespace App\Model;

class PollAnswerVote extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IpAddrTrait,
	 Component\TimestampActionTrait;

	public function editableFieldsNames() {
		return $this->fieldsNames();
	}

	public function fields() {
		return [
			'answer_id' => [
				'elem' => 'text_field',
				'label' => 'Ответ на опрос',
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
		return array(
				['type' => 'field', 'name' => 'poll_id'],
				['type' => 'field', 'name' => 'answer_id'],
				['type' => 'component', 'name' => 'IpAddr'],
				['type' => 'component', 'name' => 'TimestampAction']
		);
	}

	public function rels() {
		return array(
			'poll' => [
				'keys' => ['poll_id' => 'id'],
				'model_alias' => 'poll',
				'title' => 'Опрос'
			],
			'poll-answer' => [
				'keys' => [
					'answer_id' => 'id',
					'poll_id' => 'poll_id'
				],
				'model_alias' => 'poll-answer',
				'title' => 'Ответ'
			]
		);
	}

}
