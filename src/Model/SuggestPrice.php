<?php

namespace App\Model;

class SuggestPrice extends \Sky4\Model\Composite {

	public function idFieldsNames() {
		return ['id'];
	}

	public function name() {
		return $this->val('string');
	}

	public function fields() {
		return [
			'id' => [
				'col' => [
					'default_val' => '',
					'flags' => 'auto_increment not_null primary_key unsigned',
					'name' => 'id',
					'type' => 'int_8',
				],
				'elem' => 'text_field',
				'label' => 'id'
			],
			'string' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'name' => 'string',
					'type' => 'string(255)',
				],
				'elem' => 'text_field',
				'label' => 'string'
			],
			'rate' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'rate',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'rate'
			],
		];
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
		if ($sphinx === null) {
			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
		}

		$row = [
			'id' => $this->id(),
			'string' => $this->val('string'),
			'rate' => $this->val('rate')
		];

		$sphinx->replace()
				->into(SPHINX_PRICE_SUGGEST_INDEX)
				->set($row)
				->execute();

		return $this;
	}

	public function delete($sphinx = null) {
		$this->deleteRtIndex($sphinx);
		return parent::delete();
	}

	public function deleteRtIndex($sphinx = null) {
		if ($sphinx === null) {
			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
		}

		$sphinx->delete()
				->from(SPHINX_PRICE_SUGGEST_INDEX)
				->where('id', '=', intval($this->id()))
				->execute();

		return $this;
	}

}
