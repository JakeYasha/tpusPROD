<?php

namespace App\Model;
class FeedbackOptions extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait;

	public function fields() {
		return [
			'email' => [
				'elem' => 'text_field',
				'label' => 'Почта, куда доставлять',
				'params' => [
					'rules' => ['length' => ['max' => 255]]
				]
			],
			'subject' => [
				'elem' => 'text_field',
				'label' => 'Тема',
				'params' => [
					'rules' => ['length' => ['max' => 255]]
				]
			]
		];
	}

	public function formStructure() {
		return [
			['type' => 'component', 'name' => 'Name', 'label' => 'Заголовок формы'],
			['type' => 'field', 'name' => 'subject'],
			['type' => 'field', 'name' => 'email']
		];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Опции обратной связи';
	}

}
