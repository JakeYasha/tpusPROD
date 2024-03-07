<?php

namespace App\Model;

class AvgStatBanner extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait;

	public function fields() {
		return [
			'timestamp_inserting' => [
				'elem' => 'date_time_field',
				'label' => 'Время добавления'
			],
			'id_banner' => ['col' => \Sky4\Db\ColType::getInt(8)],
			'count_shows' => ['col' => \Sky4\Db\ColType::getInt(4)],
			'count_clicks' => ['col' => \Sky4\Db\ColType::getInt(4)],
			'month' => ['col' => \Sky4\Db\ColType::getInt(1)],
		];
	}

}
