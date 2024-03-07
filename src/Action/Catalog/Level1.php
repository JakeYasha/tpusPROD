<?php

namespace App\Action\Catalog;

use App\Controller\FirmPromo;
use App\Model\PriceCatalog;
use App\Model\PriceCatalogCount;
use App\Model\SubgroupCount;
use Sky4\Model\Utils;

class Level1 extends \App\Action\Catalog {

    public function execute($id_group = null) {
        if (str()->sub(app()->request()->getRequestUri(), -1) !== '/') {
            app()->response()->redirect(app()->link(app()->request()->getRequestUri() . '/'), 301);
        }
        app()->breadCrumbs()
                ->setElem('Каталог', app()->link('/catalog/'));

        $this->text()->getByLink($id_group ? 'catalog/' . $id_group : 'catalog');

        app()->metadata()
                ->setNew($this->text);

        if (!app()->location()->city()->exists()) {
            app()->metadata()->noIndex();
        }

        $sc = new SubgroupCount();

        $items = [];
        $sub_groups = $sc->getCurrentSubgroups($id_group);
        $id_subgroup_where_conds = Utils::prepareWhereCondsFromArray($sub_groups, 'id_subgroup');
        $childs = [];
        if ($sub_groups) {
            if ($id_group === null) {
                $pc = new PriceCatalog();
                $level_2_where = ['AND', 'node_level = :node_level', $id_subgroup_where_conds['where']];
                $level_2_params = [':node_level' => 2];
                $level_2_params = array_merge($level_2_params, $id_subgroup_where_conds['params']);

                $tmp = $pc->reader()
                        ->setSelect($pc->getFieldsForLists())
                        ->setWhere($level_2_where, $level_2_params)
                        ->setOrderBy('`web_name` ASC')
                        ->rows();

                $group = new PriceCatalog();
                $group_where = ['AND', '`id_group` != :services', '`id_group` != :equipment', '`node_level` = :node_level'];
                $group_params = [':services' => 44, ':equipment' => 22, ':node_level' => 1];

                $items = $group->reader()
                        ->setSelect($group->getFieldsForLists())
                        ->setWhere($group_where, $group_params)
                        ->setOrderBy('web_many_name ASC')
                        ->rows();

                foreach ($tmp as $sgr) {
                    $childs[$sgr['id_group']][$sgr['id_subgroup']] = [
                        'name' => $sgr['web_many_name'],
                        'link' => app()->link('/catalog/' . $sgr['id_group'] . '/' . $sgr['id_subgroup'] . '/')
                    ];
                }
            } else {
                if (true) {
                    $catalog = new PriceCatalog();
                    $catalog->getGroup($id_group);
                    $conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');

                    $pcp = new \App\Model\PriceCatalogPrice();

                    $_where = ['AND', 'path LIKE :path', $conds['where']];
                    $_params = array_merge([':path' => $catalog->getPathString() . '%'], $conds['params']);

                    $pcp_items = $pcp->reader()
                            ->setSelect(['id_catalog', 'node_level', 'COUNT(id_price) as count', 'path'])
                            ->setWhere($_where, $_params)
                            ->setGroupBy('path')
                            ->setOrderBy('count ASC')
                            ->rows();
                    
                    $_catalog_ids = [];
                    $_items = [];
                    foreach ($pcp_items as $item) {
                        $path = str()->sub(str()->sub($item['path'], 0, -1), 1);
                        $ids = array_slice(explode('][', $path), 0, 3);
                        $_catalog_ids = array_merge($_catalog_ids, $ids);
                        
                        if (!isset($_items[$ids[1]])) {
                            $_items[$ids[1]] = [ 'childs' => [] ];
                        }
                        
                        if (isset($ids[2]) && !in_array($ids[2], $_items[$ids[1]]['childs'])) {
                            $_items[$ids[1]]['childs'] []= $ids[2];
                        }
                    }
                    
                    $_catalog_ids = array_unique($_catalog_ids);
                    $_catalogs = $catalog->reader()->setOrderBy('web_name ASC')->objectsByIds($_catalog_ids);
                    
                    foreach($_catalogs as $_catalog_id => $_catalog) {
                        if (isset($_items[$_catalog_id])) {
                            $items[$_catalog_id] = $_catalog;
                            $items[$_catalog_id]->setVal('web_many_name', str()->firstCharToUpper(str()->toLower($_catalog->val('web_many_name'))));
                            foreach($_catalogs as $_child_id => $_child) {
                                if (in_array($_child_id, $_items[$_catalog_id]['childs'])) {
                                    $child = $_catalogs[$_child_id];
                                    $childs[$_catalog_id][] = [
                                        'name' => str()->firstCharToUpper($child->val('web_many_name')),
                                        'link' => $child->link()
                                    ];
                                }
                            }
                        }
                    }
                } else {

                    //забираем подгруппы выбранной группы
                    $pc = new PriceCatalog();
                    $level_2_where = ['AND', 'id_group = :id_group', 'node_level = :node_level', $id_subgroup_where_conds['where']];
                    $level_2_params = array_merge([':id_group' => $id_group, ':node_level' => 2], $id_subgroup_where_conds['params']);

                    $items = $pc->reader()
                            ->setSelect($pc->getFieldsForLists())
                            ->setWhere($level_2_where, $level_2_params)
                            ->setOrderBy('`web_many_name` ASC')
                            ->objects();

                    //забираем реальные ID каталогов, подходящие для этой группы и города, содержащие товары
                    $pc_ids = array_keys((new \App\Model\PriceCatalogPrice())->getCatalogIdsSortByCount($id_group));

                    //забираем всех детей 3 уровня
                    $pc_conds = Utils::prepareWhereCondsFromArray(array_keys($items), 'parent_node');
                    $pc_where = ['AND', 'node_level = :node_level', $pc_conds['where']];
                    $pc_params = [':node_level' => 3] + $pc_conds['params'];
                    $pc_childs = $pc->reader()
                            ->setSelect($pc->getFieldsForLists())
                            ->setWhere($pc_where, $pc_params)
                            ->setOrderBy('web_many_name ASC')
                            ->objects();

                    //собираем результирующий массив
                    foreach ($items as $k => $cat) { // перебираем подгруппы
                        $items[$k]->setVal('web_many_name', str()->firstCharToUpper(str()->toLower($cat->val('web_many_name'))));
                        foreach ($pc_childs as $kk => $child) { // перебираем все каталоги 3-го уровня
                            if ((int) $child->val('parent_node') === (int) $cat->id() && (in_array($child->id(), $pc_ids)
                                    /* || in_array($child->val('parent_node'), $pc_ids) */)) {
                                $childs[$cat->id()][] = [
                                    'name' => str()->firstCharToUpper($child->val('web_many_name')),
                                    'link' => $child->link()
                                ];
                                unset($pc_childs[$kk]);
                            }
                        }
                    }
                }
            }
        }


        app()->tabs()->setTabs([
                    ['link' => app()->link('/catalog/'), 'label' => 'Товары'],
                    ['link' => app()->link('/catalog/44/'), 'label' => 'Услуги'],
                    ['link' => app()->link('/catalog/22/'), 'label' => 'Оборудование']
                ])
                ->setActiveTab($id_group === null ? 0 : ((int) $id_group === 44 ? 1 : 2));

        $firm_promo_controller = new FirmPromo();
        $group = new PriceCatalog();
        $group->getGroup($id_group);

        if (!$childs) {
            app()->metadata()->noIndex();
            app()->response()->code404();
        }
        
        //app()->frontController()->layout()->setVar('id_group', $id_group)->setTemplate('catalog');
        if (app()->isNewTheme()) {
            app()->frontController()->layout()
                    ->setVar('rubrics', app()->chunk()->setArg($id_group)->render('catalog.rubrics'))
                    ->setVar('mobile_rubrics', app()->chunk()->setArg($id_group)->render('catalog.mobile_rubrics'))
                    ->setTemplate('catalog');
        }
        
        $this->view()
                ->set('rubrics', app()->chunk()->setArg($id_group)->render('catalog.rubrics'))
                ->set('promo', $firm_promo_controller->renderPromoBlock($id_group === null ? 0 : ((int) $id_group === 44 ? 44 : 22)))
                ->set('id_group', $id_group)
                ->set('items', $items)
                ->set('childs', isset($childs) ? $childs : [])
                ->set('tabs', app()->tabs()->render())
                ->set('mode', $id_group === null ? 'goods' : ($id_group == 44 ? 'services' : 'equipment'))
                ->set('group', $group)
                ->set('text', app()->metadata()->replaceLocationTemplates($this->text()->val('text')))
                ->setTemplate('catalog_level_1');

        return $this;
    }

}
