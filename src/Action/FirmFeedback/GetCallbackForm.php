<?php

namespace App\Action\FirmFeedback;

use App\Action\FirmFeedback;
use App\Model\Firm;
use App\Model\FirmFeedback\CallbackFormAdd;

class GetCallbackForm extends FirmFeedback {

	public function execute($id_firm, $id_service = null) {
		$firm = new Firm();
		if ($id_service === null) {
			$firm->getByIdFirm((int) $id_firm);
		} else {
			$firm->getByIdFirmAndIdService($id_firm, $id_service);
		}
		$form = new CallbackFormAdd();
		die($form->render($firm));
	}

}
