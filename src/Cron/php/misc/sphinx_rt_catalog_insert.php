<?php
ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/').'/../../../../config/config_app.php';
\Sky4\App::init();

$action = new \App\Action\Crontab\SphinxRtIndex\CatalogInsert();
if (isset($argv[1]) && $argv[1]) {
	$action->execute($argv[1]);
} else {
	$action->execute();
}
