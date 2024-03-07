<?php

namespace App\Action\FirmUser;

use App\Model\AdvertModule as AdvertModuleModel;
use App\Presenter\AdvertModuleItems;
use function app;

class AdvertModule extends \App\Action\FirmUser {

	public function execute() {
		$firm_manager_exists = app()->firmManager()->exists();
		app()->metadata()->setTitle('Личный кабинет - рекламные модули');
		app()->breadCrumbs()
				->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
				->setElem('Рекламные модули', '/firm-user/advert-module/');

		$this->params = app()->request()->processGetParams([
			'mode' => 'string',
			'id' => 'int'
		]);

		switch ($this->params['mode']) {
			case 'add' : $content = $this->getAdvertModuleAddForm();
				break;
			case 'edit' : $content = $this->getAdvertModuleEditForm();
				break;
			case 'delete' : $this->deleteAdvertModule();
				break;
			default : $content = $this->getAdvertModuleIndex();
				break;
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('has_add_btn', $firm_manager_exists && $this->params['mode'] === null)
				->set('content', $content)
				->setTemplate('advert_module_index')
				->save();
	}

	private function getAdvertModuleAddForm() {
		if (!app()->firmManager()->exists()) {
			app()->response()->redirect('/firm-user/advert-module/');
		}
		app()->breadCrumbs()
				->setElem('Добавить модуль', '');

		$form = new \App\Model\AdvertModule\UserForm();
		$form
				->setDefaultVals()
				->setVals(['id_firm' => $this->firm()->id(), 'id_service' => $this->firm()->id_service(), 'id_city' => $this->firm()->val('id_city')]);

		return $form->render();
	}

	private function getAdvertModuleEditForm() {
		if (!app()->firmManager()->exists()) {
			app()->response()->redirect('/firm-user/advert-module/');
		}
		app()->breadCrumbs()
				->setElem('Изменить модуль', '');
		$model = new AdvertModuleModel($this->params['id']);
		$this->checkModelAccess($model);

		$form = new \App\Model\AdvertModule\UserForm($model);
		return $form->render();
	}

	private function deleteAdvertModule() {
		if (!app()->firmManager()->exists()) {
			app()->response()->redirect('/firm-user/advert-module/');
		}
		$id = app()->request()->processGetParams(['id' => 'int'])['id'];
		$advert_module = new AdvertModuleModel($id);
		$this->delete($advert_module, '/firm-user/advert-module/');
	}

	private function getAdvertModuleIndex() {
		$advert_module_presenter = new AdvertModuleItems();
		$advert_module_presenter->setItemsTemplateSubdirName('firmuser');
		$advert_module_presenter->setLimit(20);
		$advert_module_presenter->findAdvertModulesByFirm($this->firm);

		return $this->view()
						->set('items', $advert_module_presenter->renderItems())
						->set('pagination', $advert_module_presenter->pagination()->render(true))
						->set('total_founded', $advert_module_presenter->pagination()->getTotalRecords())
						->setTemplate('advert_module_items')
						->render();
	}

}
