<?php

namespace App\Action;

class Vacancy extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\Vacancy());
	}

	public function execute() {
		$items = $this->model()->reader()
				->setWhere('`flag_is_active` = :yes', [':yes' => 1])
				->setOrderBy('`timestamp_inserting` DESC')
				->objects();

		$this->text()->getByLink('/vacancy/');
		app()->metadata()->setFromModel($this->text());
		app()->breadCrumbs()->setElem($this->text()->name(), '');

		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('items', $items)
				->set('item', $this->text())
				->setTemplate('index')
				->save();
	}

}
