<?php

namespace App\Model;
class Poll extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ExtendedNameTrait,
	 Component\ExtendedTextTrait,
	 Component\MetadataTrait,
	 Component\StateTrait,
	 Component\TimestampActionTrait,
	 Component\TimestampIntervalTrait,
	 Component\VotingTrait;

	public function editableFieldsNames() {
		return $this->fieldsNames();
	}

	public function fields() {
		return array(
			'flag_in_archive' => array(
				'elem' => 'single_check_box',
				'label' => 'В архиве?'
			)
		);
	}

	public function formStructure() {
		return array(
			array('type' => 'tab', 'name' => 'texts', 'label' => 'Тексты'),
			array('type' => 'tab', 'name' => 'metadata', 'label' => 'Метаданные'),
			array('type' => 'component', 'name' => 'ExtendedName'),
			array('type' => 'component', 'name' => 'State'),
			array('type' => 'component', 'name' => 'TimestampInterval'),
			array('type' => 'label', 'text' => 'Дополнительные характеристики'),
			array('type' => 'field', 'name' => 'flag_in_archive'),
			array('type' => 'component', 'name' => 'TimestampAction'),
			array('type' => 'component', 'name' => 'Voting'),
			array('type' => 'component', 'name' => 'ExtendedText', 'tab_name' => 'texts'),
			array('type' => 'component', 'name' => 'Metadata', 'tab_name' => 'metadata')
		);
	}

	public function rels() {
		return array(
			'poll-answer' => array(
				'keys' => array('id' => 'poll_id'),
				'model_alias' => 'poll-answer',
				'title' => 'Ответы'
			),
			'poll-answer-vote' => array(
				'keys' => array('id' => 'poll_id'),
				'model_alias' => 'poll-answer-vote',
				'title' => 'Голоса'
			)
		);
	}

	public function title() {
		return 'Опросы';
	}

}
