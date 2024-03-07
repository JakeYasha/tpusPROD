<?php

namespace App\Action\FirmUser;

use App\Model\FirmPromo;
use App\Model\FirmPromo\UserForm as FirmUserForm;
use App\Presenter\FirmPromoItems;
use function app;

class Promo extends \App\Action\FirmUser {

	public function execute() {
		app()->metadata()->setTitle('Личный кабинет - акции');
		app()->breadCrumbs()
				->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
				->setElem('Акции', '/firm-user/promo/');

		$this->params = app()->request()->processGetParams([
			'mode' => ['type' => 'string'],
			'id' => ['type' => 'int']
		]);

		switch ($this->params['mode']) {
			case 'add' : $content = $this->getPromoAddForm();
				break;
			case 'edit' : $content = $this->getPromoEditForm();
				break;
			case 'delete' : $this->deletePromo();
				break;
			default : $content = $this->getPromoIndex();
				break;
		}

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('has_add_btn', $this->params['mode'] === null)
				->set('content', $content)
				->setTemplate('promo_index')
				->save();
	}

	protected function getPromoAddForm() {
		app()->breadCrumbs()
				->setElem('Добавить акцию', '');
		$form = new FirmUserForm();
		$form
				->setDefaultVals()
				->setVals(['id_firm' => $this->firm()->id(), 'id_service' => $this->firm()->id_service(), 'id_city' => $this->firm()->val('id_city')]);

		return $form->render();
	}

	protected function getPromoEditForm() {
		app()->breadCrumbs()
				->setElem('Изменить акцию', '');
		$model = new FirmPromo($this->params['id']);
		$this->checkModelAccess($model);

		$form = new FirmUserForm($model);
		return $form->render();
	}

	protected function deletePromo() {
		$id = app()->request()->processGetParams(['id' => 'int'])['id'];
		$firm_promo = new FirmPromo($id);
		$this->delete($firm_promo, '/firm-user/promo/');

		return $this;
	}

	protected function getPromoIndex() {
		$promo_presenter = new FirmPromoItems();
		$promo_presenter->setItemsTemplateSubdirName('firmuser');
		$promo_presenter->setLimit(20);
		$promo_presenter->findPromosByFirm($this->firm);

		return $this->view()
						->set('items', $promo_presenter->renderItems())
						->set('pagination', $promo_presenter->pagination()->render(true))
						->set('total_founded', $promo_presenter->pagination()->getTotalRecords())
						->setTemplate('promo_items')
						->render();
	}

}
