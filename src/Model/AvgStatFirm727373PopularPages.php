<?php

namespace App\Model;

class AvgStatFirm727373PopularPages extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait;

	public function fields() {
		return [
			'count' => ['col' => \Sky4\Db\ColType::getInt(4)],
			'title' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(500)],
			'url' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(500)],
			'timestamp_inserting' => ['elem' => 'date_time_field', 'label' => 'Время добавления'],
			'month' => ['col' => \Sky4\Db\ColType::getInt(1)]
		];
	}

	public function beforeInsert(&$vals = array()) {
		if (is_array($vals)) {
			$vals['timestamp_inserting'] = isset($vals['timestamp_inserting']) ? $vals['timestamp_inserting'] : \Sky4\Helper\DeprecatedDateTime::now();
		}
		return $this;
	}

}
