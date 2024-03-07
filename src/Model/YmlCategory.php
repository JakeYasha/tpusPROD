<?php

namespace App\Model;

class YmlCategory extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\NameTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		$c = $this->fieldPropCreator();

		return [
			'id_yml_category' => $c->intField('ID категории'),
			'parent_node' => $c->intField('Родительский узел', 4, ['rules' => ['required']]),
			'id_catalog' => $c->intField('ID каталога Т+', 4),
			'flag_is_fixed' => $c->checkBox('Зафиксировать')
		];
	}

	public function prepare($items) {
		$result = [];

		foreach ($items as $item) {
			$timestamp_inserting = new \Sky4\Helper\DateTime($item->val('timestamp_inserting'));
			$catalog_name = '';
			$parent_catalog_name = '';
			$parent_name = '';
            $catalog_is_catalog = 0;
			$cat = new PriceCatalog($item->val('id_catalog'));

			if ($cat->exists()) {
                $catalog_is_catalog = $cat->val('flag_is_catalog');
				$catalog_name = $cat->name();
				$parent = $cat->adjacencyListComponent()->reader()->parentObject();
				$parent_catalog_name = $parent->name();
			} else {
				$catalog_name = 'раздел удалён';
			}

			$parent = $this->reader()->setWhere(['AND', 'id_yml_category = :parent_node', 'id_firm = :id_firm'], [':parent_node' => $item->val('parent_node'), ':id_firm' => $item->val('id_firm')])
					->objectByConds();
			$parent_name = $parent->name();

			$result[$item->id()] = [
				'id' => $item->id(),
				'name' => $item->name(),
				'id_catalog' => (int) $item->val('id_catalog'),
				'link' => $cat->link('FIRM_USER_DASHBOARD'),
				'catalog_name' => $catalog_name,
				'parent_name' => $parent_name,
				'parent_catalog_name' => $parent_catalog_name,
				'timestamp_inserting' => $timestamp_inserting->format('d.m.Y H:i:s'),
				'flag_is_fixed' => (int) $item->val('flag_is_fixed'),
				'flag_is_catalog' => $catalog_is_catalog
			];
		}

		return $result;
	}

}
