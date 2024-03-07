<?php

namespace App\Model;

class FirmFeedback extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\MessageTrait,
	 Component\TimestampActionTrait,
	 Component\UserDataTrait,
	 Component\NewStateTrait;

	public function fields() {
		return [
			'flag_is_callback' => [
				'elem' => 'single_check_box',
				'label' => 'Запрос звонка?',
				'default_val' => 0
			],
			'flag_is_for_call_center' => [
				'elem' => 'single_check_box',
				'label' => 'Запрос звонка?',
				'default_val' => 0
			],
			'flag_is_error' => [
				'elem' => 'single_check_box',
				'label' => 'Сообщение об ошибке?',
				'default_val' => 0
			]
		];
	}

	public static function prepare(FirmFeedback $item) {
		$timestamp = \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_inserting'));

		return [
			'id' => $item->id(),
			'name' => strip_tags($item->val('name')),
			'user_name' => strip_tags($item->val('user_name')),
			'user_email' => strip_tags($item->val('user_email')),
			'user_phone' => strip_tags($item->val('user_phone')),
			'timestamp_inserting' => $item->val('timestamp_inserting'),
			'datetime' => date('d.m.Y H:i', $timestamp),
			'date' => date('d.m.Y', $timestamp),
			'time' => date('H:i:s', $timestamp),
			'brief_text' => (bool) $item->val('flag_is_callback') ? 'Заказ звонка' : $item->val('message_subject'),
			'text' => $item->val('message_text'),
			'is_active' => true,
			'flag_is_callback' => (bool) $item->val('flag_is_callback')
		];
	}

}
