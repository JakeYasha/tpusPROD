<?php

namespace App\Presenter;

use App\Classes\Pagination;
use App\Model\Firm;
use function app;


class PriceRequestItems extends Presenter {

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('price_request_presenter_items')
				->setItemsTemplateSubdirName('firmreview')
				->setModelName('PriceRequest');
		return true;
	}

	public function findByFirm(Firm $firm, $filters = []) {
		$fr = $this->model();
		$where = ['AND', 'id_firm = :id_firm'];
		$params = [':id_firm' => $firm->id()];

		if ($filters['query'] !== null && $filters['query']) {
			$where[] = ['OR', 'text LIKE :q', 'brief_text LIKE :q', 'user_name LIKE :q', 'user_email LIKE :q', 'user_phone LIKE :q'];
			$params = $params + [':q' => '%' . $filters['query'] . '%'];
		}

		$this->pagination()
				->setTotalRecords($fr->reader()->setWhere($where, $params)->count())
				->setLink('/firm-user/request/')
				->setLinkParams($filters)
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->calculateParams()
				->renderElems();

		$_items = $fr->reader()
				->setWhere($where, $params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		$items = [];
		foreach ($_items as $item) {
			$items[] = $item->prepare($item);
		}

		$this->items = $items;
		$this->setItemsTemplate('price_request_presenter');

		return $this;
	}

	/**
	 * @return \App\Classes\Pagination
	 */
	public function pagination() {
		if ($this->pagination === null) {
			$this->pagination = new Pagination();
		}
		return $this->pagination;
	}

	public function getPage() {
		$params = app()->request()->processGetParams(['page' => 'int']);
		if ($params['page']) return $params['page'];
		return 1;
	}

}
