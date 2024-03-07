<?php

namespace App\Action\Page\Away;

class Firm extends \App\Action\Page\Away {

	public function execute($id_firm) {
		$firm = new \App\Model\Firm($id_firm);
		if ($firm->exists()) {
			app()->stat()->addObject(\App\Model\StatObject::FIRM_SHOW_URL_CLICK, $firm)
					->fixResponse(false);
		}

		$url = $firm->hasWebPartner() ? $firm->val('web_site_partner_url') : $firm->companyDataComponent()->val('web_site_url');
		if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
			$url = 'http://'.$url;
		}

		app()->response()->redirect($url, 301);
	}

}
