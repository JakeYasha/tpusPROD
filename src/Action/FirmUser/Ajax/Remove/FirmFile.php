<?php

namespace App\Action\FirmUser\Ajax\Remove;

use App\Model\FirmFile as FirmFileModel;
use function app;

class FirmFile extends \App\Action\FirmUser\Ajax\Remove {

	public function execute($id) {
		$file = new FirmFileModel();
		$file->get($id);

		$success = false;
		if ($file->exists() && $file->id_firm() === app()->firmUser()->id_firm() && $file->id_service() === app()->firmUser()->id_service()) {
			$file->delete();
			$success = true;
		}

		die(json_encode(['success' => $success]));
	}

}
