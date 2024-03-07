<?php

namespace App\Action\FirmFeedback;

use App\Action\FirmFeedback;
use App\Model\Firm;
use App\Model\FirmFeedback\FormAdd;
use App\Model\StatObject;
use function app;

class GetFeedbackForm extends FirmFeedback {

	public function execute($id_firm, $id_service = null) {
		$get_params = app()->request()->processGetParams([
			'id_option' => 'int',
            'old' => 'int'
		]);
		$firm = new Firm();
		if ($id_service === null) {
			$firm->getByIdFirm((int) $id_firm);
		} else {
			$firm->getByIdFirmAndIdService($id_firm, $id_service);
		}

		$form = new FormAdd();

		app()->stat()->addObject(StatObject::FORM_FEEDBACK_OPEN, $firm)
				->fixResponse(false);

		die($form->render($firm, 'Отправить сообщение', $get_params['id_option'], isset($get_params['old']) ? true : false));
	}

}
