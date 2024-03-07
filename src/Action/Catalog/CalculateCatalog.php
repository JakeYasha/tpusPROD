<?php

namespace App\Action\Catalog;

class CalculateCatalog extends \App\Action\Catalog {

	public function execute() {
		$session = new \Sky4\Session();
		$admin = new \App\Model\Administrator();
		$admin->user()->getFromSession();
		if ($admin->exists()) {
			$id_catalog = app()->request()->getGetParam('id_catalog');
			$price_catalog = new \App\Model\PriceCatalog($id_catalog);
			if ($price_catalog->exists() && $price_catalog->val('node_level') > 2) {
				$action = new \App\Action\Crontab\CatalogCounter();
				$action->store([$price_catalog->id() => $price_catalog], true);
				echo 'готово';
				exit();
			}
		}
		throw new \Sky4\Exception();
	}

}
