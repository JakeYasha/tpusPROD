<?php

namespace App\Action\Crontab;

class SphinxRtIndex extends \App\Action\Crontab {

	protected $parts_count = 1;
	protected $limit = 1000;

	public function execute() {
		throw new \Sky4\Exception();
	}

	protected function getSphinxObject() {
		return \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
	}

	protected function start() {
		echo "\r\nStarted: " . date('d.m.Y H:i:s');
		return $this;
	}

	protected function end() {
		echo "\r\nEnded: " . date('d.m.Y H:i:s');
		exit();
	}

	protected function setPartsCount($count) {
		$this->parts_count = (int) $count;
		return $this;
	}

	protected function setLimit($limit) {
		$this->limit = (int) $limit;
		return $this;
	}

	public function getPartsCount() {
		return $this->parts_count;
	}

}
