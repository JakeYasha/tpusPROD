<?php

ini_set('memory_limit', '512M');
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();
app()->getLogger()->setLogFileName('minutely-' . date('d.m'));

use App\Action\SendInTgBot;

app()->log('##Стартуем!##');
$all = app()->db()
		->query()
		->setText("SELECT * FROM `app_email_queue` WHERE `timestamp_inserting` < CURRENT_TIME and `timestamp_inserting` > CURRENT_TIME - INTERVAL 1 MINUTE")
		->fetch();

if ($all) {
	foreach ($all as $ob) {
		$data = unserialize(base64_decode($ob['data']));

		
		$log_text = 'отправлено письмо с темой '.$data['subject'].' на адрес(а):'.PHP_EOL;
		foreach ($data['to'] as $address) {
			$log_text .= ' ->'.$address;
		}
		SendInTgBot::sendMessage($log_text."\n\n\nТекст:\n".$data['body']);

	}
}


