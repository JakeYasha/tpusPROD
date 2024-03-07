<?php

namespace App\Model;
class AdvertModuleRegion extends \Sky4\Model\Composite {
	use Component\IdTrait;

	public function fields() {
		return [
			'id_advert_module' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			],
			'id_region' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			],
			'active' => [
				'elem' => 'single_check_box'
			]
		];
	}
}
