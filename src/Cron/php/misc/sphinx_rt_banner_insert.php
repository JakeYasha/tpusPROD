<?php

require_once rtrim(__DIR__, '/').'/../../../../config/config_app.php';
\Sky4\App::init();

$banner = new \App\Model\Banner();
$items = $banner->reader()->objects();

$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
foreach ($items as $item) {
	$item->updateRtIndex();
}

echo 'done';
