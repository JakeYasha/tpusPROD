<?php

namespace App\Action\Price;

class Away extends \App\Action\Price {

	public function execute($id_price) {
		$price = new \App\Model\Price($id_price);
		app()->stat()->addObject(\App\Model\StatObject::YML_URL_CLICK, $price->firm())
				->fixResponse(false);
		
		$url = $price->val('url');
		if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
			$url = 'http://'.$url;
		}

		app()->response()->redirect($url, 301);
	}

}
