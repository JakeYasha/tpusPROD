<?php

namespace App\Action;

use Sky4\App,
	Sky4\Exception;

class SendInTgBot extends \App\Classes\Action {

	public const TELEGRAM_TOKEN = '';
	public const TELEGRAM_CHATID = '';


	public function execute() {
		throw new Exception(Exception::TYPE_BAD_URL);
	}

	public static function sendMessage(?string $message = null) {
		// сюда нужно вписать токен вашего бота
		define('TELEGRAM_TOKEN', '5729388450:AAF8FEsBU22acIBXQVPMjtiM6XN6lBNAHro');

		// сюда нужно вписать ваш внутренний айдишник
		define('TELEGRAM_CHATID', '-1001668638195');

		$ch = curl_init();
		curl_setopt_array(
			$ch,
			array(
				CURLOPT_URL => 'https://api.telegram.org/bot' . TELEGRAM_TOKEN . '/sendMessage',
				CURLOPT_POST => TRUE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_TIMEOUT => 10,
				CURLOPT_POSTFIELDS => array(
					'chat_id' => TELEGRAM_CHATID,
					'text' => $message,
				),
			)
		);
		curl_exec($ch);
	}

}
