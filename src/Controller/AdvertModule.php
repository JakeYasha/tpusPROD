<?php

namespace App\Controller;

use App\Classes\Controller;
use App\Model\Firm;
use App\Model\StsService;
use App\Presenter\AdvertModuleItems;

class AdvertModule extends Controller {

	public function renderFooterAdvText(Firm $firm) {
		$service = new StsService();
		$service->get($firm->id_service());

		return $this->view()
						->set('service', $service)
						->setTemplate('footer_adv_text')
						->render();
	}

	public function renderAdvertModuleBlock($id_group = null) {
		$presenter = new AdvertModuleItems();
		$presenter->findAdvertModules($id_group);

		return $presenter->renderItems();
	}

	public function renderIndexAdvertModuleBlock() {
		$presenter = new AdvertModuleItems();
		$presenter->findIndexAdvertModules();

		return $presenter->renderItems();
	}

	/**
	 * 
	 * @return \App\Model\AdvertModule
	 */
	public function model() {
		if ($this->model === null) {
			$this->model = new \App\Model\AdvertModule();
		}
		return $this->model;
	}

}
