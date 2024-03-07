<?php

ini_set('memory_limit', '512M');
require_once rtrim(__DIR__, '/').'/../../../config/config_app.php';
\Sky4\App::init();

$price_ids = [3740694, 3658501, 3395548, 2738754, 3395556];

$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
foreach ($price_ids as $id) {
	app()->db()->query()->setText('DELETE FROM `price` WHERE id = :id_price')->execute([':id_price' => $id]);
	app()->db()->query()->setText('DELETE FROM `price_catalog_price` WHERE id_price = :id_price')->execute([':id_price' => $id]);
	$sphinx->delete()
			->from(SPHINX_PRICE_INDEX)
			->where('id', '=', intval($id))
			->execute();
}

echo 'done';
exit();
