<?php

namespace App\Action\AdvertModule;

use App\Action\AdvertModule;
use App\Model\StatObject;
use Sky4\Exception;
use function app;

class Url extends AdvertModule {

	public function execute($id = 0) {
		$this->model()->reader()->object($id);
		if ($this->model()->exists()) {
			app()->stat()->addObject(StatObject::ADVERT_MODULE_CLICK, $this->model())
					->fixResponse(false);
            
            $url = $this->model()->val('url');
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = "http://" . $url;
            }
            $more_url = $this->model()->val('more_url');
            if (!preg_match("~^(?:f|ht)tps?://~i", $more_url)) {
                $more_url = "http://" . $more_url;
            }
            
			app()->response()->redirect($more_url ? $more_url : $url);
		}
		throw new Exception();
	}

}
