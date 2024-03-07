<?php

namespace App\Action\FirmUser;

use App\Model\Image;
use function app;

class RestoreDefaultLogo extends \App\Action\FirmUser {

	public function execute() {
		$image = new Image();
		$image->reader()
				->setWhere(['AND', '`id_firm` = :id_firm', 'id_price = :nil', 'source = :source'], [':id_firm' => $this->firm()->id(), ':source' => 'ratiss', ':nil' => 0])
				->setOrderBy('timestamp_inserting DESC')
				->objectByConds();

		if ($image->exists()) {
			$this->firm()->update(['file_logo' => $image->path()]);

			$image = new Image();
			$where_delete = ['AND', '`id_firm` = :id_firm', 'source != :source', 'id_price = :nil'];
			$params_delete = [':id_firm' => $this->firm()->id(), ':source' => 'ratiss', ':nil' => 0];
			$delete_items = $image->reader()
					->setWhere($where_delete, $params_delete)
					->objects();

			foreach ($delete_items as $it) {
				$it->delete();
			}
		}

		app()->response()->redirect('/firm-user/info/?mode=description');
	}

}
