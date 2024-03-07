<?php

namespace App\Action\AppAjax\Autocomplete;

use App\Action\AppAjax\Autocomplete;
use function str;

class PriceCatalogSubgroups extends Autocomplete {

	public function execute($query) {
		$result = [];
		$limit = 20;

		$model = new \App\Model\PriceCatalog();
		$items = $model->suggestSubgroups($query);
		foreach ($items as $item) {
			$result[] = array(
				'id' => $item['key'],
				'label' => str()->firstCharToUpper(str()->toLower($item['val'])),
				'name' => str()->firstCharToUpper(str()->toLower($item['val']))
			);
		}

		die(json_encode($result));
	}

}
