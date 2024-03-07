<?php

namespace App\Action\FirmUser;

use App\Model\PriceRequest;
use App\Presenter\PriceRequestItems;
use function app;

class Request extends \App\Action\FirmUser {

	public function execute() {
		$this->updateTimestamps('request');

		app()->metadata()->setTitle('Личный кабинет - заказы');
		app()->breadCrumbs()
				->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
				->setElem('Заказы', '/firm-user/request/');

		$this->params = app()->request()->processGetParams([
			'mode' => 'string',
			'id' => 'int',
			'query' => 'string'
		]);

		if ($this->params['query'] !== null && $this->params['query']) {
			app()->breadCrumbs()
					->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
					->setElem('Поиск', '');
		}

		switch ($this->params['mode']) {
//			case 'send' : $content = $this->getRequestSendForm();
//				break;
			case 'delete' : $this->deleteRequest();
				break;
			default : $content = $this->getRequestIndex();
				break;
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('content', $content)
				->setTemplate('request_index')
				->save();
	}

	private function getRequestIndex() {
		$presenter = new PriceRequestItems();
		$presenter->setItemsTemplateSubdirName('firmuser');
		$presenter->setLimit(20);
		$presenter->findByFirm($this->firm, $this->params);

		return $this->view()
						->set('filters', $this->params)
						->set('items', $presenter->renderItems())
						->set('pagination', $presenter->pagination()->render(true))
						->set('total_founded', $presenter->pagination()->getTotalRecords())
						->setTemplate('request_items')
						->render();
	}

	private function deleteRequest() {
		$id = app()->request()->processGetParams(['id' => 'int'])['id'];
		$price_request = new PriceRequest($id);
		$this->delete($price_request, '/firm-user/request');
	}

}
