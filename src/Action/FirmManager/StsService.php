<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\StsService as StsServiceModel;
use App\Model\StsService\FormAdd;
use function app;

class StsService extends FirmManager {

	public function execute($mode = '') {
		if (app()->firmManager()->isSuperMan()) {
			if ($mode === 'success') {
				$this->view()
						->set('form', app()->chunk()->set('message', 'Данные успешно сохранены!')->render('forms.common_form_success'))
						->setTemplate('default')
						->save();
			} else {
				app()->breadCrumbs()->setElem('О представительстве', self::link('/sts-service/'));
				$form = new FormAdd(new StsServiceModel(app()->firmUser()->id_service()));

				$this->view()
						->set('bread_crumbs', app()->breadCrumbs()->render(true))
						->set('form', $form->render())
						->setTemplate('sts_service', 'firmmanager')
						->save();
			}
		}

		return $this;
	}

}
