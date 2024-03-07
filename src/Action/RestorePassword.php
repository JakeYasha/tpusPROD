<?php

namespace App\Action;

use App\Action\FirmUser as FirmUserAction;
use App\Model\Firm;
use App\Model\FirmManager;
use App\Model\FirmUser;
use Sky4\Helper\DeprecatedDateTime;
use function app;

class RestorePassword extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new FirmUser());
	}

	public function execute() {
		$this->params = app()->request()->processPostParams([
			'email' => ['type' => 'string']
		]);
		$result = 0;

		$firm_user = new FirmUser();
		$firm_user->userComponent()->findByEmail($this->params['email']);
		if ($firm_user->exists()) {
			$result = 1;
			$new_password = $firm_user->userComponent()->genPassword();
			if ($firm_user->userComponent()->saveNewPasswordWithChecking($new_password)) {
				$firm = new Firm();
				$firm->getByIdFirm($firm_user->id_firm());

				$template = ($firm_user->val('last_activity_timestamp') === DeprecatedDateTime::nil()) ? 'email_first_restore_password_with_checking' : 'email_restore_password_with_checking';

				app()->email()
						->setSubject('TovaryPlus.ru: активация нового пароля')
						->setTo($firm_user->val('email'))
						->setModel($firm)
						->setParams(['new_password' => $new_password, 'email' => $firm_user->val('email'), 'model_alias' => 'firm-user', 'id' => $firm_user->id(), 'control_number' => $firm_user->userComponent()->getControlNumber()])
						->setTemplate($template, 'firmuser')
						->sendToQuery();
			}
		} else {
			$firm_manager = new FirmManager();
			$firm_manager->userComponent()->findByEmail($this->params['email']);
			if ($firm_manager->exists()) {
				$result = 2;
				$new_password = $firm_user->userComponent()->genPassword();
				if ($firm_manager->userComponent()->saveNewPasswordWithChecking($new_password)) {
					app()->email()
							->setSubject('TovaryPlus.ru: активация нового пароля')
							->setTo($firm_user->val('email'))
							->setModel($firm_manager)
							->setParams(['new_password' => $new_password, 'email' => $firm_manager->val('email'), 'model_alias' => 'firm-manager', 'id' => $firm_manager->id(), 'control_number' => $firm_manager->userComponent()->getControlNumber()])
							->setTemplate('email_restore_password_with_checking', 'firmmanager')
							->sendToQuery();
				}
			}
		}

		if ($result !== 0) {
			if ($result === 1) {
				die(json_encode([
					'result' => 1,
					'ok' => true,
					'error_message' => $this->model()->userComponent()->errorHandler()->getLastErrorMessage(),
					'error_code' => $this->model()->userComponent()->errorHandler()->getLastErrorCode(),
					'html' => app()->chunk()->set('email', $firm_user->val('email'))->render('firmuser.email_restore_success_send')
				]));
			} elseif ($result === 2) {
				die(json_encode([
					'result' => 1,
					'ok' => true,
					'error_message' => $this->model()->userComponent()->errorHandler()->getLastErrorMessage(),
					'error_code' => $this->model()->userComponent()->errorHandler()->getLastErrorCode(),
					'html' => app()->chunk()->set('email', $firm_manager->val('email'))->render('firmuser.email_restore_success_send')
				]));
			}
		}

		$this->model()->userComponent()->errorHandler()->setError('Пользователь не найден');
		die(json_encode([
			'error_message' => $this->model()->userComponent()->errorHandler()->getLastErrorMessage(),
			'error_code' => $this->model()->userComponent()->errorHandler()->getLastErrorCode(),
		]));
	}

	/**
	 * 
	 * @return FirmUser
	 */
	public function model() {
		return parent::model();
	}

}
