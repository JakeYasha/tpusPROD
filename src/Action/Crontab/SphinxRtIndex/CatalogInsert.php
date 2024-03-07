<?php

namespace App\Action\Crontab\SphinxRtIndex;

class CatalogInsert extends \App\Action\Crontab\SphinxRtIndex {

	public function execute() {
		$sphinx = new \Foolz\SphinxQL\SphinxQL(app()->getSphinxConnection());
		$sphinx->query('TRUNCATE RTINDEX `'.SPHINX_PRICE_CATALOG_INDEX.'`');

		$pc = new \App\Model\PriceCatalog();
		$items = $pc->reader()->objects();

		$i = 0;
		foreach ($items as $item) {
			$item->updateRtIndex($sphinx);
			echo ++$i."\r";
		}

		exit();
	}

}
