<?php

namespace App\Action\Utils;

class RecountFirm extends \App\Action\Utils {

	public function __construct() {
		parent::__construct();
		if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
			exit();
		}
	}

	public function execute() {
		$params = app()->request()->processGetParams([
			'id_firm' => ['type' => 'int'],
			'mode' => ['type' => 'string']
		]);

		$firm = new \App\Model\Firm($params['id_firm']);
		if ($firm->exists()) {
			if ($params['mode'] === 'deactivate') {
				$prices = (new \App\Model\Price())->reader()->setWhere(['AND', 'id_firm = :id_firm'], [':id_firm' => $firm->id()])
						->objects();

				foreach ($prices as $price) {
					$price->update(['flag_is_active' => 0]);
					app()->db()->query()->setText('DELETE FROM `price_catalog_price` WHERE id_price = :id_price')
							->execute([':id_price' => $price->id()]);
				}

				$firm->update(['flag_is_active' => 0]);
			} elseif ($params['mode'] === 'activate') {
				$prices = (new \App\Model\Price())->reader()->setWhere(['AND', 'id_firm = :id_firm'], [':id_firm' => $firm->id()])
						->objects();

				foreach ($prices as $price) {
					$price->update(['flag_is_active' => 1]);
				}
				$firm->update(['flag_is_active' => 1]);

				(new \App\Classes\Catalog(true))->fullCatalogRebuildForFirm($firm);
			} else {
				die('Неправильные параметры');
			}
		} else {
			die('Неправильные параметры');
		}

		die('Выполнено');
	}

}
