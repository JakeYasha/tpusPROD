<?php

namespace App\Action\Crontab;
use \App\Action\Crontab\AvgStatisticsMakerTest;

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/').'/../../../config/config_app.php';
\Sky4\App::init();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//
$actions = [];

$actions += [
    //new \App\Action\Crontab\FirmTyper(),
	new \App\Action\Crontab\CatalogCounter(),
    //new \App\Action\Crontab\AvgStatisticsMaker(),
];

foreach ($actions as $action) {
	$action->execute();
}