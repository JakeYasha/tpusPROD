<?php

namespace App\Action\AppAjax\Autocomplete;

use App\Action\AppAjax\Autocomplete;
use function str;

//@TODO!
class PriceCatalogPriceAdd extends Autocomplete {

	public function execute($query) {
		$result = [];
		$limit = 20;

		$model = new \App\Model\PriceCatalog();
		$items = $model->suggestSubgroupsForPriceAdd($query);
		foreach ($items as $item) {
			$result[] = array(
				'id' => $item['id'],
				'label' => $item['label'],
				'sub_label' => $item['sub_label'],
				'name' => $item['name'],
				'href' => '/firm-user/price/add/?step=2&id_catalog=' . $item['id']
			);
		}

		die(json_encode($result));
	}

}
