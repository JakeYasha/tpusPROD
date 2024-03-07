<?php

namespace App\Action\FirmUser\Price;

use \App\Model\Price;

class Delete extends \App\Action\FirmUser\Price {

	public function execute() {
		$params = app()->request()->processGetParams([
			'id' => ['type' => 'array']
		]);

		if (!$params['id']) {
			$params = app()->request()->processGetParams([
				'id' => ['type' => 'int']
			]);
			$params['id'] = [$params['id']];
		}

		if (is_array($params['id'])) {
			$ids = $params['id'];
			foreach ($ids as $id) {
				$price = new Price($id);
				if ($price->exists() && $price->id_firm() === $this->firm()->id()) {
					if ($price->exists()) {
						$price->delete();
						$pcp = new \App\Model\PriceCatalogPrice();
						$pcp->reader()->setWhere(['AND', 'id_price = :id_price'], [':id_price' => $price->id()])
								->objectByConds();

						if ($pcp->exists()) {
							$pcp->delete();
						}
					}
				}
			}
		}
		app()->response()->redirect('/firm-user/price/');
	}

}
