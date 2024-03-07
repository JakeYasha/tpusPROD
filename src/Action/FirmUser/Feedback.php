<?php

namespace App\Action\FirmUser;

use App\Action\FirmUser;
use App\Model\FirmFeedback;
use App\Presenter\FirmFeedbackItems;
use function app;

class Feedback extends FirmUser {

	public function execute() {
		$this->updateTimestamps('feedback');

		app()->metadata()->setTitle('Личный кабинет - сообщения');
		app()->breadCrumbs()
				->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
				->setElem('Сообщения', '/firm-user/feedback/');

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
			case 'delete' : $this->deleteFeedback();
				break;
			default : $content = $this->getFeedbackIndex();
				break;
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('content', $content)
				->setTemplate('feedback_index')
				->save();
	}

	protected function getFeedbackIndex() {
		$presenter = new FirmFeedbackItems();
		$presenter->setItemsTemplateSubdirName('firmuser');
		$presenter->setLimit(20);
		$presenter->findByFirm($this->firm(), $this->params);

		return $this->view()
						->set('filters', $this->params)
						->set('items', $presenter->renderItems())
						->set('pagination', $presenter->pagination()->render(true))
						->set('total_founded', $presenter->pagination()->getTotalRecords())
						->setTemplate('feedback_items')
						->render();
	}

	protected function deleteFeedback() {
		$id = app()->request()->processGetParams(['id' => 'int'])['id'];
		$ff = new FirmFeedback($id);
		$this->delete($ff, '/firm-user/feedback/');
	}

}
