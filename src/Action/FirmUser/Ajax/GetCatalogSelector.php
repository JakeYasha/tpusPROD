<?php

namespace App\Action\FirmUser\Ajax;

class GetCatalogSelector extends \App\Action\FirmUser\Ajax {

	public function execute() {
		$result = [];
		$result['isFinish'] = false;
		$result['data'] = [];
		$result['path'] = [];

		$params = app()->request()->processPostParams([
			'type' => ['type' => 'string'],
			'level' => ['type' => 'int'],
			'id' => ['type' => 'int']
		]);

		$price_catalog = new \App\Model\PriceCatalog($params['id']);
		$price_catalog->reader()
				->setSelect(['id', 'name', 'node_level', 'id_group', 'web_many_name', 'web_name'])
				->object($params['id']);

		if ($price_catalog->exists()) {
			$catalog_items = $price_catalog->reader()
					->setSelect(['id', 'name', 'node_level', 'id_group', 'web_many_name', 'web_name'])
					->setWhere(['AND', 'parent_node = :parent_node'], [':parent_node' => $price_catalog->id()])
					->setOrderBy('web_many_name ASC')
					->objects();

			if (!$catalog_items) {
				$result['isFinish'] = true;
			} else {
				foreach ($catalog_items as $cat_item) {
					$result['data'][] = '<li><a href="#" data-id="' . $cat_item->id() . '">' . $cat_item->name() . '</a></li>';
				}
			}

			$path = $price_catalog->adjacencyListComponent()
					->reader()
					->setSelect(['id', 'name', 'node_level', 'id_group', 'web_many_name', 'web_name'])
					->pathObjects();

			foreach ($path as $path_ob) {
				$result['path'][] = '<li>' . $path_ob->name() . '</li>';
			}
			$result['level'] = (int) $price_catalog->val('node_level') + 1;
		}

		$this->setResultData($result)
				->renderResult();
	}

}
