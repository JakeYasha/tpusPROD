<?php

namespace Sky4\Component\Email;

use Sky4\Exception,
	Sky4\Helper\StringHelper,
	Sky4\Traits\ErrorHandler,
	Sky4\Traits\LogHandler;

class Smtp {

	use ErrorHandler,
	 LogHandler;

	protected $crlf = "\r\n";
	protected $email_driver = null;
	protected $socket_desc = null;

	public function __construct(Driver $email_driver = null) {
		if (is_object($email_driver)) {
			$this->setEmailDriver($email_driver);
		}
	}

	/**
	 * @return Driver
	 */
	public function emailDriver() {
		if ($this->email_driver === null) {
			throw new Exception('Email-драйвер отсутствует, его необходимо установить через конструктор либо метод setEmailDriver');
		}
		return $this->email_driver;
	}

	/**
	 * @return ErrorHandler
	 */
	public function errorHandler() {
		return $this->emailDriver()->errorHandler();
	}

	public function getCrlf() {
		return $this->crlf;
	}

	/**
	 * @return LogHandler
	 */
	public function logHandler() {
		return $this->emailDriver()->logHandler();
	}

	/**
	 * @return Smtp
	 */
	public function setCrlf($crlf) {
		$this->crlf = (string) $crlf;
		return $this;
	}

	/**
	 * @return Smtp
	 */
	public function setEmailDriver(Driver $email_driver) {
		$this->email_driver = $email_driver;
		return $this;
	}

	// -------------------------------------------------------------------------

	public function closeConnection() {
		if (is_resource($this->socket_desc)) {
			fclose($this->socket_desc);
			$this->socket_desc = null;
		}
		return $this;
	}

	public function connect($host_name, $host_port = -1, $timeout = 0) {
		if ($this->connected()) {
			// @todo Продумать.
			$this->errorHandler()->setError('Соединение уже создано [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		$this->logHandler()->addRecord('Connect');
		$error_code = 0;
		$error_message = '';
		$host_name = (string) $host_name;
		$host_port = (int) $host_port;
		$timeout = (int) $timeout;
		if ($timeout) {
			$this->socket_desc = fsockopen($host_name, $host_port, $error_code, $error_message, $timeout);
		} else {
			$this->socket_desc = fsockopen($host_name, $host_port, $error_code, $error_message);
		}
		$response = $this->getResponse();
		if (!is_resource($this->socket_desc)) {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: FAIL');
			$this->errorHandler()->setError('Ошибка при соединении [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		$this->logHandler()
				->addRecord('Response: ' . $response)
				->addRecord('Result: SUCCESS')
				->addRecord('Set timeout');
		if (!stream_set_timeout($this->socket_desc, $timeout)) {
			$this->logHandler()->addRecord('Result: FAIL');
			$this->errorHandler()->setError('Ошибка при установке таймаута [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		$this->logHandler()->addRecord('Result: SUCCESS');
		return true;
	}

	public function connected() {
		$result = false;
		if (is_resource($this->socket_desc)) {
			$socket_status = socket_get_status($this->socket_desc);
			if (isset($socket_status['eof']) && $socket_status['eof']) {
				$this->closeConnection();
			} else {
				$result = true;
			}
		}
		return $result;
	}

	protected function executeCommand($request, $success_response_code, &$response = '') {
		fputs($this->socket_desc, (string) $request . $this->crlf);
		$response = $this->getResponse();
		$response_code = (int) StringHelper::sub($response, 0, 3);
		if ((is_int($success_response_code) && ($response_code === $success_response_code)) || (is_array($success_response_code) && in_array($response_code, $success_response_code))) {
			return true;
		}
		return false;
	}

	protected function getResponse() {
		$response = '';
		while ($string = fgets($this->socket_desc, 515)) {
			$response .= $string;
			if (StringHelper::sub($string, 3, 1) === ' ') {
				break;
			}
		}
		return $response;
	}

	// -------------------------------------------------------------------------

	public function authLogin($user_name, $user_password) {
		if (!$this->connected()) {
			$this->errorHandler()->setError('Соединение отсутствует [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		$request = 'AUTH LOGIN';
		$this->logHandler()->addRecord('Request: ' . $request);
		if ($this->executeCommand($request, 334, $response)) {
			$request = base64_encode($user_name);
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: SUCCESS')
					->addRecord('Request: %USER NAME%');
			if ($this->executeCommand($request, 334, $response)) {
				$request = base64_encode($user_password);
				$this->logHandler()
						->addRecord('Response: ' . $response)
						->addRecord('Result: SUCCESS')
						->addRecord('Request: %USER PASSWORD%');
				if ($this->executeCommand($request, 235, $response)) {
					$this->logHandler()
							->addRecord('Response: ' . $response)
							->addRecord('Result: SUCCESS');
					return true;
				} else {
					$this->logHandler()
							->addRecord('Response: ' . $response)
							->addRecord('Result: FAIL');
				}
			} else {
				$this->logHandler()
						->addRecord('Response: ' . $response)
						->addRecord('Result: FAIL');
			}
		} else {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: FAIL');
		}
		return false;
	}

	public function data($data) {
		if (!$this->connected()) {
			$this->errorHandler()->setError('Соединение отсутствует [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		$request = 'DATA';
		$this->logHandler()
				->addRecord('Request: ' . $request);
		if ($this->executeCommand('DATA', 354, $response)) {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: SUCCESS')
					->addRecord('Request: %DATA%');
			fwrite($this->socket_desc, $data . $this->crlf . $this->crlf . '.' . $this->crlf);
			$response = $this->getResponse();
			$response_code = (int) StringHelper::sub($response, 0, 3);
			if ($response_code === 250) {
				$this->logHandler()
						->addRecord('Response: ' . $response)
						->addRecord('Result: SUCCESS');
				return true;
			} else {
				$this->logHandler()
						->addRecord('Response: ' . $response)
						->addRecord('Result: FAIL');
			}
		} else {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: FAIL');
		}
		return false;
	}

	public function helo($host_name) {
		if (!$this->connected()) {
			$this->errorHandler()->setError('Соединение отсутствует [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		//$request = 'EHLO ' . (string) $host_name;
		$request = 'EHLO ' . 'tovaryplus.ru';
		$this->logHandler()->addRecord('Request: ' . $request);
		if (!$this->executeCommand($request, 250, $response)) {
			$request = 'HELO ' . (string) $host_name;
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: FAIL')
					->addRecord('Request: ' . $request);
			if (!$this->executeCommand($request, 250, $response)) {
				$this->logHandler()
						->addRecord('Response: ' . $response)
						->addRecord('Result: FAIL');
				return false;
			} else {
				$this->logHandler()
						->addRecord('Response: ' . $response)
						->addRecord('Result: SUCCESS');
			}
		} else {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: SUCCESS');
		}
		return true;
	}

	public function mailFrom($address) {
		if (!$this->connected()) {
			$this->errorHandler()->setError('Соединение отсутствует [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		$request = 'MAIL FROM:<' . (string) $address . '>';
		$this->logHandler()->addRecord('Request: ' . $request);
		if ($this->executeCommand($request, 250, $response)) {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: SUCCESS');
			return true;
		} else {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: FAIL');
		}
		return false;
	}

	public function quit() {
		if (!$this->connected()) {
			$this->errorHandler()->setError('Соединение отсутствует [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		$request = 'QUIT';
		$this->logHandler()->addRecord('Request: ' . $request);
		if ($this->executeCommand($request, 221, $response)) {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: SUCCESS');
			return true;
		} else {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: FAIL');
		}
		return false;
	}

	public function rcptTo($address) {
		if (!$this->connected()) {
			$this->errorHandler()->setError('Соединение отсутствует [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		$request = 'RCPT TO:<' . (string) $address . '>';
		$this->logHandler()->addRecord('Request: ' . $request);
		if ($this->executeCommand($request, [250, 251], $response)) {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: SUCCESS');
			return true;
		} else {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: FAIL');
		}
		return false;
	}

	public function rset() {
		if (!$this->connected()) {
			$this->errorHandler()->setError('Соединение отсутствует [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		$request = 'RSET';
		$this->logHandler()->addRecord('Request: ' . $request);
		if ($this->executeCommand($request, 250, $response)) {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: SUCCESS');
			return true;
		} else {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: FAIL');
		}
		return false;
	}

	public function startTls() {
		if (!$this->connected()) {
			$this->errorHandler()->setError('Соединение отсутствует [' . __CLASS__ . '::' . __METHOD__ . ']');
			return false;
		}
		$request = 'STARTTLS';
		$this->logHandler()->addRecord('Request: ' . $request);
		if ($this->executeCommand($request, 220, $response)) {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: SUCCESS')
					->addRecord('Enable crypto');
			if (stream_socket_enable_crypto($this->socket_desc, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
				$this->logHandler()->addRecord('Result: SUCCESS');
				return true;
			} else {
				$this->logHandler()->addRecord('Result: FAIL');
			}
		} else {
			$this->logHandler()
					->addRecord('Response: ' . $response)
					->addRecord('Result: FAIL');
		}
		return false;
	}

}
