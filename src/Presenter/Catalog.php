<?php

namespace App\Presenter;

use App\Classes\Pagination;
use App\Model\Brand;
use App\Model\Firm;
use App\Model\PriceCatalogPrice;
use App\Model\Price;
use App\Model\PriceCatalog;
use App\Model\StatObject;
use Foolz\SphinxQL\SphinxQL;
use Sky4\Model\Utils;

class Catalog extends Presenter {

	protected $path = null;
	protected $catalog = null;
	protected $default_catalog_image = null;
	protected $firm_count = null;
	protected $result_firm_ids = null;
	protected $price_count = null;
	protected $result_price_ids = null;

	public function __construct() {
		parent::__construct();
		$this->setLimit(app()->config()->get('app.prices.onpage', 12));
		$this->setItemsTemplate('firm_presenter_items')
				->setItemsTemplateSubdirName('catalog')
				->setModel(new PriceCatalog());
	}

	public function findOnMap() {
		app()->setUseMap(true);
		$this->setLimit(app()->config()->get('app.firms.onmap', 1000));
		$presentrer = new FirmItems();
		$presentrer->setFilterVals($this->getFilterVals());
		$price_catalog_price = new PriceCatalogPrice();
		$firm_ids_and_counts = $price_catalog_price->getIdFirmsByCatalog($this->catalog());
		//$firm_ids_and_counts = $price_catalog_price->getIdFirmsByCatalog($this->catalog());
		$raw_price_ids = $price_catalog_price->getPriceIds($this->catalog());
		$this->getSortedPriceIds($this->catalog(), $this->getFilterVals(), $raw_price_ids, $firm_ids_and_counts['data']);

		$presentrer->setPage($this->getPage())
				->setLimit($this->getLimit());

		$presentrer->pagination()
				->setPage($this->getPage())
				->setLimit($this->getLimit())
				->setTotalRecordsParam('prices', $this->price_count)
				->setTotalRecordsParam('firms', $this->firm_count)
				->setTotalRecords($this->firm_count)
				->setLink(app()->link($this->catalog()->link()))
				->setLinkParams($this->getFilterVals())
				->calculateParams()
				->renderElems();

		$presentrer->findByIds($firm_ids_and_counts['data'], $this->getFilterVals());

		return $presentrer;
	}

	public function findFirms() {
		$this->setLimit(app()->config()->get('app.firms.onpage', 10));
		$presentrer = new FirmItems();
		$presentrer->setForceHideActivity(true)
				->setFilterVals($this->getFilterVals());

		$price_catalog_price = new PriceCatalogPrice();
		$_firm_ids_and_counts = $price_catalog_price->getIdFirmsByCatalog($this->catalog());
		$firm_ids_and_counts = $_firm_ids_and_counts['data'];
		$price_count = (int)$_firm_ids_and_counts['total_price_count'];

        $filtered_firm_ids = [];
		if ($price_count > 0 && $price_count < APP_CATALOG_MODE_THRESHOLD) {
		
			$raw_price_ids = $price_catalog_price->getPriceIds($this->catalog(), array_keys($firm_ids_and_counts));
			if ($raw_price_ids) {
				$catalogs_count = $price_catalog_price->getSubCatalogs($this->catalog(), true);
//				$this->setSidebar($this->catalog(), $this->getFilterVals(), $raw_price_ids);
                //die('STOP1');
				$res_price_ids = $this->getSortedPriceIds($this->catalog(), $this->getFilterVals(), $raw_price_ids, $firm_ids_and_counts);
                foreach($this->result_firm_ids as $filtered_firm_id) {
                    
                    if (in_array($filtered_firm_id, array_keys($firm_ids_and_counts))) {
                        $filtered_firm_ids[$filtered_firm_id] = $firm_ids_and_counts[$filtered_firm_id];
                    }
                }
                arsort($filtered_firm_ids);
                //$this->setItemsByIds($res_price_ids);
			}
		} elseif ($price_count > 0) {
			$this->firm_count = count($firm_ids_and_counts);
			$this->price_count = $price_count;
            $filtered_firm_ids = $firm_ids_and_counts;
			$catalogs_count = [];
		} else {
			$catalogs_count = [];
			app()->metadata()->noIndex();
			$this->items = [];
		}

		$presentrer->pagination()
				->setPage($this->getPage())
				->setLimit($this->getLimit())
				->setTotalRecordsParam('prices', $this->price_count)
				->setTotalRecordsParam('firms', $this->firm_count)
				->setTotalRecords($this->firm_count)
				->setLink(app()->link($this->catalog()->link()))
				->setLinkParams($this->getFilterVals())
				->calculateParams()
				->renderElems();

		$catalogs_ids = [];
		foreach ($catalogs_count as $id_firm => $ids_catalogs) {
			foreach ($ids_catalogs as $id_cat => $count) {
				$catalogs_ids[$id_cat] = 1;
			}
		}
		$catalogs = $this->catalog()->reader()
				->objectsByIds(array_keys($catalogs_ids));

		//
		//
		//structure()->reader()->childrenObjects();
//		$catalogs[$this->catalog()->id()] = $this->catalog();
//				->setWhere($where, $params)
//				->objectsByIds(array_keys($catalogs_ids));
		$presentrer->view()->set('active_catalog', $this->catalog());
		$presentrer->view()->set('catalogs', $catalogs);
		$presentrer->view()->set('catalogs_count', $catalogs_count);
		$presentrer->findByIds($filtered_firm_ids, $this->getFilterVals());

		return $presentrer;
	}

	public function findPrices() {
		app()->startTimer();
		$mode = $this->getFilterVal('mode');
		$filters = $this->getFilterVals();
		$this->setLimit(app()->config()->get('app.prices.onpage', 10));
		$price_catalog_price = new PriceCatalogPrice();

		$_firm_ids_and_counts = $price_catalog_price->getIdFirmsByCatalog($this->catalog());
		$firm_ids_and_counts = $_firm_ids_and_counts['data'];
		$price_count = (int)$_firm_ids_and_counts['total_price_count'];
                //die('STOP');
		if ($price_count > 0 && $price_count < APP_CATALOG_MODE_THRESHOLD) {
			$raw_price_ids = $price_catalog_price->getPriceIds($this->catalog());
			$this->pagination()
					->setPage($this->getPage())
					->setLimit($this->getLimit())
					->calculateParams();
			if ($raw_price_ids) {
				$this->setSidebar($this->catalog(), $this->getFilterVals(), $raw_price_ids);
                $res_price_ids = $this->getSortedPriceIdsWithMatch($this->catalog(), $this->getFilterVals(), $raw_price_ids, $firm_ids_and_counts);
                //$res_price_ids = $this->getSortedPriceIds($this->catalog(), $this->getFilterVals(), $raw_price_ids, $firm_ids_and_counts);
				$this->setItemsByIds($res_price_ids);
			}
		} elseif ($price_count > 0) {
			$this->firm_count = count($firm_ids_and_counts);
			$this->price_count = $price_count;
			$this->setItemsByIds($price_catalog_price->getPriceIds($this->catalog(), array_keys($firm_ids_and_counts), $this->getLimit(), $this->getLimit() * ($this->getPage() - 1)));
		} else {
			$catalogs_count = [];
			app()->metadata()->noIndex();
			$this->items = [];
		}
        
        $this->pagination()
				->setPage($this->getPage())
				->setTotalRecordsParam('prices', $this->price_count)
				->setTotalRecordsParam('firms', $this->firm_count)
				->setTotalRecords($this->price_count)
				->setLink(app()->link($this->catalog()->link()))
				->setLinkParams($this->getFilterVals())
				->calculateParams()
				->renderElems();

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
	}

	public function find() {
		$mode = $this->getFilterVal('mode');

		$path = $this->catalog()->adjacencyListComponent()->getPath();
		$this->setAdvertRestrictions()
				->setBreadCrumbs()
				->setAdv($this->catalog())
				->setDefaultImage();

		switch ($mode) {
			case 'map' : $result = $this->findOnMap();
				break;
			case 'price' : $result = $this->findPrices();
				break;
			default : $result = $this->findFirms();
		}

		return $result;
	}

	public function setPath($path) {
		$this->path = (string)$path;
		return $this;
	}

	public function setCatalog(PriceCatalog $catalog) {
		$this->catalog = $catalog;
		return $this;
	}

	/**
	 * 
	 * @return PriceCatalog
	 */
	public function catalog() {
		return $this->catalog;
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

	protected function setAdvertRestrictions($use_parents = false) {
		$path = $this->catalog()->getPath();
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

	private function setBreadCrumbs() {
		$path = $this->catalog()->getPath();

		foreach ($path as $cat) {
			if ($cat->val('node_level') <= 2) {
				app()->breadCrumbs()
						->setElem($cat->name(), app()->link($cat->link()));
			} else {
				app()->breadCrumbs()
						->setElem($cat->name(), app()->link($cat->link()));
			}
		}

		return $this;
	}

	private function setDefaultImage() {
		$path = $this->catalog()->getPath();
		$rev_path = array_reverse($path);
		foreach ($rev_path as $cat) {
			if ($cat->val('image')) {
				$this->default_catalog_image = $cat->imageComponent()->get();
				break;
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
			}
		}

		return $this;
	}

	public function setItemsByIds($price_ids) {
		if ($price_ids) {
			$imp_id_list = implode(',', $price_ids);
			$query = Price::priceQueryChunk().' WHERE `price`.`id` IN ('.$imp_id_list.') ORDER BY FIELD(`price`.`id`,'.$imp_id_list.')';
			$items = app()->db()->query()
					->setText($query)
					->fetch();
            
			$i = 0;
			foreach ($items as $item) {
				$i++;
				$firm = new Firm($item['id_firm']);
				$this->items[$item['id']] = Price::combine($item, $firm, [], $this->default_catalog_image);
			}
		}

		return $this;
	}

	public function setSidebar(PriceCatalog $catalog, $filters, $raw_price_ids) {
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
			$b_where = ['AND',$b_conds['where'],'`count` > :count', '`flag_is_active` = :flag_is_active'];
			$b_params = array_merge($b_conds['params'], [':count' => 1,':flag_is_active' => 1]);

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
				->setParam('top_brands', $top_brands)
				->setParam('brands_active', explode(',', $filters['brand']))
				->setParam('min_cost', (int)$min_cost)
				->setParam('max_cost', (int)$max_cost)
				->setParam('right_layout_filter_sidebar', TRUE)
				->setParam('wholesail_and_retail', (int)$catalog->val('id_group') === 44 ? FALSE : TRUE)
				->setParam('is_service', $catalog->id_group() === 44)
				->setTemplate('sidebar_price_catalog')
				->setTemplateDir('common');
	}

	public function getSortedPriceIds(PriceCatalog $catalog, $filters, $raw_price_ids, $firm_ids_and_counts) {
		/*$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() AS weight'))
				->limit(0, SPHINX_MAX_INT)
				->from(SPHINX_PRICE_INDEX)
				->where('id', 'IN', $raw_price_ids)
				->option('max_matches', SPHINX_MAX_INT);
        
		$this->setSphinxFilter($sphinx, $filters);

		$price_sorting = self::getSorting($filters['sorting']);
		foreach ($price_sorting['expression'] as $field => $direction) {
			$sphinx->orderBy($field, $direction);
		}*/
                
		$result_price_ids = [];
		$this->firm_count = 0;
		$this->result_firm_ids = [];
		$this->price_count = 0;
		$this->result_price_ids = [];
		if (($filters['sorting'] === null || $filters['sorting'] === 'default')) {
			//$prices = $sphinx->execute();
            $prices = [];
            //die(print_r(array_keys($firm_ids_and_counts)));
            foreach (array_keys($firm_ids_and_counts) as $item)
                {
                    $prices [] = ['id' => '1', 'id_firm'=> $item];
                }
//			$prices = array(
//                            0 => array(
//                                'id' => '1', 'id_firm'=> '27737'
//                            )
//                        );
			$prices_by_firms = [];
			$all_prices = [];
			$firm_counter = [];
                        //die(print_r($firm_ids_and_counts));
			$firm_index = array_flip(array_keys($firm_ids_and_counts));
			$group_prices = [];
			foreach ($prices as $price) {
				if (!isset($firm_counter[$price['id_firm']])) {
					$firm_counter[$price['id_firm']] = 0;
					$group_prices[$firm_index[$price['id_firm']]] = [];
				}
				$prices_by_firms[$price['id_firm']][] = $price['id'];
				if ($firm_counter[$price['id_firm']] < app()->config()->get('app.price.grouped.count', 5)) {
					$group_prices[$firm_index[$price['id_firm']]][] = $price['id'];
				} else {
					$all_prices[] = $price['id'];
				}
				$firm_counter[$price['id_firm']] ++;
				$this->price_count++;
			}
			$this->firm_count = count($prices_by_firms);
			$this->result_firm_ids = array_keys($prices_by_firms);

			ksort($group_prices);

			if ($all_prices) {
				$result_price_ids = array_slice(array_merge(array_merge(...$group_prices), $all_prices), $this->pagination()->getOffset(), $this->pagination()->getLimit());
			} else {
				foreach ($group_prices as $k => $v) {
					foreach ($v as $vv) {
						$result_price_ids[] = $vv;
					}
				}
			}
		} else {
//            Эта хрень походу была не дописана и соответственно не реализована если мы берем каталог организаций #5364 #seo
//            $sphinx = SphinxQL::create(app()->getSphinxConnection());
//			$_result_price_ids = $sphinx->execute();
//			$_result_firm_ids = [];
//			foreach ($_result_price_ids as $res) {
//				$result_price_ids[] = $res['id'];
//				$this->price_count++;
//				$_result_firm_ids[$res['id_firm']] = 1;
//			}
//			$this->firm_count = count($_result_firm_ids);
//            $this->result_firm_ids = array_keys($_result_firm_ids);
//			$result_price_ids = array_slice($result_price_ids, $this->pagination()->getOffset(), $this->pagination()->getLimit());
            
            //далее копипаст кода выше
            //$prices = $sphinx->execute();
            $prices = [];
            //die(print_r(array_keys($firm_ids_and_counts)));
            foreach (array_keys($firm_ids_and_counts) as $item)
                {
                    $prices [] = ['id' => '1', 'id_firm'=> $item];
                }
//			$prices = array(
//                            0 => array(
//                                'id' => '1', 'id_firm'=> '27737'
//                            )
//                        );
			$prices_by_firms = [];
			$all_prices = [];
			$firm_counter = [];
                        //die(print_r($firm_ids_and_counts));
			$firm_index = array_flip(array_keys($firm_ids_and_counts));
			$group_prices = [];
			foreach ($prices as $price) {
				if (!isset($firm_counter[$price['id_firm']])) {
					$firm_counter[$price['id_firm']] = 0;
					$group_prices[$firm_index[$price['id_firm']]] = [];
				}
				$prices_by_firms[$price['id_firm']][] = $price['id'];
				if ($firm_counter[$price['id_firm']] < app()->config()->get('app.price.grouped.count', 5)) {
					$group_prices[$firm_index[$price['id_firm']]][] = $price['id'];
				} else {
					$all_prices[] = $price['id'];
				}
				$firm_counter[$price['id_firm']] ++;
				$this->price_count++;
			}
			$this->firm_count = count($prices_by_firms);
			$this->result_firm_ids = array_keys($prices_by_firms);

			ksort($group_prices);

			if ($all_prices) {
				$result_price_ids = array_slice(array_merge(array_merge(...$group_prices), $all_prices), $this->pagination()->getOffset(), $this->pagination()->getLimit());
			} else {
				foreach ($group_prices as $k => $v) {
					foreach ($v as $vv) {
						$result_price_ids[] = $vv;
					}
				}
			}
            
            
            
		}
        $this->result_price_ids = $result_price_ids;

		return $result_price_ids;
                
                
            return [1,2];
	}
    
    public function getSortedPriceIdsWithMatch(PriceCatalog $catalog, $filters, $raw_price_ids, $firm_ids_and_counts) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() AS weight'))
				->limit(0, SPHINX_MAX_INT)
				->from(SPHINX_PRICE_INDEX)
				->where('id', 'IN', $raw_price_ids)
				->option('max_matches', SPHINX_MAX_INT);
        
		$this->setSphinxFilter($sphinx, $filters);

		$price_sorting = self::getSorting($filters['sorting']);
		foreach ($price_sorting['expression'] as $field => $direction) {
			$sphinx->orderBy($field, $direction);
		}

		$result_price_ids = [];
		$this->firm_count = 0;
		$this->result_firm_ids = [];
		$this->price_count = 0;
		$this->result_price_ids = [];
		if (($filters['sorting'] === null || $filters['sorting'] === 'default')) {
            //$this->setSphinxMatch($sphinx, $catalog);
			$prices = $sphinx->execute();
            if (true) {
                $this->setSphinxMatch($sphinx, $catalog);
                $matched_prices = $sphinx->execute();
            }
            
			$prices_by_firms = [];
			$all_prices = [];
            $all_group_prices = [];
			$firm_counter = [];
			$firm_index = array_flip(array_keys($firm_ids_and_counts));
			$group_prices = [];
            foreach ($matched_prices as $price) {
				if (!isset($firm_counter[$price['id_firm']])) {
					$firm_counter[$price['id_firm']] = 0;
					$group_prices[$firm_index[$price['id_firm']]] = [];
				}
				$prices_by_firms[$price['id_firm']][] = $price['id'];
				if ($firm_counter[$price['id_firm']] < app()->config()->get('app.price.grouped.count', 5)) {
					$group_prices[$firm_index[$price['id_firm']]][] = $price['id'];
                    $all_group_prices[] = $price['id'];
				} else {
					$all_prices[] = $price['id'];
				}
				$firm_counter[$price['id_firm']] ++;
				$this->price_count++;
            }
			foreach ($prices as $price) {
                if (in_array($price['id'], $all_prices) || in_array($price['id'], $all_group_prices)) continue;
                
				if (!isset($firm_counter[$price['id_firm']])) {
					$firm_counter[$price['id_firm']] = 0;
					$group_prices[$firm_index[$price['id_firm']]] = [];
				}
				$prices_by_firms[$price['id_firm']][] = $price['id'];
				if ($firm_counter[$price['id_firm']] < app()->config()->get('app.price.grouped.count', 5)) {
					$group_prices[$firm_index[$price['id_firm']]][] = $price['id'];
                    $all_group_prices[] = $price['id'];
				} else {
					$all_prices[] = $price['id'];
				}
				$firm_counter[$price['id_firm']] ++;
				$this->price_count++;
			}
			$this->firm_count = count($prices_by_firms);
			$this->result_firm_ids = array_keys($prices_by_firms);

			ksort($group_prices);
            
            /*$full_prices = $this->getSortedPriceIds($catalog, $filters, $raw_price_ids, $firm_ids_and_counts);
            $full_prices = array_diff($full_prices, array_merge(...$group_prices));
            $full_prices = array_diff($full_prices, $all_prices);*/

			if ($all_prices) {
				$result_price_ids = array_slice(array_merge(array_merge(...$group_prices), $all_prices), $this->pagination()->getOffset(), $this->pagination()->getLimit());
			} else {
				foreach ($group_prices as $k => $v) {
					foreach ($v as $vv) {
						$result_price_ids[] = $vv;
					}
				}
			}
		} else {
			$_result_price_ids = $sphinx->execute();
			$_result_firm_ids = [];
			foreach ($_result_price_ids as $res) {
				$result_price_ids[] = $res['id'];
				$this->price_count++;
				$_result_firm_ids[$res['id_firm']] = 1;
			}
			$this->firm_count = count($_result_firm_ids);
            $this->result_firm_ids = array_keys($_result_firm_ids);
			$result_price_ids = array_slice($result_price_ids, $this->pagination()->getOffset(), $this->pagination()->getLimit());
		}
        
        $this->result_price_ids = $result_price_ids;

		return $result_price_ids;
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
				$sphinx->match('(name,vendor)', SphinxQL::expr($query));
			}
		}

		return $this;
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

	public static function getSorting($sorting) {
		$sorting_options = self::getSortingOptions();

		return isset($sorting_options[$sorting]) ? $sorting_options[$sorting] : $sorting_options['default'];
	}

	public static function getSortingOptions() {
		return [
			'default' => [
				'name' => 'по-умолчанию',
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

}
