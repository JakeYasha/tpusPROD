<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/').'/../../../../config/config_app.php';
\Sky4\App::init();


$price = new \App\Model\Price();
$price->updateRtIndex();
$items = $price->reader()->setWhere(['AND', 'flag_is_active != :nil', 'flag_is_image_exists = :one'], [':nil' => 0, ':one' => 1])
		->objects();

$i = 0;
echo count($items).PHP_EOL;
foreach ($items as $item) {
	$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
	$item->updateRtIndex($sphinx);
	echo "\r".++$i;
}