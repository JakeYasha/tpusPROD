<?php

namespace App\Model;
class StatRequest extends \Sky4\Model\Composite {

	use Component\IdTrait;

	public function fields() {
		return [
			'id_stat_user' => ['col' => \Sky4\Db\ColType::getInt(8)],
			'request_url' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(500)],
			'request_refferer' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(500)],
			'request_text' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getText(2)],
			'response_id_city' => [
				'col' => ['flags' => 'not_null unsigned', 'col' => 'int_4']
			],
			'response_title' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(500)],
			'response_url' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(500)],
			'response_code' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(4)],
			'timestamp_inserting' => [
				'elem' => 'date_time_field',
				'label' => 'Время добавления'
			],
		];
	}

	public function beforeInsert(&$vals = array()) {
		if (is_array($vals)) {
			$vals['timestamp_inserting'] = isset($vals['timestamp_inserting']) ? $vals['timestamp_inserting'] : \Sky4\Helper\DeprecatedDateTime::now();
		}
		return $this;
	}

}
