<?php

require_once rtrim(__DIR__, '/') . '/../../../../config/config_app.php';
\Sky4\App::init();

$action = new App\Action\Crontab\SphinxRtIndex\PriceInsert();
if (isset($argv[1]) && $argv[1]) {
	$action->execute($argv[1]);
}
