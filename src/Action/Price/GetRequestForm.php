<?php

namespace App\Action\Price;

class GetRequestForm extends \App\Action\Price {

	public function execute() {
		$params = app()->request()->processGetParams([
			'id_price' => ['type' => 'int'],
			'type' => ['type' => 'string'],
			'sms_mode' => ['type' => 'int']
		]);


		$this->model()->get($params['id_price']);
		if ($this->model()->exists()) {
			$price_request = new \App\Model\PriceRequest();
			$price_request->setVals([
				'id_price' => $this->model()->val('id_price'),
				'id_firm' => $this->model()->val('id_firm'),
				'id_service' => $this->model()->val('id_service'),
				'brief_text' => in_array($this->model()->val('id_subgroup'), [258, 440, 267, 269, 272, 285, 284]) ? 'Записаться на услугу "' . $this->model()->name() . '"' : ($params['type'] === 'check' ? 'Уточнение информации для товара/услуги "' . $this->model()->name() . '"' : 'Заказ товара/услуги "' . $this->model()->name() . '"')
			]);
			$form = new \App\Model\PriceRequest\FormAdd($price_request);
			die($form->render(in_array($this->model()->val('id_subgroup'), [258, 440, 267, 269, 272, 285, 284]) ? 'Записаться' : ($params['type'] === 'check' ? 'Уточнение информации' : 'Заказ'), ($params['sms_mode'] === 1)));
		}

		die();
	}

}
