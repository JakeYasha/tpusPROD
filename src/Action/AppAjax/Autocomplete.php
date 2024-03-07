<?php

namespace App\Action\AppAjax;

use App\Action\AppAjax;
use Foolz\SphinxQL\SphinxQL;
use const APP_DIR_PATH;
use const SPHINX_FIRM_CATALOG_INDEX;
use const SPHINX_FIRM_INDEX;
use function app;

require_once APP_DIR_PATH.'/protected/langcorrect/CLangCorrect.php';

class Autocomplete extends AppAjax {

	public function execute() {
		$result = [];

		$params = app()->request()->processGetParams([
			'fieldName' => ['type' => 'string'],
			'modelAlias' => ['type' => 'string'],
			'container' => ['type' => 'string'],
			'q' => ['type' => 'string'],
			'relFields' => ['type' => 'string'],
			'location' => ['type' => 'int'],
		]);

		$query = \App\Classes\Search::clearQuery($params['q']);

		switch ($params['modelAlias']) {
			case 'search' :
				app()->location()->set($params['location']);
				$action = new Autocomplete\SuggestSearch();
				return $action->execute($query);
				break;
			case 'price-search' : $action = new Autocomplete\PriceSearch();
				return $action->execute($query, (int)$params['fieldName']);
				break;
			case 'price-user-search' : $action = new Autocomplete\PriceUserSearch();
				return $action->execute($query, (int)$params['fieldName']);
				break;
			case 'price-catalog' :
				if ($params['fieldName'] === 'web_name') {
					$action = new Autocomplete\YmlCatalogSearch();
					return $action->execute($query);
				} elseif ($params['fieldName'] === 'id') {
					//@TODO!
					$action = new Autocomplete\PriceCatalogPriceAdd();
					return $action->execute($query);
				} elseif ($params['fieldName'] === 'web_many_name_for_subgroups') {
					$action = new Autocomplete\PriceCatalogSubgroups();
					return $action->execute($query);
				} elseif ($params['fieldName'] === 'web_many_name_for_catalogs') {
					$action = new Autocomplete\PriceCatalogCatalogs();
					return $action->execute($query);
				} else {
					$action = new Autocomplete\PriceCatalog();
					return $action->execute($query);
				}

				break;
			case 'firm-type' : $action = new Autocomplete\FirmType();
				return $action->execute($query);
				break;
		}

		$action = new Autocomplete\DefaultSearch();
		return $action->execute($query, $params);
	}

	protected function ymlCatalogSearch($query, $limit = 20) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$result = $sphinx
				->select('*', SphinxQL::expr('WEIGHT() as `weight`'))
				->limit(0, $limit)
				->from([SPHINX_PRICE_CATALOG_INDEX])
				->where('flag_is_catalog', '=', 1)
				->match('(subgroup_name)', SphinxQL::expr($query))
				->orderBy('weight', 'DESC')
				->orderBy('node_level', 'ASC')
				->option('ranker', 'sph04')
				->groupBy('id_parent')
				->enqueue()
				->select('*', SphinxQL::expr('WEIGHT() as `weight`'))
				->limit(0, $limit)
				->from([SPHINX_PRICE_CATALOG_INDEX])
				->where('flag_is_catalog', '=', 1)
				->match('(name,web_name,web_many_name)', SphinxQL::expr($query))
				->orderBy('weight', 'DESC')
				->orderBy('node_level', 'ASC')
				->option('ranker', 'sph04')
				->groupBy('id_catalog')
				->executeBatch();

		return $result;
	}

	protected function suggestSearch($query, $limit = 10) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$search_limit = 50;

		$result = $sphinx
				->select('*', SphinxQL::expr('WEIGHT() as `weight`'))
				->limit(0, $search_limit)
				->from([SPHINX_PRICE_CATALOG_INDEX])
				->where('node_level', '=', 2)
				->match('(subgroup_name)', SphinxQL::expr($query))
				->orderBy('weight', 'DESC')
				->orderBy('node_level', 'ASC')
				->option('ranker', 'sph04')
				//->groupBy('id_parent')
				->enqueue()
				->select('*', SphinxQL::expr('WEIGHT() as `weight`'))
				->limit(0, $search_limit)
				->from([SPHINX_PRICE_CATALOG_INDEX])
				->match('(name,web_name,web_many_name)', SphinxQL::expr($query))
				->where('node_level', '!=', 2)
				->orderBy('weight', 'DESC')
				->orderBy('node_level', 'ASC')
				->option('ranker', 'sph04')
				->groupBy('id_catalog')
				->enqueue()
				->select('*', SphinxQL::expr('WEIGHT() as weight'))
				->limit(0, $limit)
				->from([SPHINX_FIRM_INDEX])
				->where('id', 'IN', app()->location()->getFirmIds())
				->match('(company_name,company_activity,company_address)', SphinxQL::expr($query))
				->orderBy('weight', 'DESC')
				->orderBy('rating', 'DESC')
				->option('field_weights', ['company_name' => 10, 'company_activity' => 1])
				->option('ranker', 'sph04')
				->enqueue()
				->select('*', SphinxQL::expr('WEIGHT() as weight'))
				->limit(0, $limit)
				->from([SPHINX_FIRM_CATALOG_INDEX])
				->where('node_level', '<', (int)3)
				->match('(name)', SphinxQL::expr($query))
				->orderBy('weight', 'DESC')
				->option('ranker', 'sph04')
				->executeBatch();
        
		$this->actualizeCatalogs($result, $limit);

		return $result;
	}

	private function actualizeCatalogs(&$result, $limit) {
		$catalog_ids = [];
		$actual_ids = [];
		
		foreach ($result[0] as $res) {
			$catalog_ids[$res['id_catalog']] = 1;
		}
		foreach ($result[1] as $res) {
			$catalog_ids[$res['id_catalog']] = 1;
		}
        
		if ($catalog_ids) {
			$actual_ids = (new \App\Model\PriceCatalogPrice())->getActualCatalogIds(array_keys($catalog_ids), 10);
		}
        
		foreach ($result[0] as $k => $res) {
			if (!in_array($res['id_catalog'], $actual_ids)) {
				unset($result[0][$k]);
			}
		}
		foreach ($result[1] as $k => $res) {
			if (!in_array($res['id_catalog'], $actual_ids)) {
				unset($result[1][$k]);
			}
		}

		if (count($result[0]) > $limit) {
			$result[0] = array_slice($result[0], 0, $limit, true);
		}
		if (count($result[1]) > $limit) {
			$result[1] = array_slice($result[1], 0, $limit, true);
		}

		return $this;
	}

}
