<?php

namespace App\Action\Statistics;

use App\Action\Statistics;
use App\Model\PriceCatalog;
use App\Model\PriceCatalogCount;
use App\Model\StsSubgroup;
use Sky4\Model\Utils;
use function app;

class EmptyAction extends Statistics {

	public function execute() {
		$location = (int) app()->location()->currentId();

		app()->breadCrumbs()
				->setElem('Статистика', '/statistics/')
				->setElem('Популярные разделы каталога', '/statistics/popular/catalogs/');

		$cat = new PriceCatalog();
		$catalog_ids = array_keys($cat->reader()
						->setWhere(['AND', 'node_level = :node_level'], [':node_level' => 2])
						->rowsWithKey('id'));
		$pcc = new PriceCatalogCount();
		$pcc_conds = Utils::prepareWhereCondsFromArray($catalog_ids, 'id_catalog');
		$firm_location_conds = Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');
		$rows = app()->db()->query()
				->setSelect(['COUNT(*) as `cnt`, `id_catalog`'])
				->setFrom('price_catalog_price')
				->setWhere(['AND', $pcc_conds['where'], $firm_location_conds['where']], $pcc_conds['params'] + $firm_location_conds['params'])
				->setGroupBy('id_catalog')
				->setHaving('`cnt` < 3')
				->setOrderBy('`cnt` ASC')
				->select();

		$matrix = [];
		foreach ($rows as $row) {
			$cat = new PriceCatalog($row['id_catalog']);
			if (!isset($matrix[$cat->val('id_subgroup')])) {
				$matrix[$cat->val('id_subgroup')] = [];
			}
			$matrix[$cat->val('id_subgroup')][] = ['name' => $cat->name(), 'link' => $cat->link(), 'count' => $row['cnt']];
		}

		$subgroup = new StsSubgroup();
		$subgroups = $subgroup->reader()
				->setOrderBy('id_group ASC, id_subgroup ASC')
				->rowsWithKey('id_subgroup');

		$text = $this->text()->getByLink('statistics/empty');
		$text->setVal('text', self::replaceStatText($text->val('text')));
		if ($text->exists()) {
			app()->metadata()->setFromModel($text);
		} else {
			app()->metadata()->setTitle('Пустые каталоги');
		}

		$this->view()->setTemplate('empty')
				->set('matrix', $matrix)
				->set('subgroups', $subgroups)
				//
				->set('breadcrumbs', app()->breadCrumbs()->render())
				->set('text', $text)
				->save();
	}

}
