<?php

namespace App\Action\Statistics;

use App\Action\Statistics;
use function app;
use function str;

class Common extends Statistics {

	public function execute($internal_call = false) {
		app()->breadCrumbs()
				->setElem('Статистика', '/statistics/')
				->setElem('География данных', '/statistics/common/');

		$text = $this->text()->getByLink('statistics/common');
		$text->setVal('text', self::replaceStatText($text->val('text')));
		app()->metadata()->setFromModel($text);

		$_items = app()->db()->query()
				->setText("SELECT sr.`id_city`, sr.`id_country`, sr.`id_region`, sr.`count_firms`, sr.`count_goods`, sr.`count_goods_1`, sr.`count_goods_2`, sr.`count_goods_3`, sr.`count_discounts`, src.`name`, LOWER(sc.`name`) as `city_name`
		FROM `current_region_city` sr
		LEFT JOIN `sts_region_country` src ON sr.`id_country` = src.`id_country` AND sr.`id_region` = src.`id_region_country`
		LEFT JOIN `sts_city` sc ON sr.`id_city` = sc.`id_city` AND sr.`id_region` = sc.`id_region_country`
		WHERE sr.`id_region` IS NOT NULL AND sr.`id_country` = 643 
		ORDER BY `name` ASC, `city_name` ASC")
				->fetch();

		$regions = [];
		$cities = [];
		$matrix = [];
		$counts = [];
		$region_counts = [];
		$chart_items_firms = [];
		$chart_items_goods = [];

		foreach ($_items as $it) {
			if (!isset($regions[$it['id_region']])) {
				$regions[$it['id_region']] = strip_tags(str()->firstCharOfSentenceToUpper(str()->toLower($it['name'])));
				$region_counts[$it['id_region']] = ['firms' => 0, 'goods' => 0];
				$matrix[$it['id_region']] = [];
			}

			if ($it['count_firms'] >= 1 && $it['count_goods'] > 1) {
				if (!isset($cities[$it['id_city']])) {
					$cities[$it['id_city']] = strip_tags(str()->firstCharOfSentenceToUpper(str()->toLower($it['city_name'])));
					$counts[$it['id_city']] = [
						'firms' => (int)$it['count_firms'],
						'goods' => (int)$it['count_goods'],
						'goods_1' => (int)$it['count_goods_1'],
						'goods_2' => (int)$it['count_goods_2'],
						'goods_3' => (int)$it['count_goods_3'],
						'promos' => $it['count_discounts']
					];
					$region_counts[$it['id_region']]['firms'] += (int)$it['count_firms'];
					$region_counts[$it['id_region']]['goods'] += (int)$it['count_goods'];
				}
				$matrix[$it['id_region']][] = $it['id_city'];
			}
		}

		$_chart_items_firms = [];
		$_chart_items_goods = [];
		$chart_sorter_firm = [];
		$chart_sorter_goods = [];
		foreach ($matrix as $id_region => $tmp) {
			$_chart_items_firms[$id_region] = ['count' => $region_counts[$id_region]['firms'], 'link' => '/'.$id_region];
			$_chart_items_goods[$id_region] = ['count' => $region_counts[$id_region]['goods'], 'link' => '/'.$id_region];
			$chart_sorter_firm[$id_region] = (int)$region_counts[$id_region]['firms'];
			$chart_sorter_goods[$id_region] = (int)$region_counts[$id_region]['goods'];
		}

		arsort($chart_sorter_firm);
		arsort($chart_sorter_goods);

		foreach ($chart_sorter_firm as $id_region => $tmp) {
			$chart_items_firms[$regions[$id_region]] = ['count' => $region_counts[$id_region]['firms'], 'link' => '/'.$id_region];
		}

		foreach ($chart_sorter_goods as $id_region => $tmp) {
			$chart_items_goods[$regions[$id_region]] = ['count' => $region_counts[$id_region]['goods'], 'link' => '/'.$id_region];
		}

		if ($internal_call === true) {
			return [
				'cities' => $cities,
				'counts' => $counts,
				'matrix' => $matrix,
				'regions' => $regions
			];
		}

		app()->metadata()
				->setJsFile('https://www.google.com/jsapi')
				->setJs('google.load("visualization", "1", {packages: ["corechart"]});');

		$this->view()->setTemplate('common')
				->set('chart_items_firms', $chart_items_firms)
				->set('chart_items_goods', $chart_items_goods)
				//
				->set('cities', $cities)
				->set('counts', $counts)
				->set('matrix', $matrix)
				->set('regions', $regions)
				//
				->set('breadcrumbs', app()->breadCrumbs()->render())
				->set('text', $text)
				->save();
	}

}
