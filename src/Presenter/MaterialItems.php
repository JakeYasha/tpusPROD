<?php

namespace App\Presenter;

use App\Classes\Pagination;
use App\Model\Material;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;
use function app;

class MaterialItems extends Presenter {

    public $rubrics = [];

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('presenter_material_items')
				->setItemsTemplateSubdirName('firmmanager')
				->setModelName('Material');
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
        
        public function page_material_items() {
		$this->setItemsTemplate('presenter_material_items_list_rubric')
				->setItemsTemplateSubdirName('firmmanager')
				->setModelName('Material');
		return true;
        }
        public function page_use_material_items() {
		$this->setItemsTemplate('presenter_material_items_use_list_rubric')
				->setItemsTemplateSubdirName('firmmanager')
				->setModelName('Material');
		return true;
        }

	public function getPage() {
		$params = app()->request()->processGetParams(['page' => 'int']);
		if ($params['page']) return $params['page'];
		return 1;
	}

	public function find($filters = null, $limit = 50) {
        $_where = ['AND', '`id_service` = :id_service'];
        $_params = [':id_service' => app()->firmManager()->id_service()];
        
        if ($filters && $filters['query'] !== null && $filters['query']) {
			$_where = ['AND', $_where];
			$_where[] = ['OR', 'name LIKE :q', 'short_text LIKE :q'];
			$_params = $_params + [':q' => '%'.$filters['query'].'%'];
		}
        
        $sorting = \App\Action\FirmManager\Materials::materialSortingOptions();
        if ($filters['sorting'] !== null && isset($sorting[$filters['sorting']]['expression'])) {
			$order_by[] = $sorting[$filters['sorting']]['expression'];
		} else {
			$order_by[] = 'timestamp_inserting DESC';
		}

        $this->pagination()
				->setLimit($limit)
				->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
				->setPage($this->getPage())
				->setLink('/firm-manager/materials/')
				->calculateParams()
				->renderElems();

		$this->items = $this->model()->reader()
				->setWhere($_where, $_params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy(implode(',', $order_by))
				->objects();

		return $this;
	}

}
