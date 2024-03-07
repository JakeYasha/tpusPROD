<?php

namespace App\Model;
class StatSite extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		return [
			'total_towns' => ['col' => \Sky4\Db\ColType::getInt(3)],
			'total_firms' => ['col' => \Sky4\Db\ColType::getInt(8)],
			'total_price' => ['col' => \Sky4\Db\ColType::getInt(8)],
			'total_visitors' => ['col' => \Sky4\Db\ColType::getInt(8)],
			'total_price_shows' => ['col' => \Sky4\Db\ColType::getInt(8)],
			'total_firm_shows' => ['col' => \Sky4\Db\ColType::getInt(8)]
		];
	}

}
