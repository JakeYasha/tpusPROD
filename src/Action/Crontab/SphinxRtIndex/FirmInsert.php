<?php

namespace App\Action\Crontab\SphinxRtIndex;

class FirmInsert extends \App\Action\Crontab\SphinxRtIndex {

	public function __construct() {
		parent::__construct();
		$this->setPartsCount(2)
				->setLimit(1000);
	}

	public function execute($part = 1) {
		$object = new \App\Model\Firm();
		$sphinx = $this->getSphinxObject();
		$this->start();

		$i = 0;
		$total_count = $object->reader()->count();
		$one_part = ceil($total_count / $this->parts_count);
		$stop_point = (int)($one_part * $part) - 1;
		$offset = ($part - 1) * $one_part;

		while (1) {
			$items = $object->reader()
					->setLimit($this->limit, $offset)
					->setOrderBy('id ASC')
					->objects();

			if (!$items) {
				$this->end();
				break;
			}


			if ($i % 10000 === 0) {
				$sphinx = $this->getSphinxObject();
			}

			foreach ($items as $item) {
				$i++;
				//print_r("\r".$i);

				if ($i === $stop_point) {
					echo "\r\nstopped_id: ".$item->id();
					$this->end();
				}
				$item->updateRtIndex($sphinx);
			}

			$offset += $this->limit;

			if ($i % 10000 === 0) {
				echo "\r\n".$i;
			}
		}

		$this->end();
	}

}
