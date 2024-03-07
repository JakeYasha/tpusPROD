<?php

namespace App\Action\Request;

class Result extends \App\Action\Request {

	public function execute($type = '') {
		app()->breadCrumbs()
				->setSeparator('<i>/</i>')
				->setElem('Разместить информацию на сайте', '/request/add/');

		if ($type === 'success') {
			app()->breadCrumbs()->setElem('Форма отправлена');
			$this->view()
					->setTemplate('success');
		} else {
			app()->breadCrumbs()->setElem('Ошибка');
			$this->view()
					->setTemplate('fail');
		}

		$this->view()
				->set('breadcrumbs', app()->breadCrumbs()->render())
				->save();
	}

}
