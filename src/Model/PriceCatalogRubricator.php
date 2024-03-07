<?php

namespace App\Model;
class PriceCatalogRubricator extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait,
	 Component\ActiveTrait,
	 Component\AdjacencyListTrait,
	 Component\TimestampActionTrait,
	 Component\PositionWeightTrait;

	public function defaultOrder() {
		return ['position_weight' => 'DESC'];
	}

	public function cols() {
		$cols = [
			'name' => [
				'label' => 'Название'
			],
			'link' => [
				'label' => 'Ссылка'
			]
		];

		return array_merge($cols, $this->activeComponent()->cols(), $this->positionWeightComponent()->cols());
	}

	public function fields() {
		return [
			'link' => [
				'elem' => 'text_field',
				'label' => 'Ссылка',
				'params' => [
					'rules' => []
				]
			],
			'css_class' => [
				'elem' => 'text_field',
				'label' => 'CSS класс',
				'params' => [
					'rules' => []
				]
			],
			'id' => [
				'elem' => 'text_field',
				'label' => 'ID',
				'params' => [
					'rules' => []
				]
			],
		];
	}
	
	public function link() {
		return $this->val('link') ? $this->val('link') : false;
	}
    
	public function id_catalog_rubricator() {
		return $this->val('id') ? $this->val('id') : false;
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Рубрикатор каталога';
	}

}
