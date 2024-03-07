<?php

namespace App\Model;

class PriceRequest extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\ExtendedTextTrait,
	 Component\TimestampActionTrait,
	 Component\UserDataTrait;

	public function cols() {
		$cols = [
			'user_name' => [
				'label' => 'Имя'
			],
		];

		$cols = array_merge($cols, $this->timestampActionComponent()->cols('timestamp_inserting'));

		return $cols;
	}

	public function formStructure() {
		return [
			['type' => 'component', 'name' => 'UserData'],
		];
	}

	public function defaultOrder() {
		return [
			'timestamp_inserting' => 'DESC'
		];
	}

	public function defaultInsertingEnabled() {
		return false;
	}

	public function fields() {
		return [
			'flag_is_new' => [
				'elem' => 'single_check_box',
				'label' => 'Новое?'
			],
			'id_price' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'id_price',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_price'
			]
		];
	}

	public function title() {
		return 'Заказы товаров';
	}

	public function name() {
		return $this->exists() ? $this->val('user_name') : 'Заказы';
	}

	public static function prepare(PriceRequest $item) {
        $firm = new \App\Model\Firm($item->val('id_firm'));
        
		$price = new Price();
		$price->reader()
				->setWhere(['AND', '`legacy_id_price` = :id_price', '`legacy_id_service` = :id_service'], [':id_price' => $item->val('id_price'), ':id_service' => $firm->id_service()])
				->objectByConds();

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
			'brief_text' => $item->val('brief_text'),
			'text' => $item->val('text'),
			'is_active' => true,
			'item_name' => $price->name(),
			'item_link' => $price->link()
		];
	}

}
