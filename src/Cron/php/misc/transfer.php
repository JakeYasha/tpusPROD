<?php
ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/') . '/../../../../config/config_app.php';
\Sky4\App::init();

$transfer = new \App\Action\Crontab\Transfer();
$transfer->execute();
