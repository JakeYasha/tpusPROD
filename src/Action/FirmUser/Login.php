<?php

namespace App\Action\FirmUser;

use App\Classes\Action;
use App\Model\FirmManager;
use function app;

class Login extends Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\FirmUser());
	}

	/**
	 * 
	 * @return \App\Model\FirmUser
	 */
	public function model() {
		return parent::model();
	}

	public function execute() {
		$params = app()->request()->processPostParams([
			'email' => ['type' => 'string'],
			'password' => ['type' => 'string'],
		]);

		if ($params['email'] === null || $params['password'] === null) {
			app()->response()->redirect('/#login');
		}

		$result = 0;
		$firm_manager = new FirmManager();
        
		if ($this->model()->userComponent()->findByEmail($params['email']) && $this->checkByEmailWithActiveFirm($this->model()) && $this->model()->userComponent()->login($params['password'])) {
			$this->model()->userComponent()->saveInSession();
			$result = 1;
		} elseif ($firm_manager->userComponent()->findByEmail($params['email']) && $firm_manager->userComponent()->login($params['password'])) {
			$firm_manager->userComponent()->saveInSession();
			$result = 2;
		}
		
		if ($result !== 0) {
			if ($result === 1) {
				die(json_encode([
					'result' => 1,
					'ok' => true,
					'error_message' => $this->model()->userComponent()->errorHandler()->getLastErrorMessage(),
					'error_code' => $this->model()->userComponent()->errorHandler()->getLastErrorCode(),
					'redirect' => '/firm-user/'
				]));
			} elseif ($result === 2) {
				die(json_encode([
					'result' => 1,
					'ok' => true,
					'error_message' => $this->model()->userComponent()->errorHandler()->getLastErrorMessage(),
					'error_code' => $this->model()->userComponent()->errorHandler()->getLastErrorCode(),
					'redirect' => '/firm-manager/'
				]));
			}
		} else {
			die(json_encode([
				'error_message' => $this->model()->userComponent()->errorHandler()->getLastErrorMessage(),
				'error_code' => $this->model()->userComponent()->errorHandler()->getLastErrorCode(),
				'redirect' => '/#login'
			]));
		}
	}

	private function checkByEmailWithActiveFirm(\App\Model\FirmUser $user) {
		$users_data = [];
		$users_data = $user->reader()
				->setSelect(['id', 'id_firm'])
				->setWhere(['AND', '`email` = :email'], [':email' => $user->val('email')])
				->rowsWithKey('id');
		
		/*print_r($users_data);
		exit();*/


		$last_error_num = 0;
		if (!$users_data) {
			$last_error_num = 1;
		} else {
			foreach ($users_data as $user_id => $firm_data) {
				$firm = new \App\Model\Firm();
				$firm->getByIdFirm($firm_data['id_firm']);

				if ((int) $firm->val('flag_is_active') === 1) {
					$user->reader()
							->setWhere(['AND', '`id` = :id'], [':id' => $user_id])
							->setOrderBy('`id` ASC')
							->objectByConds();
					return $user->exists();
				} else {
					$last_error_num = 3;
				}
			}
		}

		switch ($last_error_num) {
			case 2:
				$this->model()->userComponent()->errorHandler()->setError('Пользователь отсутствует', 2);
				break;
			case 3:
				$this->model()->userComponent()->errorHandler()->setError('Пользователь заблокирован', 3);
				break;
			default:
				$this->model()->userComponent()->errorHandler()->setError('E-mail отсутствует', 1);
		}

		return false;
	}

}
