<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();
ini_set('display_errors', 1);
ini_set("log_errors", 0);
error_reporting(E_ALL);

ini_set('max_execution_time', -1);

$price = new \App\Model\Price();
$offset = -10000;
while(1) {
	$offset += 10000;
    $items = $price->reader()
            ->setWhere(['AND', 'flag_is_active = :active'], [':active' => 1])
			->setLimit(10000, $offset)
            ->objects();

    if (!$items) {
		break;
	}
    
    $sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
    foreach ($items as $item) {
        if ($item->exists()) {
            try {
                $item->updateRtIndex();
            } catch (Exception $ex) {
                var_dump($ex);
            }
        } else {
            echo "\r\nnot exists _id: " . $item->id();
        }
    }
}
exit();
