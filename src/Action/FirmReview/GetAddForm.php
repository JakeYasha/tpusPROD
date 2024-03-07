<?php

namespace App\Action\FirmReview;

use App\Action\FirmReview;
use App\Model\Firm;
use App\Model\FirmReview\FormAdd;

class GetAddForm extends FirmReview {

	public function execute($id_firm = 0) {
		$form = new FormAdd();
		$firm = new Firm($id_firm);
		die($form->render('Добавить отзыв', $firm));
	}

}
