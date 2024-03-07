<?php

namespace App\Action\FirmManager\Yml;

class Offers extends \App\Action\FirmManager\Yml {

	public function execute() {
		app()->breadCrumbs()->setElem('Модерация предложений YML', self::link('/yml/offers/'));

		$filters = app()->request()->processGetParams([
			//'id_subgroup' => ['type' => 'int'],
			'id_firm' => ['type' => 'int'],
			'flag_is_active' => ['type' => 'int']
		]);

		$_SESSION['firmmanager_yml_offers_url'] = app()->request()->getRequestUri();
		$this->text()->getByLink('firm-manager/yml/offers');
		app()->metadata()->setFromModel($this->text());
		if (!$this->text()->exists()) {
			app()->metadata()->setTitle('Модерация предложений YML');
		}

		$yml = new \App\Model\Yml();
		$id_firms = array_keys($yml->reader()->setSelect(['id', 'id_firm'])
						->setWhere(['AND', 'status = :status'], [':status' => 'complete_success'])
						->rowsWithKey('id_firm'));

		$firm = new \App\Model\Firm();
		$firms = $firm->reader()->setSelect(['id', 'company_name'])->objectsByIds($id_firms);

		$presenter = new \App\Presenter\YmlOffersItems();
		$presenter->find($filters);
		//list($group_matrix, $groups, $subgroups) = $this->getGroupsAndSubgroups($filters);

		$ids_of_catalogs = $this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('firms', $firms)
				->set('filters', $filters)
				->set('items', $presenter->renderItems())
				->set('items_count', $presenter->pagination()->getTotalRecords())
				->set('pagination', $presenter->pagination()->render(true))
				//
//				->set('group_matrix', $group_matrix)
//				->set('groups', $groups)
//				->set('subgroups', $subgroups)
				->setTemplate('yml_offers', 'firmmanager')
				->save();
	}

	private function getGroupsAndSubgroups($filters) {
		$groups = [];
		$subgroups = [];
		$matrix = [];

		$yml_category = new \App\Model\YmlCategory();
		$yml_category_where = ['AND', 'id_catalog != :id_catalog', 'flag_is_fixed = :flag_is_fixed'];
		$yml_category_params = [':id_catalog' => 0, ':flag_is_fixed' => 1];

		if ($filters['id_firm']) {
			$yml_category_where[] = 'id_firm = :id_firm';
			$yml_category_params[':id_firm'] = $filters['id_firm'];
		}

		$ids_catalogs = $yml_category->reader()
				->setSelect(['DISTINCT id_catalog'])
				->setWhere($yml_category_where, $yml_category_params)
				->rowsWithKey('id_catalog');

		if ($ids_catalogs) {
			$ids_catalogs_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(array_keys($ids_catalogs), 'id');
			$catalog = new \App\Model\PriceCatalog();
			$items = $catalog->reader()
					->setSelect(['id_group', 'id_subgroup'])
					->setWhere($ids_catalogs_conds['where'], $ids_catalogs_conds['params'])
					->rows();

			$_groups = [];
			$_subgroups = [];

			foreach ($items as $item) {
				$_groups[$item['id_group']] = 1;
				$_subgroups[$item['id_subgroup']] = 1;
				if (!isset($matrix[$item['id_group']])) {
					$matrix[$item['id_group']] = [];
				}

				if (!in_array($item['id_subgroup'], $matrix[$item['id_group']])) {
					$matrix[$item['id_group']][] = $item['id_subgroup'];
				}
			}

			$sgr = new \App\Model\StsSubgroup();
			$sgr_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(array_keys($_subgroups), 'id_subgroup');
			$subgroups = $sgr->reader()
					->setWhere($sgr_conds['where'], $sgr_conds['params'])
					->setOrderBy('name ASC')
					->rowsWithKey('id_subgroup');

			$gr = new \App\Model\StsGroups();
			$gr_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(array_keys($_groups), 'id_group');
			$groups = $sgr->reader()
					->setWhere($sgr_conds['where'], $sgr_conds['params'])
					->setOrderBy('name ASC')
					->rowsWithKey('id_subgroup');
		}

		return [$matrix, $groups, $subgroups];
	}

}
