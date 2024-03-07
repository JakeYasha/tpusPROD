<?php

namespace App\Model;
class AdvertModuleRequest extends \Sky4\Model\Composite {

	use Component\IdTrait,
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
			'id_advert_module' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'id_advert_module',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_advert_module'
			]
		];
	}

	public function title() {
		return 'Заказ по рекламному модулю';
	}

	public function name() {
		return $this->exists() ? $this->val('user_name') : 'Заказ';
	}

	public static function prepare(AdvertModuleRequest $item) {
		$am = new AdvertModule();
		$am->reader()
				->setWhere(['AND', '`id_advert_module` = :id_advert_module'], [':id_advert_module' => $item->val('id_advert_module')])
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
			'item_name' => $am->name(),
			'item_link' => $am->link()
		];
	}

}
