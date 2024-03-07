<?php

namespace App\Presenter;

use App\Model\Firm;
use App\Model\FirmPromo;
use App\Model\FirmPromoCatalog;
use App\Model\PriceCatalog;
use Sky4\Helper\DeprecatedDateTime as DateTime;
use Sky4\Model\Utils;

class YmlCategoryItems extends Presenter {

    public function __construct() {
        parent::__construct();
        $this->setItemsTemplate('yml_category_presenter_items')
                ->setItemsTemplateSubdirName('ymlcategory')
                ->setModel(new \App\Model\YmlCategory());
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
        if ($params['page'])
            return $params['page'];
        return 1;
    }

    public function find($params, $id_categories) {
        if ($params['id_firm'] === null || !$params['id_firm']) {
            return $this;
        }
        
        if ($id_categories) {
            $id_categories_conds = Utils::prepareWhereCondsFromArray($id_categories, 'id');

            $_where = ['AND', 'id_firm = :id_firm', /* 'parent_node != :nil', */ $id_categories_conds['where']];
            $_params = [':id_firm' => (int) $params['id_firm'], /* ':nil' => 0 */] + $id_categories_conds['params'];
        } else {
            $_where = ['AND', 'id_firm = :id_firm', '1 = :nil'];
            $_params = [':id_firm' => (int) $params['id_firm'], ':nil' => 0];
        }

        if ($params['flag_is_fixed'] !== null && (int) $params['flag_is_fixed'] !== 0) {
            $_where[] = 'flag_is_fixed = :flag_is_fixed';
            $_params[':flag_is_fixed'] = (int) $params['flag_is_fixed'] === 1 ? 1 : 0;
        }

        if ($params['flag_id_catalog'] !== null && (int) $params['flag_id_catalog'] !== 0) {
            $_where[] = (int) $params['flag_id_catalog'] === 1 ? 'id_catalog != :nil' : 'id_catalog = :nil';
            $_params[':nil'] = 0;
        }

        if ($params['id_yml_category']) {
            $_where[] = 'id = :id';
            $_params[':id'] = $params['id_yml_category'];
        }

        if ($params['id_catalog']) {
            $_where[] = 'id_catalog = :id_catalog';
            $_params[':id_catalog'] = $params['id_catalog'];
        }

        $this->pagination()
                ->setLimit(50)
                ->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
                ->setLink('/firm-manager/yml/categories/')
                ->setLinkParams($params)
                ->setPage($this->getPage())
                ->calculateParams()
                ->renderElems();

        $_items = $this->model()->reader()
                ->setWhere($_where, $_params)
                ->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
                ->setOrderBy('parent_node ASC, name ASC')
                ->objects();

        $items = $this->model()->prepare($_items);

        $this->items = $items;

        return $this;
    }

}
