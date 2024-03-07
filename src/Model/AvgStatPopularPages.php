<?php

namespace App\Model;

class AvgStatPopularPages extends \Sky4\Model\Composite {

	use Component\IdTrait;

	//$items = app()->db()->query()->setText('SELECT `response_id_city`, `response_url`, `response_title`, COUNT(*) as `cnt` FROM `stat_request` WHERE `timestamp_inserting` BETWEEN "' . \Sky4\Helper\DeprecatedDateTime::shiftMonths(-1) . '" AND "' . \Sky4\Helper\DeprecatedDateTime::now() . '" AND `response_url` REGEXP "/catalog/[0-9]+/[0-9]*/?$" GROUP BY `response_url` ORDER BY `cnt` DESC LIMIT 24')->fetch();

	public function fields() {
		return [
			'id_city' => ['col' => \Sky4\Db\ColType::getInt(4)],
			'count' => ['col' => \Sky4\Db\ColType::getInt(4)],
			'title' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(500)],
			'url' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(500)],
			'timestamp_inserting' => ['elem' => 'date_time_field', 'label' => 'Время добавления'],
			'type' => [
				'elem' => 'drop_down_list',
				'label' => 'Тип страницы',
				'options' => ['page', 'catalog']
			],
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
