<?php

namespace App\Action\Price;

class Byfirm extends \App\Action\Price {

	public function execute() {
		$firm = new \App\Model\Firm();
		$firm->getByIdFirm((int) $id_firm);
		if ($firm->exists()) {
			app()->response()->redirect('/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/?mode=price');
		}

		throw new \Sky4\Exception();
	}

}
