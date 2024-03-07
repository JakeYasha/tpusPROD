<?php

class ACrontabCleaner extends ACrontabAction {

	public function run() {
		$this->clearTempImages()
				->disableVideos()
				->disablePromos();

		return parent::run();
	}

	public function disableVideos() {
		$firm_video = new FirmVideo();
		$items = $firm_video->getAll();

		foreach ($items as $video) {
			$firm = new Firm();
			$firm->getByIdFirm($video->id_firm());
			if ($firm->isBlocked()) {
				$video->updateOnlyVals([
					'flag_is_active' => 0
				]);
			} else {
				$video->updateOnlyVals([
					'flag_is_active' => 1
				]);
			}
		}

		return $this;
	}

	public function disablePromos() {
		$firm_promo = new FirmPromo();
		$items = $firm_promo->getAll();

		foreach ($items as $promo) {
			$firm = new Firm();
			$firm->getByIdFirm($promo->id_firm());
			if ($firm->isBlocked()) {
				$promo->updateOnlyVals([
					'flag_is_active' => 0
				]);
			} else {
				$promo->updateOnlyVals([
					'flag_is_active' => 1
				]);
			}
		}

		return $this;
	}

	public function clearTempImages() {
		$image = new Image();
		$all = $image->setWhere(['AND', '`source` = :source', '`timestamp_inserting` < :shift'], [':source' => 'temp', ':shift' => \Sky4\Helper\DeprecatedDateTime::shiftDays(-1)])
				->getAll();

		foreach ($all as $object) {
			$object->delete();
		}

		$file = new FirmFile();
		$all = $file->setWhere(['AND', 'flag_is_temp = :temp', 'timestamp_inserting < :shift'], [':temp' => 1, ':shift' => \Sky4\Helper\DeprecatedDateTime::shiftDays(-1)])
				->getAll();

		foreach ($all as $object) {
			$object->delete();
		}

		return $this;
	}

}
