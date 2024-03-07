<?php

namespace App\Action\FirmUser\Price;

class Edit extends \App\Action\FirmUser\Price {

	public function __construct() {
		parent::__construct();

		$this->params = app()->request()->processGetParams(['id' => ['type' => 'int']]);
		app()->breadCrumbs()->setElem($this->getHeading(), '/firm-user/price/add/');
	}

	public function execute() {
		$heading = $this->getHeading();
		app()->metadata()->setTitle('Личный кабинет - ' . str()->toLower($heading));

		$form = new \App\Model\Price\FormAdd(new \App\Model\Price($this->params['id']));
		$this->view()
				->set('form', $form->setParam('step', 2)->render($heading, $this->firm()))
				->set('heading', $heading)
				->setTemplate('price_form', 'firmuser')
				->save();
	}

	protected function getHeading() {
		return 'Редактирование записи';
	}

}
