<?php

namespace App\Action\AppAjax\Autocomplete;

use App\Action\AppAjax\Autocomplete;
use App\Model\Firm;
use App\Model\FirmType;
use App\Model\FirmTypeCity;
use App\Model\PriceCatalog;
use App\Model\Synonym;
use CLangCorrect;
use Sky4\Model\Utils;
use function app;
use function str;

class SuggestSearch extends Autocomplete {

	public function execute($query) {
		$result = [];
		$query = str()->toLower(trim($query));

		$syn = new Synonym();
		$synonim = $syn->reader()
				->setWhere(['AND', "`search` LIKE :search"], [':search' => $query])
				->setLimit(1)
				->objectByConds();

		if ($synonim->exists()) {
			$query = '"'.$query.'" | "'.$synonim->val('replace').'"';
		}

		$res = $this->suggestSearch($query);

		if (!($res[0] || $res[1] || $res[2] || $res[3])) {
			$lc = new CLangCorrect();
			$new_query = $lc->parse($query, CLangCorrect::KEYBOARD_LAYOUT);
			if ($new_query !== $query) {
				$res = $this->suggestSearch($new_query);
			}
		}

		$subgroup_ids = [];
		$catalog_ids = [];
		$firm_ids = [];
		$firm_types = [];

		if (isset($res[0]) && $res[0]) {
			foreach ($res[0] as $catalog) {
				$subgroup_ids[$catalog['id_catalog']] = 0;
			}
		}

		if (isset($res[1]) && $res[1]) {
			foreach ($res[1] as $catalog) {
				$catalog_ids[$catalog['id_catalog']] = 1;
				if (isset($catalog_ids[$catalog['id_parent']])) {
					$catalog_ids[$catalog['id_parent']] += 1;
				}
			}
		}

		if (isset($res[2]) && $res[2]) {
			foreach ($res[2] as $firm) {
				$firm_ids[$firm['id']] = $firm['id'];
			}
		}

		if (isset($res[3]) && $res[3]) {
			foreach ($res[3] as $type) {
				$firm_types[$type['id']] = $type['id'];
			}
		}

		if ($subgroup_ids) {
			$cat = new PriceCatalog();
			$conds = Utils::prepareWhereCondsFromArray(array_keys($subgroup_ids), 'id');
			/* @var $items PriceCatalog[] */
			$items = $cat->reader()->setWhere($conds['where'], $conds['params'])->objects();

			$group_ids = [];
			foreach ($items as $it) {
				$group_ids[] = $it->val('parent_node');
			}
			$conds_parents = Utils::prepareWhereCondsFromArray($group_ids, 'id');
			$parents = $cat->reader()->setWhere($conds_parents['where'], $conds_parents['params'])->objects();

			foreach ($items as $ob) {
				$result[$ob->id()] = [
					'id' => $ob->id(),
					'label' => $ob->name(),
					'href' => app()->link($ob->link()),
					'class' => 'catalog',
					'sub_label' => isset($parents[$ob->val('parent_node')]) ? 'Рубрика: '.$parents[$ob->val('parent_node')]->name() : ''
				];
			}
		}

		if ($catalog_ids) {
			$cat = new PriceCatalog();
			$conds = Utils::prepareWhereCondsFromArray(array_keys($catalog_ids), 'id');
			/* @var $items PriceCatalog[] */
			$items = $cat->reader()->setWhere($conds['where'], $conds['params'])->objects();
			$_result = [];
			$_counter = [];
			$_parents = [];
			foreach ($items as $ob) {
				$_result[$ob->id()] = [
					'id' => $ob->id(),
					'id_parent' => $ob->val('parent_node'),
					'label' => $ob->name(),
					'href' => app()->link($ob->link()),
					'class' => 'catalog',
					'sub_label' => ''
				];

				$_parents[$ob->val('parent_node')] = 1;

				if (!isset($counter[$ob->id()])) {
					$_counter[$ob->id()] = 0;
				}

				$_counter[$ob->id()] += $catalog_ids[$ob->id()];
			}

			$conds = Utils::prepareWhereCondsFromArray(array_keys($_parents), 'id');
			$parents = $cat->reader()->setWhere($conds['where'], $conds['params'])->objects();

			$sorter = [];
			foreach ($_result as $id => $ob) {
				$sorter[$id] = $_counter[$id];
				$_result[$id]['sub_label'] = $ob['id_parent'] > 0 ? $parents[$ob['id_parent']]->name() : '';
			}

			arsort($sorter);
			foreach ($sorter as $k => $v) {
				$result[] = $_result[$k];
			}
		}

		if ($firm_ids) {
			$f = new Firm();
			$conds = Utils::prepareWhereCondsFromArray($firm_ids, 'id');
            
            $_conds['where'] = [
                'AND',
                '`flag_is_active` = :1',
                $conds['where'],
            ];
            $_conds['params'] = array_merge([':1' => 1], $conds['params']);
            
			$items = $f->reader()->setWhere($_conds['where'], $_conds['params'])->objects();

            //Учитываем филиалы фирмы
            $_firm_ids_has_branches = app()->location()->getFirmIdsHasBranches();
            foreach ($items as $ob) {
                if (in_array($ob->val('id_city'), app()->location()->getCityIds())) {
                    $result[] = [
                        'id' => $ob->id(),
                        'label' => $ob->name(),
                        'sub_label' => $ob->activity(),
                        'ext_label' => $ob->address(),
                        'href' => $ob->linkItem()
                    ];
                }
                if ($_firm_ids_has_branches && in_array($ob->id(), $_firm_ids_has_branches)){
                    $_items = $ob->getCityFirmBranches();
                    foreach ($_items as $_ob) {
                        $result[] = [
                            'id' => $_ob->id(),
                            'label' => $_ob->name(),
                            'sub_label' => $_ob->activity(),
                            'ext_label' => $_ob->address(),
                            'href' => $_ob->linkItem()
                        ];
                    }
                }
            }
		}


		if ($firm_types) {
			$f = new FirmType();
			$conds = Utils::prepareWhereCondsFromArray($firm_types, 'id');
			$items = $f->reader()->setWhere($conds['where'], $conds['params'])->objects();
			$firm_types_ids = array_keys($items);
			$ftc = new FirmTypeCity(); //@todo
			$ftc_conds = Utils::prepareWhereCondsFromArray($firm_types_ids, 'id_type');
			$ftc_city = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
			$filtered_types = $ftc->reader()
					->setWhere(['AND', $ftc_conds['where'], $ftc_city['where']], $ftc_conds['params'] + $ftc_city['params'])
					->rowsWithKey('id_type');

			foreach ($items as $ob) {
				if (isset($filtered_types[$ob->id()])) {
					$result[] = [
						'id' => $ob->id(),
						'label' => $ob->name(),
						'sub_label' => 'Каталог фирм',
						'href' => app()->link($ob->link())
					];
				}
			}
		}

		die(json_encode($result));
	}

}
