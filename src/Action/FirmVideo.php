<?php

namespace App\Action;

class FirmVideo extends \App\Classes\Action {

	public function execute() {
		$this->text()->getByLink('firm/videos');

		app()->metadata()
				->setFromModel($this->text());

		app()->breadCrumbs()
				->setElem('Каталог фирм ' . app()->location()->currentName('genitive'), app()->link('/firm/catalog/'))
				->setElem('Новые видеоролики организаций', app()->link('/firm-video/'));

		//
		$presenter = new \App\Presenter\FirmVideoItems();
		$presenter->find();
		app()->metadata()->set($this->text(), '', '', '', true, $presenter->pagination());

		return $this->view()
						->set('items', $presenter->renderItems())
						->set('pagination', $presenter->pagination()->render())
						->set('bread_crumbs', app()->breadCrumbs()->render())
						->set('text', app()->metadata()->replaceLocationTemplates($this->text()->val('text')))
						->setTemplate('index')
						->save();
	}

}
