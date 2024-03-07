<?php

namespace App\Model;

class FirmRank extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\TimestampActionTrait;

	private $table = 'firm_rank';

	public function cols() {
		return [
			'id',
			'id_firm',
			'rank_kegeles',
			'rank_users',
			'rank_rank'
		];
	}

	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function fields() {
		return [
			'rank_kegeles' => [
				'col' => [
					'default_val' => '0.0',
					'flags' => 'not_null',
					'type' => 'double(1,1)'
				],
				'label' => 'Рейтинг по версии Товары плюс',
				'params' => [
					'rules' => ['double']
				],
				'elem' => 'text_field'
			],
			'rank_users' => [
				'col' => [
					'default_val' => '0.0',
					'flags' => 'not_null',
					'type' => 'double(1,1)'
				],
				'label' => 'Пользовательский рейтинг',
				'params' => [
					'rules' => ['double']
				],
				'elem' => 'text_field'
			],
			'rank_users_count' => [
				'col' => [
					'type' => 'int_4'
				],
				'label' => 'Количество оценок',
				'params' => [
					'rules' => ['int']
				],
				'elem' => 'text_field'
			],
			'rank' => [
				'col' => [
					'default_val' => '0.0',
					'flags' => 'not_null',
					'type' => 'double(1,1)'
				],
				'label' => 'Итоговый рейтинг',
				'params' => [
					'rules' => ['double']
				],
				'elem' => 'text_field'
			],
		];
	}

	public function title() {
		return $this->exists() ? $this->val('rank') * 5 : 'Рейтинги';
	}

	public function table() {
		return $this->table;
	}

	public function setTable($table) {
		$this->table = $table;
	}

}
