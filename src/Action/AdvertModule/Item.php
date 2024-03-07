<?php

namespace App\Action\AdvertModule;

use App\Action\AdvertModule;
use App\Model\StatObject;
use Sky4\Exception;
use function app;

class Item extends AdvertModule {

	public function execute($id = 0) {
		$this->model()->reader()->object($id);
		if ($this->model()->exists()) {
			app()->stat()->addObject(StatObject::ADVERT_MODULE_CLICK, $this->model())
					->fixResponse(false);
			app()->response()->redirect($this->model()->val('url'));
		}
		throw new Exception();
	}

}
