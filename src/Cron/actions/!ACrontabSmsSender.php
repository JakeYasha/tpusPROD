<?php

class ACrontabSmsSender extends ACrontabAction {
	public static $email_addresses = array(
		'mng@727373.ru',
		'zapros@727373.ru',
		'zapros@tovaryplus.ru',
		'gremlin@tovaryplus.ru',
		'site@tovaryplus.ru',
		'gazeta@727373.ru'
	);
	
	public function run() {
		$this->send();
		return parent::run();
	}

	private function send() {
		//Адрес для поключения по протоколу IMAP. Для gmail аккаунтов - всегда такой:
		$hostname = '{imap.gmail.com:993/ssl}INBOX';
		//E-mail:
		$username = 'podati.sms@gmail.com';
		$password = '4ycXr1FIzHZ3WrwhZtnq';
		$last_message = 10;
		
		$mbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
		$mc = imap_check($mbox);
		$delta = ($mc->Nmsgs - $last_message > 0) ? $mc->Nmsgs - $last_message : '1';
		$result = imap_fetch_overview($mbox, $delta . ":" . $mc->Nmsgs, 0);
		$sms = array();
		$i = 1;
		foreach ($result as $overview) {
			$seen_msg = $overview->seen;
			$from = explode("\<|\>", $overview->from);
			if (!$seen_msg) {
				$phone = self::clearPhone($overview->subject);
				if (in_array($from[1], self::$email_addresses) && self::detectPhone($phone)) {
					$msgbody = imap_fetchbody($mbox, $overview->msgno, 1);
					$msgbody = self::prepareText($msgbody . "", $from[1]);
					$sms[$i]['phone'] = $phone;
					$sms[$i]['body'] = $msgbody;
					$i++;
				}
			}
		}

		imap_close($mbox);

		require_once APP_DIR_PATH . '/app/components/sms/sms_service_api.php';

		$sender = new SmsServiceApi(19075, 'ypuebqazt3');
		if (count($sms) > 0) {
			foreach ($sms as $key => $value) {
				$api_params = array(
					'pid' => 20626,
					'sender' => 'IC_Kegeles',
					'to' => $value['phone'],
					'text' => $value['body']
				);
				$result = $sender->send('delivery.sendSms', $api_params);
				var_dump($result);
			}
		}
	}

	/////////////////////////////////////////////////////////////////////////////

	/* Функция раcкодирования заголовка: */
	public static function decodeSubject($s) {
		if (preg_match("/^=\?([^\?]+)\?(B|Q)\?(.+)\?=/", $s, $val)) {
			// Если да, выясняем способ кодирования по значению $val[2] и *декодируем*
			if ($val[2] == 'B') {
				// декодируем Base64
				$val[3] = base64_decode($val[3]);
			} elseif ($val[2] == 'Q') {
				// декодируем QuotedPrint
				$i = 0;
				print $len = mb_strlen($val[3]);
				$new = '';
				print $val[3];
				while ($i < $len) {
					if ($val[3][$i] == '=') {
						$new.=chr(hexdec($val[3][$i + 1] . $val[3][$i + 2]));
						$i+=3;
					} elseif ($val[3][$i] == '_') {
						$new.=" ";
						$i++;
					} else {
						$new.=$val[3][$i++];
					}
				}
				$val[3] = $new;
			}
			// Проверяем, кодировку, если KOI8-R, то *перекодируем* строку в Windows-1251.
			if (!strcasecmp($val[1], 'koi8-r')) {
				$val[3] = iconv("koi8-r", "utf-8", $val[3]);
			}
			return $val[3];
		} else {
			// Заголовок не отвечает известному нам формату кодирования - возвращаем *без изменений*
			return $s;
		}
	}

	//Функция очистки заголовка от возможных символов телефона ( ) + - 
	public static function clearPhone($s) {
		return str_replace([' ', '-', '	', '+', ')', '('], '', $s);
	}

	//Функция проверки "телефон ли это" и корректировки (подставление 7 вместо 8)
	public static function detectPhone(&$s) {
		if ($s[0] == "8" && mb_strlen($s) == 11) {
			$s[0] = "7";
		}
		if ($s[0] == "9" && mb_strlen($s) == 10) {
			$s = "7" . $s;
		}
		if (mb_strlen($s) == 11) {
			return true;
		}

		return false;
	}

	//Функция определения кодирования Base64
	public static function detectBase64($s) {
		if (preg_match('#^[A-Za-z0-9=\/\+\s]+$#i', $s)) {
			return true;
		}
		return false;
	}

	//Функция определения кодировки UTF-8
	public static function detectUTF8($string) {
		return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
        )+%xs', $string);
	}

	//Функция определения кодировки Windows-1251 и Koi8-R
	public static function detectRuEncoding($str) {
		$win = 0;
		$koi = 0;
		for ($i = 0; $i < strlen($str); $i++) {
			if (ord($str[$i]) > 224 && ord($str[$i]) < 255) {
				$win++;
			}
			if (ord($str[$i]) > 192 && ord($str[$i]) < 223) {
				$koi++;
			}
		}
		if ($win < $koi) {
			return 1;
		} else {
			return 0;
		}
	}

	//Функция обработки текста. Разбивка по строкам, выбор строк для отправки
	public static function prepareText($s, $addr) {
		$s = trim($s);
		if (self::detectBase64($s)) {
			$s = base64_decode($s);
			if (!self::detectUTF8($s)) {
				if (self::detectRuEncoding($s)) {
					$s = iconv("koi8-r", "utf-8", $s);
				} else {
					$s = iconv("windows-1251", "utf-8", $s);
				}
			}
		} else {
			$s = iconv("windows-1251", "utf-8", $s);
		}
		if ($addr == self::$email_addresses[0]) $s = 'Ответ на запрос от 727373 ' . $s;
		$ss = explode(PHP_EOL, $s);
		if (mb_strlen($ss[0]) > 70 * 3) $itog = mb_substr($ss[0], 0, 70 * 3);
		else $itog = $ss[0];

		for ($i = 1; $i < sizeof($ss); $i++) {
			if (mb_strlen($itog . "\n" . $ss[$i]) <= 70 * 3) $itog = $itog . "\n" . $ss[$i];
		}
		
		return $itog;
	}

}
