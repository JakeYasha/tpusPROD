<?php

namespace App\Action\Request;

class Add extends \App\Action\Request {

	public function execute() {
		$form = new \App\Model\Request\FormAdd();
		app()->breadCrumbs()
				->setElem('Разместить информацию на сайте');

		$this->text()->getByLink('/request/add/');

		app()->metadata()->setFromModel($this->text());
        app()->setUseAgreement(true);

		$this->view()
				->set('breadcrumbs', app()->breadCrumbs()->render())
				->set('h1', $this->text()->val('name'))
				->set('text', $this->text()->val('text'))
				->set('form', $form->render($this->text()->name()))
				->setTemplate('add')
				->save();
	}

}
