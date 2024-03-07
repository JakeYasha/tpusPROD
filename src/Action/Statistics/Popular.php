<?php

namespace App\Action\Statistics;

use App\Action\Statistics;
use Sky4\Helper\DeprecatedDateTime;
use function app;
use function str;

class Popular extends Statistics {

	public function execute($mode = null) {
		app()->metadata()
				->setJsFile('https://www.google.com/jsapi')
				->setJs('google.load("visualization", "1", {packages: ["corechart"]});');
		$location = (int) app()->location()->currentId();
		$censured_requests = ['', ''];

		if ($mode === 'catalogs') {
			app()->metadata()->noIndex(true);
			app()->breadCrumbs()
					->setElem('Статистика', '/statistics/')
					->setElem('Популярные разделы каталога', '/statistics/popular/catalogs/');

			$items = app()->db()->query()->setText('SELECT `id_city` AS `response_id_city`, `url` as `response_url`, `title` as `response_title`, SUM(`count`) as `cnt` '
							. 'FROM `avg_stat_popular_pages` WHERE `timestamp_inserting` BETWEEN "' . DeprecatedDateTime::shiftMonths(-1) . '" AND "' . DeprecatedDateTime::now() . '" '
							. 'AND `type` = "catalog" AND `url` REGEXP "/catalog/[0-9]+/[0-9]+/?[^$]*$" '
							. ($location > 0 ? 'AND `id_city` = ' . $location : '') . ' '
							. 'GROUP BY `response_title` ORDER BY `cnt` DESC LIMIT 150')->fetch();

			$text = $this->text()->getByLink('statistics/popular/catalogs');
			$text->setVal('text', self::replaceStatText($text->val('text')));
			if ($text->exists()) {
				app()->metadata()->setFromModel($text);
			} else {
				app()->metadata()->setTitle('Популярные страницы');
			}
		} elseif ($mode === 'queries') {
			app()->breadCrumbs()
					->setElem('Статистика', '/statistics/')
					->setElem('Популярные поисковые запросы', '/statistics/popular/queries/');
			$items = app()->db()->query()->setText('SELECT `response_id_city`, `response_url`, `request_text` as `response_title`, `request_text`, COUNT(*) as `cnt` '
							. 'FROM `stat_request` WHERE `timestamp_inserting` BETWEEN "' . DeprecatedDateTime::shiftMonths(-1) . '" AND "' . DeprecatedDateTime::now() . '" '
							. 'AND `request_text` != "" GROUP BY `request_text` ORDER BY `cnt` DESC LIMIT 24')->fetch();
			$text = $this->text()->getByLink('statistics/popular/queries');
			$text->setVal('text', self::replaceStatText($text->val('text')));
			if ($text->exists()) {
				app()->metadata()->setFromModel($text);
			} else {
				app()->metadata()->setTitle('Популярные поисковые запросы');
			}
		} else {
			app()->breadCrumbs()
					->setElem('Статистика', '/statistics/')
					->setElem('Популярные страницы', 'statistics/common');
			$items = app()->db()->query()->setText('SELECT `id_city` as `response_id_city`, `url` as `response_url`, `title` as `response_title`, SUM(`count`) as `cnt` '
							. 'FROM `avg_stat_popular_pages` WHERE `timestamp_inserting` BETWEEN "' . DeprecatedDateTime::shiftMonths(-1) . '" AND "' . DeprecatedDateTime::now() . '" '
							. ($location > 0 ? 'AND `id_city` = ' . $location : '') . ' '
							//. 'AND `type` = "page" AND `url` != "" GROUP BY `url`, `id_city` ORDER BY `cnt` DESC LIMIT 100')->fetch();
							. 'AND `url` != "" GROUP BY `url`, `id_city` ORDER BY `cnt` DESC LIMIT 100')->fetch();
			$text = $this->text()->getByLink('statistics/popular');
			$text->setVal('text', self::replaceStatText($text->val('text')));
			if ($text->exists()) {
				app()->metadata()->setFromModel($text);
			} else {
				app()->metadata()->setTitle('Популярные страницы');
			}
		}


		$chart_items = [];
		$table_items = [];
		foreach ($items as $it) {
			$url = str()->posArray($it['response_url'], ['/catalog/', 'firm-', '/bytype/']) === false ? $it['response_url'] : app()->link($it['response_url'], $it['response_id_city']);
			$chart_items[$it['response_title']] = ['count' => $it['cnt'], 'link' => $mode === 'queries' ? app()->link('/search/price/?query=' . $it['request_text']) : $url];
			$it['response_url'] = $mode === 'queries' ? app()->link('/search/price/?query=' . $it['request_text']) : $url;
			$table_items[] = $it;
		}

		$this->view()->setTemplate('popular')
				->set('items', $table_items)
				->set('chart_items', $chart_items)
				//
				->set('breadcrumbs', app()->breadCrumbs()->render())
				->set('text', $text)
				->save();
	}

}
