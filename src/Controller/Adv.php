<?php

namespace App\Controller;

use App\Classes\Controller;
use App\Model\AdvertAgeLimit;
use App\Model\AdvertModule;
use App\Model\AdvertRestrictions;
use App\Model\Firm;
use App\Model\StsReklInfo;
use Sky4\Model\Utils;
use function app;

class Adv extends Controller {

	public function renderBottomBanners() {
		return $this->view()
						->setTemplate('bottom_banners')
						->set('items', app()->adv()->getBottomBanners())
						->render();
	}

	public function renderRubricsBanners() {
		return $this->view()
						->setTemplate('rubrics_banners')
						->set('items', app()->adv()->getRubricsBanners())
						->render();
	}

	public function renderTopBanners() {
		return $this->view()
						->setTemplate('top_banners')
						->set('items', app()->adv()->getTopBanners())
						->render();
	}

	public function renderFirmAdvertModules() {
		return $this->view()
						->set('items', app()->adv()->getFirmAdvertModules())
						->setTemplate('firm_advert_modules')
						->render();
	}

	public function renderMiddleBanners() {
		return $this->view()
						->setTemplate('middle_banners')
						->set('items', app()->adv()->getMiddleBanners())
						->render();
	}

	public function renderTextBanners() {
		return $this->view()
						->setTemplate('text_banners')
						->set('items', app()->adv()->getTextBanners())
						->render();
	}

	public function renderLeftBanners() {
		return $this->view()
						->set('items', app()->adv()->getLeftBanners())
						->setTemplate('left_banners')
						->render();
	}

	public function renderHeaderBanners() {
		return $this->view()
						->setTemplate('header_banners')
						->set('items', app()->adv()->getHeaderBanners())
						->render();
	}

    public function renderIndexBanners() {
		return $this->view()
						->setTemplate('header_banners')
						->set('items', app()->adv()->getIndexBanners())
						->render();
	}

    
    public function renderHeaderBannersSlider() {
		return $this->view()
						->setTemplate('header_banners_slider')
						->set('items', app()->adv()->getIndexRubricBannersSlider())
						->render();
	}

	public function renderNormalBanners() {
		return $this->view()
						->setTemplate('normal_banners')
						->set('items', app()->adv()->getNormalBanners())
						->render();
	}

	public function renderFirmTopBanner(Firm $firm) {
		return $this->view()
						->setTemplate('firm_top_banner')
						->render();
	}

	public function renderAdvertRestrictions($rekl_info_ids = [], $id_subgroups = []) {
		$result = '';
		$adv = new StsReklInfo();
		$items = [];
		if (($rekl_info_ids && $rekl_info_ids[0]) || $id_subgroups) {
			if ($rekl_info_ids && $rekl_info_ids[0]) {
				$adv = new AdvertRestrictions();
				$adv_conds = Utils::prepareWhereCondsFromArray($rekl_info_ids[0], 'id');

				$items = $adv->reader()
						->setWhere($adv_conds['where'], $adv_conds['params'])
						->objects();
			}

			$result = $this->view()
					->set('items', $items)
					->setTemplate('advert_restrictions')
					->render();
		}

		return $result;
	}

	public function renderAdvertAgeRestrictions($ids = []) {
		$result = '';
		$adv = new AdvertAgeLimit();
		if ($ids && isset($ids[0]) && $ids[0]) {
			$adv = new AdvertAgeLimit();
			$adv_conds = Utils::prepareWhereCondsFromArray($ids[0], 'id');
			$items = $adv->reader()
					->setWhere($adv_conds['where'], $adv_conds['params'])
					->objects();

			$result = $this->view()
					->set('items', $items)
					->setTemplate('advert_age_restrictions')
					->render();
		}

		return $result;
	}

	public function renderAdvertModuleDefault(AdvertModule $adv) {
		return $this->view()
						->set('item', $adv)
						->setTemplate('advert_module_default', 'advertmodule')
						->render();
	}

	public function renderAdvertModuleWide(AdvertModule $adv) {
		return $this->view()
						->set('item', $adv)
						->setTemplate('advert_module_wide', 'advertmodule')
						->render();
	}

}
