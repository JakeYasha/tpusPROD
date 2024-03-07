<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();
ini_set('display_errors', 1);
ini_set("log_errors", 0);

try {
    $actions = [
        new \App\Action\Crontab\Sitemap(),
    ];

    foreach ($actions as $action) {
        $action->execute();
    }
} catch (Exception $e) {
}