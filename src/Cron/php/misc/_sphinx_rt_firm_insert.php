<?php

require_once '/var/www/sites/tovaryplus.ru/config/config_app.php';
\Sky4\App::init();

$action = new App\Action\Crontab\SphinxRtIndex\FirmInsert();
if (isset($argv[1]) && $argv[1]) {
	$action->execute($argv[1]);
}