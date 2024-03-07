<?php

namespace App\Action\FirmUser;

use App\Model\Image;
use function app;

class RemoveLogo extends \App\Action\FirmUser {

	public function execute() {
		$image = new Image();
		$image->reader()
				->setWhere(['AND', '`id_firm` = :id_firm', 'id_price = :nil'], [':id_firm' => $this->firm()->id(), ':nil' => 0])
				->setOrderBy('timestamp_inserting DESC')
				->objectByConds();

		if ($image->exists()) {
			$this->firm()->update(['file_logo' => '']);

            $image->delete();
		}

		app()->response()->redirect('/firm-user/info/?mode=description');
	}

}
