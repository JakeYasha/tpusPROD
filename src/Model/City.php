<?php

namespace App\Model;

class City extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait;

	public function fields() {
		return [
			'genitive_case' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'type' => 'string(50)',
				],
				'elem' => 'text_field',
				'label' => 'Родительный'
			],
			'prepositional_case' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'type' => 'string(50)',
				],
				'elem' => 'text_field',
				'label' => 'Предложный'
			],
		];
	}

	public function formStructure() {
		return [
			['type' => 'field', 'name' => 'name'],
			['type' => 'field', 'name' => 'genitive_case'],
			['type' => 'field', 'name' => 'prepositional_case']
		];
	}

	public function title() {
		return 'Города (падежи)';
	}

	public function getList($_select = null, $_where = null, $_order_by = null, $_limit = null, $_offset = null, $_params = null) {
		parent::getList();
		$all = $this->reader()
				->setWhere(['AND', "`name` = :name"], [':name' => app()->location()->currentName()])
				->objects();

		$res = [];
		foreach ($all as $ob) {
			$res[str()->toLower($ob->name())] = [
				'prepositional' => $ob->val('prepositional_case'),
				'genitive' => $ob->val('genitive_case')
			];
		}

		return $res;
	}

}
