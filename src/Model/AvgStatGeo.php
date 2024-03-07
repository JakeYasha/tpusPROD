<?php

namespace App\Model;

class AvgStatGeo extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait;

	public function fields() {
		return [
			'city_name' => ['col' => \Sky4\Db\ColType::getString(100)],
			'count' => ['col' => \Sky4\Db\ColType::getInt(4)],
			'timestamp_inserting' => ['elem' => 'date_time_field', 'label' => 'Время добавления'],
			'month' => ['col' => \Sky4\Db\ColType::getInt(1)]
		];
	}

}
