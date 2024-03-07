<?php

namespace App\Action\AppAjax\Autocomplete;

use App\Action\AppAjax\Autocomplete;
use App\Model\Firm;
use App\Model\PriceCatalog;
use Foolz\SphinxQL\SphinxQL;
use Sky4\Model\Utils;
use function app;

class PriceSearch extends Autocomplete {

	public function execute($query, $id_firm) {
		$result = [];
		$limit = 20;

		$firm = new Firm($id_firm);
		if ($firm->exists()) {
			$searched = SphinxQL::create(app()->getSphinxConnection()) //@todo
					->select('*', SphinxQL::expr('WEIGHT() as weight'))
					->from([SPHINX_PRICE_CATALOG_INDEX]) //0
//					->where('id_firm', '=', (int) $firm->id_firm())
//					->where('id_service', '=', (int) $firm->id_service())
					->match('*', SphinxQL::expr('^' . $query))
					->limit(0, $limit)
					->orderBy('weight', 'DESC')
//->orderBy('count', 'DESC')
					->option('ranker', 'sph04')
					->execute();

			$catalog_ids = [];
			if (!isset($searched)) $searched = [];
			foreach ($searched as $row) {
				$catalog_ids[] = $row['id_catalog'];
			}

			$result = [];

			if ($catalog_ids) {
				$cat = new PriceCatalog();
				$conds = Utils::prepareWhereCondsFromArray($catalog_ids, 'id');
				$items = $cat->reader()->setWhere($conds['where'], $conds['params'])->objects();

				foreach ($searched as $pcc) {
					if (isset($items[$pcc['id_catalog']])) {
						$ob = $items[$pcc['id_catalog']];
						$result[] = [
							'id' => $ob->id(),
							'label' => $ob->name(),
							'href' => '/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/?id_catalog=' . $ob->id() . '&mode=price',
							'class' => 'catalog',
							'sub_label' => ''
						];
					}
				}
			}

			die(json_encode($result));
		}
	}

}
