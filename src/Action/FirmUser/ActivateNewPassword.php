<?php

namespace App\Action\FirmUser;

use App\Action\FirmUser;
use CException;
use CUtils;
use function app;

class ActivateNewPassword extends FirmUser {

	public function execute() {
		$vals = app()->request()->processGetParams([
			'control_number' => ['type' => 'string'],
			'id' => ['type' => 'int'],
			'model_alias' => ['type' => 'string']
		]);

		$firm_user = \Sky4\Utils::getModelClass(isset($vals['model_alias']) ? $vals['model_alias'] : 'firm-user');
		$firm_user->get($vals['id']);
		if ($firm_user->exists()) {
			if ($firm_user->userComponent()->confirmPasswordChanging($vals['control_number'])) {
				app()->response()->redirect('/#login');
			} else {
				throw new CException(CException::TYPE_BAD_URL);
			}
		}
	}

}
