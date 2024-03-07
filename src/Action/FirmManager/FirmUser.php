<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Firm;
use App\Model\FirmUser\ManagerForm;
use App\Model\NewFirmUser\ManagerForm as NewFirmUserManagerForm;
use App\Model\FirmUser as FirmUserModel;
use Sky4\Exception;
use function app;

class FirmUser extends FirmManager {

	public function execute() {
		$this->params = app()->request()->processGetParams([
			'mode' => ['type' => 'string'],
			'id_firm' => ['type' => 'int']
		]);
		
		$firm = new Firm($this->params['id_firm']);
		
		$firm_user = new FirmUserModel();
		$firm_user->getByIdFirm($firm->id());

		if ($firm_user->exists()) {
			$form = new ManagerForm($firm_user);
			die($form->render(['id_firm' => $this->params['id_firm']], 'Данные пользователя'));
		} else {
			$email = '';
			if ($firm->exists() && $firm->hasEmail()) {
				$email = $firm->firstEmail();
			}

			$form = new NewFirmUserManagerForm();
			die($form->render(['id_firm' => $firm->id(), 'email' => $email], 'Создание пользователя'));
		}

		throw new Exception();
	}

}
