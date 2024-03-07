<?php

namespace App\Action\FirmUser;

use App\Model\FirmVideo;
use App\Model\FirmVideo\UserForm as FirmUserForm;
use App\Presenter\FirmVideoItems;
use function app;

class Video extends \App\Action\FirmUser {

	public function execute() {
		app()->metadata()->setTitle('Личный кабинет - видео');
		app()->breadCrumbs()
				->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
				->setElem('Видеоблог', '/firm-user/video/');

		$this->params = app()->request()->processGetParams([
			'mode' => ['type' => 'string'],
			'id' => ['type' => 'int']
		]);

		switch ($this->params['mode']) {
			case 'add' : $content = $this->getVideoAddForm();
				break;
			case 'edit' : $content = $this->getVideoEditForm();
				break;
			case 'delete' : $this->deleteVideo();
				break;
			default : $content = $this->getVideoIndex();
				break;
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('has_add_btn', $this->params['mode'] === null)
				->set('content', $content)
				->setTemplate('video_index')
				->save();
	}

	protected function getVideoAddForm() {
		app()->breadCrumbs()
				->setElem('Добавить видео', '');
		$form = new FirmUserForm();
		$form
				->setDefaultVals()
				->setVals(['id_firm' => $this->firm()->id(), 'id_service' => $this->firm()->id_service(), 'id_city' => $this->firm()->val('id_city')]);

		return $form->render();
	}

	protected function getVideoEditForm() {
		app()->breadCrumbs()
				->setElem('Изменить видео', '');

		$model = new FirmVideo($this->params['id']);
		$this->checkModelAccess($model);

		$form = new FirmUserForm($model);
		return $form->render();
	}

	protected function deleteVideo() {
		$id = app()->request()->processGetParams(['id' => 'int'])['id'];
		$firm_video = new FirmVideo($id);
		$this->delete($firm_video, '/firm-user/video/');
	}

	protected function getVideoIndex() {
		$presenter = new FirmVideoItems();
		$presenter->setItemsTemplateSubdirName('firmuser');
		$presenter->setLimit(20);
		$presenter->findByFirm($this->firm);

		return $this->view()
						->set('items', $presenter->renderItems())
						->set('pagination', $presenter->pagination()->render(true))
						->set('total_founded', $presenter->pagination()->getTotalRecords())
						->setTemplate('video_items')
						->render();
	}

}
