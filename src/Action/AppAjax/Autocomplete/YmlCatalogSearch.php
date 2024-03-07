<?php

namespace App\Action\AppAjax\Autocomplete;

use App\Action\AppAjax\Autocomplete;
use App\Model\PriceCatalog;
use App\Model\Synonym;
use CLangCorrect;
use Sky4\Model\Utils;
use function app;
use function str;

class YmlCatalogSearch extends Autocomplete {

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

		$res = $this->ymlCatalogSearch($query);
		if (!($res[0] && $res[1])) {
			$lc = new CLangCorrect();
			$new_query = $lc->parse($query, CLangCorrect::KEYBOARD_LAYOUT);
			if ($new_query !== $query) {
				$res = $this->ymlCatalogSearch($new_query);
			}
		}

		$subgroup_ids = [];
		$catalog_ids = [];

		if (isset($res[0]) && $res[0]) {
			foreach ($res[0] as $catalog) {
				$subgroup_ids[$catalog['id_parent']] = 0;
			}
		}

		if (isset($res[1]) && $res[1]) {
			foreach ($res[1] as $catalog) {
				$catalog_ids[$catalog['id_catalog']] = 0;
				if (isset($catalog_ids[$catalog['id_parent']])) {
					$catalog_ids[$catalog['id_parent']] += 0;
				}
			}
		}

		if ($subgroup_ids) {
			$cat = new PriceCatalog();
			$conds = Utils::prepareWhereCondsFromArray(array_keys($subgroup_ids), 'id');
			/* @var $items PriceCatalog[] */
			$items = $cat->reader()->setWhere($conds['where'], $conds['params'])->objects();

//			print_r($items);
//			exit();


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
					'class' => 'catalog',
					'sub_label' => isset($parents[$ob->val('parent_node')]) ? 'Рубрика: '.$parents[$ob->val('parent_node')]->name() : '',
					'trigger' => '.js-fix-yml-catalog-id'
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
					'class' => 'catalog',
					'sub_label' => '',
					'trigger' => '.js-fix-yml-catalog-id'
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
				$_result[$id]['sub_label'] = isset($parents[$ob['id_parent']]) ? $parents[$ob['id_parent']]->name() : '';
			}

			arsort($sorter);
			foreach ($sorter as $k => $v) {
				$result[] = $_result[$k];
			}
		}

		die(json_encode($result));
	}

}
