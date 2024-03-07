<?php

namespace App\Presenter;

use App\Classes\Pagination;
use App\Model\Brand;
use App\Model\Firm;
use App\Model\PriceCatalog;
use App\Model\PriceCatalogPrice;
use App\Model\Price as StsPrice;
use Foolz\SphinxQL\SphinxQL;
use Sky4\Model\Utils;
use function app;
use function encode;

class Search extends Presenter {

	private $catalog = null;
	private $query = null;
	private $filters = [];
	private $raw_price_ids = [];
	private $raw_price_subgroup_ids = [];
	private $raw_city_ids = [];
	private $catalog_ids_by_price = [];
	private $firm_count = 0;
	private $price_count = 0;

	public function find($query, $filters = []) {
		$this->setQuery($query)
				->initPagination()
				->setFilters($filters)
				->setSphinxRawData()
				->setCatalogIds();

		$data = $this->getSphinxSearchResults();

		$res_price_ids = $data['price_ids'];
		$res_firm_ids = $data['firm_ids'];

		$price_ids = [];
		foreach ($res_price_ids as $k => $val) {
			$price_ids[] = $val;
		}

		$res_catalog_ids = [];
		if ($price_ids) {
			$pcp = new PriceCatalogPrice();
			$res_catalog_ids = $pcp->getCatalogIdsByPriceIds($price_ids, 1);
		}

		$res_catalogs = [];
		if ($res_catalog_ids) {
			$pc = new PriceCatalog();
			$res_catalogs = $pc->reader()->objectsByIds($res_catalog_ids);
		}

//установка ограничений и рекламы
		$this->setAdvertRestrictions($res_catalogs, true);
		$this->setAdv($res_catalogs);

		if ($filters['mode'] === 'firm' || $filters['mode'] === 'map') {
			$firm_presenter = new FirmItems();
			$firm_presenter->setForceHideActivity(true)
					->setPriceSubgroupsIds($this->raw_price_subgroup_ids)
					->setPriceIds($this->raw_price_ids)
					->setCityIds($this->raw_city_ids);

			$firm_presenter->pagination()
					->setLimit($this->getLimit())
					->setPage($this->getPage())
					->setTotalRecordsParam('prices', $this->price_count)
					->setTotalRecordsParam('firms', $this->firm_count)
					->setTotalRecords($this->firm_count)
					->setLinkParams($filters)
					->calculateParams();


			$firm_ids = array_flip($res_firm_ids);
			$firm_presenter->findByIds($firm_ids, $filters, false);
			if ($filters['mode'] !== 'map') {
				$special_links = [];
				$items = $firm_presenter->getItems();
				foreach ($items as $firm) {
					$special_links[$firm->id()][] = ['name' => $filters['query'], 'url' => '?q='.encode($filters['query']).'&mode=price'];
				}

				$firm_presenter->view()->set('special_price_links', $special_links);
				$firm_presenter->pagination()->renderElems();
			}

			return $firm_presenter;
		} else {
			$this->pagination()
					->setTotalRecordsParam('prices', $this->price_count)
					->setTotalRecordsParam('firms', $this->firm_count)
					->setTotalRecords($this->price_count)
					->setLink(app()->link('/search/price/'))
					->setLinkParams($filters)
					->calculateParams()
					->renderElems();

			$this->setItemsBySphinxResult($res_price_ids);

			$template = 'catalog_price_items_presenter';
			if ($filters['display_mode'] === 'table') {
				$template .= '_table';
			}
			$this->setItemsTemplate($template);

			return $this;
		}
	}

	private function setSphinxRawData() {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() AS weight'), 'id_subgroup')
				->from(SPHINX_PRICE_INDEX)
				->where('id_firm', 'IN', app()->location()->getFirmIds())
				->orderBy('weight', 'DESC')
				->option('max_matches', SPHINX_MAX_INT)
				->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25+3*sum(hit_count*user_weight)')"))
				->limit(0, SPHINX_MAX_INT);

		if ($this->catalog()->exists()) {
			$sphinx->where('id_subgroup', '=', $this->catalog()->id_subgroup());
		}

		$this->setSphinxMatch($sphinx);
		$sphinx_raw_price_ids = $sphinx->execute();
		
		$raw_price_ids = [];
		foreach ($sphinx_raw_price_ids as $val) {
			$raw_price_ids[] = (int)$val['id'];
			if (!isset($this->raw_price_subgroup_ids[$val['id_subgroup']])) {
				$this->raw_price_subgroup_ids[$val['id_subgroup']] = 0;
			}
			$this->raw_price_subgroup_ids[$val['id_subgroup']] ++;

//			if (!isset($this->raw_city_ids[$val['id_city']])) {
//				$this->raw_city_ids[$val['id_city']] = 0;
//			}
//			$this->raw_city_ids[$val['id_city']] ++;
		}

		arsort($this->raw_price_subgroup_ids);
		arsort($this->raw_city_ids);

		$this->raw_price_ids = $raw_price_ids;
		return $this;
	}

	private function setSphinxMatch(SphinxQL &$sphinx) {
		$word = $this->query;
		if ($word !== null) {
			$search = str()->replace($word, '-', ' ');
			$sphinx->match('name', SphinxQL::expr($search));
		}

		return $this;
	}

	private function getSphinxSearchResults() {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$result = [];

		//устанавливаем запрос
		$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() AS weight'))
				->from(SPHINX_PRICE_INDEX)
				->where('id_firm', 'IN', app()->location()->getFirmIds())
				->option('ranker', SphinxQL::expr("expr('sum((4*lcs+8*(min_hit_pos==1)+2*exact_hit)*user_weight)*1000+3*sum(hit_count*user_weight)')"))
				->option('max_matches', 5000);

		$this->setSphinxMatch($sphinx)
				->setSphinxFilter($sphinx);

		if ($this->filters['sorting'] === null || $this->filters['sorting'] === 'default') {
			//выбираем всё с группировкой по фирмам, но с правильным порядком
			$sphinx
					->limit(0, 1000)
					->groupBy('id_firm');

			$default_sorting = [
                'firm_priority' => 'desc',
				'weight' => 'desc',
				'have_price' => 'desc',
				'have_inf_img_prc' => 'desc',
				'firm_rating' => 'desc',
				//'sortname' => 'asc',
//				'timestamp' => 'desc'
			];

			foreach ($default_sorting as $field => $direction) {
				$sphinx->orderBy($field, $direction);
				$sphinx->withinGroupOrderBy($field, $direction);
			}

			$grouped_firm_prices = [];
			$_grouped_firm_prices = $sphinx->execute();

			foreach ($_grouped_firm_prices as $row) {
				$grouped_firm_prices[] = (int)$row['id'];
			}

			//выбираем всё, кроме них
			if ($grouped_firm_prices) {
				$sphinx->where('id', 'NOT IN', $grouped_firm_prices);
			}

			$prices = $sphinx
					->resetGroupBy()
					->resetWithinGroupOrderBy()
					->execute();

			$result_price_ids = [];
			$result_firm_ids = [];

			foreach ($_grouped_firm_prices as $row) {
				$result_price_ids[] = $row['id'];
				$result_firm_ids[] = $row['id_firm'];
			}
			foreach ($prices as $row) {
				$result_price_ids[] = $row['id'];
			}

			$result_price_ids = array_slice($result_price_ids, $this->pagination()->getOffset(), $this->pagination()->getLimit());
			$this->price_count = count($prices) + count($grouped_firm_prices);
			$this->firm_count = count($grouped_firm_prices);

			$result = [
				'price_ids' => $result_price_ids,
				'firm_ids' => $result_firm_ids
			];
		} else {
			$sorting = self::getSorting($this->filters['sorting']);
			foreach ($sorting['expression'] as $field => $direction) {
				$sphinx->orderBy($field, $direction);
			}

			$sphinx_price_ids = $sphinx->enqueue()
					->limit($this->pagination()->getOffset(), $this->pagination()->getLimit())
					->query('SHOW META')
					->executeBatch();

			$sphinx_firm_ids = $sphinx
					->groupBy('id_firm')
					->enqueue()
					->query('SHOW META')
					->executeBatch();

			$res_firm_ids = [];
			$res_price_ids = [];
			foreach ($sphinx_firm_ids[0] as $val) {
				$res_firm_ids[] = (int)$val['id_firm'];
			}
			foreach ($sphinx_price_ids[0] as $val) {
				$res_price_ids[] = (int)$val['id'];
			}

			$this->price_count = isset($sphinx_price_ids[1][1]['Value']) ? (int)$sphinx_price_ids[1][1]['Value'] : 0;
			$this->firm_count = isset($sphinx_firm_ids[1][1]['Value']) ? (int)$sphinx_firm_ids[1][1]['Value'] : 0;

			$result = [
				'price_ids' => $res_price_ids,
				'firm_ids' => $res_firm_ids
			];
		}



		return $result;
	}

	private function setBreadCrumbs($path) {
		$pcp = new PriceCatalogPrice();

		$actual = [];
		$check_path = [];
		foreach ($path as $k => $p) {
			if ($p->val('node_level') > 2) {
				$check_path[] = $k;
			}
		}

		if ($check_path) {
			$actual = $pcp->getCatalogsByIds($check_path);
		}

		if ($actual) {
			foreach ($path as $cat) {
				if ($cat->val('node_level') <= 2) {
					app()->breadCrumbs()
							->setElem($cat->name(), app()->link($cat->link()));
				} else {
					if (isset($actual[$cat->id()])) {
						app()->breadCrumbs()
								->setElem($cat->name(), app()->link($cat->link()));
					}
				}
			}
		} else {
			foreach ($path as $cat) {
				app()->breadCrumbs()
						->setElem($cat->name(), app()->link($cat->link()));
			}
		}
	}

	private function setAdvertRestrictions($path, $use_parents = false) {
		$pc = new PriceCatalog();
		$pc;
		foreach ($path as $cat) {
			if ($use_parents) {
				$ppath = $cat->adjacencyListComponent()->getPath();
				foreach ($ppath as $cat2) {
					if ($cat2->val('advert_restrictions')) {
						app()->adv()->setAdvertRestrictions($cat2->val('advert_restrictions'));
					}
					if ($cat2->val('agelimit')) {
						app()->adv()->setAdvertAgeRestrictions($cat2->val('agelimit'));
					}
				}
			} else {
				if ($cat->val('advert_restrictions')) {
					app()->adv()->setAdvertRestrictions($cat->val('advert_restrictions'));
				}
				if ($cat->val('agelimit')) {
					app()->adv()->setAdvertAgeRestrictions($cat->val('agelimit'));
				}
			}
		}
	}

	private function setAdv($catalog) {
		if (is_array($catalog)) {
			foreach ($catalog as $cat) {
				if ($cat->val('node_level') > 2) {
                    app()->adv()->setIdCatalog($cat->id());
					app()->adv()->setIdGroup($cat->val('id_group'))
							->setIdSubGroup($cat->val('id_subgroup'));
							//->addKeyword(trim($cat->val('web_name')));
				} else {
					app()->adv()->setIdGroup($cat->val('id_group'))
							->setIdSubGroup($cat->val('id_subgroup'));
					/*$childs = $cat->adjacencyListComponent()->getChildren($cat->val('node_level') + 1);
					foreach ($childs as $child) {
						app()->adv()->addKeyword(trim($child->val('web_name')));
					}*/
				}
			}
		} else {
			if ($catalog->val('node_level') > 2) {
                app()->adv()->setIdCatalog($catalog->id());
				app()->adv()->setIdGroup($catalog->val('id_group'))
						->setIdSubGroup($catalog->val('id_subgroup'));
						//->addKeyword(trim($catalog->val('web_name')));
			} else {
				app()->adv()->setIdGroup($catalog->val('id_group'))
						->setIdSubGroup($catalog->val('id_subgroup'));
				/*$childs = $catalog->adjacencyListComponent()->getChildren($catalog->val('node_level') + 1);
				foreach ($childs as $child) {
					app()->adv()->addKeyword(trim($child->val('web_name')));
				}*/
			}
		}
	}

	private function setItemsBySphinxResult($result) {
		if (isset($result) && $result) {
			$imp_id_list = implode(',', $result);
			$sts = new Firm();
			$query = StsPrice::priceQueryChunk().' WHERE `price`.`id` IN ('.$imp_id_list.') ORDER BY FIELD(`price`.`id`,'.$imp_id_list.')';
			$items = app()->db()->query()
					->setText($query)
					->fetch();

			$i = 0;
			foreach ($items as $item) {
				$i++;
				$firm = new Firm($item['id_firm']);
				$this->items[$item['id']] = StsPrice::combine($item, $firm, []);
			}
		} else {
			$this->items = [];
		}
	}

	public function getRawCityIds() {
		return $this->raw_city_ids;
	}

	public function getRawPriceIds() {
		return $this->raw_price_ids;
	}

	public function getRawSubgroupIds() {
		return $this->raw_price_subgroup_ids;
	}

	public function __construct() {
		parent::__construct();
		$this->setLimit(app()->config()->get('app.prices.onpage', 12));
		$this->setItemsTemplate('firm_presenter_items')
				->setItemsTemplateSubdirName('catalog')
				->setModelName('Catalog');
		return true;
	}

	/**
	 * @return Pagination
	 */
	public function pagination() {
		if ($this->pagination === null) {
			$this->pagination = new Pagination();
		}
		return $this->pagination;
	}

	public function setQuery($query) {
		$this->query = $query;
		return $this;
	}

	public function setCatalog($id_catalog) {
		$this->catalog = new PriceCatalog($id_catalog);
		return $this;
	}

	public function initPagination() {
		$this->pagination()
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLinkParams($this->filters)
				->calculateParams();
		return $this;
	}

	public function setFilters($filters) {
		$this->filters = (array)$filters;
		return $this;
	}

	/**
	 * 
	 * @return PriceCatalog
	 */
	public function catalog() {
		return $this->catalog === null ? new PriceCatalog() : $this->catalog;
	}

	public function setCatalogIds() {
		if (!$this->catalog()->exists()) {
			$pc = new PriceCatalog();
			$pc_conds = Utils::prepareWhereCondsFromArray(array_keys($this->raw_price_subgroup_ids), 'id_subgroup');
			$pc_order_by = Utils::prepareOrderByField(array_keys($this->raw_price_subgroup_ids), 'id_subgroup');

			$pc_where = ['AND', 'node_level = :node_level'];
			$pc_params = array_merge([':node_level' => 2]);

			if ($this->raw_price_subgroup_ids) {
				$pc_where[] = $pc_conds['where'];
				$pc_params = $pc_params + $pc_conds['params'] + $pc_order_by['params'];
			}

			$this->catalog_ids_by_price = $pc->reader()
					->setSelect('id')
					->setWhere($pc_where, $pc_params)
					->setOrderBy($this->raw_price_subgroup_ids ? $pc_order_by['order'] : '')
					->rowsWithKey('id');
		} else {
			app()->breadCrumbs()
					->setElem($this->catalog()->name(), app()->link(app()->linkFilter('/search/', $this->filters)));

			$pc = new PriceCatalog();
			$pc_where = ['AND', 'parent_node = :parent_node'];
			$pc_params = [':parent_node' => $this->catalog()->id()];
			$this->catalog_ids_by_price = $pc->reader()
					->setSelect('id')
					->setWhere($pc_where, $pc_params)
					->rowsWithKey('id');
		}

		return $this;
	}

	public static function getSorting($sorting) {
		$sorting_options = self::getSortingOptions();

		return isset($sorting_options[$sorting]) ? $sorting_options[$sorting] : $sorting_options['default'];
	}

	public static function getSortingOptions() {
		return [
			'default' => [
				'name' => 'по рейтингу',
				'expression' => ['weight' => 'desc', 'have_inf_img_prc' => 'desc', 'have_price' => 'desc', 'sortname' => 'asc']
			],
			'price' => [
				'name' => 'по цене',
				'expression' => ['have_price' => 'desc', 'cost' => 'asc', 'sortname' => 'asc']
			],
			'alpha' => [
				'name' => 'по алфавиту',
				'expression' => ['sortname' => 'asc']
			],
			'date' => [
				'name' => 'по дате',
				'expression' => ['datetime' => 'desc', 'sortname' => 'asc']
			]
		];
	}

	private function setSphinxFilter(SphinxQL &$sphinx) {
		if ($this->filters['id_catalog']) {
			$sphinx->where('id_subgroup', '=', (int)$this->filters['id_catalog']);
		}

		if ($this->filters['id_city']) {
			$firm_location_ids = app()->location()->getFirmIds($this->filters['id_city']);
			$sphinx->where('id_firm', 'IN', $firm_location_ids);
		}

		if ($this->filters['price_type']) {
			if ($this->filters['price_type'] === 'wholesale') {
				$sphinx->where('sale1', '=', 1);
			} elseif ($this->filters['price_type'] === 'retail') {
				$sphinx->where('sale2', '=', 1);
			}
		}

		if (isset($this->filters['discount']) && $this->filters['discount']) {
			$sphinx->where('discount_yes', '=', 1);
		}

		if ($this->filters['with-price']) {
			$sphinx->where('have_price', '=', 1);
		}

		if ($this->filters['prices']) {
			$prices = explode(',', $this->filters['prices']);
			if ($prices && count($prices) === 2) {
				$sphinx->where('cost', '>=', (int)$prices[0]);
				$sphinx->where('cost', '<=', (int)$prices[1]);
			}
		}

		if ($this->filters['brand']) {
			$brand_ids = explode(',', $this->filters['brand']);
			$brand = new Brand();
			$brand_conds = Utils::prepareWhereCondsFromArray($brand_ids);
			$_brands = $brand
					->reader()->setWhere($brand_conds['where'], $brand_conds['params'])
					->rows();
			$brands = [];
			foreach ($_brands as $brand) {
                $_brand_name = str_replace('/', '\/', $brand['name']);
				$brands[] = $_brand_name;
			}

			if ($brands) {
				$query = app()->prepareSphinxMatchStatement(implode(',', $brands), '|', '*');
				$sphinx->match('name', SphinxQL::expr($query));
			}
		}

		return $this;
	}

	public function getPage() {
		$params = app()->request()->processGetParams(['page' => 'int']);
		if ($params['page']) return $params['page'];
		return 1;
	}

	public function model() {
		return new PriceCatalog();
	}

}
