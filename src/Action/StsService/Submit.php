<?php

namespace App\Action\StsService;

class Submit extends \App\Action\StsService {

	public function execute() {
		if (app()->firmManager()->exists()) {
			$service = new \App\Model\StsService($_POST['id_service']);
			if ((int) $service->val('id_service') === app()->firmManager()->id_service()) {
				if ($service->exists()) {
					$form = new \App\Model\StsService\FormAdd($service);
					$form->setInputVals($_POST);
					$form->model()->update($form->getVals());
					app()->response()->redirect('/firm-manager/sts-service/success/');
				}
			}
		}
		app()->response()->redirect('/firm-manager/sts-service/');
	}

}
