<?php

namespace App\Action;

class PhotoContest extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\PhotoContest());
	}

	public function execute() {
		$this->text()->getByLink('photo-contest/index');

		$_items = $this->model()
				->reader()
				->setSelect($this->model()->getListOfFields())
				->setWhere(['AND', 'flag_is_active = :flag_is_active'], [':flag_is_active' => 1])
				->setOrderBy('flag_is_working DESC, timestamp_inserting DESC')
				->objects();

		$items = [];
		$images = $this->model()->imageComponent()->getObjectsFromObjects($_items);
		foreach ($_items as $item) {
			$items[] = $item->prepare('list', $images);
		}

		app()->breadCrumbs()->setElem('Фото-конкурсы', '/photo-contest/');
		app()->metadata()->setFromModel($this->text());
		return $this->view()
						->set('bread_crumbs', app()->breadCrumbs()->render())
						->set('items', $items)
						->set('text', $this->text())
						->setTemplate('index')
						->save();
	}

	/**
	 * 
	 * @return \App\Model\PhotoContest
	 */
	public function model() {
		return parent::model();
	}

}
