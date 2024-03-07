<?php

namespace App\Model;

class StatBanner727373Click extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\TimestampActionTrait,
	 Component\IdFirmTrait,
	 Component\IpAddrTrait;

	public function fields() {
		return [
			'id_banner' => [
				'cols' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			],
			'id_city' => [
				'col' => [
					'flags' => 'not_null unsigned',
					'type' => 'int_4'
				],
				'elem' => 'hidden_field',
				'label' => 'ID города'
			],
			'id_stat_user' => [
				'cols' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			],
			'id_stat_request' => ['col' => \Sky4\Db\ColType::getInt(8)],
			'response_url' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(500)],
			'banner_url' => ['elem' => 'text_field', 'col' => \Sky4\Db\ColType::getString(500)],
            'cml_banner_id' => [
				'cols' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			]
		];
	}

}
