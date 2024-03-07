<?php

namespace App\Action\AppAjax\Autocomplete;

use App\Action\AppAjax\Autocomplete;
use Foolz\SphinxQL\SphinxQL;
use const SPHINX_FIRM_CATALOG_INDEX;
use const SPHINX_MAX_INT;
use function app;
use function str;

class FirmType extends Autocomplete {

	public function execute($query) {
		$result = [];
		$catalog_ids = [];
		$limit = 20;

		$searched = SphinxQL::create(app()->getSphinxConnection())
				->select('id', 'parent_node', 'node_level', SphinxQL::expr('WEIGHT() as weight'))
				->limit(0, $limit)
				->from([SPHINX_FIRM_CATALOG_INDEX])
				->match('name', SphinxQL::expr($query))
				->where('node_level', '=', 2)
				->orderBy('weight', 'DESC')
				->option('max_matches', SPHINX_MAX_INT)
				->option('ranker', 'sph04')
				->execute();

		foreach ($searched as $row) {
			$catalog_ids[] = $row['id'];
		}

		$ft = new \App\Model\FirmType();
		$catalogs = $ft->reader()->objectsByIds(array_filter($catalog_ids));

		foreach ($catalogs as $catalog) {
			$result[] = array(
				'id' => $catalog->id(),
				'label' => str()->firstCharToUpper(str()->toLower($catalog->name())),
				'name' => str()->firstCharToUpper(str()->toLower($catalog->name()))
			);
		}

		die(json_encode($result));
	}

}
