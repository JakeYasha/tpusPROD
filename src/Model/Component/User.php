<?php

namespace App\Model\Component;

use Sky4\App,
	Sky4\Exception,
	Sky4\Helper\DateTime,
	Sky4\Helper\StringHelper,
	Sky4\Model,
	Sky4\Model\Component,
	Sky4\Traits\ErrorHandler;

class User extends \Sky4\Model\Component {

	use ErrorHandler;

	protected $control_number = '';
	protected $data_changing_model = null;
	protected $new_email = '';
	protected $new_password = '';
	protected $new_phone = '';
	protected $password = '';
	protected $session_var_name = '';

	public function __construct(Model $model = null) {
		parent::__construct($model);
		$this->session_var_name = '__'.preg_replace('/[-]+/', '_', $this->model()->alias()).'_id';
	}

	/**
	 * @return \App\Model\UserDataChanging
	 */
	public function dataChangingModel() {
		if ($this->data_changing_model === null) {
			$this->data_changing_model = new \App\Model\UserDataChanging();
		}
		return $this->data_changing_model;
	}

	// -------------------------------------------------------------------------

	public function editableFieldsNames() {
		return [];
	}

	public function fields() {
		$c = $this->fieldPropCreator();
		return [
			'email' => $c->stringField('E-mail', 255, ['rules' => ['required']]),
			'last_activity_ip_addr' => $c->ipAddrField('Последняя активность - IP-адрес'),
			'last_activity_timestamp' => $c->dateTimeField('Последняя активность - Время'),
			'last_logon_ip_addr' => $c->ipAddrField('Последнее залогинивание - IP-адрес'),
			'last_logon_timestamp' => $c->dateTimeField('Последнее залогинивание - Время'),
			'login' => $c->textField('Логин', ['rules' => ['length' => ['max' => 32, 'min' => 2]]]),
			'name' => $c->stringField('Имя', 255, ['rules' => ['required']]),
			'password' => $c->passwordField('Пароль', ['rules' => ['length' => ['max' => 32, 'min' => 4]]]),
			'password_hash' => $c->textField('Пароль - Хеш', ['rules' => ['length' => ['max' => 32, 'min' => 32]]]),
			'password_salt' => $c->textField('Пароль - Соль', ['rules' => ['length' => ['max' => 32, 'min' => 32]]]),
			'phone' => $c->stringField('Телефон', 25),
			'prev_logon_ip_addr' => $c->ipAddrField('Предыдущее залогинивание - IP-адрес'),
			'prev_logon_timestamp' => $c->dateTimeField('Предыдущее залогинивание - Время'),
			'provider_alias' => $c->stringField('Провайдер - Алиас', 32),
			'provider_user_id' => $c->stringField('Провайдер - Код пользователя', 32),
			'state' => $c->radioButtons_typeList('Состояние', $this->states())
		];
	}

	public function states() {
		return [
			'' => 'Неактивный',
			'active' => 'Активный',
			'blocked' => 'Заблокирован'
		];
	}

	// -------------------------------------------------------------------------

	public function beforeInsert(&$vals = []) {
		if (is_array($vals) && isset($vals['password'])) {
			$vals['password_salt'] = $this->genPasswordSalt();
			$vals['password_hash'] = $this->genPasswordHash($vals['password'], $vals['password_salt']);
			unset($vals['password']);
		}
		return $this;
	}

	public function beforeUpdate(&$vals = []) {
		if (is_array($vals) && isset($vals['password'])) {
			$vals['password_salt'] = $this->genPasswordSalt();
			$vals['password_hash'] = $this->genPasswordHash($vals['password'], $vals['password_salt']);
			unset($vals['password']);
		}
		return $this;
	}

	// -------------------------------------------------------------------------

	public function addActivation() {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->setStates('executed');
			$control_number = $this->genControlNumber();
			$vals = [
				'control_number' => $control_number,
				'expire_timestamp' => DateTime::now()->offsetHours(1)->format(),
				'model_alias' => $this->model()->alias(),
				'object_id' => $this->model()->id(),
				'state' => '',
				'type' => 'activation'
			];
			if ($this->dataChangingModel()->insert($vals)) {
				$this->setControlNumber($control_number);
				$result = true;
			} else {
				$this->errorHandler()->setError('Ошибка при добавлении записи об активации', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function checkEmailForUniqueness($email) {
		$_where = [
			'AND',
			'`email` = :email',
			'`id` <> :id'
		];
		$_params = [
			':email' => $email,
			':id' => $this->model()->id()
		];
		return $this->model()->reader()->setWhere($_where, $_params)->count() ? false : true;
	}

	public function checkLoginForUniqueness($login) {
		$_where = [
			'AND',
			'`id` <> :id',
			'`login` = :login'
		];
		$_params = [
			':id' => $this->model()->id(),
			':login' => $login
		];
		return $this->model()->reader()->setWhere($_where, $_params)->count() ? false : true;
	}

	public function checkPhoneForUniqueness($phone) {
		$_where = [
			'AND',
			'`id` <> :id',
			'`phone` = :phone'
		];
		$_params = [
			':id' => $this->model()->id(),
			':phone' => preg_replace('/[\s\(\)\-]+/iu', '', $phone)
		];
		return $this->model()->reader()->setWhere($_where, $_params)->count() ? false : true;
	}

	public function comparePassword($password) {
		$result = false;
		if ($this->model()->exists()) {
			$password = StringHelper::trim($password);
			if ($password) {
				$password_hash = $this->genPasswordHash($password, $this->model()->val('password_salt'));
				if ($password_hash === $this->model()->val('password_hash')) {
					$result = true;
				} else {
					$this->errorHandler()->setError('Неверный пароль', 3);
				}
			} else {
				$this->errorHandler()->setError('Пароль отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function confirmActivation($control_number) {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->getByObject($this->model(), 'activation');
			if ($this->dataChangingModel()->exists()) {
				$control_number = StringHelper::trim($control_number);
				if ($control_number) {
					if ($control_number === $this->dataChangingModel()->val('control_number')) {
						if (DateTime::now()->timestamp() < (new DateTime($this->dataChangingModel()->val('expire_timestamp')))->timestamp()) {
							if ($this->model()->update(['state' => 'active'])) {
								$result = true;
							} else {
								$this->errorHandler()->setError('Ошибка при подтверждении активации', 6);
							}
						} else {
							$this->errorHandler()->setError('Время для подтверждения активации истекло', 5);
						}
						$this->dataChangingModel()->setState('executed');
					} else {
						$this->errorHandler()->setError('Неверное контрольное число', 4);
					}
				} else {
					$this->errorHandler()->setError('Контрольное число отсутствует', 3);
				}
			} else {
				$this->errorHandler()->setError('Отсутствует запись об активации', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function confirmEmailChanging($control_number) {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->getByObject($this->model(), 'email_changing');
			if ($this->dataChangingModel()->exists()) {
				$control_number = StringHelper::trim($control_number);
				if ($control_number) {
					if ($control_number === $this->dataChangingModel()->val('control_number')) {
						if (DateTime::now()->timestamp() < (new DateTime($this->dataChangingModel()->val('expire_timestamp')))->timestamp()) {
							if ($this->model()->update(['email' => $this->dataChangingModel()->val('val_1')])) {
								$result = true;
							} else {
								$this->errorHandler()->setError('Ошибка при смене e-mail', 6);
							}
						} else {
							$this->errorHandler()->setError('Время для смены e-mail истекло', 5);
						}
						$this->dataChangingModel()->setState('executed');
					} else {
						$this->errorHandler()->setError('Неверное контрольное число', 4);
					}
				} else {
					$this->errorHandler()->setError('Контрольное число отсутствует', 3);
				}
			} else {
				$this->errorHandler()->setError('Отсутствует запись о смене e-mail', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function confirmLoginChanging($control_number) {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->getByObject($this->model(), 'login_changing');
			if ($this->dataChangingModel()->exists()) {
				$control_number = StringHelper::trim($control_number);
				if ($control_number) {
					if ($control_number === $this->dataChangingModel()->val('control_number')) {
						if (DateTime::now()->timestamp() < (new DateTime($this->dataChangingModel()->val('expire_timestamp')))->timestamp()) {
							if ($this->model()->update(['login' => $this->dataChangingModel()->val('val_1')])) {
								$result = true;
							} else {
								$this->errorHandler()->setError('Ошибка при смене логина', 6);
							}
						} else {
							$this->errorHandler()->setError('Время для смены логина истекло', 5);
						}
						$this->dataChangingModel()->setState('executed');
					} else {
						$this->errorHandler()->setError('Неверное контрольное число', 4);
					}
				} else {
					$this->errorHandler()->setError('Контрольное число отсутствует', 3);
				}
			} else {
				$this->errorHandler()->setError('Отсутствует запись о смене логина', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function confirmPasswordChanging($control_number) {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->getByObject($this->model(), 'password_changing');
			if ($this->dataChangingModel()->exists()) {
				$control_number = StringHelper::trim($control_number);
				if ($control_number) {
					if ($control_number === $this->dataChangingModel()->val('control_number')) {
						if (DateTime::now()->timestamp() < (new DateTime($this->dataChangingModel()->val('expire_timestamp')))->timestamp()) {
							$vals = [
								'password_hash' => $this->dataChangingModel()->val('val_1'),
								'password_salt' => $this->dataChangingModel()->val('val_2')
							];
							if ($this->model()->update($vals)) {
								$result = true;
							} else {
								$this->errorHandler()->setError('Ошибка при смене пароля', 6);
							}
						} else {
							$this->errorHandler()->setError('Время для смены пароля истекло', 5);
						}
						$this->dataChangingModel()->setState('executed');
					} else {
						$this->errorHandler()->setError('Неверное контрольное число', 4);
					}
				} else {
					$this->errorHandler()->setError('Контрольное число отсутствует', 3);
				}
			} else {
				$this->errorHandler()->setError('Отсутствует запись о смене пароля', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function confirmPasswordRecovering($control_number) {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->getByObject($this->model(), 'password_changing');
			if ($this->dataChangingModel()->exists()) {
				$control_number = StringHelper::trim($control_number);
				if ($control_number) {
					if ($control_number === $this->dataChangingModel()->val('control_number')) {
						if (DateTime::now()->timestamp() < (new DateTime($this->dataChangingModel()->val('expire_timestamp')))->timestamp()) {
							$vals = [
								'password_hash' => $this->dataChangingModel()->val('val_1'),
								'password_salt' => $this->dataChangingModel()->val('val_2'),
								'state' => 'active'
							];
							if ($this->model()->update($vals)) {
								$result = true;
							} else {
								$this->errorHandler()->setError('Ошибка при восстановлении пароля', 6);
							}
						} else {
							$this->errorHandler()->setError('Время для восстановления пароля истекло', 5);
						}
						$this->dataChangingModel()->setState('executed');
					} else {
						$this->errorHandler()->setError('Неверное контрольное число', 4);
					}
				} else {
					$this->errorHandler()->setError('Контрольное число отсутствует', 3);
				}
			} else {
				$this->errorHandler()->setError('Отсутствует запись о восстановлении пароля', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function confirmPhoneChanging($control_number) {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->getByObject($this->model(), 'phone_changing');
			if ($this->dataChangingModel()->exists()) {
				$control_number = StringHelper::trim($control_number);
				if ($control_number) {
					if ($control_number === $this->dataChangingModel()->val('control_number')) {
						if (DateTime::now()->timestamp() < (new DateTime($this->dataChangingModel()->val('expire_timestamp')))->timestamp()) {
							if ($this->model()->update(['phone' => $this->dataChangingModel()->val('val_1')])) {
								$result = true;
							} else {
								$this->errorHandler()->setError('Ошибка при смене телефона', 6);
							}
						} else {
							$this->errorHandler()->setError('Время для смены телефона истекло', 5);
						}
						$this->dataChangingModel()->setState('executed');
					} else {
						$this->errorHandler()->setError('Неверный код', 4);
					}
				} else {
					$this->errorHandler()->setError('Код отсутствует', 3);
				}
			} else {
				$this->errorHandler()->setError('Отсутствует запись о смене телефона', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function confirmRegistration($control_number) {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->getByObject($this->model(), 'registration');
			if ($this->dataChangingModel()->exists()) {
				$control_number = StringHelper::trim($control_number);
				if ($control_number) {
					if ($control_number === $this->dataChangingModel()->val('control_number')) {
						if (DateTime::now()->timestamp() < (new DateTime($this->dataChangingModel()->val('expire_timestamp')))->timestamp()) {
							if ($this->model()->update(['state' => 'active'])) {
								$result = true;
							} else {
								$this->errorHandler()->setError('Ошибка при подтверждении регистрации', 6);
							}
						} else {
							$this->errorHandler()->setError('Время для подтверждения регистрации истекло', 5);
						}
						$this->dataChangingModel()->setState('executed');
					} else {
						$this->errorHandler()->setError('Неверное контрольное число', 4);
					}
				} else {
					$this->errorHandler()->setError('Контрольное число отсутствует', 3);
				}
			} else {
				$this->errorHandler()->setError('Отсутствует запись о регистрации', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function findByEmail($email) {
		$result = false;
		if ($email) {
			$this->getByEmail($email);
			if ($this->model()->exists()) {
				$result = true;
			} else {
				$this->errorHandler()->setError('Пользователь отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('E-mail отсутствует', 1);
		}
		return $result;
	}

	public function findById($id) {
		$result = false;
		if ($id) {
			$this->getById($id);
			if ($this->model()->exists()) {
				$result = true;
			} else {
				$this->errorHandler()->setError('Пользователь отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Идентификатор отсутствует', 1);
		}
		return $result;
	}

	public function findByLogin($login) {
		$result = false;
		if ($login) {
			$this->getByLogin($login);
			if ($this->model()->exists()) {
				$result = true;
			} else {
				$this->errorHandler()->setError('Пользователь отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Логин отсутствует', 1);
		}
		return $result;
	}

	public function findByPhone($phone) {
		$result = false;
		if ($phone) {
			$this->getByPhone($phone);
			if ($this->model()->exists()) {
				$result = true;
			} else {
				$this->errorHandler()->setError('Пользователь отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Телефон отсутствует', 1);
		}
		return $result;
	}

	public function findByProvider($provider_alias, $provider_user_id) {
		$result = false;
		if ($provider_alias) {
			if ($provider_user_id) {
				$this->getByProvider($provider_alias, $provider_user_id);
				if ($this->model()->exists()) {
					$result = true;
				} else {
					$this->errorHandler()->setError('Пользователь отсутствует', 3);
				}
			} else {
				$this->errorHandler()->setError('Код пользователя отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Алиас провайдера отсутствует', 1);
		}
		return $result;
	}

	public function getByEmail($email) {
		$user = $this->model()
				->reader()
				->setWhere('`email` = :email', [':email' => $email])
				->objectByConds();
		return $user;
	}

	public function getById($id) {
		return $this->model()
						->reader()
						->object($id);
	}

	public function getByLogin($login) {
		return $this->model()
						->reader()
						->setWhere('`login` = :login', [':login' => $login])
						->objectByConds();
	}

	public function getByPhone($phone) {
		return $this->model()
						->reader()
						->setWhere('`phone` = :phone', [':phone' => $phone])
						->objectByConds();
	}

	public function getByProvider($provider_alias, $provider_user_id) {
		$_where = [
			'AND',
			'`provider_alias` = :provider_alias',
			'`provider_user_id` = :provider_user_id'
		];
		$_params = [
			':provider_alias' => $provider_alias,
			':provider_user_id' => $provider_user_id
		];
		return $this->model()
						->reader()
						->setWhere($_where, $_params)
						->objectByConds();
	}

	public function login($password) {
		$result = false;
		if ($this->model()->exists()) {
			$password = StringHelper::trim($password);
			if ($password) {
				$password_hash = $this->genPasswordHash($password, $this->model()->val('password_salt'));
				if ($password_hash === $this->model()->val('password_hash')) {
					$this->model()->update([
						'last_activity_ip_addr' => App::request()->getRemoteAddr(''),
						'last_activity_timestamp' => DateTime::now()->format(),
						'last_logon_ip_addr' => App::request()->getRemoteAddr(''),
						'last_logon_timestamp' => DateTime::now()->format(),
						'prev_logon_ip_addr' => $this->model()->val('last_logon_ip_addr'),
						'prev_logon_timestamp' => $this->model()->val('last_logon_timestamp')
					]);
					$this->saveInSession();
					$result = true;
				} else {
					$this->errorHandler()->setError('Неверный пароль', 3);
				}
			} else {
				$this->errorHandler()->setError('Пароль отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function logout() {
		$this->removeFromSession();
		return true;
	}

	/**
	 * @todo Проблема с использованием Model::setVals и последующим Model::getVals.
	 * При Model::getVals возвращаются все значения модели, а не те которые были переданы через Model::setVals.
	 * Поэтому при регистрации не устанавливаются значения полей timestamp_inserting и timestamp_last_updating.
	 * Проблему нужно решить глобально.
	 */
	public function registerWithChecking($vals = []) {
		$result = false;
		$this->model()->setVals($vals);
		if (isset($vals['password'])) {
			$password = (string)$vals['password'];
			if ($password) {
				$password_salt = $this->genPasswordSalt();
				$_vals = $this->model()->getVals();
				$_vals['password_hash'] = $this->genPasswordHash($password, $password_salt);
				$_vals['password_salt'] = $password_salt;
				$_result = $this->model()->insert($_vals);
				if ($_result !== false) {
					$this->dataChangingModel()->setStates('executed');
					$control_number = $this->genControlNumber();
					$vals = [
						'control_number' => $control_number,
						'expire_timestamp' => DateTime::now()->offsetHours(1)->format(),
						'model_alias' => $this->model()->alias(),
						'object_id' => $this->model()->id(),
						'state' => '',
						'type' => 'registration',
						'val_1' => '',
						'val_2' => ''
					];
					if ($this->dataChangingModel()->insert($vals)) {
						$this->setControlNumber($control_number)
								->setPassword($password);
						$result = true;
					} else {
						$this->errorHandler()->setError('Ошибка при добавлении записи о регистрации', 4);
					}
				} else {
					$this->errorHandler()->setError('Ошибка при добавлении пользователя', 3);
				}
			} else {
				$this->errorHandler()->setError('Пароль отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Пароль отсутствует', 1);
		}
		return $result;
	}

	/**
	 * @todo См. registerWithChecking.
	 */
	public function registerWithoutChecking($vals = []) {
		$result = false;
		$this->model()->setVals($vals);
		if (isset($vals['password'])) {
			$password = (string)$vals['password'];
			if ($password) {
				$password_salt = $this->genPasswordSalt();
				$_vals = $this->model()->getVals();
				$_vals['password_hash'] = $this->genPasswordHash($password, $password_salt);
				$_vals['password_salt'] = $password_salt;
				$_result = $this->model()->insert($_vals);
				if ($_result !== false) {
					$this->setPassword($password);
					$result = true;
				} else {
					$this->errorHandler()->setError('Ошибка при добавлении пользователя', 3);
				}
			} else {
				$this->errorHandler()->setError('Пароль отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Пароль отсутствует', 1);
		}
		return $result;
	}

	public function saveNewEmailWithChecking($new_email) {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->setStates('executed');
			$control_number = $this->genControlNumber();
			$vals = [
				'control_number' => $control_number,
				'expire_timestamp' => DateTime::now()->offsetHours(1)->format(),
				'model_alias' => $this->model()->alias(),
				'object_id' => $this->model()->id(),
				'state' => '',
				'type' => 'email_changing',
				'val_1' => $new_email,
				'val_2' => ''
			];
			if ($this->dataChangingModel()->insert($vals)) {
				$this->setControlNumber($control_number)
						->setNewEmail($new_email);
				$result = true;
			} else {
				$this->errorHandler()->setError('Ошибка при добавлении записи о смене e-mail', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function saveNewEmailWithoutChecking($new_email) {
		$result = false;
		if ($this->model()->exists()) {
			if ($new_email) {
				if ($this->model()->update(['email' => $new_email])) {
					$result = true;
				} else {
					$this->errorHandler()->setError('Ошибка при сохранении e-mail', 3);
				}
			} else {
				$this->errorHandler()->setError('E-mail отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function saveNewLoginWithChecking() {
		throw new Exception\MethodNotExists();
	}

	public function saveNewLoginWithoutChecking() {
		throw new Exception\MethodNotExists();
	}

	public function saveNewPasswordWithChecking($new_password = null) {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->setStates('executed');
			$control_number = $this->genControlNumber();
			$new_password = ($new_password === null) ? $this->genPassword() : (string)$new_password;
			$new_password_salt = $this->genPasswordSalt();
			$vals = [
				'control_number' => $control_number,
				'expire_timestamp' => DateTime::now()->offsetHours(1)->format(),
				'model_alias' => $this->model()->alias(),
				'object_id' => $this->model()->id(),
				'state' => '',
				'type' => 'password_changing',
				'val_1' => $this->genPasswordHash($new_password, $new_password_salt),
				'val_2' => $new_password_salt
			];
			if ($this->dataChangingModel()->insert($vals)) {
				$this->setControlNumber($control_number)
						->setNewPassword($new_password);
				$result = true;
			} else {
				$this->errorHandler()->setError('Ошибка при добавлении записи о смене пароля', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function saveNewPasswordWithoutChecking($new_password) {
		$result = false;
		if ($this->model()->exists()) {
			if ($new_password) {
				$new_password_salt = $this->genPasswordSalt();
				$vals = [
					'password_hash' => $this->genPasswordHash($new_password, $new_password_salt),
					'password_salt' => $new_password_salt
				];
				if ($this->model()->update($vals)) {
					$this->setNewPassword($new_password);
					$result = true;
				} else {
					$this->errorHandler()->setError('Ошибка при сохранении пароля', 3);
				}
			} else {
				$this->errorHandler()->setError('Пароль отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function saveNewPhoneWithChecking($new_phone) {
		$result = false;
		if ($this->model()->exists()) {
			$this->dataChangingModel()->setStates('executed');
			$control_number = mt_rand(100000, 999999);
			$new_phone = preg_replace('/[\s\(\)\-]+/iu', '', $new_phone);
			$vals = [
				'control_number' => $control_number,
				'expire_timestamp' => DateTime::now()->offsetHours(1)->format(),
				'model_alias' => $this->model()->alias(),
				'object_id' => $this->model()->id(),
				'state' => '',
				'type' => 'phone_changing',
				'val_1' => $new_phone,
				'val_2' => ''
			];
			if ($this->dataChangingModel()->insert($vals)) {
				$this->setControlNumber($control_number)
						->setNewPhone($new_phone);
				$result = true;
			} else {
				$this->errorHandler()->setError('Ошибка при добавлении записи о смене телефона', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	public function saveNewPhoneWithoutChecking($new_phone) {
		$result = false;
		if ($this->model()->exists()) {
			if ($new_phone) {
				$vals = ['phone' => $new_phone];
				if ($this->model()->update($vals)) {
					$result = true;
				} else {
					$this->errorHandler()->setError('Ошибка при сохранении телефона', 3);
				}
			} else {
				$this->errorHandler()->setError('Телефон отсутствует', 2);
			}
		} else {
			$this->errorHandler()->setError('Пользователь отсутствует', 1);
		}
		return $result;
	}

	// -------------------------------------------------------------------------

	public function checkInSession() {
		return $this->getFromSession()->exists();
	}

	public function getFromSession() {
		$this->model()->resetVals();
		if ($this->sessionExists()) {
			$this->model()->reader()->object($_SESSION[$this->getSessionVarName()]);
		}
		return $this->model();
	}

	public function getSessionVarName() {
		return $this->session_var_name;
	}

	public function removeFromSession() {
		if ($this->sessionExists()) {
			unset($_SESSION[$this->getSessionVarName()]);
		}
		return $this;
	}

	public function saveInSession() {
		$_SESSION[$this->getSessionVarName()] = $this->model()->id();
		return $this;
	}

	public function sessionExists() {
		return isset($_SESSION[$this->getSessionVarName()]);
	}

	public function setSessionVarName($name) {
		$this->session_var_name = (string)$name;
		return $this;
	}

	// -------------------------------------------------------------------------

	public function genControlNumber($length = 32, $symbols = '0123456789abcdef') {
		return StringHelper::random($length, $symbols);
	}

	public function genPassword($length = 12, $symbols = '0123456789abcdefghijklmnopqrstuvwxyz') {
		return StringHelper::random($length, $symbols);
	}

	public function genPasswordHash($password, $password_salt) {
		return md5(md5((string)$password).(string)$password_salt);
	}

	public function genPasswordSalt($length = 32, $symbols = '0123456789abcdef') {
		return StringHelper::random($length, $symbols);
	}

	public function getControlNumber() {
		return $this->control_number;
	}

	public function getNewEmail() {
		return $this->new_email;
	}

	public function getNewPassword() {
		return $this->new_password;
	}

	public function getNewPhone() {
		return $this->new_phone;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setControlNumber($control_number) {
		$this->control_number = (string)$control_number;
		return $this;
	}

	public function setNewEmail($email) {
		$this->new_email = (string)$email;
		return $this;
	}

	public function setNewPassword($password) {
		$this->new_password = (string)$password;
		return $this;
	}

	public function setPassword($password) {
		$this->password = (string)$password;
		return $this;
	}

	public function setNewPhone($phone) {
		$this->new_phone = (string)$phone;
		return $this;
	}

}