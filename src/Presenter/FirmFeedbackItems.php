<?php

namespace App\Presenter;

use App\Classes\Pagination;
use App\Model\Firm;
use function app;

class FirmFeedbackItems extends Presenter {

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('firm_feedback_presenter_items')
				->setItemsTemplateSubdirName('firmreview')
				->setModelName('FirmFeedback');
		return true;
	}

	public function findByFirm(Firm $firm, $filters = []) {
		$where = ['AND', 'id_firm = :id_firm'];
		$params = [':id_firm' => $firm->id()];

		if ($filters['query'] !== null && $filters['query']) {
			$where[] = ['OR', 'message_text LIKE :q', 'message_subject LIKE :q', 'user_name LIKE :q', 'user_email LIKE :q', 'user_phone LIKE :q'];
			$params = $params + [':q' => '%' . $filters['query'] . '%'];
		}

		$this->pagination()
				->setTotalRecords($this->model()->reader()->setWhere($where, $params)->count())
				->setLink('/firm-user/feedback/')
				->setLinkParams($filters)
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->calculateParams()
				->renderElems();

		$_items = $this->model()->reader()
				->setWhere($where, $params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		$items = [];
		foreach ($_items as $item) {
			$items[] = $item->prepare($item);
		}

		$this->items = $items;
		$this->setItemsTemplate('firm_feedback_presenter');

		return $this;
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

}
