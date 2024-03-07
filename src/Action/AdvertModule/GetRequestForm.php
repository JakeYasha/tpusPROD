<?php

namespace App\Action\AdvertModule;

use App\Model\AdvertModule\RequestFormAdd;
use App\Model\AdvertModuleRequest;
use Sky4\Exception;
use function app;

class GetRequestForm extends \App\Action\AdvertModule {

	public function execute() {
		$params = app()->request()->processGetParams([
			'id_advert_module' => ['type' => 'int'],
			'type' => ['type' => 'string']
		]);

		$this->model()->reader()->object($params['id_advert_module']);
		if ($this->model()->exists()) {
			$amr = new AdvertModuleRequest();
			$amr->setVals([
				'id_advert_module' => $params['id_advert_module'],
				'brief_text' => ($this->model()->val('callback_btn_name') == 'order' ? 'Заказ по рекламному модулю: ' : 'Запись по рекламному модулю: ') . $this->model()->val('header') . '"'
			]);

			$form = new RequestFormAdd($amr);

			die($form->render($this->model()->val('callback_btn_name') == 'order' ? 'Заказ' : 'Запись'));
		}

		throw new Exception();
	}

}
