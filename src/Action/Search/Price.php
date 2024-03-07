<?php

namespace App\Action\Search;

use App\Presenter\PriceItems;

class Price extends \App\Action\Search {

	public function execute() {
		$filters = app()->request()->processGetParams([
			'query' => ['type' => 'string'],
			'id_catalog' => ['type' => 'int'],
			'id_city' => ['type' => 'int'],
			'mode' => ['type' => 'string'],
			'price_type' => ['type' => 'string'],
			'brand' => ['type' => 'string'],
			'with-price' => ['type' => 'string'],
			'prices' => ['type' => 'string'],
			'display_mode' => ['type' => 'string'],
			'sorting' => ['type' => 'string'],
		]);
        if ($filters['mode']==''){
            $filters['mode'] = 'firm';// делаем фирму первой в выдаче поиска Зачем??? - вопросы к Г.В. от 20.04.2021 
        }
        $filters['query'] = \App\Classes\Search::clearYo($filters['query']);

		$_SESSION['search_settings']['mode'] = 'price';
		$_SESSION['search_settings']['city'] = (int)app()->location()->currentId() === 76 ? 'index' : app()->location()->currentId();
		$_SESSION['search_settings']['action'] = app()->link('/search/'.$_SESSION['search_settings']['mode'].'/');

		$url = '/search/price/';
		app()->breadCrumbs()
				->setElem('Поиск товаров и услуг', app()->link(app()->linkFilter($url, $filters, ['id_catalog' => false])));

		$base_query = trim($filters['query']);
		$this->setQuery($base_query, true);

		$presenter = new \App\Presenter\Search();
		$presenter = $presenter->find($this->query, $filters);
		
		$items = $presenter->getItems();
		$has_results = count($items) > 0;

		$this->view()->set('pagination', $presenter->pagination()->render());

        $subgroup_ids = $presenter->getRawSubgroupIds();
        
		$this->setPriceCatalogsByQuery()
				->setPriceSubgroups($subgroup_ids, $url, $filters)
				->setCities($presenter->getRawCityIds(), $url, $filters)
				->setSearchSidebar($presenter, $filters, $url);
		//установка текста страницы и метатегов
		$this->text()->getByLink($has_results ? '/search' : 'bad_search_price');
		$this->text()->setVals([
			'text' => app()->metadata()->replaceLocationTemplates(str()->replace($this->text()->val('text'), '%query', encode($base_query)))
		]);
		app()->metadata()
				->setFromModel($this->text())
				->noIndex()
				->replace('%query', encode($base_query))
				->replace('%what', 'товаров и услуг ');

		//настройка баннеров и рекламных текстов
		/*app()->adv()
				->reset()
				->addKeyword($this->query);*/
        if (isset($filters['id_catalog']) && $filters['id_catalog']) {
            app()->adv()
                        ->setIdSubGroup($filters['id_catalog']);
        } else {
            if (true) {
                foreach($this->price_subgroup_matrix as $id_parent => $childs) {
                    foreach ($childs['items'] as $val) {
                        app()->adv()
                            ->setIdSubGroup($val['id_subgroup'])
                            ->setAdvertRestrictions($val['advert_restrictions'])
                            ->setAdvertAgeRestrictions($val['agelimit']);
                    }
                }
            } else {
                foreach ($this->price_catalogs as $pc) {
                    if ($pc->val('node_level') == 2) {

                    }
                }
            }
        }

		app()->tabs()
				->setTabs([
					['link' => app()->linkFilter(app()->link($url), $filters, ['mode' => 'price']), 'label' => 'Товары'],
					['link' => app()->linkFilter(app()->link($url), $filters, ['mode' => 'firm']), 'label' => 'Компании'],
					['link' => app()->linkFilter(app()->link($url), $filters, ['mode' => 'map']), 'label' => 'На карте', 'nofollow' => true]
				])
//				->setActiveTab($filters['mode'] === 'map' ? 2 : (($filters['mode'] === 'firm') ? 1 : 0))
				->setActiveTab($filters['mode'] === 'price' ? 0 : 1)
				->setActiveSortOption($filters['sorting'])
				->setFilters($filters)
				->setTabsNumericValues([$presenter->pagination()->getTotalRecordsParam('prices'), $presenter->pagination()->getTotalRecordsParam('firms')])
//				->setDisplayMode($filters['mode'] !== 'firm' && $filters['mode'] !== 'map')
				->setDisplayMode($filters['mode'] === 'price' ? false : true)
				->setSortOptions(PriceItems::getSortingOptions());
        
		if ($filters['mode'] === 'map') {
			app()->setUseMap(true);
		}

		$this->view()
				->set('tabs', app()->tabs()->render());

		app()->frontController()->layout()->setTemplate('catalog_unfixed_sidebar_footer');

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('title', 'Результаты поиска по запросу &QUOT;'.encode($base_query).'&QUOT;')
				->set('filters', $filters)
				->set('has_results', $has_results)
				->set('query', encode($base_query))
				->set('text', $this->text()->val('text'))
				//
				->set('price_catalogs', $this->renderPriceCatalogs())
				->set('items', $presenter->renderItems())
				->setTemplate('prices_new', 'search')
				->save();
	}

}
