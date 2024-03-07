<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Banner;
use App\Model\BannerGroup;
use App\Model\BannerCatalog;
use App\Model\Firm;
use App\Model\FirmManager as FirmManagerModel;
use App\Model\PriceCatalog;
use App\Presenter\FirmUserStatistics;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;

class Banners extends FirmManager {

	public function execute($html_mode = false) {
		app()->breadCrumbs()->setElem('Баннеры', self::link('/banners/'));

		$current_id_service = app()->firmManager()->id_service();
		$firm = new Firm();
		$firm_ids = array_keys($firm->reader()
						->setSelect('id')
						->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $current_id_service])
						->rowsWithKey('id'));
		$firm_ids_conds = Utils::prepareWhereCondsFromArray($firm_ids, 'id_firm');

		$this->text()->getByLink('firm-manager/banners');
		app()->metadata()->setFromModel($this->text());
		if (!$this->text()->exists()) {
			app()->metadata()->setTitle('Баннеры');
		}

		$banner = new Banner();
		$banner_ids = array_keys($banner->reader()
						->setSelect(['id'])
						->setWhere($firm_ids_conds['where'], $firm_ids_conds['params'])
						->rowsWithKey('id'));

		$firm_ids = array_keys($banner->reader()
						->setSelect(['id_firm'])
						->setWhere($firm_ids_conds['where'], $firm_ids_conds['params'])
						->rowsWithKey('id_firm'));

		$bg = new BannerGroup();
		$banner_groups = [];
		if (count($banner_ids) > 0) {
			$bg_conds = Utils::prepareWhereCondsFromArray($banner_ids, 'id_banner');
			$banner_groups = $bg->reader()
					->setWhere($bg_conds['where'], $bg_conds['params'])
					->rows();
		}
        
        $bc = new BannerCatalog();
		$banner_catalogs = [];
		if (count($banner_ids) > 0) {
			$bc_conds = Utils::prepareWhereCondsFromArray($banner_ids, 'id_banner');
			$banner_catalogs = $bc->reader()
                    ->setWhere($bc_conds['where'], $bc_conds['params'])
					->rows();
		}

		$groups_ids = [];
		$subgroups_ids = [];

		foreach ($banner_groups as $bg) {
			$groups_ids[] = $bg['id_group'];
			$subgroups_ids[] = $bg['id_subgroup'];
		}
        
        $catalog_ids = [];
		foreach ($banner_catalogs as $bc) {
			$catalog_ids[] = $bc['id_catalog'];
		}

		$groups_ids = array_filter($groups_ids);
		$subgroups_ids = array_filter($subgroups_ids);
        $catalog_ids = array_filter($catalog_ids);

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
        
        $catalogs = [];
		if (count($catalog_ids) > 0) {
			$pc_catalog_conds = Utils::prepareWhereCondsFromArray($catalog_ids, 'id');
			$catalogs = $pc->reader()
                    ->setWhere(['AND', $pc_catalog_conds['where']], $pc_catalog_conds['params'])
					->setOrderBy('web_many_name ASC')
					->objects();
		}

		$this->params = app()->request()->processGetParams([
			'id_group' => ['type' => 'int', 'default_val' => 0],
			'id_subgroup' => ['type' => 'int', 'default_val' => 0],
            'id_catalog' => ['type' => 'int', 'default_val' => 0],
			'id_manager' => ['type' => 'int', 'default_val' => 0],
			'id_firm' => ['type' => 'int', 'default_val' => 0],
			'max_count' => ['type' => 'int', 'default_val' => 1],
			'active' => ['type' => 'int', 'default_val' => 1],
			'type' => ['type' => 'string', 'default_val' => 'all']
		]);

		$this->params['html_mode'] = $html_mode ? true : false;

		$presenter = new FirmUserStatistics();

		$presenter->setLimit(20);
		if ($html_mode) {
			$presenter->setLimit(500);
		}

		$presenter->findBannersByService($current_id_service, $this->params);

		$managers = [];
		$managers = $this->model()->reader()
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

		$firm_ids_conds = Utils::prepareWhereCondsFromArray(array_keys($firm->reader()->setSelect('id')->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $current_id_service])->rowsWithKey('id')), 'id_firm');
		$where = ['AND', $firm_ids_conds['where']];
		$params = $firm_ids_conds['params'];

		$count_banner_types = app()->db()->query()
				->setText("SELECT COUNT(`id`) as `count`, `type` FROM `banner`
                                                WHERE " . $firm_ids_conds['where'] . " AND `flag_is_active` = :flag_is_active AND timestamp_ending > :now
                                                GROUP BY 2
                                                ORDER BY `type`")
				->setParams([':flag_is_active' => 1, ':now' => DeprecatedDateTime::now()] + $firm_ids_conds['params'])
				->fetch();

		$banner_count_by_types = [];
		foreach ($count_banner_types as $banner_type) {
			$banner_count_by_types[$banner_type['type']] = ['name' => '', 'count' => $banner_type['count']];
		}

		$banner_types = [];
		foreach ($banner->types() as $key => $value) {
			$banner_types[$key == '' ? 'context' : $key] = $value;
			$banner_count_by_types[$key]['name'] = $value;
		}

		$groups = $this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('firms', $firms)
				->set('groups', $groups)
				->set('types', $banner_types)
				->set('banner_count_by_types', $banner_count_by_types)
				->set('subgroups', $subgroups)
                ->set('catalogs', $catalogs)
				->set('filters', $this->params)
				->set('items', $presenter->renderItems())
				->set('items_count', $presenter->pagination()->getTotalRecords())
				->set('pagination', $presenter->pagination()->render(true))
				->set('managers', $managers)
				->setTemplate('banners')
				->save();
	}

	public function getData($bannerId = 795) {
		$bannerFind = new Banner();
		
		$bannerFindId = $bannerFind->reader()
						->setSelect(['id', 'timestamp_ending'])
						->setWhere([
							'AND', 
							'id = :id',
				
						], [
							':id' => $bannerId,
						])
						->rowsWithKey('id');			
		return $bannerFindId;
	}

	public function updateData($bannerId = 795) {
		$bannerFind = new Banner();
		
		$bannerFindId = $bannerFind->reader()
						->setSelect(['id', 'timestamp_ending'])
						->setWhere([
							'AND', 
							'id = :id',
				
						], [
							':id' => $bannerId,
						])
						->rowsWithKey('id');

		
		app()->db()->query()
			->setUpdate('banner')
			->setSet(['timestamp_ending' => date("m.d.y H:i:s", time())])
			->setWhere(['AND', '`id` = :id'], [':id' => $bannerId])
			->update();				
		return $bannerFindId;
	}
}
