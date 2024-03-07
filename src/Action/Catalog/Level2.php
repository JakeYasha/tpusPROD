<?php

namespace App\Action\Catalog;

use App\Controller\FirmPromo;
use App\Model\PriceCatalog;
use App\Model\PriceCatalogCount;
use App\Model\SubgroupCount;
use Sky4\Model\Utils;
use App\Model\AdvText;
use function app;
use function str;

class Level2 extends \App\Action\Catalog {

	public function execute($id_group) {
		if (str()->sub(app()->request()->getRequestUri(), -1) !== '/') {
			app()->response()->redirect(app()->link(app()->request()->getRequestUri().'/'), 301);
		}

		$sg = new PriceCatalog();
		$sg->getGroup($id_group);

		app()->breadCrumbs()
				->setElem('Каталог', app()->link('/catalog/'))
				->setElem($sg->name(), app()->link('/catalog/'.$sg->id_group().'/'));

		$sc = new SubgroupCount();
		$ids_matrix = [];
		$items = [];

		$sub_groups = $sc->getCurrentSubgroups($id_group);
		$childs = [];
		if ($sub_groups) {
            $ss = new PriceCatalog();
			$id_subgroup_where_conds = Utils::prepareWhereCondsFromArray($sub_groups, 'id_subgroup');
			$ss_where = ['AND', 'id_group = :id_group', 'node_level = :node_level', $id_subgroup_where_conds['where']];
			$ss_params = [':node_level' => 2, ':id_group' => $id_group];
			$ss_params = array_merge($ss_params, $id_subgroup_where_conds['params']);

			$items = $ss->reader()
					->setSelect($ss->getFieldsForLists())
					->setWhere($ss_where, $ss_params)
					->setOrderBy('`web_many_name` ASC')
					->rowsWithKey('id_subgroup');
            
            $pcp = new \App\Model\PriceCatalogPrice();
			$pc_ids = array_keys($pcp->getCatalogIdsSortByCount($id_group));

			$pc = new PriceCatalog();
			$pc_conds = Utils::prepareWhereCondsFromArray(array_keys($items), 'id_subgroup');
			$pc_id_conds = Utils::prepareWhereCondsFromArray($pc_ids, 'id');
			$pc_where = ['AND', 'flag_is_catalog = :flag_is_catalog', '`node_level` = :node_level', $pc_conds['where'], $pc_id_conds['where']];
			$pc_params = [':node_level' => 3, ':flag_is_catalog' => 1];
			$pc_params = array_merge($pc_params, $pc_conds['params'], $pc_id_conds['params']);
			$childs = $pc->reader()
					->setSelect(['id', 'web_many_name', 'name', 'id_subgroup', 'id_group', 'flag_is_catalog'])
					->setWhere($pc_where, $pc_params)
					->setOrderBy('web_many_name ASC')
					->objects();
            
			foreach ($items as $ik => $iv) {
				if (!isset($ids_matrix[$ik])) {
					$ids_matrix[$ik] = [];
				}
			}

			foreach ($childs as $child) {
				$ids_matrix[$child->val('id_subgroup')][] = $child->id();
			}
		}

		foreach ($ids_matrix as $key => $val) {
			if (!$val) {
				unset($ids_matrix[$key]);
			}
		}

		$subgroup_names = [];
		foreach ($items as $k => $item) {
			$items[$k]['name'] = str()->firstCharToUpper(str()->toLower($item['name']));
			$subgroup_names[] = str()->toLower($item['web_many_name']);
		}

		app()->metadata()
				->set($sg, $sg->name().' _Cp_'.': обзор предложений, поставщики, магазины', $sg->name().' '.app()->location()->currentCaseName('prepositional').', товары, каталог товаров, продажа, магазины, поставщики, '.app()->location()->currentName(), 'В каталоге товаров '.$sg->name().' '.app()->location()->currentCaseName('prepositional').' представлены следующие предложения: '.implode(', ', $subgroup_names), true, null, [], true)
				->setHeader($sg->name());

		if (!$ids_matrix && !$items) {
			app()->metadata()->noIndex();
		} else if (!app()->location()->city()->exists()) {
			app()->metadata()->noIndex();
		}

		$fpc = new FirmPromo();

		$title = $this->getCatalogTitle();
		$title .= ' '.app()->location()->currentCaseName('prepositional');
        
        if (app()->isNewTheme()) {
            if (in_array($id_group, [22,44])) {
                app()->frontController()->layout()
                        ->setVar('rubrics', app()->chunk()->setArg($id_group)->render('catalog.rubrics'))
                        ->setVar('mobile_rubrics', app()->chunk()->setArg($id_group)->render('catalog.mobile_rubrics'))
                        ->setTemplate('catalog');
            }
        }

        $adv_text = new AdvText();
		$this->view()
				->set('promo', $fpc->renderPromoBlock($id_group))
				->set('matrix', $ids_matrix)
				->set('ext_title', $title)
				->set('id_group', $id_group)
				->set('items', $items)
				->set('group', $sg)
				->set('childs', $childs)
                                ->set('position', $adv_text->getByUrl(app()->location()->linkPrefix() . app()->request()->getRequestUri()))
				->setTemplate('catalog_level_2');

		return $this;
	}

}
