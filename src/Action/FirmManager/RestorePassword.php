<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Firm;
use App\Model\FirmUser;
use Sky4\Helper\DeprecatedDateTime;
use function app;

class RestorePassword extends FirmManager {

	public function execute() {
		$this->params = app()->request()->processGetParams([
			'id_firm_user' => ['type' => 'int'],
			'id_firm' => ['type' => 'int'],
			'id_service' => ['type' => 'int'],
			'new_email' => ['type' => 'string']
		]);
//        if (APP_IS_DEV_MODE){
//            ini_set('error_reporting', E_ALL);
//            ini_set('display_errors', 1);
//            ini_set('display_startup_errors', 1);
//        }
		$firm_user = new FirmUser($this->params['id_firm_user']);
		if ($firm_user->exists()) {
			if ($this->params['new_email']) {
				$firm_user->update(['email' => trim($this->params['new_email'])]);
			}
			$new_password = $firm_user->userComponent()->genPassword();
			if ($firm_user->userComponent()->saveNewPasswordWithoutChecking($new_password)) {
                
                $id_firm = $this->params['id_firm'];
                $id_service = isset($this->params['id_service']) && $this->params['id_service'] ? $this->params['id_service'] : null;

                $firm = new Firm();
                if ($id_service === null) {
                    $firm->getByIdFirm((int) $id_firm);
                } else {
                    $firm->getByIdFirmAndIdService($id_firm, $id_service);
                }

				$template = ($firm_user->val('last_activity_timestamp') === DeprecatedDateTime::nil()) ? 'email_first_restore_password' : 'email_restore_password';
				$subject = ($firm_user->val('last_activity_timestamp') === DeprecatedDateTime::nil()) ? 'TovaryPlus.ru: доступ в личный кабинет' : 'TovaryPlus.ru: восстановление пароля в личный кабинет';
				app()->email()
						->setSubject($subject)
						->setTo($firm_user->val('email'))
						->setModel($firm)
						->setParams(['new_password' => $new_password, 'email' => $firm_user->val('email')])
						->setTemplate($template, 'firmuser')
						->sendToQuery();
                
				die(json_encode(['html' => 'Пароль отправлен на ' . $firm_user->val('email') . '!', 'style' => 'color:green;text-decoration:none;']));
			}
		}

		die(json_encode(['html' => 'Ошибка отправки пароля!', 'style' => 'color:red;text-decoration:none;']));
	}

}
