<?php

namespace App\Action\FirmUser\Ajax;

use App\Action\FirmUser\Ajax;
use App\Model\Image;
use App\Model\Price;
use function app;

class SetPriceImage extends Ajax {

	public function execute() {
		$get = app()->request()->processGetParams([
			'id_image' => ['type' => 'int'],
			'id_price' => ['type' => 'int'],
		]);

		$result = ['success' => false];
		$image = new Image($get['id_image']);

		if ($image->exists() && $image->id_firm() === app()->firmUser()->id_firm() && $image->id_service() === app()->firmUser()->id_service() && $image->val('id') !== $get['id_price']) {
			$vals = $image->getVals();
			unset($vals['id']);
			unset($vals['timestamp_inserting']);
			unset($vals['timestamp_last_updating']);

			$vals['id_price'] = $get['id_price'];
			$vals['source'] = 'client';

			$name_hash = '';
			$_price = (new Price($get['id_price']));
			if ($_price->exists()) {
				$name_hash = md5($_price->name());
			}
			$vals['name_hash'] = $name_hash;

			// Необходимо найти старый image, привязанный к этому товару и обновить его, если такового нет - то просто вставляем новый
			if (true) {
				$old_image = new Image();
				$where_old = ['AND', '`id_firm` = :id_firm', 'source != :source', 'id_price = :id_price'];
				$params_old = [':id_firm' => app()->firmUser()->id_firm(), ':source' => 'ratiss', ':id_price' => $get['id_price']];
				$old_items = $old_image->reader()
						->setWhere($where_old, $params_old)
						->objects();

				foreach ($old_items as $item) {
					$item->delete();
				}
			}

			if ($image->insert($vals)) {
				$image->embededFileComponent()->setSubDirName('image');

				$images_list = '';
				if (true) {
					$images_list = $this->renderPriceImagesList($get['id_price']);
				}

				$result = [
					'success' => true,
					'thumb_path' => $image->embededFileComponent()->iconLink(),
					'image_id' => $image->id(),
					'images_list' => $images_list,
					'id_price' => $image->val('id_price'),
					'name_hash' => $name_hash
				];
				
				$_price->update(['flag_is_image_exists' => 1]);
			}
		}

		die(json_encode($result));
	}

}
