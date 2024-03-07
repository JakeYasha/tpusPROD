<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/').'/../../../config/config_app.php';
\Sky4\App::init();
ini_set('display_errors', 1);
ini_set("log_errors", 0);

/*$actions = [
    new \App\Action\Crontab\CatalogCounter(),
	new \App\Action\Crontab\StsStatistics(),
	new \App\Action\Crontab\CurrentRegionStats(),
	new \App\Action\Crontab\SiteStatistics(),
	new \App\Action\Crontab\AvgStatisticsMaker(),
	new \App\Action\Crontab\AvgStatistics727373Maker(),
	new \App\Action\Crontab\Cleaner(),
	new \App\Action\Crontab\Sitemap(),
	new \App\Action\Crontab\Notifier()
];

foreach ($actions as $action) {
	$action->execute();
}*/

$where = ['AND', 'source = :source'];
$params = [':source' => 'auto'];

$im = new \App\Model\Image();
$images = $im->reader()
        ->setWhere($where,$params)
        ->setOrderBy('timestamp_inserting DESC')
        ->objects();

$_image_ids_for_remove = [];
$_price_ids = [];
foreach($images as $image) {
    $file_path = APP_DIR_PATH . '/public' . $image->path();
    $http_path = APP_URL . $image->path();

    if (!isset($_price_ids[$image->val('id_price')])) {
        $_price_ids[$image->val('id_price')] = $image;
//        print_r('Оставляем ' . $image->val('id_price') . ' ' . $http_path . '(' . (file_exists($file_path) ? 'Есть' : 'Нету') . ') от ' . $image->val('timestamp_inserting') . PHP_EOL);
    } else {
        $image->delete();
//        print_r('Удаляем ' . $image->val('id_price') . ' ' . $http_path . '(' . (file_exists($file_path) ? 'Есть' : 'Нету') . ') от ' . $image->val('timestamp_inserting') . PHP_EOL);
    }
}

/*
$where = ['AND', 'id_firm = :id_firm', 'id_price = :id_price', 'source = :source'];
$params = [':id_firm' => (int)$image['id_firm'], ':id_price' => (int)$image['id_price'], ':source' => 'auto'];

$im = new \App\Model\Image();
$im->reader()
        ->setWhere($where, $params)
        ->setOrderBy('timestamp_inserting DESC')
        ->objects();

*/