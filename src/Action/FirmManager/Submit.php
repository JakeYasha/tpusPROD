<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Firm;
use App\Model\NewFirmUser\ManagerForm as NewFirmUserManagerForm;
use App\Model\FirmUser;
use Sky4\Utils;
use function app;

class Submit extends FirmManager {

	public function execute($form_alias) {
		$this->params = app()->request()->processGetParams([
			'redirect' => ['type' => 'string'],
			'name' => ['type' => 'string'],
			'id_firm' => ['type' => 'int'],
			'id_service' => ['type' => 'int'],
			'email' => ['type' => 'string'],
		]);

		$redirect = $this->params['redirect'];

		$form = Utils::getFormClass($form_alias, 'Manager');
		$form->setInputVals($_POST);
		$errors = $form->errorHandler()->getErrors();

		if (!$errors) {
			$form->model()->setVals($form->getVals());
			if ($form->model()->exists()) {
				$form->model()->update($form->getVals());
			} else {
				$form->model()->insert($form->getVals());
				if ($form instanceof NewFirmUserManagerForm) {
					$firm_user = new FirmUser();
					$firm_user->getByIdFirm($form->getVal('id_firm'));
					$new_password = $firm_user->userComponent()->genPassword();

					if ($firm_user->userComponent()->saveNewPasswordWithoutChecking($new_password)) {
						$firm = new Firm();
						$firm->getByIdFirm($form->getVal('id_firm'));

						if ($firm->exists()) {
							app()->email()
									->setSubject('TovaryPlus.ru: доступ в личный кабинет')
									->setTo($firm_user->val('email'))
									->setModel($firm)
									->setParams(['new_password' => $new_password, 'email' => $firm_user->val('email')])
									->setTemplate('email_first_restore_password', 'firmuser')
									->sendToQuery();
						}
					}
				}
			}
		}

		die(json_encode([
			'result' => 1,
			'ok' => true,
			'error_message' => $form->model()->userComponent()->errorHandler()->getLastErrorMessage(),
			'error_code' => $form->model()->userComponent()->errorHandler()->getLastErrorCode(),
			'redirect' => $redirect
		]));
	}

}
