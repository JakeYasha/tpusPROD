<?php

namespace App\Model;
class Subgroup extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait,
	 Component\TimestampActionTrait,
	 Component\MetadataTrait,
	 Component\TextTrait;

	public function fields() {
		return [
			'group_id' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_2',
					'default_val' => 0
				],
				'elem' => 'text_field',
				'label' => 'Подгруппа',
				'params' => [
					'rules' => ['int']
				]
			]
		];
	}

	public function rels() {
		return [
			'group' => [
				'keys' => ['group_id' => 'id'],
				'model_alias' => 'group',
				'title' => 'Группы'
			]
		];
	}

	public function relWithParentModel() {
		return [
			'keys' => ['group_id' => 'id'],
			'model_alias' => 'group'
		];
	}
	
	public function title() {
		return 'Подгруппы';
	}
}
