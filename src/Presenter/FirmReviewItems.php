<?php

namespace App\Presenter;

use App\Classes\Pagination;
use App\Model\Firm;
use App\Model\FirmReview;
use Sky4\Model\Utils;
use function app;

class FirmReviewItems extends Presenter {

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('firm_review_presenter_items')
				->setItemsTemplateSubdirName('firmreview')
				->setModelName('FirmReview');
		return true;
	}

	/**
	 * @return Pagination
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

	public function findByFirm(Firm $firm, $filters = []) {
		$fr = $this->model();
		$where = ['AND', 'id_firm = :id_firm'];
		$params = [':id_firm' => $firm->id()];

		$this->pagination()
				->setTotalRecords($fr->reader()->setWhere($where, $params)->count())
				->setLink('/firm-user/review/')
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

		$files = [];
		foreach ($_items as $item) {
			$files[] = Utils::getFirstCompositeId($item->val('image'));
		}

		$files = Utils::getObjectsByIds($files);

		$items = [];
		foreach ($_items as $item) {
			$image_key = Utils::getFirstCompositeId($item->val('image'));
			$items[] = FirmReview::prepare($item);
		}

		$this->items = $items;
		$this->setItemsTemplate('firm_review_presenter');

		return $this;
	}

}
