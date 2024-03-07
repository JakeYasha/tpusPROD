<?php

namespace App\Model;
class Brand extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait;
	
	private $table = 'brand';

	public function fields() {
		return [
			'count' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'text_field'
			],
			'site_name' => [
				'col' => \Sky4\Db\ColType::getString(255),
				'elem' => 'text_field'
			],
			'hash' => [
				'col' => \Sky4\Db\ColType::getMd5(),
				'elem' => 'text_field'
			]
		];
	}

	public function defaultOrder() {
		return ['name' => 'ASC'];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Бренды';
	}
	
	public function table() {
		return $this->table;
	}

	public function setTable($table) {
		$this->table = $table;
	}
	
	public function siteName() {
		return $this->val('site_name');
	}

}
