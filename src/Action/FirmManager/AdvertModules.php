<?php

namespace App\Action\FirmManager;

use App\Model\AdvertModule;
use App\Model\AdvertModuleFirmType;
use App\Model\AdvertModuleGroup;
use App\Model\Firm;
use App\Model\FirmManager;
use App\Model\FirmType;
use App\Model\PriceCatalog;
use App\Presenter\FirmUserStatistics;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;

class AdvertModules extends \App\Action\FirmManager {

	public function execute($html_mode = null) {
		app()->breadCrumbs()->setElem('Рекламные модули', self::link('/advert-modules/'));

		$current_id_service = app()->firmManager()->id_service();
		$firm = new Firm();
		$firm_ids_conds = Utils::prepareWhereCondsFromArray(array_keys($firm->reader()->setSelect('id')->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $current_id_service])->rowsWithKey('id')), 'id_firm');

		$this->text()->getByLink('firm-manager/advert-modules');
		app()->metadata()->setFromModel($this->text());
		if (!$this->text()->exists()) {
			app()->metadata()->setTitle('Рекламные модули');
		}

		$advert_module = new AdvertModule();
		$firm_location_conds = Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');
		$advert_module_ids = array_keys($advert_module->reader()
						->setSelect(['id'])
						->setWhere($firm_location_conds['where'], $firm_location_conds['params'])
						->rowsWithKey('id'));

		$firm_ids = array_keys($advert_module->reader()
						->setSelect(['id_firm'])
						->setWhere($firm_location_conds['where'], $firm_location_conds['params'])
						->rowsWithKey('id_firm'));

		$amg = new AdvertModuleGroup();
		$advert_module_groups = [];
		if (count($advert_module_ids) > 0) {
			$amg_conds = Utils::prepareWhereCondsFromArray($advert_module_ids, 'id_advert_module');
			$advert_module_groups = $amg->reader()
					->setWhere($amg_conds['where'], $amg_conds['params'])
					->rows();
		}

		$groups_ids = [];
		$subgroups_ids = [];

		foreach ($advert_module_groups as $amg) {
			$groups_ids[] = $amg['id_group'];
			$subgroups_ids[] = $amg['id_subgroup'];
		}

		$groups_ids = array_filter($groups_ids);
		$subgroups_ids = array_filter($subgroups_ids);

		$pc = new PriceCatalog();
		$groups = [];
		if (count($groups_ids) > 0) {
			$pc_group_conds = Utils::prepareWhereCondsFromArray($groups_ids, 'id_group');
			$groups = $pc->reader()
					->setWhere(['AND', 'node_level = :node_level', $pc_group_conds['where']], [':node_level' => 1] + $pc_group_conds['params'])
					->setOrderBy('web_many_name ASC')
					->objects();
		}

		$subgroups = [];
		if (count($subgroups_ids) > 0) {
			$pc_subgroup_conds = Utils::prepareWhereCondsFromArray($subgroups_ids, 'id_subgroup');
			$subgroups = $pc->reader()
					->setWhere(['AND', 'node_level = :node_level', $pc_subgroup_conds['where']], [':node_level' => 2] + $pc_subgroup_conds['params'])
					->setOrderBy('web_many_name ASC')
					->objects();
		}

		$amft = new AdvertModuleFirmType();
		$advert_module_firm_types = [];
		if (count($advert_module_ids) > 0) {
			$amft_conds = Utils::prepareWhereCondsFromArray($advert_module_ids, 'id_advert_module');
			$advert_module_firm_types = $amft->reader()
					->setWhere($amft_conds['where'], $amft_conds['params'])
					->rows();
		}

		$firmtype_ids = [];
		foreach ($advert_module_firm_types as $amft) {
			$firmtype_ids[] = $amft['id_firm_type'];
		}

		$firmtypes = [];
		if (count($firmtype_ids) > 0) {
			$firmtype_conds = Utils::prepareWhereCondsFromArray($firmtype_ids, 'id');
			$ft = new FirmType();
			$firmtypes = $ft->reader()
					->setWhere($firmtype_conds['where'], $firmtype_conds['params'])
					->setOrderBy('name ASC')
					->objects();
		}

		$this->params = app()->request()->processGetParams([
			'id_group' => ['type' => 'int', 'default_val' => 0],
			'id_subgroup' => ['type' => 'int', 'default_val' => 0],
			'id_manager' => ['type' => 'int', 'default_val' => 0],
			'id_firm' => ['type' => 'int', 'default_val' => 0],
			'id_firm_type' => ['type' => 'int', 'default_val' => 0],
			'linked' => ['type' => 'int', 'default_val' => 0],
			'active' => ['type' => 'int', 'default_val' => 1],
			'type' => ['type' => 'string', 'default_val' => 'all']
		]);

		$this->params['html_mode'] = $html_mode ? true : false;

		$presenter = new FirmUserStatistics();

		$presenter->setLimit(20);
		if ($html_mode) {
			$presenter->setLimit(500);
		}
		//===============================================================
		$presenter->findAdvertModulesByService($this->params);
		//===============================================================
		$managers = [];
		$fm = new FirmManager();
		$managers = $fm->reader()
				->setWhere(['AND', 'id_service = :id_service', 'type != :type'], [':id_service' => $current_id_service, ':type' => 'service'])
				->setOrderBy('name ASC')
				->objects();

		$firms = [];
		$firm = new Firm();
		if (count($firm_ids) > 0) {
			$firm_where = ['AND', 'id_service = :id_service'];
			$firm_params = [':id_service' => $current_id_service];
			$firm_conds = Utils::prepareWhereCondsFromArray($firm_ids, 'id');
			$firm_where[] = $firm_conds['where'];
			$firm_params += $firm_conds['params'];

			$firms = $firm->reader()
					->setWhere($firm_where, $firm_params)
					->setOrderBy('company_name ASC')
					->objects();
		}
		if ($html_mode) {
			return $presenter->renderItems();
		}

		$count_advert_module_types = app()->db()->query()
				->setText("SELECT COUNT(`id`) as `count`, `type` FROM `advert_module`
                                                WHERE ".$firm_ids_conds['where']." AND `flag_is_active` = :flag_is_active AND timestamp_ending > :now
                                                GROUP BY 2
                                                ORDER BY `type`")
				->setParams([':flag_is_active' => 1, ':now' => DeprecatedDateTime::now()] + $firm_ids_conds['params'])
				->fetch();

		$advert_module_count_by_types = [];
		foreach ($count_advert_module_types as $advert_module_type) {
			$advert_module_count_by_types[$advert_module_type['type']] = ['name' => '', 'count' => $advert_module_type['count']];
		}

		$advert_module_types = [];
		foreach ($advert_module->types() as $key => $value) {
			$advert_module_types[$key == '' ? 'context' : $key] = $value;
			$advert_module_count_by_types[$key]['name'] = $value;
		}

		$groups = $this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('firms', $firms)
				->set('groups', $groups)
				->set('types', $advert_module_types)
				->set('advert_module_count_by_types', $advert_module_count_by_types)
				->set('subgroups', $subgroups)
				->set('firmtypes', $firmtypes)
				->set('filters', $this->params)
				->set('items', $presenter->renderItems())
				->set('items_count', $presenter->pagination()->getTotalRecords())
				->set('pagination', $presenter->pagination()->render(true))
				->set('managers', $managers)
				->setTemplate('advert_modules')
				->save();
	}

}
