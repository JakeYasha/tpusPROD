<?php

namespace App\Action\Crontab;
use \App\Action\Crontab\AvgStatisticsMakerTest;

ini_set('memory_limit', '8G');
require_once rtrim(__DIR__, '/').'/../../../config/config_app.php';
\Sky4\App::init();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//
$pc = new \App\Model\PriceCatalog();

$catalogs = $pc->reader()
        ->objects();

$j = 0;
$_j = 0;
foreach($catalogs as $catalog) {
    $j++;
    if (preg_match('~([^0-9\]\[]+)|(\[{2,})|(\]{2,})~', $catalog->val('path'))) {
        $_j++;
        $path = preg_replace('~([^0-9\]\[]+)|(\[{2,})|(\]{2,})~', '', $catalog->val('path'));
        app()->db()->query()->setText('UPDATE `price_catalog` SET `path` = :path WHERE id = :id')->execute([':path' => $path, ':id' => $catalog->id()]);
        $catalog->updateRtIndex();
    }
}

echo PHP_EOL . 'DONE (PrcieCatalog.Count = ' . $_j . '/' . $j . ')';

$pcp = new \App\Model\PriceCatalogPrice();

$catalogPrices = $pcp->reader()
        ->objects();

$i = 0;
$_i = 0;
foreach($catalogPrices as $catalogPrice) {
    $i++;
    if (preg_match('~([^0-9\]\[]+)|(\[{2,})|(\]{2,})~', $catalogPrice->val('path'))) {
        $_i++;
        $path = preg_replace('~([^0-9\]\[]+)|(\[{2,})|(\]{2,})~', '', $catalogPrice->val('path'));
        app()->db()->query()->setText('UPDATE `price_catalog_price` SET `path` = :path WHERE id = :id')->execute([':path' => $path, ':id' => $catalogPrice->id()]);
    }
}
echo PHP_EOL . 'DONE (PrcieCatalogPrice.Count = ' . $_i . '/' . $i . ')';