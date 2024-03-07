<?php

namespace App\Action\Catalog;

use App\Model\PriceCatalog;
use App\Presenter\PriceItems;
use Sky4\Exception as Exception;
use App\Model\AdvText;
use function app;
use function str;

class Level3 extends \App\Action\Catalog {

	public function execute($id_group, $id_subgroup, $id_catalog = null, $mnemonick = null) {
		$this->init($id_group, $id_subgroup, $id_catalog, $mnemonick);

		$group = (new PriceCatalog())->getGroup($id_group);
		$subgroup = (new PriceCatalog())->getSubGroup($id_group, $id_subgroup);

		if (!($group->exists() && $subgroup->exists())) {
			throw new \Sky4\Exception(Exception::TYPE_BAD_URL);
		}

		$catalog = new PriceCatalog($id_catalog);
		if ($id_catalog !== null && (!$catalog->exists() || ($catalog->id_group() !== (int)$id_group || $catalog->id_subgroup() !== (int)$id_subgroup))) {
			throw new \Sky4\Exception(Exception::TYPE_BAD_URL);
		}

		if (!$catalog->exists()) {
			$catalog = $subgroup;
		}

		$filters = $this->getFilters();
		$presenter = new \App\Presenter\Catalog();
		$presenter = $presenter->setCatalog($catalog)
				->setFilterVals($filters)
				->setPage($this->getPage())
				->find();
        
		if ((int)$presenter->pagination()->getTotalPages() < (int)$filters['page']) {
			$url = parse_url(app()->uri())['path'];
			app()->response()->redirect(app()->link(app()->linkFilter($url, $filters, ['page' => false])), 301);
		}

		$this->setMetadata($catalog, $presenter, $mnemonick)
				->setTabs($presenter, $id_group);

		$items_exists = count($presenter->getItems()) > 0;
		$no_items_groups = [];
        if (app()->isNewTheme()) {
            if (in_array($id_group, [22,44])) {
                app()->frontController()->layout()
                        ->setVar('rubrics', app()->chunk()->setArg($id_group)->render('catalog.rubrics'))
                        ->setVar('mobile_rubrics', app()->chunk()->setArg($id_group)->render('catalog.mobile_rubrics'));
            }
        }

		if (!$items_exists) {
			$parent = $catalog->adjacencyListComponent()->getParent();
			$no_items_groups = $this->getTagsByCatalog($parent);
			app()->metadata()->noIndex(true);
			$sidebar_params = app()->sidebar()->getParams();
            
			if (isset($sidebar_params['right_layout_filter_sidebar'])) {
				app()->frontController()->layout()->setTemplate('catalog_unfixed_sidebar_footer');
			} else {
				app()->frontController()->layout()->setTemplate('page');
			}
			app()->response()->code404();
		} else if (!app()->location()->city()->exists()) {
			app()->metadata()->noIndex();
		}

		$analog_catalogs = $this->getAnalogCatalogs($catalog);
		$promo = new \App\Presenter\FirmPromoItems();
		$promo_items = app()->inDebugMode() ? '' : $promo->findFirmPromos($id_group, $id_subgroup)->renderItems();

		if ((int)$presenter->pagination()->getPage() !== 1) {
			app()->metadata()->setCanonicalUrl(app()->link('/catalog/'.$id_group.'/'.$id_subgroup.'/'
							.($id_catalog != null ? $id_catalog.'/' : '').($mnemonick != null ? $mnemonick : '')
							.($filters['mode'] === 'price' ? '?mode=price' : '')));
		} else if ((int)$presenter->pagination()->getPage() === 1 && (isset($filters['brand']) && $filters['brand'])) {
			app()->metadata()->setCanonicalUrl(app()->link('/catalog/'.$id_group.'/'.$id_subgroup.'/'
							.($id_catalog != null ? $id_catalog.'/' : '').($mnemonick != null ? $mnemonick : '')
                            .('?brand=' . $filters['brand'])
							.($filters['mode'] === 'price' ? '&mode=price' : '')));
		}

		$title = $this->getCatalogTitle() . app()->metadata()->getFilterString($filters);
		if ($filters['mode'] === 'price') {
			$title .= ' '.app()->location()->currentCaseName('prepositional');
		}

                $adv_text = new AdvText();
        
		//$idcat = new PriceCatalog();
        $href_idcat = '#';
        $idcat = '';
        if ($id_catalog != null){
            $idcat = $id_catalog;
            $href_idcat = 'https://www.tovaryplus.ru/cms/data-manager/update-object/price-catalog/'.$idcat.'/';
        }else{
            $idcat = $id_subgroup;
            $href_idcat = 'https://www.tovaryplus.ru/cms/data-manager/update-object/price-catalog/'.$idcat.'/';
        }
        
		$this->view()
				->set('advert_restrictions', app()->adv()->renderRestrictions())
				->set('advert_age_restrictions', app()->adv()->renderAgeRestrictions())
				->set('analog_catalogs', $analog_catalogs)
				->set('bottom_text', $catalog->getBottomText())
				->set('ext_title', $title)
				->set('filters', $filters)
				->set('group', $group)
				->set('item', $catalog)
				->set('items', $presenter->renderItems())
				->set('items_exists', $items_exists)
				->set('no_items_groups', $no_items_groups)
				->set('pagination', $presenter->pagination()->render())
				->set('promo_items', $promo_items)
				->set('promo_catalog_data', $catalog->getPromoCatalogData($id_group, $id_subgroup))
				->set('sub_group', $subgroup)
				->set('idcat', $idcat)
				->set('href_idcat', $href_idcat)
				->set('tabs', app()->tabs()->render())
				->set('tags', $this->getTagsByCatalog($catalog->exists() ? $catalog : $subgroup, true))
				->set('top_text', $catalog->getTopText($id_group, $id_subgroup, $filters))
				->set('annotation_text', $catalog->getAnnotationText($id_group, $id_subgroup, $filters))
				->set('total_firms_count', $presenter->pagination()->getTotalRecordsParam('firms'))
				->set('total_prices_count', $presenter->pagination()->getTotalRecordsParam('prices'))
                                ->set('position', $adv_text->getByUrl(app()->location()->linkPrefix() . app()->request()->getRequestUri()))
				->setTemplate('catalog_level_3');

		return $this->afterExecute($this);
	}

	public function init($id_group, $id_subgroup, $id_catalog, $mnemonick) {
		app()->frontController()->layout()->setTemplate('catalog_unfixed_sidebar');
		$test_params = $_GET;
		if (isset($test_params['url'])) {
			unset($test_params['url']);
		}

		$this->catalogRedirect(func_get_args());
		app()->assert([$id_group, $id_subgroup, $id_catalog], ['int', 'int', 'intnull']);
		if ($mnemonick === null && $id_catalog === null && !$test_params) {
			if (str()->sub(app()->request()->getRequestUri(), -1) !== '/' && !isset($_GET['mode'])) {
				app()->response()->redirect(app()->link(app()->request()->getRequestUri().'/'), 301);
			}
		}

		app()->breadCrumbs()
				->setElem('Каталог', app()->link('/catalog/'));

		return $this;
	}

	public function getFilters() {
		return app()->request()->processGetParams([
					'brand' => ['type' => 'string'],
					'discount' => ['type' => 'int'],
					'prices' => ['type' => 'string'],
					'with-price' => ['type' => 'int'],
					'price_type' => ['type' => 'string'],
            
					'display_mode' => ['type' => 'string'],
            
					'sorting' => ['type' => 'string'],
            
					'mode' => ['type' => 'string'],
            
					'page' => ['type' => 'int']
		]);
	}

	public function setMetadata(PriceCatalog $catalog, \App\Presenter\Presenter $presenter, $mnemonick = null) {
		$filters = $presenter->getFilterVals();
		if ($mnemonick !== null && str()->translit(trim($catalog->val('web_many_name'))).'.htm' !== (string)$mnemonick) {
			app()->response()->redirect(app()->link($catalog->link()), 301);
		}
		app()->metadata()
				->setNew($catalog, $filters, $presenter->pagination())
				->setHeader($catalog->name());

		$this->setCanonicalLink($catalog->link(), $filters);

		return $this;
	}

	public function setTabs(\App\Presenter\Presenter $presenter, $id_group) {
		$filters = $presenter->getFilterVals();
        
		$tabs = [
			['link' => app()->linkFilter($presenter->pagination()->getLink(), $filters, ['mode' => false, 'page' => false]), 'label' => 'Компании'],
			['link' => app()->linkFilter($presenter->pagination()->getLink(), $filters, ['mode' => 'price', 'page' => false]), 'label' => (int)$id_group === 44 ? 'Услуги' : 'Товары'],
			['link' => app()->linkFilter($presenter->pagination()->getLink(), $filters, ['mode' => 'map', 'page' => false]), 'label' => 'На карте', 'nofollow' => true]
				//todo убрать отсюда карту, когда будет готова верстка
		];
		app()->setVar('on_map_link', app()->linkFilter($presenter->pagination()->getLink(), $filters, ['mode' => 'map', 'page' => false]));
        
		app()->tabs()
				->setActiveSortOption(self::getCurrentSorting($filters))
				->setActiveTab($filters['mode'] == 'price' ? 1 : ($filters['mode'] == 'map' ? 2 : 0))
				//->setLink(app()->link(app()->uri()))
				->setDisplayMode($filters['mode'] === 'price' ? true : false)
				->setFilters($filters)
				->setTabs($tabs)
				->setTabsNumericValues([$presenter->pagination()->getTotalRecordsParam('firms'), $presenter->pagination()->getTotalRecordsParam('prices'), null]);
        
		return $this;
	}

}
