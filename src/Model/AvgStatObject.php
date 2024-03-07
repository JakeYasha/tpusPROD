<?php

namespace App\Model;

class AvgStatObject extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait;

	public function fields() {
		return [
			'timestamp_inserting' => [
				'elem' => 'date_time_field',
				'label' => 'Время добавления'
			],
			't1' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't2' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't3' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't4' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't5' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't6' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't7' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't8' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't9' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't10' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't11' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't12' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't13' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't14' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't15' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't16' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't17' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't18' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't19' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't20' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't21' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't22' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't23' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't24' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't25' => ['col' => \Sky4\Db\ColType::getInt(4)],
			't26' => ['col' => \Sky4\Db\ColType::getInt(4)],
			'month' => ['col' => \Sky4\Db\ColType::getInt(1)],
		];
	}

}
