<?php

namespace App\Action\Crontab\SphinxRtIndex;

class PriceInsert extends \App\Action\Crontab\SphinxRtIndex {

	public function __construct() {
		parent::__construct();
		$this->setPartsCount(4)
				->setLimit(10000);
	}

	public function execute($part = 1) {
		$price = new \App\Model\Price();
		$sphinx = $this->getSphinxObject();
		$this->start();

		$i = 0;
		$total_count = $price->reader()->count();
		$one_part = (int)ceil($total_count / $this->parts_count);
		$stop_point = (int)(($one_part * $part) - 1);
		$offset = (int)(($part - 1) * $one_part);

		while (1) {
			$items = $price->reader()
					->setWhere(['AND', 'flag_is_active = :active'], [':active' => 1])
					->setLimit($this->limit, $offset)
					->setOrderBy('id ASC')
					->objects();
			
			
			print_r($this->limit);
			print_r($offset);

			exit();


			if (!$items) {
				$this->end();
				break;
			}


			if ($i % 10000 === 0) {
				$sphinx = $this->getSphinxObject();
			}

			foreach ($items as $item) {
				$i++;
				if ($i === $stop_point) {
					echo "\r\nstopped_id: ".$item->id();
					$this->end();
				}

				$item->updateRtIndex($sphinx);
				if ($i % 100 === 0) {
					echo "\r".$i;
				}
			}

			$offset += $this->limit;
		}

		$this->end();
	}

}
