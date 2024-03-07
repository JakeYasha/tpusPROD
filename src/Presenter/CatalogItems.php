<?php

namespace App\Presenter;

use App\Action\Catalog;
use App\Classes\Pagination;
use App\Model\Firm;
use App\Model\Price;
use App\Model\PriceCatalog;
use Foolz\SphinxQL\SphinxQL;
use Sky4\Model\Utils;
use const SPHINX_PRICE_INDEX;
use function app;

class CatalogItems extends Presenter {

	public function __construct() {
		parent::__construct();
		$this->setLimit(10);
		$this->setItemsTemplate('firm_presenter_items')
				->setItemsTemplateSubdirName('catalog')
				->setModelName('PriceCatalog');
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

	public function find($id_group, $id_subgroup, $filters, $id_catalog) {
		$cities_where_conds = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
		$sorting = Catalog::getCurrentSorting($filters);

		$mode = $filters['mode'] ? (string) $filters['mode'] : 'firm';

		$this->pagination()
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLinkParams($filters)
				->calculateParams();

		$catalog = new PriceCatalog($id_catalog);
		$path = $catalog->adjacencyListComponent()->getPath();

		foreach ($path as $cat) {
			app()->breadCrumbs()
					->setElem($cat->name(), app()->link($cat->link()));
		}

		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$sphinx->select('id', 'id_firm', 'id_service', SphinxQL::create(app()->getSphinxConnection())->expr('WEIGHT() as weight'))
				->from(SPHINX_PRICE_INDEX)
				->where('id_subgroup', '=', intval($id_subgroup))
				//->where('have_price', '=', 1)
				->where('id_firm', 'IN', app()->location()->getFirmIds())
				->orderBy('have_inf_img_prc', 'DESC')
				->orderBy('have_price', 'DESC');

		$price_sorting = self::getSorting($sorting);
		app()->tabs()->setSortOptions(self::getSortingOptions());
		$sphinx->orderBy($price_sorting['field'], $price_sorting['direction']);


		if ($mode !== 'price') {
			//если показываем не товары, то забираем все фирмы
			$sphinx->offset(0);
		} else {
			$sphinx->limit($this->pagination()->getOffset(), $this->pagination()->getLimit());
		}

		if ($catalog->exists()) {
			if (app()->location()->currentId() == 76004 && $catalog->val('metadata_title')) {
				app()->metadata()->setFromModel($catalog);
			} else {
				app()->metadata()
						->setHeader($catalog->name() . ' ' . app()->location()->currentName('prepositional'))
						->setTitle(app()->metadata()->getHeader() . '.' . app()->config()->get('catalog.additional.title'));
			}

			$sphinx->match('name', $catalog->val('name'))
					->orderBy('weight', 'DESC');
		} else {
			$catalog->reader()->setWhere(['AND', '`id_subgroup` = :id_subgroup', '`flag_is_catalog` = :0'], [':id_subgroup' => $id_subgroup, ':0' => 0])->objectByConds();
		}

		$res = $sphinx
				->enqueue(SphinxQL::create(app()->getSphinxConnection())->query('SHOW META'))
				->executeBatch();

		$res_firms = $sphinx
				->groupBy('id_firm')
				->enqueue(SphinxQL::create(app()->getSphinxConnection())->query('SHOW META'))
				->executeBatch();

		if ($mode === 'price') {
			$this->pagination()
					->setTotalRecordsParam('prices', $res[1][1]['Value'])
					->setTotalRecordsParam('firms', $res_firms[1][1]['Value'])
					->setTotalRecords($res[1][1]['Value'])
					->setLink(app()->linkFilter(app()->link($catalog->link())))
					//->setBasicLink(app()->link($catalog->link()))
					->calculateParams()
					->renderElems();

			$id_list = [];

			if (isset($res[0]) && $res[0]) {
				foreach ($res[0] as $row) {
					$id_list[] = $row['id'];
				}
			} else {
				app()->metadata()->noIndex();
				return $this;
			}

			$imp_id_list = implode(',', $id_list);

			$sts = new Firm();

			$query = Price::catalogQueryChunk($imp_id_list);
			$items = app()->db()->query()
					->setText($query)
					->fetch();

			$i = 0;
			foreach ($items as $it) {
				$i++;
				$firm = new Firm();
				$firm->setVals($it);

				$this->items[$it['id']] = Price::combine($it, $firm, []);
				if ($it['photo']) {
					$this->items[$it['id']]['image'] = Price::photoLink($it['legacy_id_firm'], $it['legacy_id_service'], $it['photo']);
				}
			}

			$this->setItemsTemplate('catalog_' . $mode . '_items_presenter');

			return $this;
		} else {
			$presentrer = new FirmItems();
			$price_catalog = new PriceCatalog($id_catalog);
			if (!$price_catalog->exists()) {
				$price_catalog->reader()->setWhere(['AND', '`id_subgroup` = :id_subgroup', '`node_level` = :2'], [':id_subgroup' => $id_subgroup, ':2' => 2])->objectByConds();
			}

			$price_catalog_count = new \App\Model\PriceCatalogPrice();
			$id_firms = $price_catalog_count->getIdFirmsByCatalog($price_catalog->id());
			$catalogs_count = $price_catalog_count->getSubCatalogs($price_catalog);

			$presentrer
					->setPage($this->getPage())
					->setLimit($this->getLimit());

			$presentrer->pagination()
					->setPage($this->getPage())
					->setLimit($this->getLimit())
					->setTotalRecordsParam('prices', $res[1][1]['Value'])
					->setTotalRecordsParam('firms', count($id_firms['data']))
					->setTotalRecords(count($id_firms['data']))
					->setLink(app()->linkFilter(app()->link($catalog->link()), $filters))
					->calculateParams()
					->renderElems();

			$catalogs = $price_catalog->adjacencyListComponent()->getChildren();
			if ($price_catalog->exists()) {
				$catalogs[$price_catalog->id()] = $price_catalog;
			}

			$presentrer->view()->set('catalogs', $catalogs);
			$presentrer->view()->set('catalogs_count', $catalogs_count);

			$presentrer->findByIds($id_firms['data'], $filters);

			return $presentrer;
		}

		return $this;
	}

	public function getTagsByCatalogs($catalogs, $id_subgroup) {
		$firm_location_conds = Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');
		$c = new PriceCatalog();

		if (!$catalogs) {
			$c_where = ['AND', '`level` = :1', '`id_subgroup` = :id_subgroup', $firm_location_conds['where'], '`cnt` != :0'];
			$c_params = [':1' => 1, ':0' => 0, ':id_subgroup' => $id_subgroup];
			$c_params = array_merge($c_params, $firm_location_conds['params']);
		} else {
			$count = count($catalogs);
			if ($count === 3) return [];

			$last = end($catalogs);
			$c_where = ['AND', '`level` = :level', '`id_subgroup` = :id_subgroup', $firm_location_conds['where'], '`cnt` != :0', '`id_parent` = :id_parent'];
			$c_params = [':level' => 1 + $count, ':0' => 0, ':id_subgroup' => $id_subgroup, ':id_parent' => $last->id()];
			$c_params = array_merge($c_params, $firm_location_conds['params']);
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
				'field' => 'cost',
				'direction' => 'asc',
				'name' => 'по цене'
			],
			'alpha' => [
				'field' => 'sortname',
				'direction' => 'asc',
				'name' => 'по алфавиту'
			],
			'date' => [
				'field' => 'datetime',
				'direction' => 'asc',
				'name' => 'по дате'
			]
		];
	}

}
