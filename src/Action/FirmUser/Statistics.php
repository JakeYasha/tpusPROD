<?php

namespace App\Action\FirmUser;

class Statistics extends \App\Action\FirmUser {

	public function execute() {
		$filters = $this->getFilters();
		$this->text()->getByLink('firm-user/statistics');
		if (!$this->text()->getVal('title')) {
			$this->text()->setVal('metadata_title', 'Личный кабинет - статистика tovarylpus.ru');
		}
		app()->metadata()->setFromModel($this->text());

		$url = '/statistics/';
		$base_url = self::link('/statistics/');
		if ($filters !== null) {
			$this->params = $filters;
		} else {
			$this->params = self::initFilters(app()->request()->processGetParams([
								'mode' => 'string',
								't_start' => 'int',
								't_end' => 'int',
								'group' => 'string',
								'page' => 'int'
			]));
		}

		//
		app()->breadCrumbs()
				->setElem('Статистика', $base_url);

		$presenter = new \App\Presenter\FirmUserStatistics();

		$dates_block = '';
		switch ($this->params['mode']) {
			case 'pages' : $active_index = 1;
				list($dates_block, $visible) = self::getDatesBlock($url, $this->params);
				$presenter->findPages($this->params);
				break;
			case 'dynamic' : $active_index = 2;
				$presenter->findDynamic($this->params);
				break;
			case 'cities' : $active_index = 3;
				list($dates_block, $visible) = self::getDatesBlock($url, $this->params);
				$presenter->findGeo($this->params);
				break;
			default :$active_index = 0;
				list($dates_block, $visible) = self::getDatesBlock($url, $this->params);
				$presenter->findSummary($this->params);
				break;
		}

		app()->metadata()
				->setJsFile('https://www.google.com/jsapi')
				->setJs('google.load("visualization", "1", {packages: ["corechart", "line"]});');

		$tabs = [
			['link' => app()->linkFilter($base_url, $this->params, ['mode' => false]), 'label' => 'Обзор'],
			// ['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'pages']), 'label' => 'Страницы'],
			// ['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'dynamic']), 'label' => 'Динамика посещений'],
			// ['link' => app()->linkFilter($base_url, $this->params, ['mode' => 'cities']), 'label' => 'География'],
		];

		app()->tabs()
				->setActiveTab($active_index)
				->setLink('/firm-user/statistics/')
				->setTabs($tabs)
				->setFilters($this->params)
				->setActiveGroupOption($this->params['group'])
				->setGroupOptions([
					'months' => ['name' => 'по месяцам'],
					'weeks' => ['name' => 'по неделям'],
		]);

		if ($this->isHtmlMode()) {
			return $presenter->renderItems();
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('dates_block', $dates_block)
				->set('items', $presenter->renderItems())
                ->set('pagination', $presenter->pagination()->render(true))
				->set('tabs', app()->tabs()->render(null, true))
				->setTemplate('statistics_index')
				->save();
	}

}
