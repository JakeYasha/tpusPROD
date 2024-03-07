<?php

namespace App\Action\FirmUser\Ajax\Remove;

use App\Model\Image;
use function app;

class FirmImage extends \App\Action\FirmUser\Ajax\Remove {

	public function execute($id) {
		$file = new Image();
		$file->get($id);

		$success = false;
		if ($file->exists() && $file->id_firm() === app()->firmUser()->id_firm() && $file->id_service() === app()->firmUser()->id_service()) {
			$file->delete();
			$success = true;
		}

		die(json_encode(['success' => $success]));
	}

}
