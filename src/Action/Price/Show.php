<?php

namespace App\Action\Price;

use App\Model\Firm;
use App\Model\PriceCatalog;
use App\Model\PriceCatalogPrice;
use App\Model\StatObject;
use App\Model\StsCost;
use App\Presenter\FirmItems;
use App\Presenter\PriceItems;
use Sky4\Exception;
use function app;
use function str;

class Show extends \App\Action\Price {

	public function execute($id_price, $id_service = null) {
		if ($id_price && $id_service) {
			$this->findModelObject($id_price, $id_service);
			$this->checkUrl('/price/show/[0-9]+/[0-9]+/', $this->model()->link());
			app()->assert([$id_price, $id_service], ['int', 'int'], func_get_args(), 2);
		} elseif ($id_price) {
			$this->findModelObjectById($id_price);
			$this->checkUrl('/price/show/[0-9]+/', '/price/show/'.$this->model()->id().'/');
			app()->assert([$id_price], ['int'], func_get_args(), 1);
		}

		if ((int)$this->model()->val('flag_is_active') !== 1) {
			throw new Exception(Exception::TYPE_BAD_URL);
		}

		app()->stat()->addObject(StatObject::PRICE_SHOW, $this->model());

		$firm = $this->model()->getFirm();
        
        if ((int)$firm->val('flag_is_active') !== 1) {
            throw new Exception(Exception::TYPE_BAD_URL);
		}
        
		$pcp = new PriceCatalogPrice();
		$pcp->reader()
				->setWhere(['AND', '`id_price` = :id_price'], [':id_price' => $this->model()->id()])
				->setOrderBy('`node_level` DESC')
				->objectByConds();

		app()->breadCrumbs()->setElem('Каталог', app()->link('/catalog/'));
		$catalog = null;
		if ($pcp->exists()) {
			$catalog = new PriceCatalog((int)$pcp->val('id_catalog'));
			$path = $catalog->adjacencyListComponent()->getPath();
		} else {
			$catalog = new PriceCatalog();
			$catalog->reader()->setWhere(['AND', 'id_subgroup = :id_subgroup', 'node_level = :node_level'], [':id_subgroup' => $this->model()->val('id_subgroup'), ':node_level' => 2])
					->objectByConds();
			$path = $catalog->adjacencyListComponent()->reader()->pathObjects();
		}

		$price_parent_catalogs = [];
		$current_catalog_link = '';



		foreach ($path as $cat) {
			if ($cat->val('advert_restrictions')) {
				app()->adv()->setAdvertRestrictions($cat->val('advert_restrictions'));
			}
			if ($cat->val('agelimit')) {
				app()->adv()->setAdvertAgeRestrictions($cat->val('agelimit'));
			}

//			app()->breadCrumbs()
//					//->setElem($cat->name(), app()->link($cat->link() . ($cat->val('node_level') > 1 ? '?mode=price' : '')));
//					->setElem($cat->name(), app()->link($cat->link()));
			$current_catalog_link = app()->link($cat->link());
            
			app()->metadata()
					->setCanonicalUrl(app()->link(app()->linkFilter($cat->link(), ['mode' => 'price'])));

			if ($cat->val('node_level') > 1) {
				$price_parent_catalogs[$cat->id()] = ['name' => $cat->name(), 'link' => app()->link($cat->link().'?mode=price'), 'node_level' => $cat->val('node_level')];
			}
		}

		if ($price_parent_catalogs) {
			$pcp = new PriceCatalogPrice();
			$actual = $pcp->getCatalogsByIds(array_keys($price_parent_catalogs));
			$actual_keys = array_intersect_key($price_parent_catalogs, $actual);
			$_price_parent_catalogs_filtered = [];
			foreach ($price_parent_catalogs as $k => $v) {
				if ((int)$v['node_level'] === 2) {
					$_price_parent_catalogs_filtered[$k] = $v;
				}
			}
			foreach ($actual_keys as $k => $v) {
				$_price_parent_catalogs_filtered[$k] = $price_parent_catalogs[$k];
			}
			$price_parent_catalogs = $_price_parent_catalogs_filtered;
		}

		$this->setBreadCrumbs($path);

		if ($catalog->val('advert_restrictions')) {
			app()->adv()->setAdvertRestrictions($catalog->val('advert_restrictions'));
		}
		if ($catalog->val('agelimit')) {
			app()->adv()->setAdvertAgeRestrictions($catalog->val('agelimit'));
		}
		if ($catalog->val('id_subgroup')) {
			app()->adv()->setIdGroup($catalog->val('id_group'))
					->setIdSubGroup($catalog->val('id_subgroup'));
		}

		app()->adv()->setIdCatalog($catalog->id());

		app()->breadCrumbs()
				->setElem($this->model()->name(), $this->model()->link());

		app()->metadata()
				->setHeader($this->model()->name())
				->setMetatag('keywords', $this->getExtendedHeaderFromPrice().($this->model()->val('id_group') == 44 ? ' предложение и заказ услуги' : ' купить').app()->location()->currentCaseName('prepositional').', объявление id#'.$this->model()->val('id_price'))
				->setMetatag('description', 'Объявление id#'.$this->model()->val('id_price').app()->location()->currentCaseName('prepositional').' - '.$this->getExtendedHeaderFromPrice().'. Уточнить цены и '.($this->model()->val('id_group') == 44 ? 'заказать услугу' : 'купить товар').' вы можете позвонив в фирму или оформив заказ на сайте TovaryPlus.ru')
				->setTitle($this->getExtendedHeaderFromPrice().', '.app()->location()->currentName().' (id#'.$this->model()->val('id_price').')');
		//->setTitle($this->model()->name());

		$other_items_presenter = new PriceItems();
		$additional_items_presenter = new PriceItems();
		$other_items = $other_items_presenter->findOtherItemsByFirm($this->model(), $catalog)->renderItems();
		$additional_items = $additional_items_presenter->findOtherItemsByCatalog($this->model(), $catalog)->renderItems();

		//0.75

		if ($firm->hasAddress()) {
			app()->setUseMap(true);
			$presenter = new FirmItems();
			$presenter
					->setLimit(1000)
					->setPage($this->getPage());

			$presenter->findByIds([$firm->id() => 1], ['mode' => 'map'], false);
			$price_on_map = $presenter->setItemsTemplate('bottom_block_common_info_map')->renderItems();
		}

		$firm_catalog_analog_prices_url = '';
		$firm_catalog_name_analog_prices = '';
		$firm_all_prices_count = 0;
		$firm_catalog_analog_prices_count = 0;
		if ($catalog->val('id') && $firm->exists()) {
			$firm_catalog_name_analog_prices = str()->firstCharToUpper($catalog->val('web_many_name'));
			$firm_catalog_analog_prices_url = '/firm/show/'.$firm->id_firm().'/'.$firm->id_service().'/?id_catalog='.$catalog->val('id').'&mode=price';

			$pcp = new PriceCatalogPrice();
			$row = $pcp->reader()->setSelect('COUNT(*) as count')
					->setWhere(['AND', '`id_firm` = :id_firm', '`path` LIKE :path'], [':id_firm' => $firm->id(), ':path' => $catalog->getPathString().'%'])
					->rowByConds();

			$firm_all_prices_count = $firm->getTotalPriceListCount();
			$firm_catalog_analog_prices_count = $row['count'];
		}

		app()->frontController()->layout()->setTemplate('price');
		$this->view()
				->setTemplate('show')
				->set('advert_restrictions', app()->adv()->renderRestrictions())
				->set('advert_age_restrictions', app()->adv()->renderAgeRestrictions())
				->set('additional_items', $additional_items)
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('firm_catalog_analog_prices_url', $firm_catalog_analog_prices_url)
				->set('firm_catalog_name_analog_prices', $firm_catalog_name_analog_prices)
				->set('firm_all_prices_count', $firm_all_prices_count)
				->set('firm_catalog_analog_prices_count', $firm_catalog_analog_prices_count)
				->set('current_catalog_link', $current_catalog_link)
				->set('item', $this->model()->prepare($path))
				->set('firm', $firm)
				->set('other_items', $other_items)
				->set('price_parent_catalogs', $price_parent_catalogs)
				->set('price_on_map', isset($price_on_map) ? $price_on_map : FALSE)
				->save();
	}

	private function checkUrl($rule, $redirect) {
		if (!preg_match('~'.$rule.'~', app()->url())) {
			app()->response()->redirect($redirect, 301);
			exit();
		}
	}

	private function setBreadCrumbs($path) {
		foreach ($path as $cat) {
			app()->breadCrumbs()
					->setElem($cat->name(), app()->link($cat->link()));
		}
	}

	// Добавляем в header производство и фасовку
	private function getExtendedHeaderFromPrice() {
		$item = $this->model()->prepare();
		$header = $item['name'];
		if (str()->length($item['country_of_origin']) > 2) {
			$header .= ', производство: '.trim($item['production']);
		}

		return $header;
	}

}
