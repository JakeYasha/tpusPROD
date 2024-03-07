<?php

namespace App\Action;

use App\Classes\Action;
use App\Model\PriceCatalog;
use function app;

class Catalog extends Action {

	public function execute($id_group = null, $id_subgroup = null, $id_catalog = null, $mnemonick = null) {
		if ($id_catalog !== null && !is_numeric($id_catalog)) {
			$this->redirect(func_get_args());
		}

		$this->checkOldFiltersAndRedirect();

		if ($id_group === null) {
			$action = new Catalog\Level1();
			$action->execute();
		} elseif ($id_subgroup === null && ($id_group == 44 || $id_group == 22)) {
			$action = new Catalog\Level1($id_group);
			$action->execute($id_group);
		} elseif ($id_group !== null && $id_subgroup === null) {
			$action = new Catalog\Level2($id_group);
			$action->execute($id_group);
		} elseif ($id_group !== null && $id_subgroup !== null) {
			$action = new Catalog\Level3();
			$action->execute($id_group, $id_subgroup, $id_catalog, $mnemonick);
		}
		return $this->afterExecute($action);
	}

	protected function afterExecute(Catalog $action) {
		$this->args = $action->args;
		$this->model = $action->model;
		$this->model_name = $action->model_name;
		$this->options = $action->options;
		$this->params = $action->params;
		$this->text = $action->text;
		$this->view = $action->view;
		$this->view_name = $action->view_name;

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('title', $this->getCatalogTitle())
				->save();

		return $this;
	}

	public function getCatalogTitle() {
		return $this->text()->exists() ? $this->text()->val('name') : app()->metadata()->getHeader();
	}

	/**
	  @deprecated
	  delete
	 */
	private function checkOldFiltersAndRedirect() {
		$filters = app()->request()->processGetParams([
			'fm' => ['type' => 'string'],
			's' => ['type' => 'string'],
            
			'brand' => ['type' => 'string'],
			'discount' => ['type' => 'int'],
			'prices' => ['type' => 'string'],
			'price_type' => ['type' => 'string'],
			'with-price' => ['type' => 'int'],
            
			'display_mode' => ['type' => 'string'],
            
			'sorting' => ['type' => 'string'],
            
			'mode' => ['type' => 'string'],
            
			'page' => ['type' => 'string'],
		]);

		$url = parse_url(app()->uri())['path'];

		if ($filters['fm']) {
			app()->response()->redirect(app()->link($url), 301);
		} elseif ($filters['s']) {
			app()->response()->redirect(app()->link(app()->linkFilter($url, ['price_type' => 'wholesale'])), 301);
		} elseif (isset($filters['mode']) && strlen($filters['mode']) < 1) {
			app()->response()->redirect(app()->link(app()->linkFilter($url, $filters, ['mode' => false])), 301);
		}
	}

	public function catalogRedirect($args) {
		$args = array_filter($args);
		if (count($args) === 2) {
			return;
		}

		if ((count($args) > 2 && (isset($args[2]) && is_numeric($args[2])))) {
			$id_group = (int)$args[0];
			$id_subgroup = (int)$args[1];
			$id_catalog = (int)$args[2];

			$pc = new PriceCatalog($id_catalog);
			if ($pc->exists() && ($id_group !== 0 && $id_subgroup !== 0) && $pc->id_group() == $id_group && $pc->id_subgroup() == $id_subgroup) {
				if ($pc->node_level() > 2) {
					return;
				} else {
					app()->response()->redirect(app()->link($pc->link()), 301);
				}
			}
		}

		$pc = new PriceCatalog();
		if (count($args) > 2) {
			$id_group = (int)$args[0];
			$id_subgroup = (int)$args[1];

			if ($id_group !== 0 && $id_subgroup !== 0) {
				unset($args[0]);
				unset($args[1]);

				$words = array_values(array_reverse($args));
				foreach ($words as $k => $v) {
					$words[$k] = trim(urldecode($v));
				}

				foreach ($words as $word) {
					$pc_where = [
						'AND',
						['OR', 'web_many_name LIKE :web_many_name', 'web_name LIKE :web_many_name', 'name LIKE :web_many_name'],
						['AND', 'id_group = :id_group', 'id_subgroup = :id_subgroup']
					];
					$pc_params = [':id_group' => $id_group, ':id_subgroup' => $id_subgroup, ':web_many_name' => $word];
					$pc->reader()
							->setWhere($pc_where, $pc_params)
							->objectByConds();
					if ($pc->exists()) {
						break;
					}
				}

				if (!$pc->exists()) {
					$pc->getSubGroup($id_group, $id_subgroup);
				}
			}
		}

		if ($pc->exists()) {
			app()->response()->redirect(app()->link($pc->link()), 301);
		}
	}

	public function getAnalogCatalogs(PriceCatalog $catalog) {
		$result = [];
		if ($catalog->exists()) {
			$where = ['AND', 'id_subgroup != :id_subgroup', 'name LIKE :search', 'node_level > :node_level'];
			$params = [':id_subgroup' => $catalog->id_subgroup(), ':search' => $catalog->val('name'), ':node_level' => 2];

			$catalog_ids = $this->model()->reader()
					->setSelect(['id'])
					->setWhere($where, $params)
					->rowsWithKey('id');

			if ($catalog_ids) {
				$pcp = new \App\Model\PriceCatalogPrice();
				$_result = $pcp->getCatalogsByIds(array_keys($catalog_ids));
				$result = [];
				$i = 0;
				foreach ($_result as $cat) {
					$i++;
					if ($i === 10) break;
					$result[] = [
						'catalog' => $cat
					];
				}
			}
		}

		return $result;
	}

	public static function getCurrentSorting($filters) {
		$sorting = 'default';

		if (isset($filters['sorting'])) {
			if ($filters['sorting']) {
				app()->cookie()->setExpireDay()->set('price_catalog_sorting', $filters['sorting']);
				$sorting = $filters['sorting'];
			} else {
				$sorting = app()->cookie()->get('price_catalog_sorting');
				if (!$sorting) {
					$sorting = 'default';
				}
			}
		}

		return $sorting;
	}

	protected function getTagsByCatalog(PriceCatalog $catalog, $next_level_only = false) {
		$result = [];
		$matrix = [];
		$catalogs = [];

		$path = $catalog->getPathString();
		$firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');
		$price_catalog_price = new \App\Model\PriceCatalogPrice();
		
		$rows = $price_catalog_price->reader()->setSelect(['COUNT(id_price) as count', 'REPLACE(path,"'.$path.'","") as path'])
				->setWhere(['AND', $firm_conds['where'], 'path LIKE :path', 'node_level >= :node_level'], [':path' => $path.'%'] + $firm_conds['params'] + [':node_level' => (int)$catalog->val('node_level') + 1])
				->setGroupBy('path')
				->rows();

		foreach ($rows as $row) {
			preg_match_all('~\[([0-9]+)\]~', $row['path'], $matches);
			if (isset($matches[1][0])) {
				if (!isset($matrix[$matches[1][0]])) {
					$matrix[$matches[1][0]] = 0;
				}
				$matrix[$matches[1][0]] += $row['count'];
			}
		}

		if ($matrix) {
			$catalogs = $catalog->reader()->setOrderBy('web_name ASC')->objectsByIds(array_keys($matrix));
			foreach ($catalogs as $id_catalog => $_catalog) {
                if ($next_level_only && $_catalog->val('node_level') != (int)$catalog->val('node_level') + 1) {
                    continue;
                }
				if (isset($matrix[$id_catalog])) {
					$result[$id_catalog] = [
						'catalog' => $catalogs[$id_catalog],
						'count' => $matrix[$id_catalog]
					];
				}
			}
			/* $catalogs = $catalog->reader()->objectsByIds(array_keys($matrix));
			  arsort($matrix);
			  foreach ($matrix as $id_catalog => $count) {
			  if (isset($catalogs[$id_catalog])) {
			  $result[$id_catalog] = [
			  'catalog' => $catalogs[$id_catalog],
			  'count' => $count
			  ];
			  }
			  } */
		}

		return $result;
	}

	protected function redirect($args, $return_url = null) {
		$id_subgroup = $args[1];
		$last = urldecode(end($args));

		$catalog = new PriceCatalog();
		$catalog->reader()
				->setWhere([
					'AND',
					'`id_subgroup` = :id_subgroup',
					'`web_many_name` = :string'
						], [
					':id_subgroup' => (int)$id_subgroup,
					':string' => $last
				])
				->setOrderBy('`node_level` ASC')
				->objectByConds();
		if ($catalog->exists()) {
			if ($return_url === true) {
				return $catalog->link();
			} else {
				app()->response()->redirect(app()->link($catalog->link()), 301);
			}
		}
	}

	protected function setCanonicalLink($link, $filters) {
		foreach ($filters as $k => $v) {
			if (($v !== null && ($k === 'sorting' || $k === 'discount' || $k === 'price_type' || $k === 'with-price' || $k === 'display_mode' || $k === 'prices')) || ($k === 'mode' && $v === 'map')) {
				app()->metadata()
						->setCanonicalUrl(app()->link(app()->linkFilter($link, ['mode' => ($filters['mode'] && $filters['mode'] !== 'map') ? $filters['mode'] : null])))
						->noIndex(true);
			}
		}
	}

	//

	public function __construct() {
		parent::__construct();
		$this->setModel(new PriceCatalog());
		$this->view()->setSubdirName('catalog');
	}

}
