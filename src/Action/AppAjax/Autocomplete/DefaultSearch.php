<?php

namespace App\Action\AppAjax\Autocomplete;

use App\Action\AppAjax\Autocomplete;
use App\Model\CurrentRegionCity;
use App\Model\StsCity;
use CDbConnection;
use CUtils;
use function str;

class DefaultSearch extends Autocomplete {

	public function execute($query, $params) {
		$container = $params['container'] ? $params['container'] : 'sidebar';
		$field_name = $params['fieldName'];
		$model_alias = $params['modelAlias'];
		$rel_fields = $params['relFields'] ? $params['relFields'] : [];
		$location = $params['location'];
		$result = [];

		if ($field_name == 'name' && $model_alias == 'sts-city' && $container == 'firm-branch') {
			$db = new CDbConnection();
			$items = $db->query()
					//->setText("SELECT DISTINCT c.`id_country` , c.`id_region` , c.`id_city` , c.`name` , s.`id_city` FROM `current_region_city` c LEFT JOIN `subgroup_count` s ON c.`id_city` = s.`id_city` WHERE c.`id_region` > :nil AND c.`id_country` > :nil AND c.`name` LIKE :q HAVING s.`id_city` > :nil OR c.`id_city` = :nil ORDER BY `name` ASC LIMIT 0, 10")
					->setText("SELECT DISTINCT `id_country`, `id_region_country` , `id_city` , `name` FROM `sts_city` WHERE `id_region_country` > :nil AND `id_country` > :nil AND `name` LIKE :q ORDER BY `name` ASC LIMIT 0, 10")
					->setParams([':q' => '%' . $query . '%', ':nil' => 0])
					->fetch();
            foreach ($items as $item) {
				$city_name = $item['id_city'] ? str()->firstCharsOfWordsToUpper(str()->toLower($item[$field_name])) : false;
				if (!$city_name) {
					$city_name = str()->toLower($item[$field_name]);
					$city_name_array = explode(" ", $city_name);
					$city_name = count($city_name_array) > 1 ? str()->firstCharToUpper(str()->toLower($city_name)) : str()->firstCharsOfWordsToUpper(str()->toLower($city_name));
				}
				if ($item['id_city']) {
					$region = new \App\Model\StsRegionCountry();
					$region->reader()
							->setWhere([
								'AND',
								"`id_region_country` = :id_region_country",
								"`id_country` = :id_country",
									], [
								':id_region_country' => $item['id_region_country'],
								':id_country' => $item['id_country'],
							])
							->objectByConds();

					$rNameA = explode(" ", $region->val('name'));
					$region = count($rNameA) > 1 ? str()->firstCharToUpper(str()->toLower($region->val('name'))) : str()->firstCharsOfWordsToUpper(str()->toLower($region->val('name')));

                    $result[] = [
                        'id' => $item['id_city'],
                        'label' => $city_name,
                        'sub_label' => trim($region),
                        'no_link' => 1
                    ];
                }
            }
        } else if ($field_name == 'name' && $model_alias == 'sts-city') {
			$db = new CDbConnection();
			$items = $db->query()
					//->setText("SELECT DISTINCT c.`id_country` , c.`id_region` , c.`id_city` , c.`name` , s.`id_city` FROM `current_region_city` c LEFT JOIN `subgroup_count` s ON c.`id_city` = s.`id_city` WHERE c.`id_region` > :nil AND c.`id_country` > :nil AND c.`name` LIKE :q HAVING s.`id_city` > :nil OR c.`id_city` = :nil ORDER BY `name` ASC LIMIT 0, 10")
					->setText("SELECT DISTINCT c.`id_country` , c.`id_region` , c.`id_city` , c.`name` , s.`id_city` FROM `current_region_city` c LEFT JOIN `firm_type_city` s ON c.`id_city` = s.`id_city` WHERE c.`id_region` > :nil AND c.`id_country` > :nil AND c.`name` LIKE :q HAVING s.`id_city` > :nil OR c.`id_city` = :nil ORDER BY `name` ASC LIMIT 0, 10")
					->setParams([':q' => '%' . $query . '%', ':nil' => 0])
					->fetch();

			foreach ($items as $item) {
				$city_name = $item['id_city'] ? str()->firstCharsOfWordsToUpper(str()->toLower($item[$field_name])) : false;
				if (!$city_name) {
					$city_name = str()->toLower($item[$field_name]);
					$city_name_array = explode(" ", $city_name);
					$city_name = count($city_name_array) > 1 ? str()->firstCharToUpper(str()->toLower($city_name)) : str()->firstCharsOfWordsToUpper(str()->toLower($city_name));
				}
				if ($item['id_city']) {
					$region = new CurrentRegionCity();
					$region->reader()
							->setWhere([
								'AND',
								"`id_region` = :id_region",
								"`id_country` = :id_country",
								"`id_city` = :nil",
									], [
								':id_region' => $item['id_region'],
								':id_country' => $item['id_country'],
								':nil' => 0
							])
							->objectByConds();

					$rNameA = explode(" ", $region->val('name'));
					$region = count($rNameA) > 1 ? str()->firstCharToUpper(str()->toLower($region->val('name'))) : str()->firstCharsOfWordsToUpper(str()->toLower($region->val('name')));

					if ($container == 'sidebar') {
						$result[] = [
//'id' => $item['id_region'] . '-' . $item['id_country'] . '-' . $item['id_city'],
							'id' => $item['id_city'] == '76004' ? 'index' : ($item['id_country'] == 643 ? $item['id_city'] : $item['id_country'] . '-' . $item['id_city']),
							'label' => $city_name,
							'sub_label' => trim($region),
							'href' => '/utils/change-location/' . ($item['id_city'] == '76004' ? 'index' : ($item['id_country'] == 643 ? $item['id_city'] : ($item['id_country'] . '-' . $item['id_city']))) . '/'
						];
					} else {
						$sts_city = new StsCity();
						$sts_city->reader()
								->setWhere([
									'AND',
									"`id_country` = :id_country",
									"`id_city` = :id_city",
										], [
									':id_country' => $item['id_country'],
									':id_city' => $item['id_city']
								])
								->objectByConds();
						$result[] = [
							'id' => $sts_city->val('id_region_country'),
							'label' => $city_name,
							'sub_label' => trim($region),
							'no_link' => 1
						];
					}
				} else {
					$result[] = [
						'id' => $item['id_region'],
						'label' => $city_name,
						'sub_label' => '',
						'href' => '/utils/change-location/' . $item['id_region']
					];
				}
			}
		} else {
			$model = \Sky4\Utils::getModelClass($model_alias);
			$items = $model->suggest($query, $field_name, $rel_fields);
			foreach ($items as $item) {
				$result[] = array(
					'id' => $item['key'],
					'label' => str()->firstCharToUpper(str()->toLower($item['val'])),
					'name' => str()->firstCharToUpper(str()->toLower($item['val']))
				);
			}
		}

		die(json_encode($result));
	}

}
