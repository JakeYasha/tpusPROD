<?php

namespace App\Presenter;

use App\Action\Catalog;
use App\Action\FirmManager;
use App\Classes\Pagination;
use App\Classes\YandexMaps;
use App\Controller\Firm;
use App\Model\Firm as FirmController;
use App\Model\FirmFirmType;
use App\Model\FirmType;
use App\Model\FirmUser;
use App\Model\StatObject;
use App\Model\StsService;
use Foolz\SphinxQL\SphinxQL;
use Sky4\Exception;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;
use const SPHINX_FIRM_INDEX;
use const SPHINX_MAX_INT;
use function app;
use function str;

class FirmItems extends Presenter {

	private $firm_types = [];
	private $firm_types_matrix = [];
	private $force_hide_activity = false;

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('firm_presenter_items')
				->setItemsTemplateSubdirName('firm')
				->setModelName('Firm');
		return true;
	}

	public function getItemsComplexSort($_where, $_params, $firm_sorting, $firm_ids_and_counts) {
		$matrix = $this->model()->reader()
				->setSelect(['id', 'id_firm'])
				->setWhere($_where, $_params)
				->setOrderBy($firm_sorting['expression'])
				->objects();

		$_items = [];
		$unique_counts = array_count_values($firm_ids_and_counts);
		foreach ($unique_counts as $count => $size) {
			if ($size > 1) {
				$size_matrix = [];
				foreach ($firm_ids_and_counts as $id_firm => $counter) {
					if ($counter === $count) {
						$size_matrix[] = $id_firm;
					}
				}

				foreach ($matrix as $id_firm => $it) {
					foreach ($size_matrix as $m_id_firm) {
						if ($id_firm === $m_id_firm) {
							$_items[$it->id()] = $it;
						}
					}
				}
			} else {
				foreach ($firm_ids_and_counts as $id_firm => $counter) {
					if ($count === $counter && isset($matrix[$id_firm])) {
						$_items[$matrix[$id_firm]->id()] = $matrix[$id_firm];
					}
				}
			}
		}

		$where_conds = Utils::prepareWhereCondsFromArray(array_keys($_items));
		$order_conds = Utils::prepareOrderByField(array_keys($_items), 'id');
		return $this->model()->reader()
						->setWhere($where_conds['where'], $where_conds['params'])
						->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
						->setOrderBy($order_conds['order'])
						->objects();
	}

	public function getFirmTypes() {
		return $this->firm_types;
	}

	public function getFirmTypesMatrix() {
		return $this->firm_types_matrix;
	}

	public function getPage() {
		$params = app()->request()->processGetParams(['page' => 'int']);
		if ($params['page']) return $params['page'];
		return 1;
	}

	// -------------------------------------------------------------------------

	public function findByIds($firm_ids_and_counts, $filters, $add_to_stat_catalog_price_list_firm = true) {
		$firm_ids = array_keys($firm_ids_and_counts);
		if (!$firm_ids) {
			$this->items = [];
		} else {
			$intersect_ids = array_intersect(app()->location()->getFirmIds(), $firm_ids);
			$where_conds_id = Utils::prepareWhereCondsFromArray($intersect_ids, 'id');

			$_where = [
				'AND',
				$where_conds_id['where'],
			];

			$_params = $where_conds_id['params'];

			$sorting = Catalog::getCurrentSorting($filters);
			$firm_sorting = self::getSorting($sorting, $firm_ids);
			app()->tabs()->setSortOptions(self::getSortingOptions($firm_ids));

			if ($sorting === 'default' && $filters['mode'] !== 'map') {
				$items = $this->getItemsComplexSort($_where, $_params, $firm_sorting, $firm_ids_and_counts);
			} else {
				$items = $this->model()->reader()
						->setWhere($_where, $_params)
						->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
						->setOrderBy($firm_sorting['expression'])
						->objects();
			}

			if ($filters['mode'] === 'map') {
				app()->tabs()->setSortOptions([]);
				$this->setItemsTemplate('firm_presenter_items_on_map');
				$this->items = ['items' => $items];

				foreach ($items as $firm) {
					if ($add_to_stat_catalog_price_list_firm) {
						app()->stat()->addObject(StatObject::CATALOG_PRICE_LIST_FIRM, $firm);
					}
					$this->items['coords'][$firm->id()] = YandexMaps::geocode($firm->address());
				}
			} else {
				$this->items = $items;
				foreach ($items as $firm) {
					if ($add_to_stat_catalog_price_list_firm) {
						app()->stat()->addObject(StatObject::CATALOG_PRICE_LIST_FIRM, $firm);
					}
				}
			}
		}

		return $this;
	}

	public function findByBranchesIds($firm_ids_and_counts, $filters, $add_to_stat_catalog_price_list_firm = true) {
		$firm_ids = array_keys($firm_ids_and_counts);
		if (!$firm_ids) {
			$this->items = [];
		} else {
			$where_conds_id = Utils::prepareWhereCondsFromArray($firm_ids, 'id');
			$_where = [
				'AND',
				$where_conds_id['where']
			];

			$_params = $where_conds_id['params'];

			$sorting = Catalog::getCurrentSorting($filters);
			$firm_sorting = self::getSorting($sorting, $firm_ids);
			app()->tabs()->setSortOptions(self::getSortingOptions($firm_ids));

			if ($sorting === 'default' && $filters['mode'] !== 'map') {
				$items = $this->getItemsComplexSort($_where, $_params, $firm_sorting, $firm_ids_and_counts);
			} else {
				$items = $this->model()->reader()
						->setWhere($_where, $_params)
						->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
						->setOrderBy($firm_sorting['expression'])
						->objects();
			}

			if ($filters['mode'] === 'map') {
				app()->tabs()->setSortOptions([]);
				$this->setItemsTemplate('firm_presenter_items_on_map');
				$this->items = ['items' => $items];

				foreach ($items as $firm) {
					if ($add_to_stat_catalog_price_list_firm) {
						app()->stat()->addObject(StatObject::CATALOG_PRICE_LIST_FIRM, $firm);
					}
					$this->items['coords'][$firm->id()] = YandexMaps::geocode($firm->address());
					if ($this->model()->id() === $firm->id() && !$firm->isBranch()) {
						$this->items['current_firm'] = $firm->id();
					}
				}
			} else {
				$this->items = $items;
				foreach ($items as $firm) {
					if ($add_to_stat_catalog_price_list_firm) {
						app()->stat()->addObject(StatObject::CATALOG_PRICE_LIST_FIRM, $firm);
					}
				}
			}
		}

		return $this;
	}
    
    public function findByFirmBranchesIds($firm_branches_ids_and_counts, $filters) {
		$firm_branch_ids = array_keys($firm_branches_ids_and_counts);
		if (!$firm_branch_ids) {
			$this->items = [];
		} else {
			$where_conds_id = Utils::prepareWhereCondsFromArray($firm_branch_ids, 'id');
			$_where = [
				'AND',
				$where_conds_id['where']
			];

			$_params = $where_conds_id['params'];

            $_fb = new \App\Model\FirmBranch();
            $items = $_fb->reader()
                    ->setWhere($_where, $_params)
                    ->objects();

			if ($filters['mode'] === 'map') {
				$this->setItemsTemplate('firm_presenter_items_on_map');
				$this->items = ['items' => $items];

				foreach ($items as $firm_branch) {
					$this->items['coords'][$firm_branch->id()] = YandexMaps::geocode($firm_branch->address());
					if ($this->model()->isBranch() && $this->model()->branch_id == $firm_branch->id()) {
						$this->items['current_firm'] = $firm_branch->id();
					}
				}
			} else {
				$this->items = $items;
			}
		}

		return $this;
	}

	public function findNew($filters) {
		$firm_location_conds = Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id');
		$_where = [
			'AND',
			'`flag_is_active` = :1',
			$firm_location_conds['where'],
		];

		$_params = [':1' => 1];
		$_params = array_merge($_params, $firm_location_conds['params']);

		$items = $this->model()->reader()
				->setWhere($_where, $_params)
				->setLimit(50, $this->pagination()->getOffset())
				->setOrderBy("`timestamp_inserting` DESC")
				->objects();

		if ($filters['mode'] === 'map') {
			$this->setItemsTemplate('firm_presenter_items_on_map');
			$this->items = ['items' => $items];

			foreach ($items as $firm) {
				$this->items['coords'][$firm->id()] = YandexMaps::geocode($firm->address());
			}
		} else {
			$this->items = $items;
		}

		return $this;
	}

	public function findPopular($filters) {
		$types_ids = StatObject::getTypesIdsForPopularity();
		$types_string = '(SUM(t'.implode(')+SUM(t', $types_ids).'))';
		$service = new StsService();
		$id_service = $service->getIdByLocation();

		$_firm_ids = app()->db()->query()->setText("SELECT ".$types_string." as `cnt`, id_firm FROM `avg_stat_object` WHERE `id_firm` IN (".implode(',', app()->location()->getFirmIds()).") AND timestamp_inserting > '".DeprecatedDateTime::fromTimestamp(mktime(0, 0, 0, date('m') - 1))."' GROUP BY `id_firm` ORDER BY cnt DESC LIMIT 50")->fetch();

		if ($_firm_ids) {

			$firm_ids = [];
			foreach ($_firm_ids as $row) {
				$firm_ids[] = $row['id_firm'];
			}

			$where_conds_city = Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id');
			$where_conds_ids = Utils::prepareWhereCondsFromArray($firm_ids, 'id');
			$_where = [
				'AND',
				'`flag_is_active` = :1',
				$where_conds_ids['where'],
				$where_conds_city['where'],
			];

			$_params = [':1' => 1];
			$_params = array_merge($_params, $where_conds_ids['params'], $where_conds_city['params']);

			$items = $this->model()->reader()
					->setWhere($_where, $_params)
					->setLimit(50, $this->pagination()->getOffset())
					->setOrderBy("FIELD (`id`,".implode(',', $firm_ids).")")
					->objects();

			if ($filters['mode'] === 'map') {
				$this->setItemsTemplate('firm_presenter_items_on_map');
				$this->items = ['items' => $items];

				foreach ($items as $firm) {
					$this->items['coords'][$firm->id()] = YandexMaps::geocode($firm->address());
				}
			} else {
				$this->items = $items;
			}
		}

		return $this;
	}

	public function findBest($filters) {
		$where_conds_city = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
		$_where = [
			'AND',
			'`flag_is_active` = :1',
			$where_conds_city['where'],
		];

		$_params = [':1' => 1];
		$_params = array_merge($_params, $where_conds_city['params']);

		$items = $this->model()->reader()
				->setWhere($_where, $_params)
				->setLimit(50, $this->pagination()->getOffset())
				->setOrderBy("`rating` DESC")
				->objects();

		if ($filters['mode'] === 'map') {
			$this->setItemsTemplate('firm_presenter_items_on_map');
			$this->items = ['items' => $items];

			foreach ($items as $firm) {
				$this->items['coords'][$firm->id()] = YandexMaps::geocode($firm->address());
			}
		} else {
			$this->items = $items;
		}

		return $this;
	}
    public function getIdService(){
        return app()->firmManager()->id_service();
    }
	public function findByManager($manager_ids, $filters = []) {
		if (count($manager_ids) === 1 && app()->firmManager()->isSuperMan()) {
			$where = 'id_service = :id_service';
			$params = [':id_service' => app()->firmManager()->id_service()];
		} else {
			$conds = Utils::prepareWhereCondsFromArray($manager_ids, 'id_manager');
			//$where = $conds['where'];
			//$params = $conds['params'];
            $where = [
				'AND',
				'id_service = :id_service',
				$conds['where']
			];

			$params = array_merge(
					[':id_service' => app()->firmManager()->id_service()], $conds['params']
			);
		}

		$order_by = ['`flag_is_active` DESC'];

		if ($filters['query'] !== null && $filters['query']) {
			$where = ['AND', $where];
			$where[] = ['OR', 'company_name LIKE :q', 'company_email LIKE :q', 'company_activity LIKE :q'];
			$params = $params + [':q' => '%'.$filters['query'].'%'];
		}

		$sorting = FirmManager::firmSortingOptions();
		if ($filters['sorting'] !== null && isset($sorting[$filters['sorting']]['expression'])) {
			$order_by[] = $sorting[$filters['sorting']]['expression'];
		} else {
			$order_by[] = 'company_name ASC';
		}

		$this->pagination()
				->setTotalRecords($this->model()->reader()->setWhere($where, $params)->count())
				->setLink('/firm-manager/')
				->setLinkParams($filters)
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->calculateParams()
				->renderElems();

		$_items = $this->model()->reader()
				->setWhere($where, $params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy(implode(',', $order_by))
				->objects();

		$items = [];
		$fusers = [];
		foreach ($_items as $item) {
			// костыль для выявления оригинального пользователя
			$lead_user_id = $item->val('id_firm_user');
			$email = '';
			if (!empty($lead_user_id) && $lead_user_id != 0) {
				$fuser = new FirmUser($lead_user_id);
				$fusers = $fuser->reader()
						->setWhere(['AND', 'email = :email'], [':email' => $fuser->val('email')])
						->setOrderBy('id ASC')
						->setLimit(1)
						->objectByConds();
				if ($fuser->exists()) {
					$lead_user_id = $fuser->id();
					$email = $fuser->val('email');
				}
			}
			$items[] = array_merge(self::formatFirm($item), ['is_lead' => $lead_user_id != 0 && $lead_user_id == $item->val('id_firm_user') ? true : false, 'email' => $email]);
		}

		$this->items = $items;
		$this->setItemsTemplate('firm_presenter');
	}

	public function findByType($id_type, $id_sub_type = null, $filters) {
		$firm_type = new FirmType($id_type);

		$rel_model = new FirmFirmType();

		$sorters = Firm::getSortingOptions();
		$sorter = '`priority` DESC, `rating` DESC';
		if (isset($filters['sorting']) && isset($sorters[$filters['sorting']])) {
			$sorter = '`'.$sorters[$filters['sorting']]['field'].'` '.$sorters[$filters['sorting']]['direction'];
		}

        $_where = [];
        $_params = [];
        if ($id_type === null) {
			$firm_sub_types = $firm_type
					->setOrderBy('`name` ASC')
					->getAll();
			$types_where_conds = Utils::prepareWhereCondsFromArray(array_keys($firm_sub_types), 'id_type');
			$_where = [
				'AND',
				$types_where_conds['where'],
			];

			$_params = $types_where_conds['params'];
		} elseif ($id_sub_type === null) {
			$firm_sub_types = $firm_type
					->setWhere(['AND', '`parent_node` = :id_type'], [':id_type' => $id_type])
					->setOrderBy('`name` ASC')
					->getAll();
            

            if ($firm_sub_types) {
                $types_where_conds = Utils::prepareWhereCondsFromArray(array_keys($firm_sub_types), 'id_type');
                $_where = [
                    'AND',
                    $types_where_conds['where'],
                ];

                $_params = $types_where_conds['params'];
            } else {
                $_where = [
                    'AND',
                    '`id_type` = :id_type',
                ];

                $_params[':id_type'] = $id_type;
            }
		} else {
			$_where = [
				'AND',
				'`id_type` = :id_type',
			];

			$_params[':id_type'] = $id_sub_type;
		}

        $cities_where_conds = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
        
        if (/*APP_IS_DEV_MODE*/true) {
            $_fb = new \App\Model\FirmBranch();
            $_firm_ids = array_keys($_fb->reader()
                    ->setWhere($cities_where_conds['where'], $cities_where_conds['params'])
                    ->rowsWithKey('firm_id'));
            
            if ($_firm_ids) {
                $_firm_ids_conds = Utils::prepareWhereCondsFromArray($_firm_ids, 'id_firm');
                $__where = $_where;
                $__where []= $_firm_ids_conds['where'];
                $__params = array_merge($_params, $_firm_ids_conds['params']);
                $_firm_ids = array_keys(
                        $rel_model->reader()
                                ->setWhere($__where, $__params)
                                ->rowsWithKey('id_firm')
                );
            }
        }
        
        $_where []= $cities_where_conds['where'];
        $_params = array_merge($_params, $cities_where_conds['params']);

		$firm_ids = array_keys(
				$rel_model->reader()
						->setWhere($_where, $_params)
						->rowsWithKey('id_firm')
		);
        
        if (/*APP_IS_DEV_MODE*/true) {
            $firm_ids = array_merge($firm_ids, $_firm_ids);
        }
        
		if ($firm_ids) {
			$id_where_conds = Utils::prepareWhereCondsFromArray($firm_ids);

			$_where = [
				'AND',
				'`flag_is_active` = :1',
				$id_where_conds['where']
			];

			$_params = array_merge(
					[':1' => 1], $id_where_conds['params']
			);

			if ($filters['id_district']) {
				$where[] = '`id_region_city` = :id_region_city';
				$_params[':id_region_city'] = $filters['id_district'];
			}

			$this->pagination()
					->setLimit($this->getLimit())
					->setPage($this->getPage())
					->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
					->calculateParams()
					->renderElems();

			$items = $this->model()->reader()
					->setWhere($_where, $_params)
					->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
					->setOrderBy($sorter)
					->objects();

			if ($filters['on_map']) {
				$this->setItemsTemplate('firm_presenter_items_on_map');
				$this->items = ['items' => $items];

				/* @var $firm FirmController */
				foreach ($items as $firm) {
					//app()->stat()->addObject(StatObject::CATALOG_FIRM_LIST_FIRM, $firm);
					$this->items['coords'][$firm->id()] = YandexMaps::geocode($firm->address());
				}
			} else {
				$this->items = $items;
				foreach ($items as $firm) {
					app()->stat()->addObject(StatObject::CATALOG_FIRM_LIST_FIRM, $firm);
				}
			}
		} else {
			throw new Exception();
		}

		return $this;
	}

	public function setTypesByFirmIds($firm_ids, $filters) {
		$fft = new FirmFirmType();
		$types_rows = $fft->getByFirmIds($firm_ids);

		$ft = new FirmType($filters['id_type']);
		$filter_types = [];
		if ($ft->exists()) {
			if ((int)$ft->val('node_level') === 1) {
				$filter_types = array_keys($ft->adjacencyListComponent()->getChildren(2));
			} else {
				$filter_types = [$ft->id()];
			}

			app()->breadCrumbs()
					->setElem('В категории '.$ft->name());
		}

		$type_ids = [];
		foreach ($types_rows as $row) {
			if ($filter_types && (int)$ft->val('node_level') === 2) {
				break;
			}
			if ($filter_types && !in_array($row['id_type'], $filter_types)) {
				continue;
			}
			if (!isset($type_ids[$row['id_type']])) {
				$type_ids[$row['id_type']] = 0;
			}
			$type_ids[$row['id_type']] ++;
		}
		arsort($type_ids);

		$ft = new FirmType();
		$types = $ft->reader()
				->setSelect(['id', 'node_level', 'parent_node', 'name', 'advert_restrictions'])
				->objectsByIds(array_keys($type_ids));

		$parent_type_ids = [];
		$matrix = [];
		foreach ($types as $type) {
			$parent_type_ids[] = $type->val('parent_node');
			if (!isset($matrix[$type->val('parent_node')])) {
				$matrix[$type->val('parent_node')] = [];
			}
			$matrix[$type->val('parent_node')][] = $type->id();
		}

		$parent_types = $ft->reader()
				->setSelect(['id', 'node_level', 'parent_node', 'name', 'advert_restrictions'])
				->objectsByIds(array_filter($parent_type_ids));

		$this->firm_types = $types + $parent_types;
		$this->firm_types_matrix = $matrix;

		return $this;
	}

	public function findByQuery($query, $filters) {
		$this->pagination()
				->setLimit(app()->config()->get('app.firms.onpage'))
				->setPage($this->getPage())
				->calculateParams()
				->renderElems();

		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$sphinx
				->select('*', SphinxQL::expr('WEIGHT() as weight'))
				->limit(0, SPHINX_MAX_INT)
				->from([SPHINX_FIRM_INDEX]) //0
				->where('id', 'IN', app()->location()->getFirmIds())
				->where('flag_is_active', '=', 1);

		$ft = new FirmType($filters['id_type']);
		$firm_ids_by_type = [];
		if ($ft->exists()) {
			$fft = new FirmFirmType();
			$city_conds = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
			$where = ['AND', $city_conds['where'], ['OR', 'id_type = :id_type', 'id_type = :id_parent']];
			$params = [':id_type' => $ft->id(), ':id_parent' => $ft->val('parent_node')] + $city_conds['params'];
			$firm_ids_by_type = array_keys($fft->reader()
							->setWhere($where, $params)
							->rowsWithKey('id_firm'));
		}


		if ($firm_ids_by_type) {
			$sphinx->where('id', 'IN', $firm_ids_by_type);
		}

		if (str()->pos($query, 'кафе') !== false && str()->pos($query, 'кафедр') === false) {
			$query = str()->replace($query, 'кафе', 'кафе -кафедральный'); //@hook
		}

		$results = $sphinx->match('(company_name,company_name_jure,company_activity,company_address)', SphinxQL::expr($query))
				->orderBy('priority', 'DESC')
				->orderBy('weight', 'DESC')
				->orderBy('rating', 'DESC')
				->orderBy('sortname', 'ASC')
				//		->option('field_weights', ['wname' => 200, 'w2name' => 150, 'lightname' => 100, 'company_name' => 50, 'company_activity' => 1, 'company_phone' => 1, 'company_address' => 10])
				->option('field_weights', ['wname' => 200, 'w2name' => 150, 'lightname' => 100, 'company_name' => 50, 'company_activity' => 1, 'company_address' => 10])
				->option('ranker', SphinxQL::expr("expr('sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25+3*sum(hit_count*user_weight)')"))
				->option('max_matches', SPHINX_MAX_INT)
				->enqueue()
				->query('SHOW META')
				->executeBatch();

		$this->pagination()
				->setTotalRecords($results[1][1]['Value'])
				->setLink(app()->link('/search/firms/'))
				->setLinkParams($filters)
				->calculateParams()
				->renderElems();

		$firm_ids = [];
		$firms = [];
		if (isset($results[0]) && $results[0]) {
			foreach ($results[0] as $res) {
				$firm_ids[] = $res['id'];
			}

			//$this->setTypesByFirmIds($firm_ids, $filters); //для показа всех типов
		}

		if ($firm_ids) {
			$where_conds_id = Utils::prepareWhereCondsFromArray($firm_ids, 'id');
			$_where = [
				'AND',
				$where_conds_id['where'],
			];

			$order_by_conds = Utils::prepareOrderByField($firm_ids, 'id');
			$_params = array_merge($where_conds_id['params'], $order_by_conds['params']);

			$items = $this->model()->reader()
					->setWhere($_where, $_params)
					->setOrderBy($order_by_conds['order'])
					->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
					->objects();

			/* foreach ($items as $firm) {
			  app()->stat()->addObject(StatObject::SEARCH_FIRM_LIST, $firm);
			  } */

			if ($filters['mode'] === 'map') {
				$this->setItemsTemplate('firm_presenter_items_on_map');
				$this->items = ['items' => $items];

				foreach ($items as $firm) {
					$this->items['coords'][$firm->id()] = YandexMaps::geocode($firm->address());
				}
			} else {
				$this->items = $items;
			}
		}
	}

	public static function formatFirm(FirmController $item) {
		return [
			'formatted_date' => \App\Classes\Helper::formatDate($item->val('timestamp_inserting')),
			'id' => $item->id(),
			'id_firm' => $item->id_firm(),
			'id_service' => $item->id_service(),
			'file_logo' => $item->val('file_logo') ? $item->val('file_logo') : false,
			'link' => $item->link(),
			'link_pricelist' => $item->linkPricelist(),
			'name' => trim($item->val('company_name')),
			'phone' => $item->val('company_phone'),
			'address' => $item->address(), // $item->val('company_address'),
			'company_activity' => trim($item->val('company_activity')),
			'rating' => $item->val('rating'),
			'is_active' => $item->val('flag_is_active'),
			'timestamp_inserting' => $item->val('timestamp_inserting'),
			'timestamp_last_updating' => $item->val('timestamp_ratiss_updating'),
			'id_firm_user' => $item->val('id_firm_user')
		];
	}

	public static function getSorting($sorting, $id_firms = null) {
		$sorting_options = self::getSortingOptions($id_firms);

		return isset($sorting_options[$sorting]) ? $sorting_options[$sorting] : $sorting_options['default'];
	}

	public static function getSortingOptions($id_firms = null) {
		return [
			'default' => [
				'name' => 'по-умолчанию',
				'expression' => '`priority` DESC, `rating` DESC, `company_name` ASC, `timestamp_last_updating` DESC'
			],
			'rank' => [
				'name' => 'по рейтингу',
				'expression' => '`rating` DESC, `company_name` ASC, `timestamp_last_updating` DESC'
			],
			'alpha' => [
				'name' => 'по алфавиту',
				'expression' => '`company_name` ASC, `rating` DESC, `timestamp_last_updating` DESC'
			],
		];
	}

	/**
	 * @return \\App\Classes\Pagination
	 */
	public function pagination() {
		if ($this->pagination === null) {
			$this->pagination = new Pagination();
		}
		return $this->pagination;
	}

	//from pricepresenter
	private $price_subgroup_ids = [];
	private $price_ids = [];
	private $city_ids = [];

	public function setPriceSubgroupsIds($price_subgroup_ids) {
		$this->price_subgroup_ids = $price_subgroup_ids;
		return $this;
	}

	public function setPriceIds($price_subgroup_ids) {
		$this->price_ids = $price_subgroup_ids;
		return $this;
	}

	public function setForceHideActivity($flag) {
		$this->force_hide_activity = $flag;
		return $this;
	}

	public function setCityIds($city_ids) {
		$this->city_ids = $city_ids;
		return $this;
	}

	public function getRawSubgroupIds() {
		return $this->price_subgroup_ids;
	}

	public function getRawPriceIds() {
		return $this->price_ids;
	}

	public function getRawCityIds() {
		return $this->city_ids;
	}

	public function getForceHideActivity() {
		return $this->force_hide_activity;
	}

}
