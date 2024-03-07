<?php

namespace App\Model;

class FirmTypeCity extends \Sky4\Model {

	private $table = 'firm_type_city';

	public function idFieldsNames() {
		return ['id_type'];
	}

	public function fields() {
		return [
			'id_type' => [
				'col' => [
					'flags' => 'unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'ID типа фирмы',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'id_city' => [
				'col' => [
					'flags' => 'unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'ID типа фирмы',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'cnt' => [
				'col' => [
					'flags' => 'unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'ID типа фирмы',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
		];
	}

	public function table() {
		return $this->table;
	}

	public function setTable($table) {
		$this->table = $table;
	}

}
