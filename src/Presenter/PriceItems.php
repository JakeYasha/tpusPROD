<?php

namespace App\Presenter;

use App\Action\Catalog;
use App\Classes\Pagination;
use App\Model\Brand;
use App\Model\Firm;
use App\Model\PriceCatalog;
use App\Model\PriceCatalogPrice;
use App\Model\StatObject;
use App\Model\Price;
use App\Model\Synonym;
use App\Model\WordException;
use Foolz\SphinxQL\SphinxQL;
use Sky4\Model\Utils;
use function app;
use function encode;
use function str;

class PriceItems extends Presenter {

	private $catalog_ids_by_price = null;
	private $price_ids_in_catalog = null;
	private $price_ids_in_catalog_id = null;
	private $res_price_count = 0;
	private $res_firm_count = 0;
	private $raw_price_ids = [];
	private $raw_price_ids_subgroups = [];
	private $default_catalog_image = null;

	public function __construct() {
		parent::__construct();
		$this->setLimit(app()->config()->get('app.prices.onpage', 12));
		$this->setItemsTemplate('firm_presenter_items')
				->setItemsTemplateSubdirName('catalog')
				->setModel(new PriceCatalog());
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

	public function getPage() {
		$params = app()->request()->processGetParams(['page' => 'int']);
		if ($params['page']) return $params['page'];
		return 1;
	}

// -------------------------------------------------------------------------

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

		return $this;
	}

	private function setDefaultImage($path) {
		$rev_path = array_reverse($path);
		foreach ($rev_path as $cat) {
			if ($cat->val('image')) {
				$this->default_catalog_image = $cat->imageComponent()->get();
				break;
			}
		}

		return $this;
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

		return $this;
	}

	private function setAdv($catalog) {
		if (is_array($catalog)) {
			foreach ($catalog as $cat) {
				if ($cat->val('node_level') > 2) {
					app()->adv()->setIdCatalog($cat->id());
					app()->adv()->setIdGroup($cat->val('id_group'))
							->setIdSubGroup($cat->val('id_subgroup'));
					//->addKeyword(trim($cat->val('web_name')));
					$parent = $cat->adjacencyListComponent()->getParent();
					if ($parent->exists()) {
						app()->adv()->setIdCatalog($parent->id(), true);
					}
				} else {
					//$childs = $cat->adjacencyListComponent()->getChildren($cat->val('node_level') + 1);
					app()->adv()->setIdGroup($cat->val('id_group'))
							->setIdSubGroup($cat->val('id_subgroup'));
					//foreach ($childs as $child) {
					//app()->adv()->addKeyword(trim($child->val('web_name')));
					//}
				}
			}
		} else {
			if ($catalog->val('node_level') > 2) {
				app()->adv()->setIdCatalog($catalog->id(), true);
				app()->adv()->setIdGroup($catalog->val('id_group'))
						->setIdSubGroup($catalog->val('id_subgroup'));
				//->addKeyword(trim($catalog->val('web_name')));
				$parent = $catalog->adjacencyListComponent()->getParent();
				if ($parent->exists()) {
					app()->adv()->setIdCatalog($parent->id(), true);
				}
			} else {
				//$childs = $catalog->adjacencyListComponent()->getChildren($catalog->val('node_level') + 1);
				app()->adv()->setIdGroup($catalog->val('id_group'))
						->setIdSubGroup($catalog->val('id_subgroup'));
				//foreach ($childs as $child) {
				//app()->adv()->addKeyword(trim($child->val('web_name')));
				//}
			}
		}

		return $this;
	}

	private function getSphinxRawPriceIds(PriceCatalog $catalog, $query = null) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		if ($catalog->exists() && $query === null) {
			$sphinx->select('id', 'id_subgroup')
					->from(SPHINX_PRICE_INDEX)
					->where('id_subgroup', '=', $catalog->id_subgroup())
					->where('id_firm', 'IN', app()->location()->getFirmIds())
					->limit(0, SPHINX_MAX_INT)
					->option('max_matches', SPHINX_MAX_INT)
					->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25+3*sum(hit_count*user_weight)')"));

			if ($catalog->node_level() > 2) {
				$this->setSphinxMatch($sphinx, $catalog);
			}
		} else {
			$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() AS weight'), 'id_subgroup')
					->from(SPHINX_PRICE_INDEX)
					->where('id_firm', 'IN', app()->location()->getFirmIds())
					->orderBy('weight', 'DESC')
					->option('max_matches', SPHINX_MAX_INT)
					->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25+3*sum(hit_count*user_weight)')"))
					->limit(0, SPHINX_MAX_INT);

			$this->setSphinxMatch($sphinx, $catalog, $query);

			if ($catalog->exists()) {
				$sphinx->where('id_subgroup', '=', $catalog->id_subgroup());
			}
		}
		$sphinx_raw_price_ids = $sphinx->execute();

		$raw_price_ids = [];
		foreach ($sphinx_raw_price_ids as $val) {
			$raw_price_ids[] = (int)$val['id'];
			if (!isset($this->raw_price_ids_subgroups[$val['id_subgroup']])) {
				$this->raw_price_ids_subgroups[$val['id_subgroup']] = 0;
			}
			$this->raw_price_ids_subgroups[$val['id_subgroup']] ++;
		}

		if ($catalog->exists()) {
			$pcp = new PriceCatalogPrice();
			$catalog_price_ids = $pcp->getPriceIds($catalog);
			foreach ($catalog_price_ids as $val) {
				if (!in_array((int)$val, $raw_price_ids)) {
					$raw_price_ids[] = (int)$val;
					if (!isset($this->raw_price_ids_subgroups[$catalog->id_subgroup()])) {
						$this->raw_price_ids_subgroups[$catalog->id_subgroup()] = 0;
					}
					$this->raw_price_ids_subgroups[$catalog->id_subgroup()] ++;
				}
			}
		}

		arsort($this->raw_price_ids_subgroups);

		return $raw_price_ids;
	}

	private function getSphinxResults(PriceCatalog $catalog = null, $filters, $query = null, $raw_price_ids = []) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());

		if ($raw_price_ids) {
			if (($filters['sorting'] === null || $filters['sorting'] === 'default')) {
				$sphinx2 = SphinxQL::create(app()->getSphinxConnection());

				if ($catalog !== null && $catalog->node_level() > 1 && !$raw_price_ids) {
					$this->setSphinxMatch($sphinx, $catalog);
				}

				if ($catalog !== null) {
					$this->setSphinxFilter($sphinx, $filters);
				}

				$sphinx2->select('id', 'id_firm', SphinxQL::expr('WEIGHT() as weight'))
						->limit(0, SPHINX_MAX_INT)
						->from(SPHINX_PRICE_INDEX)
						->where('id', 'IN', $raw_price_ids)
						->groupBy('id_firm');

				if (isset($filters['mode']) && $filters['mode'] === 'price') {
					$price_sorting = self::getSorting($filters['sorting']);
					foreach ($price_sorting['expression'] as $field => $direction) {
						$sphinx2->orderBy($field, $direction);
						$sphinx2->withinGroupOrderBy($field, $direction);
					}
				}

				$grouped_prices = [];
				$_grouped_firm_prices = $sphinx2->execute();

				foreach ($_grouped_firm_prices as $row) {
					$grouped_prices[] = (int)$row['id'];
				}

				if ($grouped_prices) {
					$sphinx2->where('id', 'NOT IN', $grouped_prices);
				}

				$prices = $sphinx2
						->resetGroupBy()
						->resetWithinGroupOrderBy()
						->execute();

				foreach ($_grouped_firm_prices as $row) {
					$result_price_ids[] = $row['id'];
					$result_firm_ids[] = $row['id_firm'];
				}
				foreach ($prices as $row) {
					$result_price_ids[] = $row['id'];
				}

				$result_price_ids = array_slice($result_price_ids, $this->pagination()->getOffset(), $this->pagination()->getLimit());

				$temp_for_result = [];
				foreach ($result_price_ids as $id) {
					$temp_for_result[] = ['id' => $id];
				}
				$this->res_price_count = count($prices) + count($grouped_prices);
				$this->res_firm_count = count($_grouped_firm_prices);

				$result = [
					'price_ids' => [0 => $temp_for_result],
					'firm_ids' => $result_firm_ids
				];
			}

			if (!isset($result)) {
				$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() as weight'))
						->from(SPHINX_PRICE_INDEX)
						->where('id', 'IN', $raw_price_ids)
						->limit($this->pagination()->getOffset(), $this->pagination()->getLimit());
			}
		} else {
			if ($catalog !== null && $catalog->exists()) {
				if ($catalog->val('flag_is_strict')) {
					$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() as weight'))
							->from(SPHINX_PRICE_INDEX)
							->where('id_subgroup', '=', $catalog->id_subgroup())
							->where('id_firm', 'IN', app()->location()->getFirmIds())
							->where('weight', '>', 1550)
							->option('field_weights', ['wname' => 10, 'w2name' => 4, 'name' => 1])
							//->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+3*sum(hit_count*user_weight)')"))
							->option('ranker', SphinxQL::expr("bm25"))
							->limit($this->pagination()->getOffset(), $this->pagination()->getLimit());
				} else {
					$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() as weight'))
							->from(SPHINX_PRICE_INDEX)
							->where('id_subgroup', '=', $catalog->id_subgroup())
							->where('id_firm', 'IN', app()->location()->getFirmIds())
							->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+3*sum(hit_count*user_weight)')"))
							->limit($this->pagination()->getOffset(), $this->pagination()->getLimit());
				}
			} else {
				$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() AS weight'))
						->from(SPHINX_PRICE_INDEX)
						->where('id_firm', 'IN', app()->location()->getFirmIds())
						->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+3*sum(hit_count*user_weight)')"))
						->limit($this->pagination()->getOffset(), $this->pagination()->getLimit());

				$this->setSphinxMatch($sphinx, $catalog, $query);
			}
		}

		if (!isset($result)) {


			if (isset($filters['mode']) && $filters['mode'] === 'price') {
				$price_sorting = self::getSorting($filters['sorting']);
				foreach ($price_sorting['expression'] as $field => $direction) {
					$sphinx->orderBy($field, $direction);
				}
			}

			if ($query !== null) {
				$sphinx->orderBy('weight', 'DESC')
						->orderBy('exist_image', 'DESC')
						->orderBy('have_price', 'DESC')
						->orderBy('have_info', 'DESC')
						->orderBy('sortname', 'ASC');
			} else {
				$sphinx->orderBy('sortname', 'ASC');
			}

			if ($catalog !== null && $catalog->node_level() > 1 && !$raw_price_ids) {
				$this->setSphinxMatch($sphinx, $catalog);
			}

			if ($catalog !== null) {
				$this->setSphinxFilter($sphinx, $filters);
			}

			$sphinx_price_ids = $sphinx
					->option('max_matches', SPHINX_MAX_INT)
					->enqueue()
					->query('SHOW META')
					->executeBatch();

			$sphinx_firm_ids = $sphinx
					->groupBy('id_firm')
					->limit(0, SPHINX_MAX_INT)
					->enqueue()
					->query('SHOW META')
					->executeBatch();

			$res_firm_ids = [];
			foreach ($sphinx_firm_ids[0] as $val) {
				$res_firm_ids[] = (int)$val['id_firm'];
			}

			$this->res_price_count = isset($sphinx_price_ids[1][1]['Value']) ? (int)$sphinx_price_ids[1][1]['Value'] : 0;
			$this->res_firm_count = isset($sphinx_firm_ids[1][1]['Value']) ? (int)$sphinx_firm_ids[1][1]['Value'] : 0;

			$result = ['price_ids' => $sphinx_price_ids, 'firm_ids' => $res_firm_ids];
		}

		return $result;
	}

	private function getSphinxSearchResults(PriceCatalog $catalog = null, $filters, $query = null) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());

		if ($catalog !== null && $catalog->exists()) {
			if ($catalog->val('flag_is_strict')) {
				$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() as weight'))
						->from(SPHINX_PRICE_INDEX)
						->where('id_subgroup', '=', $catalog->id_subgroup())
						->where('id_firm', 'IN', app()->location()->getFirmIds())
						->where('weight', '>', 1550)
						->option('field_weights', ['wname' => 10, 'w2name' => 4, 'name' => 1])
						//->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+3*sum(hit_count*user_weight)')"))
						->option('ranker', SphinxQL::expr("bm25"))
						->limit($this->pagination()->getOffset(), $this->pagination()->getLimit());
			} else {
				$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() as weight'))
						->from(SPHINX_PRICE_INDEX)
						->where('id_subgroup', '=', $catalog->id_subgroup())
						->where('id_firm', 'IN', app()->location()->getFirmIds())
						->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+3*sum(hit_count*user_weight)')"))
						->limit($this->pagination()->getOffset(), $this->pagination()->getLimit());
			}
		} else {
			$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() AS weight'))
					->from(SPHINX_PRICE_INDEX)
					->where('id_firm', 'IN', app()->location()->getFirmIds())
					->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+3*sum(hit_count*user_weight)')"))
					->limit($this->pagination()->getOffset(), $this->pagination()->getLimit());
		}

		if (isset($filters['mode']) && $filters['mode'] === 'price') {
			$price_sorting = self::getSorting($filters['sorting']);
			foreach ($price_sorting['expression'] as $field => $direction) {
				$sphinx->orderBy($field, $direction);
			}
		}

		if ($query !== null) {
			$sphinx->orderBy('weight', 'DESC')
					->orderBy('exist_image', 'DESC')
					->orderBy('have_price', 'DESC')
					->orderBy('have_info', 'DESC')
					->orderBy('sortname', 'ASC');
		} else {
			$sphinx->orderBy('sortname', 'ASC');
		}

		//if ($catalog !== null && $catalog->node_level() > 1) {
		$this->setSphinxMatch($sphinx, $catalog, $query)
				->setSphinxFilter($sphinx, $filters);
		//}

		$sphinx_price_ids = $sphinx
				->option('max_matches', SPHINX_MAX_INT)
				->enqueue()
				->query('SHOW META')
				->executeBatch();

		$sphinx_firm_ids = $sphinx
				->groupBy('id_firm')
				->limit(0, SPHINX_MAX_INT)
				->enqueue()
				->query('SHOW META')
				->executeBatch();

		$res_firm_ids = [];
		foreach ($sphinx_firm_ids[0] as $val) {
			$res_firm_ids[] = (int)$val['id_firm'];
		}

		$this->res_price_count = isset($sphinx_price_ids[1][1]['Value']) ? (int)$sphinx_price_ids[1][1]['Value'] : 0;
		$this->res_firm_count = isset($sphinx_firm_ids[1][1]['Value']) ? (int)$sphinx_firm_ids[1][1]['Value'] : 0;

		return ['price_ids' => $sphinx_price_ids, 'firm_ids' => $res_firm_ids];
	}

	public function find($id_group, $id_subgroup, $filters, $id_catalog) {
		$mode = $filters['mode'] ? (string)$filters['mode'] : 'firm';
		$this->pagination()
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLinkParams($filters)
				->calculateParams();

//setting bread crumbs, restrictions and adverts
		$catalog = new PriceCatalog($id_catalog);
		if (!$catalog->exists()) {
			$catalog->getSubGroup($id_group, $id_subgroup);
		}
		$path = $catalog->adjacencyListComponent()->getPath();
		$this->setAdvertRestrictions($path)
				->setBreadCrumbs($path)
				->setAdv($catalog)
				->setDefaultImage($path);

//get raw and current results by price index
		$raw_price_ids = $this->getSphinxRawPriceIds($catalog);
		$data = $this->getSphinxResults($catalog, $filters, null, $raw_price_ids);

		$res_price_ids = $data['price_ids'];
		$res_firm_ids = $data['firm_ids'];

		if ($raw_price_ids) {
//set sidebar filters
			$this->setSidebar($catalog, $filters, $raw_price_ids);
		} else {
			app()->metadata()->noIndex();
			$this->items = [];
		}

		if ($mode === 'price') {
			$this->pagination()
					->setTotalRecordsParam('prices', $this->res_price_count)
					->setTotalRecordsParam('firms', $this->res_firm_count)
					->setTotalRecords($this->res_price_count)
					->setLink(app()->linkFilter(app()->link($catalog->link())))
					->calculateParams()
					->renderElems();

			$this->setItemsBySphinxResult($res_price_ids);
			$template = 'catalog_'.$mode.'_items_presenter';
			if ($filters['display_mode'] === 'table') {
				$template .= '_table';
			}
			$this->setItemsTemplate($template);
			foreach ($this->items as $it) {
				$sts_price = new Price($it['id']);
				$_firm = new Firm();
				$_firm->reader()->object($it['id_firm']);
				app()->stat()->addObject(StatObject::CATALOG_PRICE_LIST_PRICE, $_firm);
			}
			app()->tabs()->setSortOptions(self::getSortingOptions());

			return $this;
		} else {
			$presentrer = new FirmItems();
			$price_catalog_count = new PriceCatalogPrice();
			$firm_ids = $price_catalog_count->getIdFirmsByCatalog($catalog->id());
			$catalogs_count = $price_catalog_count->getSubCatalogs($catalog->id());

//			if (count($catalogs_count) !== 0 && count($catalogs_count) < $this->res_firm_count) {
//				$this->res_firm_count = count($catalogs_count);
//			}

			$presentrer
					->setPage($this->getPage())
					->setLimit($this->getLimit());

			$presentrer->pagination()
					->setPage($this->getPage())
					->setLimit($this->getLimit())
					->setTotalRecordsParam('prices', $this->res_price_count)
					->setTotalRecordsParam('firms', $this->res_firm_count)
					->setTotalRecords($this->res_firm_count)
					->setLink(app()->link($catalog->link()))
					->setLinkParams($filters)
					->calculateParams()
					->renderElems();

			$catalogs = $catalog->adjacencyListComponent()->getChildren();
			if ($catalog->node_level() >= 2) {
				$catalogs[$catalog->id()] = $catalog;
			}

			$presentrer->view()->set('active_catalog', $catalog);
			$presentrer->view()->set('catalogs', $catalogs);
			$presentrer->view()->set('catalogs_count', $catalogs_count);
			$presentrer->findByIds($firm_ids['data'], $filters);

			return $presentrer;
		}

		return $this;
	}

	public function findByQuery($query, $filters) {
		$this->pagination()
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLinkParams($filters)
				->calculateParams();

//get raw and current results by price index
		$catalog = new PriceCatalog($filters['id_catalog']);
		$raw_price_ids = $this->getSphinxRawPriceIds($catalog, $query);

		if (!$catalog->exists()) {
			$pc = new PriceCatalog();
			$pc_conds = Utils::prepareWhereCondsFromArray(array_keys($this->raw_price_ids_subgroups), 'id_subgroup');
			$pc_order_by = Utils::prepareOrderByField(array_keys($this->raw_price_ids_subgroups), 'id_subgroup');

			$pc_where = ['AND', 'node_level = :node_level'];
			$pc_params = array_merge([':node_level' => 2]);

			if ($this->raw_price_ids_subgroups) {
				$pc_where[] = $pc_conds['where'];
				$pc_params = $pc_params + $pc_conds['params'] + $pc_order_by['params'];
			}

			$this->catalog_ids_by_price = $pc->reader()
					->setSelect('id')
					->setWhere($pc_where, $pc_params)
					->setOrderBy($this->raw_price_ids_subgroups ? $pc_order_by['order'] : '')
					->rowsWithKey('id');
		} else {
			app()->breadCrumbs()
					->setElem($catalog->name(), app()->link(app()->linkFilter('/search/', $filters)));

			$pc = new PriceCatalog();
			$pc_where = ['AND', 'parent_node = :parent_node'];
			$pc_params = [':parent_node' => $catalog->id()];
			$this->catalog_ids_by_price = $pc->reader()
					->setSelect('id')
					->setWhere($pc_where, $pc_params)
					->rowsWithKey('id');
		}

		$this->raw_price_ids = $raw_price_ids;
		$data = $this->getSphinxSearchResults($catalog, $filters, $query);

		$res_price_ids = $data['price_ids'];
		$res_firm_ids = $data['firm_ids'];

		$price_ids = [];
		foreach ($res_price_ids[0] as $k => $val) {
			$price_ids[] = $val['id'];
		}

		$res_catalog_ids = [];
		if ($price_ids) {
			$pcp = new PriceCatalogPrice();
			$res_catalog_ids = $pcp->getCatalogIdsByPriceIds($price_ids);
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

			$firm_presenter->pagination()
					->setLimit($this->getLimit())
					->setPage($this->getPage())
					->setTotalRecordsParam('prices', $this->res_price_count)
					->setTotalRecordsParam('firms', $this->res_firm_count)
					->setTotalRecords($this->res_firm_count)
					->setLinkParams($filters)
					->calculateParams();


			$firm_ids = array_flip($res_firm_ids);
			$firm_presenter->findByIds($firm_ids, $filters, false);
			if ($filters['mode'] !== 'map') {
				$special_links = [];
				$items = $firm_presenter->getItems();
				foreach ($items as $firm) {
					$special_links[$firm->id()][] = ['name' => $filters['query'], 'url' => $firm->linkItem().'?q='.encode($filters['query']).'&mode=price'];
				}

				$firm_presenter->view()->set('special_price_links', $special_links);
				$firm_presenter->pagination()->renderElems();
			}

			return $firm_presenter;
		} else {
			$this->pagination()
					->setTotalRecordsParam('prices', $this->res_price_count)
					->setTotalRecordsParam('firms', $this->res_firm_count)
					->setTotalRecords($this->res_price_count)
//->setLink(app()->linkFilter(app()->link('/search/'), $filters))
					->calculateParams()
					->renderElems();

			$this->setItemsBySphinxResult($res_price_ids);
			$template = 'catalog_price_items_presenter';
			$this->setItemsTemplate($template);

			return $this;
		}
	}

	public function getCatalogIds() {
		$result = [];
		if ($this->catalog_ids_by_price !== null) {
			foreach ($this->catalog_ids_by_price as $val) {
				$result[] = $val['id'];
			}
		}

		return $result;
	}

	public function getRawPriceIds() {
		return $this->raw_price_ids;
	}

	public function setSidebar(PriceCatalog $catalog, $filters, $raw_price_ids) {
		$brand_ids = [];
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$conds = Utils::prepareWhereCondsFromArray($raw_price_ids, 'price_id');
		$brand_ids = array_keys((new \App\Model\BrandPrice())->reader()
						->setSelect(['DISTINCT brand_id'])
						->setWhere(['AND', $conds['where']], $conds['params'])
						->rowsWithKey('brand_id'));

		$brands = [];
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
					->setOrderBy('site_name ASC')
					->rows();
		}

		$min_cost = $max_cost = 0;
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
//$this->setSphinxFilter($sphinx, $filters);
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
				->setLink($catalog->link(), $filters)
				->setParam('filters', $filters)
				->setParam('brands', $brands)
				->setParam('brands_active', explode(',', $filters['brand']))
				->setParam('min_cost', (int)$min_cost)
				->setParam('max_cost', (int)$max_cost)
				->setParam('right_layout_filter_sidebar', TRUE)
				->setParam('wholesail_and_retail', (int)$catalog->val('id_group') === 44 ? FALSE : TRUE)
				->setParam('is_service', $catalog->id_group() === 44)
				->setTemplate('sidebar_price_catalog')
				->setTemplateDir('common');
	}

	public function setSearchSidebar($filters, $price_catalogs_sidebar) {
		$raw_price_ids = $this->getRawPriceIds();
		$brand_ids = [];
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$conds = Utils::prepareWhereCondsFromArray($raw_price_ids, 'price_id');
		$brand_ids = array_keys((new \App\Model\BrandPrice())->reader()
						->setSelect(['DISTINCT brand_id'])
						->setWhere(['AND', $conds['where']], $conds['params'])
						->rowsWithKey('brand_id'));

		$brands = [];
		if ($brand_ids) {
			$b = new Brand();
			$b_conds = Utils::prepareWhereCondsFromArray($brand_ids, 'id');
			$b_where = ['AND'];
			$b_where[] = $b_conds['where'];
			$b_where[] = '`count` > :count';
			$b_params = array_merge($b_conds['params'], [':count' => 1]);

			$brands = $b->reader()->setSelect(['id', 'site_name'])
					->setWhere($b_where, $b_params)
					->setOrderBy('site_name ASC')
					->rows();
		}

		$min_cost = $max_cost = 0;
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
//$this->setSphinxFilter($sphinx, $filters);
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
				->setLink('/search/price/', $filters)
				->setParam('filters', $filters)
				->setParam('brands', $brands)
				->setParam('brands_active', explode(',', $filters['brand']))
				->setParam('min_cost', (int)$min_cost)
				->setParam('max_cost', (int)$max_cost)
				->setParam('price_catalogs_sidebar', $price_catalogs_sidebar)
				->setParam('right_layout_filter_sidebar', TRUE)
				->setParam('wholesail_and_retail', TRUE)
				->setTemplate('sidebar_price_search')
				->setTemplateDir('common');
	}

	public function findAllInFirm(Firm $firm, $filters, $firm_user_mode = false) {
		$id_catalog = $filters['id_catalog'];
		$catalog = new PriceCatalog($id_catalog);
		app()->breadCrumbs()->setElemBottom('Прайс-лист', app()->linkFilter($firm->linkItem(), ['mode' => 'price']));

		$this->pagination()
				->setLink($firm_user_mode ? '/firm-user/price/' : $firm->linkItem())
				->setLinkParams($filters)
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->calculateParams();

		$sorting = Catalog::getCurrentSorting($filters);

		$price_sorting = self::getSorting($sorting);
		app()->tabs()->setSortOptions(self::getSortingOptions());

		$sphinx = SphinxQL::create(app()->getSphinxConnection());

		$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() as weight'), 'have_price', 'cost', 'sortname')
				->from(SPHINX_PRICE_INDEX)
				->where('id_firm', '=', (int)$firm->id())
				->limit($this->pagination()->getOffset(), $this->pagination()->getLimit())
				//->option('max_matches', $this->pagination()->getOffset() > 1000 ? $this->pagination()->getOffset() + $this->pagination()->getLimit() : 1000)
				->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25+3*sum(hit_count*user_weight)')"));

		if (isset($filters['export']) && $filters['export'] === 'xls') {
			$sphinx->option('max_matches', $this->pagination()->getOffset() > 5000 ? $this->pagination()->getOffset() + $this->pagination()->getLimit() : 5000);
		} else {
			$sphinx->option('max_matches', $this->pagination()->getOffset() > 1000 ? $this->pagination()->getOffset() + $this->pagination()->getLimit() : 1000);
		}

		/*  Сортировка по рейтингу (default) на страницах прайс листа работала некорректно */
		if ($sorting !== 'default') {
			foreach ($price_sorting['expression'] as $field => $direction) {
				$sphinx->orderBy($field, $direction);
			}
		} else {
			$sphinx->orderBy('have_inf_img_prc', 'DESC')
					->orderBy('have_price', 'DESC');
		}

		if ($catalog->exists()) {
			$path = $catalog->adjacencyListComponent()->getPath();
			foreach ($path as $cat) {
				if ($cat->val('advert_restrictions')) {
					app()->adv()->setAdvertRestrictions($cat->val('advert_restrictions'));
				}
				if ($cat->val('agelimit')) {
					app()->adv()->setAdvertAgeRestrictions($cat->val('agelimit'));
				}
				if ((int)$cat->val('id_subgroup') !== 0) {
					app()->breadCrumbs()
							->setElemBottom($cat->name(), app()->linkFilter($firm->linkItem(), array_merge($filters, ['id_catalog' => $cat->id()])));
				}
			}

			if ($catalog->val('advert_restrictions')) {
				app()->adv()->setAdvertRestrictions($catalog->val('advert_restrictions'));
			}
			if ($catalog->val('agelimit')) {
				app()->adv()->setAdvertAgeRestrictions($catalog->val('agelimit'));
			}

			app()->metadata()->setHeader($catalog->name());
			$sphinx->where('id_subgroup', (int)$catalog->val('id_subgroup'));

			$catalog_children = $catalog->adjacencyListComponent()->getChildren();
			$catalog_children[$catalog->id()] = 1;
			$pcp = new \App\Model\PriceCatalogPrice();
			$id_prices = $pcp->getPriceIdsByFirm($catalog, $firm);
			if ($id_prices) {
				$sphinx->where('id', 'IN', $id_prices);
			}

			$sphinx->orderBy('weight', 'DESC');
		} elseif ($filters['q']) {
			app()->breadCrumbs()->setElemBottom('Поиск', '');
			app()->metadata()->setHeader('Поиск: '.encode($filters['q']));
			//todo clearence and synonyms
			$query = (string)$filters['q'];

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

			if (true) {
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

			$sphinx->match('(name,info)', SphinxQL::expr($query))
					->orderBy('weight', 'DESC');
		}

		$res = $sphinx
				->enqueue(SphinxQL::create(app()->getSphinxConnection())->query('SHOW META'))
				->executeBatch();

		$this->pagination()
				->setTotalRecords($res[1][1]['Value'])
				->calculateParams()
				->renderElems();
        
        $this->view()->set('firm', $firm);

		$this->setItemsBySphinxResult($res, isset($filters['export']) && $filters['export'] === 'xls' ? false : true);

		if ($filters['display_mode'] !== '') {
			$display_mode = $filters['display_mode'];
			app()->cookie()->setExpireMonth()->set('_pricelist_display_mode', $display_mode);
		} else {
			$display_mode = app()->cookie()->get('_pricelist_display_mode');
		}

		if ($display_mode === 'table') {
			$this->setItemsTemplate('catalog_firm_price_items_presenter_table');
		} else {
			if (APP_IS_DEV_MODE) {
				$this->setItemsTemplate('catalog_firm_price_items_presenter_price_list');
			} else {
				$this->setItemsTemplate('catalog_firm_price_items_presenter');
			}
		}

		return $this;
	}

	public function findOtherItemsByFirm(Price $item, PriceCatalog $catalog = null) {
		$firm = $item->getFirm();

		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$sphinx->select('id', 'id_firm', 'id_subgroup', SphinxQL::expr('WEIGHT() AS weight'))
				->from(SPHINX_PRICE_INDEX)
				->where('id_subgroup', '=', (int)$item->val('id_subgroup'))
				->where('id_firm', '=', (int)$firm->id())
				//->where('exist_image', '=', 1)
				->where('id', '!=', (int)$item->id())
				->orderBy('weight', 'DESC')
				->limit(0, 20);

		if ($catalog !== null && $catalog->exists()) {
			$price_ids = $this->getPriceIdsInCatalog($catalog, $firm->id());
			if ($price_ids) {
				$sphinx->where('id', 'IN', $price_ids);
			}
		}

		$res[0] = $sphinx->execute();

		if (!$res[0]) {
			$sphinx->resetWhere()
					->where('id_subgroup', '=', (int)$item->val('id_subgroup'))
					->where('id_firm', '=', (int)$firm->id())
					//->where('id_service', '=', (int) $item->id_service())
					//->where('exist_image', '=', 1)
					->where('id', '!=', (int)$item->id());

			$res[0] = $sphinx->execute();
		}

		switch ($item->val('id_group')) {
			case 44 : $header = 'Другие услуги фирмы';
				break;
			case 22 : $header = 'Другое оборудование фирмы';
				break;
			default : $header = 'Другие товары фирмы';
				break;
		}

		$this->view()->set('header', $header);

		//$this->setItemsBySphinxResult($res);
		$this->setItemsBySphinxResultWithManualOrdering($res);
		$this->setItemsTemplate('price_show_other_items_presenter');

		return $this;
	}

	public function findOtherItemsByCatalog(Price $item, PriceCatalog $catalog = null) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$sphinx->select('id', 'id_firm', 'id_subgroup', SphinxQL::expr('WEIGHT() AS weight'))
				->from(SPHINX_PRICE_INDEX)
				->where('id_subgroup', '=', (int)$item->val('id_subgroup'))
				->where('id_firm', '!=', (int)$item->getFirm()->id())
				->where('id_firm', 'IN', app()->location()->getFirmIds())
				->where('id', '!=', (int)$item->id())
				->orderBy('weight', 'DESC')
				->limit(0, 20);

		if ($catalog !== null && $catalog->exists()) {
			$price_ids = $this->getPriceIdsInCatalog($catalog);
			//$price_ids = $this->getPriceIdsInCatalogByCity($catalog, $item->val('id_city'));
			if ($price_ids) {
				$sphinx->where('id', 'IN', $price_ids);
			}
		}

		$res[0] = $sphinx->execute();

		if (!$res[0]) {
			if ($catalog !== null && $catalog->exists()) {
				$parent = $catalog->adjacencyListComponent()->getParent();
				if ($parent->exists()) {
					$price_ids = $this->getPriceIdsInCatalog($parent, $item->getFirm()->id_service());
					if ($price_ids) {
						$sphinx->where('id', 'IN', $price_ids);
					}
				}
			}

			$sphinx->resetWhere()
					->where('id_subgroup', '=', (int)$item->val('id_subgroup'))
					->where('id_firm', '!=', (int)$item->getFirm()->id())
					->where('id_firm', 'IN', app()->location()->getFirmIds())
					->where('id', '!=', (int)$item->id());

			$res[0] = $sphinx->execute();
		}

		$this->setItemsBySphinxResultWithManualOrdering($res);
		$this->setItemsTemplate('price_show_other_additional_items_presenter');

		switch ($item->val('id_group')) {
			case 44 : $header = 'Похожие услуги в других фирмах';
				break;
			case 22 : $header = 'Похожее оборудование в других фирмах';
				break;
			default : $header = 'Похожие товары в других фирмах';
				break;
		}

		$this->view()->set('header', $header);

		return $this;
	}

	public function findPopularInFirm(Firm $firm) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$sphinx->select('id', 'id_firm')
				->from(SPHINX_PRICE_INDEX)
				->where('id_firm', '=', (int)$firm->id())
				->where('exist_image', '=', 1)
				->orderBy('RAND()')
				->limit(0, 20);

		$res[0] = $sphinx->execute();

		$this->setItemsBySphinxResult($res);
		$this->setItemsTemplate('firm_popular_price_items_presenter');

		return $this;
	}

	public function findPopularByQuery($query, $limit = 20) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() AS weight'))
				->from(SPHINX_PRICE_INDEX)
				->where('id_firm', 'IN', app()->location()->getFirmIds())
				->orderBy('weight', 'DESC')
				->orderBy('exist_image', 'DESC')
				->orderBy('have_price', 'DESC')
				->orderBy('have_info', 'DESC')
				->orderBy('sortname', 'ASC')
				->limit(0, $limit)
				->option('max_matches', SPHINX_MAX_INT)
//->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25+3*sum(hit_count*user_weight)')"))
				->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+3*sum(hit_count*user_weight)')"));

		$this->setSphinxMatch($sphinx, new PriceCatalog(), $query);

		$results = $sphinx->enqueue()
				->query('SHOW META')
				->executeBatch();

		$this->setItemsBySphinxResult($results);
		/* $stat_count = 0;
		  foreach ($this->items as $it) {
		  $stat_count++;
		  $sp = new StsPrice($it['id']);
		  // app()->stat()->addObject(StatObject::SEARCH_MAIN, $sp);
		  //  if ($stat_count === 6) {
		  //  break;
		  //  }
		  $_firm = new Firm();
		  $_firm->getByIdFirm($sp->id_firm()));
		  app()->stat()->addObject(StatObject::SEARCH_MAIN, $_firm);
		  } */
		$this->setItemsTemplate('firm_popular_searched_price_items_presenter');

		return $results[1][1]['Value'];
	}

	private function setItemsBySphinxResult($result, $get_images = true) {
		if (isset($result[0]) && $result[0]) {
			$id_list = [];
			foreach ($result[0] as $row) {
				$id_list[] = $row['id'];
			}

			$imp_id_list = implode(',', $id_list);

			$sts = new Firm();

			$query = Price::priceQueryChunk().' WHERE `price`.`id` IN ('.$imp_id_list.') ORDER BY FIELD(`price`.`id`,'.$imp_id_list.')';
			$items = app()->db()->query()
					->setText($query)
					->fetch();

			$i = 0;
			foreach ($items as $item) {
				$i++;
				$firm = new Firm($item['id_firm']);
				$this->items[$item['id']] = Price::combine($item, $firm, [], $this->default_catalog_image, $get_images);
			}
		} else {
			$this->items = [];
		}
	}

	private function setItemsBySphinxResultWithManualOrdering($result) {
		if (isset($result[0]) && $result[0]) {
			$id_list = [];
			foreach ($result[0] as $row) {
				$id_list[] = $row['id'];
			}

			$imp_id_list = implode(',', $id_list);

			$sts = new Firm();

			$query = Price::priceQueryChunk().' WHERE `price`.`id` IN ('.$imp_id_list.') ORDER BY RAND()+flag_is_image_exists DESC';
			$items = app()->db()->query()
					->setText($query)
					->fetch();

			$i = 0;
			foreach ($items as $item) {
				$i++;
				$firm = new Firm();
				$firm->getByIdFirm($item['id_firm']);

				$this->items[$item['id']] = Price::combine($item, $firm, [], $this->default_catalog_image);
			}
		} else {
			$this->items = [];
		}
	}

	public function getTagsByCatalogs($catalogs, $id_subgroup) {
		$c = new PriceCatalog();

		if (!$catalogs) {
			$c_where = ['AND', '`level` = :1', '`id_subgroup` = :id_subgroup', '`cnt` != :0'];
			$c_params = [':1' => 1, ':0' => 0, ':id_subgroup' => $id_subgroup];
		} else {
			$count = count($catalogs);
			if ($count === 3) return [];

			$last = end($catalogs);
			$c_where = ['AND', '`level` = :level', '`id_subgroup` = :id_subgroup', '`cnt` != :0', '`id_parent` = :id_parent'];
			$c_params = [':level' => 1 + $count, ':0' => 0, ':id_subgroup' => $id_subgroup, ':id_parent' => $last->id()];
		}

		return $c->reader()
						->setWhere($c_where, $c_params)
						->setOrderBy('`count` DESC')
						->objects();
	}

	public static function formatPrice($price_row) { //@todo
		return [
		];
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

	private function setSphinxFilter(SphinxQL &$sphinx, $filters) {
		if ($filters['price_type']) {
			if ($filters['price_type'] === 'wholesale') {
				$sphinx->where('sale1', '=', 1);
			} elseif ($filters['price_type'] === 'retail') {
				$sphinx->where('sale2', '=', 1);
			}
		}

		if (isset($filters['discount']) && $filters['discount']) {
			$sphinx->where('discount_yes', '=', 1);
		}

		if ($filters['with-price']) {
			$sphinx->where('have_price', '=', 1);
		}

		if ($filters['prices']) {
			$prices = explode(',', $filters['prices']);
			if ($prices && count($prices) === 2) {
				$sphinx->where('cost', '>=', (int)$prices[0]);
				$sphinx->where('cost', '<=', (int)$prices[1]);
			}
		}

		if ($filters['brand']) {
			$brand_ids = explode(',', $filters['brand']);
			$brand = new Brand();
			$brand_conds = Utils::prepareWhereCondsFromArray($brand_ids);
			$_brands = $brand->reader()
					->setWhere($brand_conds['where'], $brand_conds['params'])
					->rows();
			$brands = [];
			foreach ($_brands as $brand) {
				$brands[] = $brand['name'];
			}

			if ($brands) {
				$query = app()->prepareSphinxMatchStatement(implode(',', $brands), '|', '*');
				$sphinx->match('name', SphinxQL::expr($query));
			}
		}
	}

	private function setSphinxMatch(SphinxQL &$sphinx, PriceCatalog $catalog, $query = null) {
		$word = null;

		if ($catalog->exists() && $catalog->node_level() > 2 && $query === null) {
			$word = $catalog->val('name');
		} elseif ($query !== null) {
			$word = $query;
		}

		if ($word !== null) {
			$search = str()->replace($word, '-', ' ');

			if ($catalog->exists() && $catalog->node_level() > 2 && $catalog->val('flag_is_strict')) {
				$sphinx->match('name', SphinxQL::expr($search));
			} else {
				$sphinx->match('name', SphinxQL::expr($search));
			}
		}
	}

	private function getPriceIdsInCatalog(PriceCatalog $catalog, $id_firm = null) {
		if ($this->price_ids_in_catalog == $catalog->id() && $this->price_ids_in_catalog !== null) {
			return $this->price_ids_in_catalog;
		}

		if ($this->price_ids_in_catalog === null || $this->price_ids_in_catalog_id != $catalog->id()) {
			$this->price_ids_in_catalog_id = $catalog->id();

			$pcp = new PriceCatalogPrice();
			$this->price_ids_in_catalog = $pcp->getPriceIdsByFirm($catalog, new Firm($id_firm));
		}

		return $this->price_ids_in_catalog;
	}

}
