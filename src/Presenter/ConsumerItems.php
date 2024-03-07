<?php

namespace App\Presenter;
class ConsumerItems extends \App\Presenter\Presenter {

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('consumer_presenter_items')
				->setItemsTemplateSubdirName('consumer')
				->setModelName('Consumer');
		return true;
	}

	/**
	 * @return \\App\Classes\Pagination
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

	public function find() {
		$_where = ['AND', '`flag_is_active` = :flag_is_active'];
		$_params = [':flag_is_active' => 1];
		
		$this->pagination()
				->setLimit(10)
				->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
				->setPage($this->getPage())
				->setLink('/consumer/')
				->calculateParams()
				->renderElems();

		$_items = $this->model()->reader()
				->setWhere($_where, $_params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		$items = [];
		foreach ($_items as $item) {
			$items[] = [
				'user_name' => $item->val('user_name'),
				'user_email' => $item->val('user_email'),
				'question' => $item->val('question'),
				'answer' => $item->val('answer'),
				'link' => $item->link(),
				'timestamp_inserting' => $item->val('timestamp_inserting'),
				'metadata_title' => $item->val('metadata_title')
			];
		}

		$this->items = $items;

		return $this;
	}
}
