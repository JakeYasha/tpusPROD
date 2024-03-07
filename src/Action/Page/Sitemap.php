<?php

namespace App\Action\Page;

use App\Classes\App;
use App\Model\StsService;
use function app;

class Sitemap extends \App\Action\Page {

	public function execute($id = 0) {
		$bc = app()->breadCrumbs()
				->setSeparator('<i>/</i>')
				->setElem('Карта сайта')
				->render();

		$stat = new \App\Action\Statistics\Common();
		$data = $stat->execute(true);
		app()->metadata()->setTitle('Карта сайта tovaryplus.ru');
		app()->metadata()->setMetatag('description', 'Карта сайта содержит информацию о количестве товаров, услуг и фирм размещенных в базе данных сайта на текущий момент в разрезе городов');

		$matrix = $data['matrix'];

		$service = new StsService();
		$services = $service
				->setWhere(['AND', 'exist = :exist'], [':exist' => 1])
				->selectAllWithKey('id_region_country');

		$this->view()
				->set('breadcrumbs', $bc)
				->set('cities', $data['cities'])
				->set('counts', $data['counts'])
				->set('matrix', $data['matrix'])
				->set('regions', $data['regions'])
				->set('services', $services)
				->setTemplate('sitemap')
				->save();
	}

}
