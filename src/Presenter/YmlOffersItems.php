<?php

namespace App\Presenter;

use App\Model\Firm;
use App\Model\FirmPromo;
use App\Model\FirmPromoCatalog;
use App\Model\PriceCatalog;
use Sky4\Helper\DeprecatedDateTime as DateTime;
use Sky4\Model\Utils;

class YmlOffersItems extends Presenter {

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('yml_offers_presenter_items')
				->setItemsTemplateSubdirName('ymloffer')
				->setModel(new \App\Model\YmlOffer());
		return true;
	}

	/**
	 * @return \App\Classes\Pagination
	 */
	public function pagination() {
		if ($this->pagination === null) {
			$this->pagination = new \App\Classes\Pagination();
		}
		return $this->pagination;
	}

	public function getPage() {
		$params = app()->request()->processGetParams(['page' => 'int']);
		if ($params['page']) return $params['page'];
		return 1;
	}

	public function find($params) {
		if ($params['id_firm'] === null || !$params['id_firm']) {
			return $this;
		}

		$_where = ['AND', 'id_firm = :id_firm'];
		$_params = [':id_firm' => (int) $params['id_firm']];

		$this->pagination()
				->setLimit(50)
				->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
				->setLink('/firm-manager/yml/offers/')
				->setLinkParams($params)
				->setPage($this->getPage())
				->calculateParams()
				->renderElems();

		$_items = $this->model()->reader()
				->setWhere($_where, $_params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('name ASC')
				->objects();

		$items = $this->model()->prepare($_items);

		$this->items = $items;

		return $this;
	}

}
