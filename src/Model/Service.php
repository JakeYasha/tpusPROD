<?php

namespace App\Model;
class Service extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait,
	 Component\ActiveTrait,
	 Component\PositionWeightTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		return [
			'price' => [
				'attrs' => ['rows' => '5'],
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_4'
				],
				'elem' => 'tiny_mce',
				'label' => 'Стоимость услуги',
				'params' => [
					'parser' => true
				]
			],
			'text' => [
				'attrs' => ['rows' => '10', 'style' => 'height: 200px;'],
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_4'
				],
				'elem' => 'tiny_mce',
				'label' => 'Описание',
				'params' => [
					'parser' => true,
					'rules' => []
				]
			]
		];
	}

	public function getListForCheckboxes() {
		$exception = (app()->location()->currentName() === 'Ярославская область' || app()->location()->currentName() === 'Ярославль') ? 13 : 10;
		$result = [];
		$list = $this->reader()
				->setSelect(['id', 'name'])
				->setWhere(['AND','`flag_is_active` = :yes','id != :exception'], [':yes' => 1, ':exception' => $exception])
				->objects();

		foreach ($list as $res) {
			$result[$res->id()] = $res->name();
		}

		return $result;
	}

	public function formStructure() {
		return array(
			array('type' => 'component', 'name' => 'Name'),
			array('type' => 'field', 'name' => 'text'),
			array('type' => 'field', 'name' => 'price'),
			array('type' => 'component', 'name' => 'PositionWeight'),
			array('type' => 'component', 'name' => 'TimestampAction'),
			array('type' => 'component', 'name' => 'Active'),
		);
	}

	public function cols() {
		return [
			'name' => [
				'label' => 'Название',
				'style_class' => 'left'
			],
			'flag_is_active' => [
				'label' => 'Активна?',
				'type' => 'flag'
			]
		];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Услуги';
	}

	public function quickViewFieldsNames() {
		return array('id', 'name');
	}

}
