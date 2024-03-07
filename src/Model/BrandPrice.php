<?php

namespace App\Model;

class BrandPrice extends \Sky4\Model\Composite {

	private $table = 'brand_price';

	use Component\IdTrait;

	public function fields() {
		return [
			'brand_id' => [
				'col' => [
					'flags' => 'unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'ID бренда',
				'params' => [
					'rules' => ['int', 'required']
				]
			],
			'price_id' => [
				'col' => [
					'flags' => 'unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'ID товара',
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

	public function afterUpdate(&$vals) {
		$result = parent::afterUpdate($vals);
		$this->updateRtIndex();
		return $result;
	}

	public function afterInsert(&$vals, $parent_object = null) {
		$result = parent::afterInsert($vals, $parent_object);
		$this->updateRtIndex();
		return $result;
	}

	public function updateRtIndex($sphinx = null) {
//		if ($sphinx === null) {
//			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
//		}
//
//		$row = [
//			'id' => $this->id(),
//			'id_price' => $this->val('price_id'),
//			'id_brand' => $this->val('brand_id')
//		];
//
//		$sphinx->replace()
//				->into(SPHINX_PRICE_BRAND_INDEX)
//				->set($row)
//				->execute();

		return $this;
	}
	
	public function delete($sphinx = null) {
		$this->deleteRtIndex($sphinx);
		return parent::delete();
	}

	public function deleteRtIndex($sphinx = null) {
//		if ($sphinx === null) {
//			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
//		}
//
//		$sphinx->delete()
//				->from(SPHINX_PRICE_BRAND_INDEX)
//				->where('id', '=', intval($this->id()))
//				->execute();

		return $this;
	}

}
