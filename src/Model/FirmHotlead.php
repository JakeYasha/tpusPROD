<?php

namespace App\Model;

class FirmHotlead extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\TimestampActionTrait,
	 Component\ActiveTrait;

	public function fields() {
		return [
			'id_collector' => [
				'elem' => 'text_field',
				'label' => 'ID hotlead',
				'params' => [
					'rules' => ['length' => ['max' => 150, 'min' => 1]]
				]
			],
			'login' => [
				'elem' => 'text_field',
				'label' => 'Логин',
				'params' => [
					'rules' => ['length' => ['max' => 255, 'min' => 1]]
				]
			],
			'password' => [
				'elem' => 'text_field',
				'label' => 'Пароль',
				'params' => [
					'rules' => ['length' => ['max' => 255, 'min' => 1]]
				]
			]
		];
	}

	public function cols() {
		return [
			'id_firm' => ['label' => 'Фирма'],
			'login' => ['label' => 'Email'],
			'timestamp_last_updating' => ['class' => 'date-time', 'label' => 'Дата изменения'],
		];
	}

	public function filterFields() {
		return [
			'id_firm' => [
				'elem' => 'drop_down_list',
				'label' => 'Фирма',
				'options' => $this->idFirmComponent()->getFirmNamesForFilter(),
				'field_name' => 'id_firm'
			],
			'flag_is_active' => [
				'elem' => 'single_check_box',
				'label' => 'Активные',
				'cond' => 'flag',
				'field_name' => 'flag_is_active'
			]
		];
	}

	public function filterFormStructure() {
		return [
			['type' => 'field', 'name' => 'id_firm'],
			['type' => 'field', 'name' => 'flag_is_active']
		];
	}

	public function formStructure() {
		return [
			['type' => 'field', 'name' => 'login'],
			['type' => 'field', 'name' => 'password'],
			['type' => 'field', 'name' => 'id_firm'],
			['type' => 'field', 'name' => 'flag_is_active'],
			['type' => 'field', 'name' => 'id_collector'],
		];
	}

	public function title() {
		return $this->exists() ? 'Hotlead #' . $this->val('id_collector') : 'Hotlead';
	}

}
