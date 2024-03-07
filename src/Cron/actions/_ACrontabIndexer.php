<?php

class ACrontabIndexer extends ACrontabAction {

	public function run() {
		$this->log('старт переиндексации вспомогательных индексов');
		App::system()->reindex(SPHINX_BANNER_INDEX);
		App::system()->reindex(SPHINX_CATALOG_SUGGEST_INDEX);
		App::system()->reindex(SPHINX_PRICE_SUGGEST_INDEX);
		//App::system()->reindex(SPHINX_PRICE_BRAND_INDEX);
		App::system()->reindex(SPHINX_FIRM_INDEX);

		return parent::run();
	}

}
