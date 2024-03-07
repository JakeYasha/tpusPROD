<?php

namespace App\Model;

class AdvertModuleFirm extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait;

	public function fields() {
		return [
			'id_advert_module' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			],
			'active' => [
				'elem' => 'single_check_box'
			]
		];
	}

}
