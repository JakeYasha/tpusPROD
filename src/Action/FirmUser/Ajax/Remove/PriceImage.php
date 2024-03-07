<?php

namespace App\Action\FirmUser\Ajax\Remove;

class PriceImage extends \App\Action\FirmUser\Ajax\Remove {

	public function execute($id) {
		$file = new \App\Model\Image();
		$file->get($id);

		$success = false;
		if ($file->exists() && $file->id_firm() === app()->firmUser()->id_firm() && $file->id_service() === app()->firmUser()->id_service()) {
			$file->delete();
			$success = true;
		}

		die(json_encode(['success' => $success]));
	}

}
