<?php

namespace App\Model;
class BannerRegion extends \Sky4\Model\Composite {

	use Component\IdTrait;

	public function fields() {
		return [
			'id_banner' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			],
			'id_region' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			]
		];
	}

}
