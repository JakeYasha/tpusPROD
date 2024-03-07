<?php

namespace App\Action\Utils;

class TestFirmUserSpeed extends \App\Action\Utils {

	public function execute() {
//		$firm = new \App\Model\Firm();
//		$items = $firm->reader()->setWhere(['AND', 'id_service = :id_service'], [':id_service' => 10])
//				->setOrderBy('id DESC')
//				->setLimit(20, 50)
//				->objects();
		//	foreach ($items as $item) {
		$item = new \App\Model\Firm(142070);
		$_SESSION['_virtual_id_firm'] = $item->id();
		app()->startTimer();
		(new \App\Action\FirmUser\Price())->execute();
		print_r($item->id().'=='.app()->endTimer(0, false).PHP_EOL);
		//}

		exit();
	}

}
