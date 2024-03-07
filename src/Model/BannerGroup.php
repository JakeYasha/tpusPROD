<?php

namespace App\Model;

class BannerGroup extends \Sky4\Model\Composite {

	use Component\IdTrait;

	public function fields() {
		return [
			'id_banner' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			],
			'id_group' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			],
			'id_subgroup' => [
				'col' => \Sky4\Db\ColType::getInt(8),
				'elem' => 'hidden_field'
			]
		];
	}

	public function insert($vals = null, $parent_object = null) {
		$vals['id_group'] = $this->getGroupBySubgroup($vals);
		return parent::insert($vals, $parent_object);
	}

	public function update($vals = null) {
		$vals['id_group'] = $this->getGroupBySubgroup($vals);
		return parent::update($vals);
	}

	private function getGroupBySubgroup(&$vals) {
		if ((int) $vals['id_subgroup'] !== 0) {
			$pc = new PriceCatalog();
			$pc->reader()
					->setSelect(['id_group'])
					->setWhere(['AND', 'id_subgroup = :id_subgroup', 'node_level = :node_level'], [':id_subgroup' => (int) $vals['id_subgroup'], ':node_level' => 2])
					->objectByConds();

			return (int) $pc->id_group();
		}

		return 0;
	}

	public function getSubgroupIdsByBannerId($banner_id) {
		return array_keys(
				$this->reader()
						->setSelect(['id_subgroup'])
						->setWhere(['AND', 'id_banner = :id_banner'], [':id_banner' => $banner_id])
						->rowsWithKey('id_subgroup')
		);
	}

}
