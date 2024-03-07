<?php

namespace App\Action\Page;

use App\Classes\App;
use App\Model\StsService;
use function app;

class Handbook extends \App\Action\Page {

	public function execute($id = 0) {
		$bc = app()->breadCrumbs()
				->setSeparator('<i>/</i>')
				->setElem('Электронный справочник')
				->render();

		app()->metadata()->setTitle('Электронный справочник города Ярославль, сайта tovaryplus.ru');
		app()->metadata()->setMetatag('description', 'Электронный справочник содержит информацию о фирмах и услугах города Ярославль от сайта Товары+');


		$this->view()
				->set('breadcrumbs', $bc)
				->setTemplate('handbook')
				->save();
	}

}
