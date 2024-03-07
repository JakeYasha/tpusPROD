<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/').'/../../../config/config_app.php';
\Sky4\App::init();
ini_set('display_errors', 1);
ini_set("log_errors", 0);

//(new \App\Action\Crontab\FirmRating(true))->execute();

(new \App\Action\Crontab\Sitemap())->execute();
