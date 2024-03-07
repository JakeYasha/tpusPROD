<?php

namespace App\Action\Statistics;

use App\Model\PriceCatalog;
use App\Model\StsCity;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;
use function app;

class Dynamic extends \App\Action\Statistics {

	public function execute() {
		app()->breadCrumbs()
				->setElem('Статистика', '/statistics/')
				->setElem('Динамика посещаемости страниц каталога', '/statistics/dynamic/');

		$params = app()->request()->processGetParams([
			'id_catalog' => 'int'
		]);

		if ($params['id_catalog'] === null) {
			throw new \Sky4\Exception();
		}

		$catalog = new PriceCatalog($params['id_catalog']);
		$text = $this->text()->getByLink('statistics/dynamic');
		$text->setVal('text', self::replaceStatText($text->val('text')));
		if ($text->exists()) {
			app()->metadata()->setFromModel($text);
		} else {
			app()->metadata()->setTitle('Динамика посещаемости страниц каталога');
		}

		$items = [];
		$months = [];
		$chart_items = ['columns' => [], 'data' => []];
		$_donut_chart_items = [];
		if ($catalog->exists()) {
			$path = $catalog->adjacencyListComponent()->getPath();
			$start_element = current($path);
			if ((int) $start_element->val('id_group') === 22 || (int) $start_element->val('id_group') === 44) {
				unset($path[$start_element->id()]);
			}
			foreach ($path as $cat) {
				$chart_items['columns'][] = $cat->name();
				$rows = app()->db()->query()->setText('SELECT `id_city` as `response_id_city`, `url` as `response_url`, `timestamp_inserting`, SUM(`count`) as `cnt` FROM `avg_stat_popular_pages` WHERE `timestamp_inserting` BETWEEN "' . date("Y-m-01 00:00:00", strtotime(DeprecatedDateTime::shiftMonths(-5))) . '" AND "' . DeprecatedDateTime::now() . '" AND `url` LIKE "' . $cat->link() . '%" != "" GROUP BY YEAR(`timestamp_inserting`), MONTH(`timestamp_inserting`) ORDER BY `timestamp_inserting` DESC LIMIT 12')->fetch();

				$items[$cat->id()] = ['name' => $cat->name(), 'link' => app()->link($cat->link()), 'space' => str_repeat('&nbsp;&nbsp;', $cat->val('node_level') - 1), 'stats' => []];
				foreach ($rows as $row) {
					$items[$cat->id()]['stats'][DeprecatedDateTime::month($row['timestamp_inserting'])] = ['name' => DeprecatedDateTime::monthName($row['timestamp_inserting']), 'count' => $row['cnt']];
					if (!isset($months[DeprecatedDateTime::month($row['timestamp_inserting'])])) {
						$months[DeprecatedDateTime::month($row['timestamp_inserting'])] = DeprecatedDateTime::monthName($row['timestamp_inserting']);
					}
				}

				if ($cat->id() === $catalog->id()) {
					$geo_rows = app()->db()->query()->setText('SELECT `id_city` as `response_id_city`, `url` as `response_url`, `timestamp_inserting`, SUM(`count`) as `cnt` FROM `avg_stat_popular_pages` WHERE `timestamp_inserting` BETWEEN "' . date("Y-m-01 00:00:00", strtotime(DeprecatedDateTime::shiftMonths(-5))) . '" AND "' . DeprecatedDateTime::now() . '" AND `url` LIKE "' . $cat->link() . '%" != "" GROUP BY `id_city` ORDER BY `cnt`')->fetch();
					foreach ($geo_rows as $g_row) {
						if (!isset($_donut_chart_items[$g_row['response_id_city']])) {
							$_donut_chart_items[$g_row['response_id_city']] = ['count' => 0, 'link' => ''];
						}
						$_donut_chart_items[$g_row['response_id_city']]['count'] += $g_row['cnt'];
					}
				}
			}
		}

		$cities = [];
		$donut_chart_items = [];
		if ($_donut_chart_items) {
			$sc = new StsCity();
			$sc_conds = Utils::prepareWhereCondsFromArray(array_keys($_donut_chart_items), 'id_city');
			$cities = $sc->reader()
					->setWhere($sc_conds['where'], $sc_conds['params'])
					->objects();
			foreach ($_donut_chart_items as $id_city => $dci) {
				if (isset($cities[$id_city])) {
					$donut_chart_items[$cities[$id_city]->name()] = $dci;
				}
			}
		}


		$sorted_months = array_reverse($months, true);
		foreach ($sorted_months as $k => $month_name) {
			foreach ($items as $cat_id => $row) {
				$chart_items['data'][$month_name][] = (int) (isset($row['stats'][$k]['count']) ? $row['stats'][$k]['count'] : 0);
			}
		}

		app()->metadata()
				->setJsFile('https://www.google.com/jsapi')
				->setJs('google.load("visualization", "1", {packages: ["corechart", "line"]});');

		$this->view()->setTemplate('dynamic')
				->set('item', $catalog)
				->set('items', $items)
				->set('chart_items', $chart_items)
				->set('donut_chart_items', array_reverse($donut_chart_items))
				->set('months', $months)
				//
				//
				->set('breadcrumbs', app()->breadCrumbs()->render())
				->set('text', $text)
				->save();
	}

}
