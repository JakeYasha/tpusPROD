<?php

namespace App\Model;
class StatUser extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\TimestampIntervalTrait,
	 Component\IpAddrTrait;

	public function fields() {
		return [
			'id_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'hidden_field',
				'label' => 'ID города'
			],
			'user_city_name' => [
				'elem' => 'text_field',
				'label' => 'Название города'
			],
			'id_user' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_8'
				],
				'elem' => 'hidden_field',
				'label' => 'ID пользователя'
			],
			'cookie_hash' => [
				'col' => \Sky4\Db\ColType::getMd5(),
				'elem' => 'hidden_field'
			],
			'referer' => [
				'col' => \Sky4\Db\ColType::getString(500),
				'elem' => 'hidden_field'
			],
			'user_agent' => [
				'col' => \Sky4\Db\ColType::getString(500),
				'elem' => 'hidden_field'
			]
		];
	}

	public function isValid() {
		return \Sky4\Helper\DeprecatedDateTime::toTimestamp($this->val('timestamp_ending')) < (time() - app()->config()->get('app.statistics.new.user.delay', (60 * 15)));
	}

}
