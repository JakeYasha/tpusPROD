<?php

namespace App\Model;
class Group extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait,
	 Component\TimestampActionTrait,
	 Component\MetadataTrait;

	public function rels() {
		return [
			'subgroup' => [
				'keys' => ['id' => 'group_id'],
				'model_alias' => 'subgroup',
				'title' => 'Подгруппы'
			]
		];
	}
	
	public function title() {
		return 'Группы';
	}

}
