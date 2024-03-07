<?php

class KCServiceApi {
	private $auth_key = 'b7a0b117-64f0-496b-9db5-819a7ec6cb24';
	private $api_url = 'http://213.187.100.121/';
	private $params = [];

	public function GetAudio($_asterisk_id, $_date_begin, $_date_end) {
		$this->params = json_encode(array('json' => array(
			'action' => 'get-call', 
			'date-begin' => $_date_begin,//'2019-10-18',
			'date-end' => $_date_end,//'2019-11-18', 
			'asterisk-id' => $_asterisk_id,//'1574080315.742'
		)));

		$signature = md5($this->params . $this->auth_key);

		$ch = curl_init('http://213.187.100.121/');
		curl_setopt($ch, CURLOPT_PORT, 7123);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//curl_setopt($ch, CURLOPT_NOBODY, FALSE); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Signature: ' . $signature,
			'Content-Length: ' . strlen($this->params),
			'Content-Type: application/json'
		));

		$response = curl_exec($ch);
		$errorCode = curl_errno($ch);

		switch($errorCode) {
			case CURLE_OK:
				// Успешно
				header('Content-Type: audio/mpeg');
				header('Content-Disposition: attachment; filename="audio.mp3"');
				echo $response;
				break;
			case CURLE_OPERATION_TIMEOUTED:
				// Таймаут
				echo 'OPERATION TIMEOUTED';
				break;
			default:
				// Неудача
				echo "ERROR. curl: {$errorCode}";
				break;
		}

		curl_close($ch);
	}		
}

