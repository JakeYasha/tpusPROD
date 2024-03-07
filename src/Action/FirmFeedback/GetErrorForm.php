<?php

namespace App\Action\FirmFeedback;

use App\Model\FirmFeedback\FormAdd;

class GetErrorForm extends \App\Action\FirmFeedback {

	public function execute($id_firm, $id_service = null) {
		$form = new FormAdd();
		die($form->renderErrorForm('Сообщить об ошибке', $id_firm, $id_service));
	}

}
