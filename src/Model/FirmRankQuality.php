<?php

namespace App\Model;
class FirmRankQuality extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		return [
			'quality_check_result' => [
				'col' => [
					'type' => 'int_1',
				],
				'label' => 'Итог проверки',
				'params' => [
					'rules' => ['int']
				]
			],
		];
	}

	public function title() {
		return 'Проверка качества';
	}

}
