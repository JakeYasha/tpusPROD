<?php

namespace App\Action\Search;

use App\Presenter\FirmItems;
use function app;
use function str;

class Firms extends \App\Action\Search {

	public function execute() {
		$filters = app()->request()->processGetParams([
			'query' => ['type' => 'string'],
			'id_type' => ['type' => 'int'],
			'mode' => ['type' => 'string'],
		]);
        
        $filters['query'] = \App\Classes\Search::clearYo($filters['query']);

		$_SESSION['search_settings']['mode'] = 'firms';
		$_SESSION['search_settings']['city'] = (int) app()->location()->currentId() === 76 ? 'index' : app()->location()->currentId();
		$_SESSION['search_settings']['action'] = app()->link('/search/' . $_SESSION['search_settings']['mode'] . '/');

		app()->breadCrumbs()
				//->setElem('Результаты поиска', app()->link(app()->linkFilter('/search/', $filters)))
				->setElem('Поиск фирм', app()->link(app()->linkFilter('/search/firms/', $filters, ['id_type' => false])));

		$base_query = trim($filters['query']);
		$this->setQuery(trim($filters['query']));

		$firm_presenter = new FirmItems();
		$firm_presenter
				->setForceHideActivity(true)
				->findByQuery($this->query, $filters);
		$this->view()->set('pagination', $firm_presenter->pagination()->render());

		$has_results = count($firm_presenter->getItems()) > 0;

		$this->setFirmCatalogsByQuery();

		//установка текста страницы и метатегов
		$this->text()->getByLink($has_results ? '/search' : 'bad_search_firms');
		$this->text()->setVals([
			'text' => app()->metadata()->replaceLocationTemplates(str()->replace($this->text()->val('text'), '%query', encode($base_query)))
		]);
		app()->metadata()
				->setFromModel($this->text())
				->noIndex()
				->replace('%query', encode($base_query))
				->replace('%what', 'фирм ');

		//настройка баннеров и рекламных текстов
		//app()->adv()->addKeyword($this->query);
		foreach ($this->firm_catalogs as $pc) {
			app()->adv()->setAdvertRestrictions($pc->val('advert_restrictions'));
		}

		app()->tabs()
				->setTabs([['link' => app()->linkFilter(app()->link('/search/firms/'), $filters, ['mode' => false]), 'label' => 'Компании'], ['link' => app()->linkFilter(app()->link('/search/firms/'), $filters, ['mode' => 'map']), 'label' => 'На карте']])
				->setActiveTab($filters['mode'] === 'map' ? 1 : 0)
				->setFilters($filters)
				->setTabsNumericValues([$firm_presenter->pagination()->getTotalRecords(), null]);

		if ($filters['mode'] === 'map') {
			app()->setUseMap(true);
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('filters', $filters)
				->set('has_results', $has_results)
				->set('query', encode($base_query))
				->set('tabs', app()->tabs()->render())
				->set('text', $this->text()->val('text'))
				//
				->set('firm_catalogs', $this->renderFirmCatalogs())
				->set('items', $firm_presenter->renderItems())
				->set('query', $this->query)
				->setTemplate('firms', 'search')
				->save();
	}

}
