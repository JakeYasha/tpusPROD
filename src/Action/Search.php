<?php

namespace App\Action;

use App\Classes\Action;
use App\Model\Brand;
use App\Model\Firm;
use App\Model\FirmFirmType;
use App\Model\FirmType;
use App\Model\PriceCatalog;
use App\Model\StsCity;
use App\Model\Synonym;
use App\Model\WordException;
use App\Presenter\PriceItems;
use CLangCorrect;
use Foolz\SphinxQL\SphinxQL;
use Sky4\Model\Utils;
use const SPHINX_FIRM_CATALOG_INDEX;
use const SPHINX_FIRM_INDEX;
use const SPHINX_MAX_INT;
use const SPHINX_PRICE_INDEX;
use function app;
use function str;

class Search extends Action {

	protected $firm_catalogs = [];
	protected $firm_catalogs_matrix = [];
	protected $firms_total_found = 0;
	protected $price_catalogs = [];
	protected $price_catalogs_matrix = [];
	protected $prices_total_found = 0;
	protected $price_subgroup_matrix = [];
	protected $price_city_matrix = [];
	protected $query = null;

	public function execute() {
		$has_results = false;
		$filters = app()->request()->processGetParams([
			'query' => 'string'
		]);

		$filters['query'] = $this->prepareSearchQuery($filters['query']);

		app()->breadCrumbs()
				->setElem('Результаты поиска', app()->link(app()->linkFilter('/search/', $filters)));

		$base_query = $filters['query'];
		$this->setQuery($filters['query'], true);

		//получаем фирмы, товары и каталоги
		list($firms, $prices, $price_catalogs, $firm_catalogs) = $this->setSearch();
		if (!($firms || $prices || $price_catalogs || $firm_catalogs)) {
			$lc = new CLangCorrect();
			$new_query = $lc->parse($this->query, CLangCorrect::KEYBOARD_LAYOUT);
			if ($new_query !== $this->query) {
				$this->setQuery($new_query, true);
				list($firms, $prices, $price_catalogs, $firm_catalogs) = $this->setSearch();
				if ($firms || $prices || $price_catalogs || $firm_catalogs) {
					$this->query = $new_query;
					$base_query = $new_query;
					$filters['query'] = $new_query;
					$has_results = true;
				}
			}
		} else {
			$has_results = true;
			/* foreach ($firms as $firm) {
			  app()->stat()->addObject(StatObject::SEARCH_MAIN, $firm);
			  } */
		}

		//установка текста страницы и метатегов
		$this->text()->getByLink($has_results ? '/search' : 'bad_search');
		$this->text()->setVals([
			'text' => app()->metadata()->replaceLocationTemplates(str()->replace($this->text()->val('text'), '%query', $base_query))
		]);
		app()->metadata()
				->setFromModel($this->text())
				->noIndex()
				->replace('%query', $base_query)
				->replace('%what', '');

		//установка табов
		$this->setTabs($filters, $firms, $prices);

		//настройка баннеров и рекламных текстов
		//app()->adv()->addKeyword($this->query);
		foreach ($this->price_catalogs as $pc) {
			app()->adv()->setIdCatalog($pc->id());
			app()->adv()
					->setIdSubGroup($pc->id_subgroup())
					->setAdvertRestrictions($pc->val('advert_restrictions'))
					->setAdvertAgeRestrictions($pc->val('agelimit'));
		}
		foreach ($this->firm_catalogs as $pc) {
			app()->adv()->setAdvertRestrictions($pc->val('advert_restrictions'));
		}


		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('filters', $filters)
				->set('has_results', $has_results)
				->set('query', $base_query)
				->set('tabs', app()->tabs()->render())
				//
				->set('firms', $firms)
				->set('firms_total_found', ($this->firms_total_found - 3) <= 0 ? 0 : ($this->firms_total_found - 3))
				->set('firm_catalogs', $this->renderFirmCatalogs())
				->set('prices', $prices)
				->set('prices_total_found', ($this->prices_total_found - 20) <= 0 ? 0 : ($this->prices_total_found - 20))
				->set('price_catalogs', $this->renderPriceCatalogs())
				//
				->set('text', $this->text()->val('text'))
				->setTemplate('index', 'search')
				->save();
	}

	//getters
	public function getFirmsByQuery($limit = 3) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$results = $sphinx
				->select('*', SphinxQL::expr('WEIGHT() as weight'))
				->limit(0, $limit)
				->from([SPHINX_FIRM_INDEX]) //0
				->where('id', 'IN', app()->location()->getFirmIds())
				->match('(company_name,company_name_jure,company_activity,company_address,company_phone)', SphinxQL::expr($this->query))
				->orderBy('priority', 'DESC')
				->orderBy('weight', 'DESC')
				->orderBy('rating', 'DESC')
				//->orderBy('sortname', 'ASC')
				->option('field_weights', ['company_name' => 10, 'company_activity' => 0, 'company_phone' => 0, 'company_address' => 0])
				->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25+3*sum(hit_count*user_weight)')"))
				->enqueue()
				->query('SHOW META')
				->executeBatch();

		$firm = new Firm();
		$firm_ids = [];
		$firms = [];
		if (isset($results[0]) && $results[0]) {
			foreach ($results[0] as $res) {
				$firm_ids[] = $res['id'];
			}
		}

		$firms = $firm->reader()->objectsByIds($firm_ids);
		$this->firms_total_found = $results[1][1]['Value'];

		return $firms;
	}

	public function getFullPricesByQuery($query, $filters) {
		$result = [];
		$price_presenter = new PriceItems();
		$price_presenter->findByQuery($query, $filters);

		if ($price_presenter->getItems()) {
			$this->view()->set('pagination', $price_presenter->pagination()->render());
			$result = $price_presenter->renderItems();
		} else {
			$this->view()->set('pagination', '');
		}

		$catalog_ids = $price_presenter->getCatalogIds();
		$catalog_tags = [];
		if ($catalog_ids) {
			$pc = new PriceCatalog();
			$where_conds = Utils::prepareWhereCondsFromArray($catalog_ids);
			$order_conds = Utils::prepareOrderByField($catalog_ids, 'id');
			$pc_where = ['AND', 'node_level = :node_level', $where_conds['where']];
			$pc_params = array_merge([':node_level' => 2], $where_conds['params'], $order_conds['params']);
			$catalog_tags = $pc->reader()
					->setWhere($pc_where, $pc_params)
					->setOrderBy($order_conds['order'])
					->objects();
		}

		$this->view()->set('price_tags', $catalog_tags);

		return $result;
	}

	public function getPricesByQuery($limit = 20) {
		$price_presenter = new PriceItems();
		$this->prices_total_found = $price_presenter->findPopularByQuery($this->query);

		return $price_presenter->getItems() ? $price_presenter->renderItems() : [];
	}

	//setters
	protected function setCities($city_ids, $url, $filters) {
		if ($city_ids) {
			$city = new StsCity();
			$city_conds = Utils::prepareWhereCondsFromArray(array_keys($city_ids), 'id_city');
			$city_order = Utils::prepareOrderByField(array_keys($city_ids), 'id_city');
			$cities = $city->reader()
					->setSelect(['id_city', 'name'])
					->setWhere($city_conds['where'], $city_conds['params'] + $city_order['params'])
					->setOrderBy($city_order['order'])
					->rows();

			$matrix = [];
			if ($cities) {
				foreach ($cities as $city) {
					$matrix[$city['id_city']] = [
						'id' => $city['id_city'],
						'id_city' => $city['id_city'],
						'count' => $city_ids[$city['id_city']],
						'name' => str()->firstCharsOfWordsToUpper(str()->toLower($city['name'])),
						'link' => app()->linkFilter($url, $filters, ['id_city' => $city['id_city']])
					];
				}
			}

			if (count($matrix) === 1) {
				$matrix = [];
			}

			$this->price_city_matrix = $matrix;
		}

		return $this;
	}

	protected function setFirmCatalogsByQuery($limit = 1000) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$result = $sphinx
				->select('id', 'parent_node', 'node_level', SphinxQL::expr('WEIGHT() as weight'))
				->limit(0, $limit)
				->from([SPHINX_FIRM_CATALOG_INDEX])
				->match('name', SphinxQL::expr($this->query))
				->where('node_level', '=', 2)
				->orderBy('weight', 'DESC')
				->option('max_matches', SPHINX_MAX_INT)
				->option('ranker', 'sph04')
				->execute();

		$catalog_ids = [];
		$parent_ids = [];
		$matrix = [];
		$test_ids = [];
		foreach ($result as $row) {
			$parent = $row['parent_node'];
			if (!isset($matrix[$parent])) {
				$matrix[$parent] = [];
			}
			$matrix[$parent][] = $row['id'];

			$catalog_ids[] = $row['id'];
			$parent_ids[] = $parent;
		}

		if ($catalog_ids) {
			$fft = new FirmFirmType();
			$fft_type_conds = Utils::prepareWhereCondsFromArray($catalog_ids, 'id_type');

			$fft_city_conds = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
			$fft_where = ['AND', $fft_city_conds['where'], $fft_type_conds['where'], 'flag_is_active = :flag_is_active'];
			$fft_params = array_merge($fft_city_conds['params'], $fft_type_conds['params'], [':flag_is_active' => 1]);

			$test_ids = array_keys($fft->reader()
							->setSelect(['id_type'])
							->setWhere($fft_where, $fft_params)
							->rowsWithKey('id_type'));

			foreach ($catalog_ids as $k => $cat_id) {
				if (!in_array($cat_id, $test_ids)) {
					unset($catalog_ids[$k]);
				}
			}

			foreach ($matrix as $k => $catalogs) {
				foreach ($catalogs as $kk => $cat_id) {
					if (!in_array($cat_id, $catalog_ids)) {
						unset($matrix[$k][$kk]);
					}
				}
			}

			$catalog_ids = array_merge($catalog_ids, $parent_ids);

			$ft = new FirmType();
			$catalogs = $ft->reader()->objectsByIds(array_filter($catalog_ids));

			$this->firm_catalogs = $catalogs;
			$this->firm_catalogs_matrix = $matrix;
		}



		return $this;
	}

	protected function setPriceCatalogsByQuery($limit = 1000) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());

		$result = $sphinx
				->select('id_catalog', 'id_parent', 'node_level', SphinxQL::expr('WEIGHT() as weight'))
				->limit(0, $limit)
				->from([SPHINX_PRICE_CATALOG_INDEX])
				->match('(name,web_name,web_many_name)', SphinxQL::expr($this->query))
				->orderBy('weight', 'DESC')
				->groupBy('id_catalog')
				->option('max_matches', SPHINX_MAX_INT)
				->option('ranker', 'sph04')
				->execute();

		$catalog_ids = [];
		$matrix = [];
		foreach ($result as $row) {
			$catalog_ids[] = $row['id_catalog'];
			$catalog_ids[] = $row['id_parent'];
		}

		$catalogs = [];
		if ($catalog_ids) {
			$pc = new PriceCatalog();
			$catalogs_ids = (new \App\Model\PriceCatalogPrice())->getActualCatalogIds(array_filter($catalog_ids));
			$catalogs = $pc->reader()->objectsByIds($catalogs_ids);
		}

		foreach ($result as $row) {
			if (isset($catalogs[$row['id_catalog']]) && isset($catalogs[$row['id_parent']])) { //@todo
				$parent = $row['id_parent'];
				if (!isset($matrix[$parent])) {
					$matrix[$parent] = [];
				}
				$matrix[$parent][] = $row['id_catalog'];
			}
		}

		$this->price_catalogs = $catalogs;
		$this->price_catalogs_matrix = $matrix;

		return $this;
	}

	protected function setPriceSubgroups($subgroup_ids, $url, $filters) {
		if ($subgroup_ids) {
			$cat = new PriceCatalog();
			$cat_conds = Utils::prepareWhereCondsFromArray(array_keys($subgroup_ids), 'id_subgroup');
			$cat_order = Utils::prepareOrderByField(array_keys($subgroup_ids), 'id_subgroup');
			$subgroups = $cat->reader()
					->setSelect(['id', 'id_group', 'id_subgroup', 'parent_node', 'web_many_name', 'advert_restrictions', 'agelimit'])
					->setWhere(['AND', 'node_level = :node_level', $cat_conds['where']], [':node_level' => 2] + $cat_conds['params'] + $cat_order['params'])
					->setOrderBy($cat_order['order'])
					->rows();

			$matrix = [];
			if ($subgroups) {
				$group_ids = [];
				foreach ($subgroups as $row) {
					$group_ids[] = $row['parent_node'];
				}

				$groups = $cat->reader()->objectsByIds($group_ids);


				foreach ($subgroups as $id => $cnt) {
					if (!isset($matrix[$groups[$subgroups[$id]['parent_node']]->id()])) {
						$matrix[$groups[$subgroups[$id]['parent_node']]->id()] = [
							'name' => $groups[$subgroups[$id]['parent_node']]->name(),
							'items' => []
						];
					}
					$matrix[$groups[$subgroups[$id]['parent_node']]->id()]['items'][] = [
						'id' => $subgroups[$id]['id'],
						'count' => $subgroup_ids[$subgroups[$id]['id_subgroup']],
						'name' => $subgroups[$id]['web_many_name'],
						'link' => app()->link(app()->linkFilter($url, $filters, ['id_catalog' => $subgroups[$id]['id_subgroup']])),
						'id_subgroup' => $subgroups[$id]['id_subgroup'],
						'advert_restrictions' => $subgroups[$id]['advert_restrictions'],
						'agelimit' => $subgroups[$id]['agelimit'],
					];
				}
			}

			$this->price_subgroup_matrix = $matrix;
		}

		return $this;
	}

	public function setQuery($query, $use_synonims = false) {
		$query = str()->toLower(trim(self::clearQuery($query)));

		if (str()->length($query) === 0) {
			app()->response()->redirect(app()->link('/search/empty/'));
		}

		$we = new WordException();
		$exceptions = $we->reader()->rowsWithKey('name');
		$words = explode(' ', $query);
		$result_words = [];
		$_query = $query;
		foreach ($words as $word) {
			$word = trim($word);
			if (!isset($exceptions[$word])) {
				$result_words[] = $word;
			}
		}

		$query = implode(' ', $result_words);
		$result_words[] = $_query;

		if ($use_synonims) {
			//синонимы
			$replace = [];
			$syn = new Synonym();
			foreach ($result_words as $k => $s) {
				$synonims = $syn->reader()
					->setWhere(['AND', "`search` = :search"], [':search' => $s])
					->rows();

				foreach ($synonims as $kk => $r) {
					$replace[$k][$kk] = $r['replace'];
				}
			}

			$words_with_synonims = [];
			if ($replace) {
				foreach ($replace as $k => $synonims) {
					foreach ($synonims as $kk => $rep) {
						$words_with_synonims[] = '('.str()->replace($query, $result_words[$k], $replace[$k][$kk]).')';
					}
				}
			}

			$words_with_synonims = array_unique($words_with_synonims);
			if ($words_with_synonims) {
				$query = '('.$query.')|'.implode('|', array_reverse($words_with_synonims));
			}
		}

		$this->query = $query;

		return $this->query;
	}

	protected function setSearch() {
		$this->setPriceCatalogsByQuery();
		$this->setFirmCatalogsByQuery();

		$firms = $this->getFirmsByQuery();
		$goods = $this->getPricesByQuery();
		$price_catalogs = $this->price_catalogs;
		$firm_catalogs = $this->firm_catalogs;

		return [$firms, $goods, $price_catalogs, $firm_catalogs];
	}

	public function setSearchSidebar($presenter, $filters, $url) {
		$raw_price_ids = $presenter->getRawPriceIds();
		if ($raw_price_ids) {
			$brand_ids = [];
			$conds = Utils::prepareWhereCondsFromArray($raw_price_ids, 'price_id');
			$brand_ids = array_keys((new \App\Model\BrandPrice())->reader()
							->setSelect(['DISTINCT brand_id'])
							->setWhere(['AND', $conds['where']], $conds['params'])
							->rowsWithKey('brand_id'));

			$brands = [];
            $top_brands = [];
			if ($brand_ids) {
				$b = new Brand();
				$b_conds = Utils::prepareWhereCondsFromArray($brand_ids, 'id');
				$b_where = ['AND'];
				$b_where[] = $b_conds['where'];
				$b_where[] = '`count` > :count';
				$b_params = array_merge($b_conds['params'], [':count' => 1]);

				$brands = $b->reader()
						->setSelect(['id', 'site_name'])
						->setWhere($b_where, $b_params)
						->setOrderBy('site_name ASC, count DESC')
						->rows();
                $top_brands = $b->reader()
						->setSelect(['id', 'site_name'])
						->setWhere($b_where, $b_params)
						->setOrderBy('count DESC')
                        ->setLimit(10)
						->rows();
			}

			$min_cost = $max_cost = 0;
			$sphinx = SphinxQL::create(app()->getSphinxConnection());

			$min_max_cost = $sphinx
					->select(SphinxQL::expr('MAX(cost) as `max`'), SphinxQL::expr('MIN(cost) as `min`'))
					->from(SPHINX_PRICE_INDEX)
					->where('id', 'IN', $raw_price_ids)
					->where('cost', '>', 0)
					->offset(0, SPHINX_MAX_INT)
					->execute();

			if ($min_max_cost && isset($min_max_cost[0])) {
				$min_cost = $min_max_cost[0]['min'];
				$max_cost = $min_max_cost[0]['max'];
			}

			app()->sidebar()
					->setLink($url, $filters)
					->setParam('filters', $filters)
					->setParam('brands', $brands)
					->setParam('top_brands', $top_brands)
					->setParam('brands_active', explode(',', $filters['brand']))
					->setParam('min_cost', (int)$min_cost)
					->setParam('max_cost', (int)$max_cost)
					->setParam('price_subgroups', $this->price_subgroup_matrix)
					->setParam('price_cities', $this->price_city_matrix)
					->setParam('right_layout_filter_sidebar', TRUE)
					->setParam('wholesail_and_retail', TRUE)
					->setTemplate('sidebar_price_search')
					->setTemplateDir('common');
		}
	}

	protected function setTabs($filters, $firms, $goods) {
		$tabs = [
			['link' => app()->linkFilter(app()->link('/search/'), $filters, ['mode' => false]), 'label' => 'Краткий обзор'],
			['link' => app()->linkFilter(app()->link('/search/prices/'), $filters, ['mode' => 'prices']), 'label' => 'Поиск товаров и услуг'],
			['link' => app()->linkFilter(app()->link('/search/firms/'), $filters, ['mode' => 'firms']), 'label' => 'Поиск фирм'],
		];

		if (!$this->firms_total_found) {
			unset($tabs[2]);
		}

		if (!$this->prices_total_found) {
			unset($tabs[1]);
		}

		if (count($tabs) === 1) {
			unset($tabs[0]);
		}

		app()->tabs()
				->setActiveTab(0)
				->setFilters($filters)
				->setTabs($tabs)
				->setSortOptions([]);

		return $this;
	}

	//to delete
	public static function prepareSearchQuery($search_query) {
		return \App\Classes\Search::prepareSearchQuery($search_query);
	}

	public static function clearQuery($query) {
		return \App\Classes\Search::clearQuery($query);
	}

	protected function renderFirmCatalogs($search_links = false) {
		$result = '';
		if ($this->firm_catalogs) {
			$result = $this->view()
					->set('search_links', $search_links)
					->set('items', $this->firm_catalogs)
					->set('matrix', $this->firm_catalogs_matrix)
					->setTemplate('firm_catalogs', 'search')
					->render();
		}

		return $result;
	}

	protected function renderPriceCatalogs() {
		$result = '';
		if ($this->price_catalogs) {
			$result = $this->view()
					->set('items', $this->price_catalogs)
					->set('matrix', $this->price_catalogs_matrix)
					->setTemplate('price_catalogs', 'search')
					->render();
		}

		return $result;
	}

}
