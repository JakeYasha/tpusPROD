<?php

namespace App\Model;

class FirmManagerRole extends \Sky4\Model\Composite {

	use Component\IdTrait;

	public function cols() {
		return [
			'firm_manager_id' => ['label' => 'ID_MANAGER'],
			'service_role' => ['label' => 'Права службы'],
			'news_editor_role' => ['label' => 'Права редактора газеты'],
		];
	}

	public function fields() {
		return [
			'firm_manager_id' => [
				'col' => [
					'flags' => 'not_null primary_key',
					'name' => 'firm_manager_id',
					'type' => 'int_2',
				],
				'elem' => 'text_field',
				'label' => 'firm_manager_id'
			]
		];
	}

	public function hasServiceRole() {
		return (int)$this->val('service_role');
	}

	public function hasNewsEditorRole() {
		return (int)$this->val('news_editor_role');
	}

}
