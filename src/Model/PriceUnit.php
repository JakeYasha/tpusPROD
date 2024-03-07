<?php

namespace App\Model;

class PriceUnit extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait,
	 Component\PositionNumberTrait,
	 Component\TimestampActionTrait;

	public function cols() {
		return [
			'id',
			'name',
			'position_number' => [
				'type' => 'position_changer'
			]
		];
	}

	public function title() {
		$this->exists() ? $this->name() : 'Единица измерения';
	}

	public function defaultOrder() {
		return ['position_number' => 'asc'];
	}

	public function getListForDropDown() {
		$result = [];
		$items = $this->reader()->objects();

		foreach ($items as $item) {
			$result[$item->name()] = $item->name();
		}

		return $result;
	}

}
