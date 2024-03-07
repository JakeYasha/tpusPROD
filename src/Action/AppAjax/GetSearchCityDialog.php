<?php

namespace App\Action\AppAjax;

use App\Action\AppAjax;
use App\Classes\Autocomplete;
use function app;
use function str;

class GetSearchCityDialog extends AppAjax {

	public function execute() {
		$_items = app()->db()->query()
				->setText("SELECT sr.`id_city`, sr.`id_country`, sr.`id_region`, sr.`count_firms`, sr.`count_goods`, sr.`count_goods_1`, sr.`count_goods_2`, sr.`count_goods_3`, sr.`count_discounts`, src.`name`, LOWER(sc.`name`) as `city_name`
		FROM `current_region_city` sr
		LEFT JOIN `sts_region_country` src ON sr.`id_country` = src.`id_country` AND sr.`id_region` = src.`id_region_country`
		LEFT JOIN `sts_city` sc ON sr.`id_city` = sc.`id_city` AND sr.`id_region` = sc.`id_region_country`
		WHERE sr.`id_region` IS NOT NULL AND sr.`id_country` = 643 
		ORDER BY `name` ASC, `city_name` ASC")
				->fetch();

		$cities = [];
		foreach ($_items as $it) {
			if ($it['count_firms'] > 80) {
				$name = trim(str()->firstCharsOfWordsToUpper($it['city_name']));
				$cities[$name] = [
					'name' => str()->firstCharsOfWordsToUpper($it['city_name']),
					'id' => $it['id_city']
				];
			}
		}

		ksort($cities);

		$autocomplete = new Autocomplete();
		$autocomplete
				->setName('code')
				->setAttrs([
					'class' => 'js-autocomplete-city-search',
					'placeholder' => 'Введите название...'
				])
				->setParams([
					'model_alias' => 'sts-city',
					'val_mode' => 'id',
					'field_name' => 'name'
		]);
		$result = $this->view()
				->set('cities', $cities)
				->set('autocomplete', $autocomplete->render())
				->setTemplate('search_city_select', 'forms')
				->render();
		die($result);
	}

}
