<?php

namespace App\Presenter;

use App\Classes\Pagination;
use App\Model\Firm;
use App\Model\FirmVideo;
use Sky4\Model\Utils;
use function app;

class FirmVideoItems extends Presenter {

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('firm_video_presenter_items')
				->setItemsTemplateSubdirName('firmvideo')
				->setModelName('FirmVideo');
		return true;
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

	public function find() {
		$firm = new Firm();
		$conds_city_id = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
		$_where = ['AND', 'flag_is_active = :flag_is_active', $conds_city_id['where']];
		$_params = [':flag_is_active' => 1] + $conds_city_id['params'];

		$this->pagination()
				->setLimit(20)
				->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
				->setLink(app()->link('/firm-video/'))
				->setPage($this->getPage())
				->calculateParams()
				->renderElems();

		$_items = $this->model()->reader()
				->setWhere($_where, $_params)
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
			$firm = new Firm();
			$firm->getByIdFirm($item->id_firm());
			$items[] = $item;
		}

		$this->items = $items;

		return $this;
	}

	public function findByFirm(Firm $firm, $filters = []) {
		$fp = new FirmVideo();
		$where = ['AND', 'id_firm = :id_firm'];
		$params = [':id_firm' => $firm->id()];

		$this->pagination()
				->setTotalRecords($fp->reader()->setWhere($where, $params)->count())
				->setLink('/firm-user/video/')
				->setLinkParams($filters)
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->calculateParams()
				->renderElems();

		$_items = $fp->reader()
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
			$items[] = FirmVideo::prepare($item, isset($files[$image_key]) ? $files[$image_key]->iconLink('-150x150') : false);
		}

		$this->items = $items;
		$this->setItemsTemplate('firm_video_presenter');

		return $this;
	}

}
