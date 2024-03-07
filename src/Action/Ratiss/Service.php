<?php

namespace App\Action\Ratiss;
use App\Model\AdvText;

class Service extends \App\Action\Ratiss {

	public function execute($id_service = null) {
		$services = new \App\Model\StsService();
		$service = $id_service > 0 ? $services->get($id_service) : app()->stsService();

		if (!$service->exists()) {
			throw new \Sky4\Exception();
		}

		$region = new \App\Model\StsRegionCountry();
		$region->reader()
				->setWhere(['AND', '`id_region_country` = :id_region_country', '`id_country` = :id_country'], [
					':id_region_country' => $service->val('id_region_country'),
					':id_country' => $service->val('id_country')
				])->objectByConds();

		app()->breadCrumbs()
				->setElem('TovaryPlus ' . str()->firstCharToUpper(str()->toLower($region->name())));

		app()->metadata()
				->setMetatag('description', 'Мы размещаем рекламу компаний региона ' . str()->firstCharToUpper(str()->toLower($region->name())) . ' на сайте tovaryplus.ru, обращайтесь')
				->setMetatag('keywords', $service->name() . ' - представительство сайта tovaryplus.ru для региона ' . str()->firstCharToUpper(str()->toLower($region->name())). ', товары плюс ' . str()->firstCharToUpper(str()->toLower($region->name())))
				->setTitle($service->name() . ' - представительство сайта tovaryplus.ru для региона ' . str()->firstCharToUpper(str()->toLower($region->name())));

		$coords = array();
		if ($service->hasAddress()) {
			app()->setUseMap(true);
			$coords = \App\Classes\YandexMaps::geocode($service->val('address'));
		}
                $adv_text = new AdvText();
		return $this->view()
						->set('bread_crumbs', app()->breadCrumbs()->render())
						->set('coords', $coords)
						->set('region', str()->firstCharToUpper(str()->toLower($region->name())))
                                                ->set('position', $adv_text->getByUrl(app()->location()->linkPrefix() . app()->request()->getRequestUri()))
						->set('item', $service)
						->setTemplate('service')
						->save();
	}

}
