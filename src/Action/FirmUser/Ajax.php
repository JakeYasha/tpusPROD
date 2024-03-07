<?php

namespace App\Action\FirmUser;

use App\Model\Image;
use Sky4\Exception;
use function app;

class Ajax extends \App\Action\FirmUser {

	use \App\Classes\Traits\Ajax;

	public function execute() {
		throw new Exception();
	}

	public function renderPriceImagesList($id_price) {
		$result = '';

		$image = new Image();
		$images = $image->reader()
				->setWhere(['AND', 'id_firm = :id_firm', 'id_price != :nil'], [':id_firm' => app()->firmUser()->id_firm(), ':nil' => 0])
				->setGroupBy('file_name')
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		if (count($images) > 0) {
			$result = $this->view()
					->set('id_price', $id_price)
					->set('images', $images)
					->setTemplate('sts_price_images_list', 'forms')
					->render();
		}

		return $result;
	}

}
