<?php

namespace App\Model;

class News extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ExtendedNameTrait,
	 Component\ExtendedTextTrait,
	 Component\MetadataTrait,
	 Component\StateTrait,
	 Component\TimestampActionTrait;

	public function cols() {
		return [
			'name' => [
				'label' => 'Название'
			],
			'timestamp_inserting' => [
				'label' => 'Дата',
				'style_class' => 'date-time'
			]
		];

		parent::cols();
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Новости';
	}

}
