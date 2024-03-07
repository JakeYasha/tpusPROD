<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/').'/../../../config/config_app.php';
\Sky4\App::init();

//$model = new \App\Model\Banner();
//$items = $model->reader()->objects();
//
//$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
//foreach ($items as $item) {
//	$item->updateRtIndex();
//}
//$model = new \App\Model\BrandPrice();
//$items = $model->reader()->objects();
//
//$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
//foreach ($items as $item) {
//	$item->updateRtIndex();
//}

//$model = new \App\Model\FirmType();
//$items = $model->reader()->objects();
//
//$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
//foreach ($items as $item) {
//	$item->updateRtIndex();
//}

//$model = new \App\Model\SuggestPrice();
//$items = $model->reader()->objects();
//
//$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
//foreach ($items as $item) {
//	$item->updateRtIndex();
//}

$model = new \App\Model\PriceCatalog();
$items = $model->reader()->objects();

$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
foreach ($items as $item) {
	$item->updateRtIndex();
}