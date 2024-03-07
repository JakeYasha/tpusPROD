<?php

namespace App\Action\Page;

use App\Action\Page;
use App\Model\Firm;
use App\Model\StatObject;

class Away extends Page {

	public function execute() {
		$params = app()->request()->processGetParams([
			'url' => ['type' => 'string'],
			'id_firm' => ['type' => 'int'],
			'mode' => ['type' => 'string']
		]);

		if ($params['id_firm'] !== null) {
			$firm = new Firm();
			$firm->reader()->object($params['id_firm']);
			if ($firm->exists()) {
				if ($params['mode'] === 'yml') {
					app()->stat()->addObject(StatObject::YML_URL_CLICK, $firm)
							->fixResponse(false);
				} else {
					app()->stat()->addObject(StatObject::FIRM_SHOW_URL_CLICK, $firm)
							->fixResponse(false);
				}
			}
		}

		$url = $params['url'];
		if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
			$url = 'http://' . $url;
		}

		app()->response()->redirect($url, 301);
	}

}
