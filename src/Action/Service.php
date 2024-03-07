<?php

namespace App\Action;

class Service extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\Service());
	}

	public function execute() {
		$exception = (app()->location()->currentName() === 'Ярославская область' || app()->location()->currentName() === 'Ярославль') ? 13 : 10;
		$items = $this->model()->reader()
				->setWhere(['AND', '`flag_is_active` = :yes', 'id != :exception'], [':yes' => 1, ':exception' => $exception])
				->setOrderBy('`position_weight` ASC')
				->objects();

		$this->text()->getByLink('/service/');
		$keywords = [];
		foreach ($items as $it) {
			$keywords[] = $it->name();
		}
		$this->text()->setVal('metadata_key_words', implode(', ', $keywords));
		app()->metadata()->setFromModel($this->text());
		app()->breadCrumbs()->setElem($this->text()->name(), '');
        
		$this->view()
				->set('breadcrumbs', app()->breadCrumbs()->render())
				->set('items', $items)
				->set('item', $this->text())
				->set('last_changes', $this->model()->query()->setSelect("MAX(timestamp_inserting) as `time`")->setFrom(['`service`'])->select()[0]['time'])
				->setTemplate('index')
				->save();
	}

}
