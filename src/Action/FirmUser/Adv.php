<?php

namespace App\Action\FirmUser;

use App\Action\FirmUser;
use App\Model\Banner;
use App\Presenter\FirmUserStatistics;
use function app;

class Adv extends FirmUser {

	public function execute($html_mode = null, $filters = null) {
		$filters = $this->getFilters();
		$this->text()->getByLink('firm-user/adv');
		app()->metadata()->setFromModel($this->text());
		if (!$this->text()->exists()) {
			app()->metadata()->setTitle('Личный кабинет - статистика баннеров');
		}

        $url = '/adv/';
		$base_url = self::link($url);
		if ($filters === null) {
			$filters = self::initFilters(app()->request()->processGetParams([
								't_start' => ['type' => 'int'],
								't_end' => ['type' => 'int'],
								'group' => ['type' => 'string'],
								'page' => ['type' => 'int'],
								'id' => ['type' => 'int']
			]));
		}

		list($dates_block, $visible) = self::getDatesBlock($url, $filters);

		if (!isset($filters['id']) || $filters['id'] === null) {
			$b = new Banner();
			$count_banners = (int)$b->reader()
							->setWhere(['AND', 'id_firm = :id_firm'], [':id_firm' => app()->firmUser()->id_firm()])
							->count();

			if ($count_banners > 0) {
				$presenter = new FirmUserStatistics();
				$presenter->setLimit($this->isHtmlMode() ? 99999 : 20);

                $presenter->findBanners($filters);

				app()->metadata()
						->setJsFile('https://www.google.com/jsapi')
						->setJs('google.load("visualization", "1", {packages: ["corechart", "line"]});');

				$tabs = [
					['link' => app()->linkFilter($base_url, $filters, ['mode' => false]), 'label' => 'Статистика']
				];

				app()->tabs()
						->setActiveTab(0)
						->setLink('/firm-user/adv/')
						->setTabs($tabs)
						->setFilters($filters)
						->setActiveGroupOption($filters['group'])
						->setGroupOptions([
							'months' => ['name' => 'по месяцам'],
							'weeks' => ['name' => 'по неделям'],
				]);

				if ($this->isHtmlMode()) {
					return $presenter->renderItems();
				}

				$this->view()
						->set('dates_block', $dates_block)
						->set('items', $presenter->renderItems())
						->set('pagination', $presenter->pagination()->render(true))
						->set('tabs', app()->tabs()->render(null, true));
			}

			$this->view()
					->set('bread_crumbs', app()->breadCrumbs()->render(true))
					->set('has_banners', $count_banners > 0)
					->setTemplate('banner_index')
					->save();
		} else {
			if ($filters['id'] === 0 && $filters['html_mode']) {
				$banner = new Banner();
				$all = $banner->reader()
						->setWhere(['AND', '`id_firm` = :id_firm'], [':id_firm' => app()->firmUser()->id_firm()])
						->setOrderBy('`id` DESC')
						->objects();
				

				$result = '';
				foreach ($all as $banner) {
					$presenter = new FirmUserStatistics();
					$presenter->setLimit(999);
					$presenter->findBannerClicks($banner, $filters);
					$result .= $presenter->renderItems();
				}
				return $result;
			} else {
				$banner = new Banner($filters['id']);
				app()->metadata()->setTitle('Личный кабинет - статистика баннеров - баннер #'.$banner->id());

				app()->breadCrumbs()
						->setElem('Статистика баннеров', '/firm-user/adv/')
						->setElem('Баннер #'.$banner->id());

				$presenter = new FirmUserStatistics();
				$presenter->setLimit(20);
				$presenter->findBannerClicks($banner, $filters);

				$this->view()
						->set('bread_crumbs', app()->breadCrumbs()->render(true))
						->set('ban', $banner)
						->set('items', $presenter->renderItems())
						->set('pagination', $presenter->pagination()->render(true))
						->set('dates_block', $dates_block)
						//->set('has_banners', $count_banners > 0)
						->setTemplate('banner_clicks')
						->save();
			}
		}
	}

}
