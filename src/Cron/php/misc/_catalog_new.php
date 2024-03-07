<?php

require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();

$pcp = new \App\Model\PriceCatalogPrice();
$offset = -10000;
while (1) {
	$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
	$offset += 10000;
	$items = $pcp->reader()
			->setLimit(10000, $offset)
			->objects();

	if (!$items) {
		break;
	}

	foreach ($items as $item) {
		$item->updateRtIndex($sphinx);
	}

	echo "\r" . $offset;
}