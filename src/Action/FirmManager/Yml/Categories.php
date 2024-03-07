<?php

namespace App\Action\FirmManager\Yml;

class Categories extends \App\Action\FirmManager\Yml {

    public function execute() {
        app()->breadCrumbs()->setElem('Модерация категорий YML', self::link('/yml/categories/'));

        $filters = app()->request()->processGetParams([
            'flag_id_catalog' => ['type' => 'int'],
            'id_yml_category' => ['type' => 'int'],
            'id_catalog' => ['type' => 'int'],
            'id_firm' => ['type' => 'int'],
            'flag_is_fixed' => ['type' => 'int'],
            'flag_is_catalog' => ['type' => 'int']
        ]);

        $_SESSION['firmmanager_yml_categories_url'] = app()->request()->getRequestUri();
        $this->text()->getByLink('firm-manager/yml/categories');
        app()->metadata()->setFromModel($this->text());
        if (!$this->text()->exists()) {
            app()->metadata()->setTitle('Модерация категорий YML');
        }

        $yml = new \App\Model\Yml();
        $id_firms = array_keys($yml->reader()->setSelect(['id', 'id_firm'])
                        ->setWhere(['AND', 'status != :status1', 'status != :status2'], [':status1' => 'complete_fail', ':status2' => ''])
                        ->rowsWithKey('id_firm'));

        $firm = new \App\Model\Firm();
        $firms = $firm->reader()->setSelect(['id', 'company_name'])->objectsByIds($id_firms);

        $yml_offer = new \App\Model\YmlOffer();
        $firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($id_firms, 'id_firm');
        $id_categories = array_keys($yml_offer->reader()->setSelect(['DISTINCT(id_yml_category)'])
                        ->setWhere($firm_conds['where'], $firm_conds['params'])
                        ->rowsWithKey('id_yml_category'));

        $catalogs = [];
        if ($filters['id_firm'] !== null) {
            if ($filters['flag_is_catalog'] !== null && (int) $filters['flag_is_catalog'] !== 0) {
                $catalog_ids = array_keys($yml_offer->reader()->setSelect(['id_catalog'])
                                ->setWhere(['AND', 'id_firm = :id_firm'], [':id_firm' => $filters['id_firm']])
                                ->rowsWithKey('id_catalog'));

                //var_dump('Каталогов всего у фирмы: ' . count($catalog_ids));

                if ($catalog_ids) {
                    $catalogs_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($catalog_ids, 'id');
                    $_where = ['AND', 'flag_is_catalog = :flag_is_catalog', $catalogs_conds['where']];
                    $_params = [':flag_is_catalog' => (int) $filters['flag_is_catalog'] === 1 ? 0 : 1] + $catalogs_conds['params'];

                    $catalog = new \App\Model\PriceCatalog();
                    $filtered_catalog_ids = array_keys($catalog->reader()->setSelect(['id'])
                                    ->setWhere($_where, $_params)
                                    ->rowsWithKey('id'));

                    //var_dump('Отфильтрованных каталогов всего: ' . count($filtered_catalog_ids));

                    //$filtered_catalog_ids []= 999999999;
                    if ($filtered_catalog_ids) {
                        $filtered_catalogs_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($filtered_catalog_ids, 'id_catalog');
                        //var_dump($filtered_catalog_ids);

                        $_filtered_where = ['AND', 'id_firm = :id_firm', $filtered_catalogs_conds['where']];
                        $_filtered_params = [':id_firm' => $filters['id_firm']] + $filtered_catalogs_conds['params'];
                        //var_dump($_filtered_params);

                        $id_categories = $yml_offer->reader()->setSelect(['DISTINCT(id_yml_category)'])
                                        ->setWhere($_filtered_where, $_filtered_params)
                                        ->rowsWithKey('id_yml_category');
                        //var_dump('Отфильтрованых категорий всего: ' . count($id_categories));
                        //var_dump($id_categories);
                        $id_categories = array_keys($id_categories);
                    } else {
                        $id_categories = [];
                    }

                    //$id_categories []= 999999999;
                }

                $id_catalogs = array_keys($yml_offer->reader()->setSelect(['DISTINCT(id_catalog)'])
                                ->setWhere(['AND', 'id_firm = :id_firm'], [':id_firm' => $filters['id_firm']])
                                ->rowsWithKey('id_catalog'));

                if ($id_catalogs) {
                    $catalogs_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($id_catalogs, 'id');
                    $catalog = new \App\Model\PriceCatalog();
                    $catalogs = $catalog->reader()->setWhere($catalogs_conds['where'], $catalogs_conds['params'])
                            ->setOrderBy('web_many_name ASC')
                            ->getList();
                }
            }
        }


        $presenter = new \App\Presenter\YmlCategoryItems();
        $presenter->find($filters, $id_categories);

        $this->view()
                ->set('bread_crumbs', app()->breadCrumbs()->render(true))
                ->set('catalogs', $catalogs)
                ->set('firms', $firms)
                ->set('filters', $filters)
                ->set('items', $presenter->renderItems())
                ->set('items_count', $presenter->pagination()->getTotalRecords())
                ->set('pagination', $presenter->pagination()->render(true))
                ->setTemplate('yml_categories', 'firmmanager')
                ->save();
    }

}
