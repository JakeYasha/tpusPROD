<?php

namespace App\Controller;

use App\Classes\Controller;
use App\Model\FirmPromo as FirmPromoModel;
use App\Model\StatObject;
use App\Presenter\FirmPromoItems;
use Sky4\Exception;
use Sky4\Model\Utils;
use function app;

class FirmPromo extends Controller {

	public function renderPromoBlock($id_group = null) {
		$presenter = new FirmPromoItems();
		$presenter->findFirmPromos($id_group);

		return $presenter->renderItems();
	}
    
    public function renderIndexPromoBlock() {
		$presenter = new FirmPromoItems();
		$presenter->findIndexFirmPromos();

		return $presenter->renderItems();
	}

	public function renderPromoItem($id, $link) {
		$this->model()->get($id);
		if ($this->model()->exists() && (($this->model()->isActual() && (int) $this->model()->val('flag_is_active') === 1) || app()->firmUser()->exists())) {
			app()->stat()->addObject(StatObject::PROMO_SHOW, $this->model());
			app()->breadCrumbs()
					->setElemBottom('Все акции фирмы', app()->linkFilter($link, ['id_promo' => null, 'mode' => 'promo']))
					->setElemBottom($this->model()->name(), $link);

			app()->metadata()->setStripped($this->model(), $this->model()->name() . ' - выгодное предложение' . app()->location()->currentName('prepositional'), $this->model()->name() . ', акции, скидки' . app()->location()->currentName('genitive'), ($this->model()->val('brief_text') ? $this->model()->val('brief_text') : $this->model()->name()) . ' - выгодные скидки и акции ' . app()->location()->currentName('genitive'));

			$files = Utils::getObjectsByIds(Utils::getFirstCompositeId($this->model()->val('image')));
			$image_key = Utils::getFirstCompositeId($this->model()->val('image'));

			$item = FirmPromoModel::prepare($this->model(), isset($files[$image_key]) ? $files[$image_key]->iconLink('-320x180') : false, isset($files[$image_key]) ? $files[$image_key]->iconLink() : false);

			$presenter = new FirmPromoItems();
			$presenter->findSameFirmPromos($this->model()->id_firm(), $this->model()->id());

			return $this->view()
							->set('item', $item)
							->set('another_items', $presenter->renderItems())
							->setTemplate('item')
							->render();
		} else {
			throw new Exception(Exception::TYPE_BAD_URL);
		}
	}

	/**
	 * 
	 * @return FirmPromoModel
	 */
	public function model() {
		if ($this->model === null) {
			$this->model = new FirmPromoModel();
		}
		return $this->model;
	}

}
