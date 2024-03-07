<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/').'/../../../config/config_app.php';
\Sky4\App::init();

$firms = (new \App\Model\Firm())->reader()->setSelect(['id'])->objects();

$i = 0;
foreach ($firms as $firm) {
	$image = new \App\Model\Image();
	$image->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_price= :nil'], [':id_firm' => $firm->id(), ':nil' => 0])
			->setOrderBy('CAST(`source` AS CHAR) ASC, `timestamp_inserting` DESC')
			->objectByConds();

	if ($image->exists()) {
		$firm->query()
				->setUpdate('`'.(string)$firm->table().'`')
				->setSet(['file_logo' => '/image/'.$image->val('file_subdir_name').'/'.$image->val('file_name').'.'.$image->val('file_extension')])
				->setWhere(['AND', 'id = :id'], [':id' => $firm->id()])
				->setLimit(1)
				->update();
	}

	echo "\r".$i++;
}

