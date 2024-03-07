<?php

namespace App\Action\FirmUser\Price;

class Add extends \App\Action\FirmUser\Price {

	private $catalog = null;

	public function __construct() {
		parent::__construct();

		$this->params = app()->request()->processGetParams(['step' => ['type' => 'int'], 'id_catalog' => ['type' => 'int']]);
		app()->breadCrumbs()->setElem('Добавление предложения', '/firm-user/price/add/');
	}

	public function execute() {
		$heading = $this->getHeading();
		app()->metadata()->setTitle('Личный кабинет - '.str()->toLower($heading));

		if ((int)$this->params['step'] === 2) {
			app()->breadCrumbs()->setElem($heading, '/firm-user/price/add/?step=2&id_catalog='.$this->params['id_catalog']);
		}
		
		$form = new \App\Model\Price\FormAdd(new \App\Model\Price(), $this->params);

		$this->view()
				->set('form', $form->render($this->getHeadingTop(), $this->firm(), $this->catalog()))
				->set('heading', $heading)
				->setTemplate('price_form', 'firmuser')
				->save();
	}

	protected function getHeading() {
		$heading = 'Добавление';
		if ($this->catalog()->exists()) {
			switch ((int)$this->catalog()->val('id_group')) {
				case 44 : $heading .= ' услуги';
					break;
				case 22 : $heading .= ' оборудования';
					break;

				default : $heading .= ' товара';
			}
		}

		return $heading;
	}

	protected function getHeadingTop() {
		$heading = 'Добавление';

		if ($this->catalog()->exists()) {
			switch ((int)$this->catalog()->val('id_group')) {
				case 44 : $heading .= ' услуги';
					break;
				case 22 : $heading .= ' оборудования';
					break;

				default : $heading .= ' товара';
			}

			$heading .= ' в раздел "'.$this->catalog()->name().'"';
		}

		return $heading;
	}

	/**
	 * 
	 * @return \App\Model\PriceCatalog
	 */
	public function catalog() {
		if ($this->catalog === null) {
			$this->catalog = new \App\Model\PriceCatalog();
			if ($this->params['id_catalog'] !== null) {
				$this->catalog->reader()->object($this->params['id_catalog']);
			}
		}

		return $this->catalog;
	}

}
