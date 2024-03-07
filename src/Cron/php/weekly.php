<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();

$actions = [
    new \App\Action\Crontab\BrandMaker(),
];

foreach ($actions as $action) {
    $action->execute();
}