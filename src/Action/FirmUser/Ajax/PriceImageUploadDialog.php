<?php

namespace App\Action\FirmUser\Ajax;

use App\Action\FirmUser\Ajax;
use App\Model\Image;
use App\Model\Price;
use function app;

class PriceImageUploadDialog extends Ajax {

	public function execute($id_price) {
		$sts_price = new Price($id_price);
		$item = $sts_price->prepare();

		$fi = new Image();
		$images = $fi->reader()
				->setWhere(['AND', 'id_firm = :id_firm', 'id_price != :nil'], [':id_firm' => $sts_price->firm()->id(), ':nil' => 0])
				->setGroupBy('file_name')
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		if ($sts_price->exists() && (int) $sts_price->firm()->id() === (int) app()->firmUser()->id_firm() && (int) $sts_price->firm()->id_service() === (int) app()->firmUser()->id_service()) {
			die($this->view()
							->set('item', $item)
							->set('id_firm', app()->firmUser()->id_firm())
							->set('id_price', (int) $sts_price->id())
							->set('images', $images)
							->setTemplate('sts_price_image_form', 'forms')
							->render());
		}
	}

}
