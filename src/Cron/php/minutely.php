<?php

ini_set('memory_limit', '512M');
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();
app()->getLogger()->setLogFileName('minutely-' . date('d.m'));

app()->log('### minutely go"');
$actions = [
	// new \App\Action\Crontab\SmsSender(),
	new \App\Action\Crontab\ServiceStatisticsMaker(),
	new \App\Action\Crontab\EmailSender(),
];
app()->log('### minutely end"');

foreach ($actions as $action) {
	try {
		$action->execute();
	} catch (\Exception $e) {
		app()->log('### minutely Error"');
		app()->log($e);
	}
}