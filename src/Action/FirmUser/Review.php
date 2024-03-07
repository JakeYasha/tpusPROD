<?php

namespace App\Action\FirmUser;

use App\Model\FirmReview;
use App\Model\FirmReview\FirmUserForm;
use App\Presenter\FirmReviewItems;
use function app;

class Review extends \App\Action\FirmUser {

	public function execute() {
		$this->updateTimestamps('review');

		app()->metadata()->setTitle('Личный кабинет - отзывы');
		app()->breadCrumbs()
				->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
				->setElem('Отзывы', '/firm-user/review/');

		$this->params = app()->request()->processGetParams([
			'mode' => 'string',
			'id' => 'int'
		]);

		switch ($this->params['mode']) {
			case 'edit' : $content = $this->getReviewEditForm();
				break;
			case 'delete' : $this->deleteReview();
				break;
			default : $content = $this->getReviewIndex();
				break;
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('content', $content)
				->setTemplate('review_index')
				->save();
	}

	protected function getReviewEditForm() {
		app()->breadCrumbs()
				->setElem('Ответить на отзыв', '');

		$model = new FirmReview($this->params['id']);
		$this->checkModelAccess($model);
		$form = new FirmUserForm($model);
		return $form->render();
	}

	protected function deleteReview() {
		$id = app()->request()->processGetParams(['id' => 'int'])['id'];
		$firm_review = new FirmReview($id);
		if (!app()->firmManager()->exists()) {
			app()->response()->redirect('/firm-user/review/');
		}
		$this->delete($firm_review, '/firm-user/review');
	}

	protected function getReviewIndex() {
		$presenter = new FirmReviewItems();
		$presenter->setItemsTemplateSubdirName('firmuser');
		$presenter->setLimit(20);
		$presenter->findByFirm($this->firm);

		return $this->view()
						->set('items', $presenter->renderItems())
						->set('pagination', $presenter->pagination()->render(true))
						->set('total_founded', $presenter->pagination()->getTotalRecords())
						->setTemplate('review_items')
						->render();
	}

}
