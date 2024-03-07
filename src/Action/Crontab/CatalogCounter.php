<?php

namespace App\Action\Crontab;

class CatalogCounter extends \App\Action\Crontab {

	public function __construct($internal_call = false) {
		parent::__construct($internal_call);
	}

	public function execute() {
		$this->startAction();
		$cat = new \App\Classes\Catalog();
		$cat->setTmpMode()->fullCatalogRebuild();
		$this->endAction();
	}

}
