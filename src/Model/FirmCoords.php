<?php

namespace App\Model;
class FirmCoords extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\CoordsTrait;

	public function fields() {
		return [
			'hash' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null',
					'name' => 'hash',
					'type' => 'string(1032)',
				],
				'elem' => 'text_field',
				'label' => 'hash'
			],
		];
	}

}
