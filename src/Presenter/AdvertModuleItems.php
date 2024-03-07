<?php

namespace App\Presenter;

use App\Classes\Pagination;
use App\Model\AdvertModule;
use App\Model\AdvertModuleGroup;
use App\Model\AdvertModuleRegion;
use App\Model\AdvertRestrictions;
use App\Model\Firm;
use App\Model\File;
use App\Model\PriceCatalog;
use App\Model\StatObject;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;
use function app;

class AdvertModuleItems extends Presenter {

	private $groups = [];
	public $subgroups = [];
	public $restrictions = [];
    
    public $rubrics = [];

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('advert_module_presenter_items_short')
				->setItemsTemplateSubdirName('advertmodule')
				->setModelName('AdvertModule');
		return true;
	}

	/**
	 * @return \App\Classes\Pagination
	 */
	public function pagination() {
		if ($this->pagination === null) {
			$this->pagination = new Pagination();
		}
		return $this->pagination;
	}

	public function getPage() {
		$params = app()->request()->processGetParams(['page' => 'int']);
		if ($params['page']) return $params['page'];
		return 1;
	}

	public function find($id_group = null, $id_subgroup = null, $filters = null) {
		$amr = new AdvertModuleRegion();
		$am_ids = $amr->reader()
				->setSelect('id_advert_module')
				->setWhere(['AND', '`id_region` = :id_region'], [':id_region' => (int) app()->location()->getRegionId()])
				->rowsWithKey('id_advert_module');
		$conds_am_id = Utils::prepareWhereCondsFromArray(array_keys($am_ids), 'id_advert_module');

		$amg = new AdvertModuleGroup();
		if ($id_group !== null && $id_subgroup === null) {
			$am_ids = $amg->reader()
					->setSelect('id_advert_module')
					->setWhere(['AND', '`id_group` = :id_group', $conds_am_id['where']], [':id_group' => (int) $id_group] + $conds_am_id['params'])
					->rowsWithKey('id_advert_module');
		} else if ($id_group !== null && $id_subgroup !== null) {
			$am_ids = $amg->reader()
					->setSelect('id_advert_module')
					->setWhere(['AND', '`id_group` = :id_group', '`id_subgroup` = :id_subgroup', $conds_am_id['where']], [':id_group' => (int) $id_group, ':id_subgroup' => (int) $id_subgroup] + $conds_am_id['params'])
					->rowsWithKey('id_advert_module');
		}

		if (($id_group !== null || $id_subgroup !== null) && !$am_ids) {
			return $this;
		}

		//$conds_city_id = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
		$_where = ['AND', 'flag_is_active = :flag_is_active', 'timestamp_ending > :now'/* , $conds_city_id['where'] */];
		$_params = [':flag_is_active' => 1, ':now' => DeprecatedDateTime::now()]/* + $conds_city_id['params'] */;
		if (count($am_ids) > 0) {
			$am_conds = Utils::prepareWhereCondsFromArray(array_keys($am_ids), 'id');
			$_where = array_merge($_where, [$am_conds['where']]);
			$_params = $_params + $am_conds['params'];
		}

		$filters_no_page = $filters;
		if (isset($filters_no_page['page'])) {
			unset($filters_no_page['page']);
		}
		$this->pagination()
				->setLimit(24)
				->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
				->setLink(app()->linkFilter(app()->location()->link(app()->linkFilter('/advert-module/', $filters_no_page))))
				//->setLinkParams($filters)
				->setPage($this->getPage())
				->calculateParams()
				->renderElems();

		$items = $this->model()->reader()
				->setWhere($_where, $_params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('`flag_is_commercial` ASC, `priority` DESC, RAND()')
				->objects();

		$files = [];
		$_subgroup_ids = [];
		foreach ($items as $item) {
			$files[] = Utils::getFirstCompositeId($item->val('image'));
			$subgroup_ids = explode(',', $item->val('subgroup_ids'));
			$subgroup_ids = array_filter($subgroup_ids);
			foreach ($subgroup_ids as $sid) {
				$_subgroup_ids[] = $sid;
			}
			$item->subgroups = $subgroup_ids;
			app()->stat()->addObject(StatObject::ADVERT_MODULE_SHOW, $item);
		}
		$files = Utils::getObjectsByIds($files);

		foreach ($items as $item) {
			$_subgroup_ids = $item->subgroups;
			$item->restrictions = array();

			$_catalogs = [];

			if (count($_subgroup_ids) > 0) {
				$subgroup_conds = Utils::prepareWhereCondsFromArray($_subgroup_ids, 'id_subgroup');

				$__where = ['AND', '`node_level` = :node_level', $subgroup_conds['where']];
				$__params = [':node_level' => 2] + $subgroup_conds['params'];

				$cat = new PriceCatalog();
				$_catalogs = $cat->reader()
						->setWhere($__where, $__params)
						->objects();
			}

			$good_restriction = '';
			$service_restriction = '';
			foreach ($_catalogs as $_catalog) {
				$catalogs = $_catalog->adjacencyListComponent()->getPath();
				foreach ($catalogs as $cat) {
					$restriction = new AdvertRestrictions($cat->val('advert_restrictions'));
					if ($restriction->exists() && !in_array($restriction->name(), $item->restrictions)) {
						if ($cat->id_group() == '44') {
							$service_restriction = $restriction->name();
						} else {
							$good_restriction = $restriction->name();
						}
					}
				}
			}
			$item->restrictions = $service_restriction != '' ? array($service_restriction) : array($good_restriction);
		}

		$this->items = $items;

		return $this;
	}
    
    public function findAdvertModuleByCatalog($filters = null) {
		$amr = new AdvertModuleRegion();
		$am_ids = $amr->reader()
				->setSelect('id_advert_module')
				->setWhere(['AND', '`id_region` = :id_region'], [':id_region' => (int) app()->location()->getRegionId()])
				->rowsWithKey('id_advert_module');
		$conds_am_id = Utils::prepareWhereCondsFromArray(array_keys($am_ids), 'id_advert_module');

        $active_catalog_ids = [];
        foreach($this->rubrics as $promo_rubric_name => $promo_rubric) {
            if ($filters['path'] == md5($promo_rubric_name)) {
                $active_catalog_ids = $promo_rubric['data'];
            }
        }
        
        $pc = new PriceCatalog();
        $subgroup_ids = [];
        if ($active_catalog_ids) {
            $subgroup_ids = $pc->getPriceCatalogSubgroups($active_catalog_ids);
        }
        
        if ($subgroup_ids) {
            $conds_subgroup_ids = Utils::prepareWhereCondsFromArray($subgroup_ids, 'id_subgroup');

            $amg = new AdvertModuleGroup();
            $am_ids = $amg->reader()
                    ->setSelect('id_advert_module')
                    ->setWhere(['AND', $conds_subgroup_ids['where'], $conds_am_id['where']], $conds_subgroup_ids['params'] + $conds_am_id['params'])
                    ->rowsWithKey('id_advert_module');
        }
        
        if (!$subgroup_ids && !array_keys($am_ids)) {
            return $this;
        }
		//$conds_city_id = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
		$_where = ['AND', 'flag_is_active = :flag_is_active', 'timestamp_ending > :now'/* , $conds_city_id['where'] */];
		$_params = [':flag_is_active' => 1, ':now' => DeprecatedDateTime::now()]/* + $conds_city_id['params'] */;
		if (array_keys($am_ids)) {
			$am_conds = Utils::prepareWhereCondsFromArray(array_keys($am_ids), 'id');
			$_where = array_merge($_where, [$am_conds['where']]);
			$_params = $_params + $am_conds['params'];
		}

		$filters_no_page = $filters;
		if (isset($filters_no_page['page'])) {
			unset($filters_no_page['page']);
		}
		/*$this->pagination()
				->setLimit(24)
				->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
				->setLink(app()->linkFilter(app()->location()->link(app()->linkFilter('/advert-module/', $filters_no_page))))
				//->setLinkParams($filters)
				->setPage($this->getPage())
				->calculateParams()
				->renderElems();*/

		$items = $this->model()->reader()
				->setWhere($_where, $_params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('`flag_is_commercial` ASC, `priority` DESC, RAND()')
				->objects();
        
		$files = [];
		$_subgroup_ids = [];
		foreach ($items as $item) {
			$files[] = Utils::getFirstCompositeId($item->val('image'));
			$subgroup_ids = explode(',', $item->val('subgroup_ids'));
			$subgroup_ids = array_filter($subgroup_ids);
			foreach ($subgroup_ids as $sid) {
				$_subgroup_ids[] = $sid;
			}
			$item->subgroups = $subgroup_ids;
			app()->stat()->addObject(StatObject::ADVERT_MODULE_SHOW, $item);
		}
		$files = Utils::getObjectsByIds($files);

		foreach ($items as $item) {
			$_subgroup_ids = $item->subgroups;
			$item->restrictions = array();

			$_catalogs = [];

			if (count($_subgroup_ids) > 0) {
				$subgroup_conds = Utils::prepareWhereCondsFromArray($_subgroup_ids, 'id_subgroup');

				$__where = ['AND', '`node_level` = :node_level', $subgroup_conds['where']];
				$__params = [':node_level' => 2] + $subgroup_conds['params'];

				$cat = new PriceCatalog();
				$_catalogs = $cat->reader()
						->setWhere($__where, $__params)
						->objects();
			}

			$good_restriction = '';
			$service_restriction = '';
			foreach ($_catalogs as $_catalog) {
				$catalogs = $_catalog->adjacencyListComponent()->getPath();
				foreach ($catalogs as $cat) {
					$restriction = new AdvertRestrictions($cat->val('advert_restrictions'));
					if ($restriction->exists() && !in_array($restriction->name(), $item->restrictions)) {
						if ($cat->id_group() == '44') {
							$service_restriction = $restriction->name();
						} else {
							$good_restriction = $restriction->name();
						}
					}
				}
			}
			$item->restrictions = $service_restriction != '' ? array($service_restriction) : array($good_restriction);
		}

		$this->items = $items;

		return $this;
	}

	public function findAdvertModulesByFirm(Firm $firm, $filters = []) {
		$advert_module = new AdvertModule();
		$where = ['AND', 'id_firm = :id_firm'];
		$params = [':id_firm' => $firm->id()];

		$this->pagination()
				->setTotalRecords($advert_module->reader()->setWhere($where, $params)->count())
				->setLink('/firm-user/advert-module/')
				->setLinkParams($filters)
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->calculateParams();

		$_items = $advert_module->reader()
				->setWhere($where, $params)
				->setLimit($this->pagination()->getOffset(), $this->pagination()->getLimit())
				->setOrderBy('timestamp_inserting desc ')
				->objects();

		$files = [];
		foreach ($_items as $item) {
			$files[] = Utils::getFirstCompositeId($item->val('full_image'));
			app()->stat()->addObject(StatObject::ADVERT_MODULE_SHOW, $item);
		}


		$files = Utils::getObjectsByIds($files);

		$items = [];

		$stub_file = new File();

		foreach ($_items as $item) {
			$full_image_key = Utils::getFirstCompositeId($item->val('full_image'));
			$items[] = AdvertModule::prepare($item, isset($files[$full_image_key]) ? $files[$full_image_key]->iconLink('-thumb') : $stub_file->get(EMPTY_IMAGE_FILEID_STUB)->iconLink('-thumb'));
		}
		$this->items = $items;
		$this->setItemsTemplate('advert_module_presenter');

		return $this;
	}

	public function findAdvertModules($id_group = null, $id_subgroup = null) {
		$amg = new AdvertModuleGroup();

		$advert_module = new AdvertModule();
		$amr = new AdvertModuleRegion();

		$am_ids = $amr->reader()
				->setSelect('id_advert_module')
				->setWhere(['AND', '`id_region` = :id_region'], [':id_region' => (int) app()->location()->getRegionId()])
				->rowsWithKey('id_advert_module');
		$conds_am_id = Utils::prepareWhereCondsFromArray(array_keys($am_ids), 'id');

		if ($id_group !== null && $id_subgroup === null) {
			$am_ids = $amg->reader()
					->setSelect('id_advert_module')
					->setWhere(['AND', '`id_group` = :id_group', $conds_am_id['where']], [':id_group' => (int) $id_group] + $conds_am_id['params'])
					->rowsWithKey('id_advert_module');
		} else if ($id_group !== null && $id_subgroup !== null) {
			$am_ids = $amg->reader()
					->setSelect('id_advert_module')
					->setWhere(['AND', '`id_group` = :id_group', '`id_subgroup` = :id_subgroup', $conds_am_id['where']], [':id_group' => (int) $id_group, ':id_subgroup' => (int) $id_subgroup] + $conds_am_id['params'])
					->rowsWithKey('id_advert_module');
		}

		if ($am_ids) {
			$am_conds = Utils::prepareWhereCondsFromArray(array_keys($am_ids), 'id');
			$_where = [
				'AND',
				['AND', 'timestamp_ending > :now'],
				['AND', '`flag_is_active` = :flag'],
				//['AND', '`id_city` = :id_city'],
				$am_conds['where']
			];

			$_params = array_merge([':flag' => 1, ':now' => DeprecatedDateTime::now()/* , ':id_city' => app()->location()->currentId() */], $am_conds['params']);

			$_items = $advert_module->reader()
					->setWhere($_where, $_params)
					->setOrderBy('RAND()')
					->objects();
			$files = [];
			$full_files = [];
			foreach ($_items as $item) {
				$files[] = Utils::getFirstCompositeId($item->val('image'));
				$full_files[] = Utils::getFirstCompositeId($item->val('full_image'));
			}

			$files = Utils::getObjectsByIds($files);
			$full_files = Utils::getObjectsByIds($full_files);
			$items = [];

			$stub_file = new File();

			foreach ($_items as $item) {
				$firm = new Firm();
				$firm->getByIdFirm($item->id_firm());
				if ($firm->isBlocked()) {
					continue;
				}
				app()->stat()->addObject(StatObject::ADVERT_MODULE_SHOW, $item);
				$image_key = Utils::getFirstCompositeId($item->val('image'));
				$full_image_key = Utils::getFirstCompositeId($item->val('full_image'));

				$items[] = AdvertModule::prepare($item, isset($files[$image_key]) ? $files[$image_key]->link() : (isset($full_files[$full_image_key]) ? $full_files[$full_image_key]->link() : $stub_file->get(EMPTY_IMAGE_FILEID_STUB)->link()), isset($full_files[$full_image_key]) ? $full_files[$full_image_key]->link() : $stub_file->get(EMPTY_IMAGE_FILEID_STUB)->link());
			}

			$this->items = $items;
			$this->setItemsTemplate('advert_module_presenter_subgroup_advert_module_block');
		}

		return $this;
	}

	public function findIndexAdvertModules() {
		$advert_module = new AdvertModule();
		$amr = new AdvertModuleRegion();

		$am_ids = $amr->reader()
				->setSelect('id_advert_module')
				->setWhere(['AND', '`id_region` = :id_region'], [':id_region' => (int) app()->location()->getRegionId()])
				->rowsWithKey('id_advert_module');
		$am_conds = Utils::prepareWhereCondsFromArray(array_keys($am_ids), 'id');

		if ($am_ids) {
			$_where = [
				'AND',
				['AND', 'timestamp_ending > :now'],
				['AND', '`flag_is_active` = :flag'],
				$am_conds['where']
			];

			$_params = array_merge([':flag' => 1, ':now' => DeprecatedDateTime::now()/* , ':id_city' => app()->location()->currentId() */], $am_conds['params']);

			$_items = $advert_module->reader()
					->setWhere($_where, $_params)
					->setOrderBy('`flag_is_commercial` ASC, RAND()')
                    ->setLimit(3)
					->objects();
            
			$files = [];
			$full_files = [];
			foreach ($_items as $item) {
				$files[] = Utils::getFirstCompositeId($item->val('image'));
				$full_files[] = Utils::getFirstCompositeId($item->val('full_image'));
			}

			$files = Utils::getObjectsByIds($files);
			$full_files = Utils::getObjectsByIds($full_files);
			$items = [];

			$stub_file = new File();

	        $rubrics = [
                'Красота и здоровье' => ['sprite' => 'save_the_children', 'data' => [4355, 4430, 5300, 5347, 7064, 7141, 7903, 7970, 8020, 8084, 17695, 40171, 41094, 43727, 46059, 46478, 48716, 48888]],
                'Рестораны и кафе' => ['sprite' => 'meal', 'data' => [45406]],
                'Досуг, спорт, туризм' => ['sprite' => 'camping_tent', 'data' => [16674, 23056, 23345, 23544, 24164, 24260, 39543, 39548, 39884, 40898, 43627, 43932, 44569, 53040]],
                'Все для детей' => ['sprite' => 'carousel', 'data' => [3025, 4316, 6120, 20363, 49080, 1602, 1700, 1711, 18118, 18418]],
                'Животные и растения' => ['sprite' => 'plush', 'data' => [9655, 10131, 10170, 10248, 10268, 10270, 10397, 10459, 10717, 10760, 10796, 11155, 17294, 39357, 40120]],
                'Строим и обустраиваем' => ['sprite' => 'worker', 'data' => [8790, 8821, 9080, 9349, 9418, 9470, 9475, 9509, 9528, 9551, 5468, 5917, 5971, 5992, 6157, 6728, 53025, 12595, 12964, 13455, 14132, 14268, 14500, 15362, 15664, 15951, 16724, 16832, 17458, 17958, 18086, 21084, 21267, 21371, 21405, 22175, 24355, 24689, 24793, 24948, 25247, 25494, 25854, 26164, 26530, 26588, 26738, 26798, 27025, 41475, 43041, 44645, 44920,  45674, 45977, 47982, 48126, 49406, 49477, 49542, 49632, 49651, 49666, 49708, 50138, 50467, 50636, 50893, 51286, 51616, 2098, 2214, 2288, 2471, 2494, 2589, 2657, 2689, 3008]],
                'Транспорт' => ['sprite' => 'cars', 'data' => [2, 4329, 11472, 29499, 29517, 29549, 29787, 29862, 29960, 30100, 30138, 32839, 35992, 36031, 36688, 36834, 36907, 37001, 37328, 38053, 38622, 38646,38649, 43844, 46338, 53041]],
                'Для учебы и бизнеса' => ['sprite' => 'degrees', 'data' => [4085, 4088, 4130, 4226, 4230, 4274, 4297, 15542, 17056, 17099, 18010, 19758, 20173, 20261, 20348, 246,302, 22209, 24405, 24485, 24542, 24687, 39184,39569, 39932, 40112, 40127, 40863, 41022, 41981, 44137, 44199, 44450, 44583, 44630, 44825, 45622, 46105, 46310, 46348, 46407, 49724]],
                'Обувь, одежда, текстиль' => ['sprite' => 'wedding_dress', 'data' => [18118, 18152, 18173, 18191, 18247, 18374, 18418, 18579, 18745, 18821, 18884, 19026, 19697, 29008, 29193, 29325, 29361, 29485, 41301, 45029, 890, 1023,1098, 1299]],
                'Бытовая техника и электроника' => ['sprite' => 'barcode_scanner', 'data' => [24, 31, 87, 90, 91, 112, 151, 152, 3738, 3741, 3743, 3748, 3762, 3770, 3796, 3806, 3905, 3920, 3923, 4085, 4088, 315, 550, 581, 606, 629, 714, 765, 778, 791, 801, 42112, 42374, 45201, 46480, 48833, 48923, 808, 813, 814, 821, 833, 874, 882]],
                'Разное' => ['sprite' => 'gift', 'data' => [3681, 3738, 10808, 11185, 11253, 157, 162, 243, 11357, 11371, 11378, 11389, 11395, 11409, 11418, 11426, 11441, 11454, 11457, 11708, 11933, 12018, 12158, 12788, 14827, 15134, 15174, 15652, 16116, 16161, 16504, 17860, 17917,  18105, 18107, 18113, 18116, 20373, 20374, 20477, 20508, 20579, 20599, 20615, 20621, 20632, 20693, 20761, 20786, 20824, 20842, 20879, 20903, 20918, 20994, 21027, 21061, 21082, 22186, 22852, 24284, 24337, 24338, 24486, 27279, 27657, 27674, 28212, 28360, 28364, 28369, 28504, 28513, 28718, 28828, 39278, 41597, 41663, 43553, 44618, 45032, 45482, 45606, 45968, 46457,  46463, 53006, 46570, 46666, 46756, 48713, 48820, 48976, 49283, 52502, 2554]]
            ];
            foreach ($_items as $item) {
                $firm = new Firm();
				$firm->getByIdFirm($item->id_firm());
				if ($firm->isBlocked()) {
					continue;
				}
				app()->stat()->addObject(StatObject::ADVERT_MODULE_SHOW, $item);
				$image_key = Utils::getFirstCompositeId($item->val('image'));
				$full_image_key = Utils::getFirstCompositeId($item->val('full_image'));

                $_item = AdvertModule::prepare($item, isset($files[$image_key]) ? $files[$image_key]->link() : (isset($full_files[$full_image_key]) ? $full_files[$full_image_key]->link() : $stub_file->get(EMPTY_IMAGE_FILEID_STUB)->link()), isset($full_files[$full_image_key]) ? $full_files[$full_image_key]->link() : $stub_file->get(EMPTY_IMAGE_FILEID_STUB)->link());
                foreach($rubrics as $name => $rubric) {
                    if (count(array_intersect($rubric['data'], explode(",", $item->val('catalog_ids'))))) {
                        $_item['rubric'] = $name;
                    }
                }
                
				$items[] = $_item;
			}

			$this->items = $items;
			$this->setItemsTemplate('advert_module_presenter_subgroup_advert_module_block');
		}

		return $this;
	}
}
