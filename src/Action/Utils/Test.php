<?php

namespace App\Action\Utils;

use Foolz\SphinxQL\SphinxQL;

class Test extends \App\Action\Utils {

	public function execute() {
		//$_SESSION['test'] = 'abcd';
		print_r($_SESSION['test']);

		exit();
		$rows = app()->db()->query()->setText('SHOW TABLES')->fetch();
		$items = [];
		foreach ($rows as $row) {
			$rrow = null;
			try {
				$rrow = app()->db()->query()->setText('SHOW INDEX FROM '.$row['Tables_in_tovaryplus_new'])->fetch();
			} catch (\Sky4\Exception $exc) {
				;
			}
			
			if($rrow) {
				$items[$row['Tables_in_tovaryplus_new']] = $rrow;
			}
		}

		print_r($items);
		exit();




		exit();
		$object = new \App\Model\SuggestPrice();
		$offset = 0;
		while (1) {
			$items = $object->reader()
					->setLimit(5000, $offset)
					->setOrderBy('id ASC')
					->objects();

			if (!$items) {
				break;
			}

			$sphinx = SphinxQL::create(app()->getSphinxConnection());
			foreach ($items as $item) {
				$item->updateRtIndex($sphinx);
			}

			$offset += 5000;
			echo "\r".$offset;
		}



		echo 'done';
	}

}
