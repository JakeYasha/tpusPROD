<?php

namespace App\Presenter;
class ChangelogItems extends \App\Presenter\Presenter {

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('changelog_presenter_items')
				->setItemsTemplateSubdirName('changelog')
				->setModelName('Changelog');
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
        $_where = ['AND', '`flag_is_active` = :yes'];
        $_params = [':yes' => 1];
        if (!APP_IS_DEV_MODE) {
            $_where = array_merge($_where, ['`flag_is_hidden` = :hidden']);
            $_params += [':hidden' => 0];
        }
        
		$this->pagination()
				->setLimit(10)
				->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
				->setPage($this->getPage())
				->setLink('/changelog/')
				->calculateParams()
				->renderElems();

		$_items = $this->model()->reader()
				->setWhere($_where, $_params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		$items = [];
		foreach ($_items as $item) {
            $vote = isset($_SESSION['changelog']) && isset($_SESSION['changelog'][$item->id()]) ? true : false;
            $vote_result = $vote && isset($_SESSION['changelog'][$item->id()]['thumb']) ? $_SESSION['changelog'][$item->id()]['thumb'] : null;
            
			$items[] = [
				'title' => $item->val('title'),
				'text' => $item->val('text'),
				'likes' => $item->val('likes'),
				'dislikes' => $item->val('dislikes'),
                'vote' => $vote,
                'vote_result' => $vote_result,
				'sites' => $item->sites()[$item->val('sites')],
				'flag_is_hidden' => $item->val('flag_is_hidden'),
				'link' => $item->link(),
				'timestamp_inserting' => $item->val('timestamp_inserting'),
				'timestamp_last_updating' => $item->val('timestamp_last_updating')
			];
		}

		$this->items = $items;

		return $this;
	}
}
