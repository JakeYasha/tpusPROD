<?php

namespace App\Action\Page;

use App\Classes\App;
use App\Model\StsService;
use function app;

class Issue extends \App\Action\Page {

	public function execute($id = 0) {
		$bc = app()->breadCrumbs()
				->setSeparator('<i>/</i>')
				->setElem('Электронный справочник')
				->render();

		app()->metadata()->setTitle('Выпуск новостей #1');
		app()->metadata()->setMetatag('description', 'Выпуск новостей содержит информацию о фирмах и услугах города Ярославль от сайта Товары+');
		/* Сделать через GET-параметр получение номера выпуска */

		$this->view()
				->set('breadcrumbs', $bc)
				->setTemplate('issue')
				->save();
	} 

}
