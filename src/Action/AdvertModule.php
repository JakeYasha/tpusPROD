<?php

namespace App\Action;

use App\Classes\Action;
use App\Model\AdvertModule as AdvertModuleModel;
use App\Model\AdvertModuleGroup;
use App\Model\Firm;
use App\Model\FirmPromoCatalog;
use App\Model\PriceCatalog;
use App\Model\Price;
use App\Model\Text;
use App\Presenter\AdvertModuleItems;
use App\Presenter\FirmPromoItems;
use Sky4\Model;
use Sky4\Model\Utils;
use function app;

class AdvertModule extends Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new AdvertModuleModel());
	}

	public function execute() {
		$params = app()->request()->processGetParams([
			'id_group' => ['type' => 'int'],
			'id_subgroup' => ['type' => 'int'],
            'path' => ['type' => 'string']
		]);

		$this->text()->getByLink('/advert-module/');
		app()->breadCrumbs()->setElem($this->text()->name(), app()->link(app()->linkFilter('/advert-module/', ['id_group' => null, 'id_subgroup' => null])));
        
        if (isset($params['path']) && $params['path']) {
            app()->metadata()->setCanonicalUrl(app()->link('/advert-module/'));
        }

		$presenter = new AdvertModuleItems();

		$id_group = $params['id_group'];
		$id_subgroup = $params['id_subgroup'];

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

        if (APP_IS_DEV_MODE) {
            //$presenter->find($id_group, $id_subgroup, $params);
            $presenter->rubrics = $rubrics;
            $presenter->findAdvertModuleByCatalog($params);
        } else {
            $presenter->find($id_group, $id_subgroup, $params);
        }

		$catalog = new PriceCatalog();

		app()->metadata()->setFromModel($this->text(), null)
				->setHeader('Спецпредложения, скидки и акции ' . app()->location()->currentName('prepositional'));

		if (!app()->location()->city()->exists()) {
			app()->metadata()->noIndex();
		}

		if ($id_group !== null) {
			if ($id_subgroup === null) {
				$catalog = $catalog->reader()
						->setWhere(['AND', '`node_level` = :node_level', '`id_group` = :id_group'], [':node_level' => 1, ':id_group' => (int) $id_group])
						->objectByConds();
			} elseif ($id_subgroup !== null) {
				$catalog = $catalog->reader()
						->setWhere(['AND', '`node_level` = :node_level', '`id_group` = :id_group', '`id_subgroup` = :id_subgroup'], [':node_level' => 2, ':id_group' => (int) $id_group, ':id_subgroup' => (int) $id_subgroup])
						->objectByConds();
			}

			$path = $catalog->adjacencyListComponent()->getPath();
			$title = $catalog->name() . ' - скидки и акции ' . app()->location()->currentName('prepositional');

			foreach ($path as $cat) {
				app()->breadCrumbs()
						->setElem($cat->name(), app()->link(app()->linkFilter('/advert-module/', ['id_group' => $cat->id_group()])));
			}
			app()->metadata()->set(new Text(), $title, $title . ', распродажи ' . app()->location()->currentName('genitive') . ', выгодные цены, подарки и бонусы от компаний', $catalog->name() . app()->location()->currentName('prepositional') . ' по выгодным ценам. Воспользуйтесь скидками, получайте бонусы и подарки по акциям, не упустите ни одного предложения', false)
					->setHeader($catalog->name() . ' - скидки и акции');
		}

		$promo_presenter = new FirmPromoItems();
        $promo_presenter->rubrics = $rubrics;
        $promo_presenter->findFirmPromoByCatalog($params['path'], 'firm_promo_presenter_advert_module_promo_short_block');
        
		$tags = $this->getTags($params, $promo_presenter->getItems());
        
        if (!$presenter->getItems() && !$promo_presenter->getItems()) {
            app()->metadata()->noIndex();
        }
        
		$firm_location_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id');
		$_where = [
			'AND',
			'`flag_is_active` = :1',
            'web_site_partner_url <> :web_site_partner_url',
			$firm_location_conds['where'],
		];

		$_params = [
            ':1' => 1, 
            ':web_site_partner_url' => '',
        ];
		$_params = array_merge($_params, $firm_location_conds['params']);
        $yml_firms_reader = new Firm();
        $yml_firms = $yml_firms_reader->reader()
                ->setWhere($_where, $_params)
                ->setOrderBy("`timestamp_inserting` DESC")
                ->objects();
        
		return $this->view()
						->set('advert_restrictions', app()->adv()->renderRestrictions())
						->set('items', $presenter->renderItems())
						->set('yml_firms', $yml_firms)
						->set('tags', $tags)
						->set('promo_rubrics', $rubrics)
						->set('path', $params['path'])
						->set('promo', $promo_presenter->renderItems())
						->set('filters', $params)
						->set('bread_crumbs', app()->breadCrumbs()->render())
						->set('text', app()->metadata()->replaceLocationTemplates($this->text()->val('text')))
						->setTemplate('index')
						->save();
	}

	protected static function getModelInfo(Model $model) { //@watch
		$result = [
			'alias' => $model->alias(),
			'id_firm' => $model->val('id_firm', 0),
			'id_service' => $model->val('id_service', 0),
			'id_city' => $model->val('id_city', app()->location()->currentId()),
			'name' => $model instanceof Price ? ($model->val('name') . ' ' . $model->val('unit') . ' ' . $model->val('vendor')) : $model->val('name', '')
		];

		$result['id'] = $model->id();
		return $result;
	}

	protected function getTags($params, $promo_items = []) {//@watch
		$result = [];
		$catalogs = [];
		$am = new AdvertModuleModel();
		$active_advert_module_ids = $am->getActiveAdvertModuleIds();

		if (!$active_advert_module_ids) return $result;

		if ($params['id_group'] === null && $params['id_subgroup'] === null) {
			$amg = new AdvertModuleGroup();
			$amg_conds = Utils::prepareWhereCondsFromArray($active_advert_module_ids, 'id_advert_module');
			$group_ids = $amg->reader()
					->setSelect('id_group')
					->setWhere($amg_conds['where'], $amg_conds['params'])
					->rowsWithKey('id_group');

			$pc = new PriceCatalog();
			$pc_conds = Utils::prepareWhereCondsFromArray(array_keys($group_ids), 'id_group');
			$pc_conds['where'] = ['AND', '`node_level` = :node_level', $pc_conds['where']];
			$pc_conds['params'] = [':node_level' => 1] + $pc_conds['params'];
			$rows = $pc->reader()
					->setWhere($pc_conds['where'], $pc_conds['params'])
					->rows();

			$ids = [];
			foreach ($rows as $row) {
				$ids[$row['id']] = 1;
			}

			$cat_ids = array_keys($ids);
			$catalog = new PriceCatalog();
			$cat_conds = Utils::prepareWhereCondsFromArray($cat_ids, 'id');
			$catalogs = $catalog->reader()
					->setWhere($cat_conds['where'], $cat_conds['params'])
					->setOrderBy('web_many_name ASC')
					->objects();
		} else if ($params['id_group'] !== null && $params['id_subgroup'] === null) {
			$amg = new AdvertModuleGroup();
			$amg_conds = Utils::prepareWhereCondsFromArray($active_advert_module_ids, 'id_advert_module');
			$amg_conds['where'] = ['AND', '`id_group` = :id_group', $amg_conds['where']];
			$amg_conds['params'] = [':id_group' => (int) $params['id_group']] + $amg_conds['params'];
			$subgroup_ids = $amg->reader()
					->setSelect('id_subgroup')
					->setWhere($amg_conds['where'], $amg_conds['params'])
					->rowsWithKey('id_subgroup');

			if (count($subgroup_ids) > 0) {
				$pc = new PriceCatalog();
				$pc_conds = Utils::prepareWhereCondsFromArray(array_keys($subgroup_ids), 'id_subgroup');
				$pc_conds['where'] = ['AND', '`node_level` = :node_level', $pc_conds['where']];
				$pc_conds['params'] = [':node_level' => 2] + $pc_conds['params'];
				$rows = $pc->reader()
						->setWhere($pc_conds['where'], $pc_conds['params'])
						->rows();

				$ids = [];
				foreach ($rows as $row) {
					$ids[$row['id']] = 1;
				}

				$cat_ids = array_keys($ids);
				$catalog = new PriceCatalog();
				$cat_conds = Utils::prepareWhereCondsFromArray($cat_ids, 'id');
				$catalogs = $catalog->reader()
						->setWhere($cat_conds['where'], $cat_conds['params'])
						->setOrderBy('web_many_name ASC')
						->objects();
			}
		}

		if ($promo_items) {
			$promo_ids = [];
			foreach ($promo_items as $item) {
				$promo_ids[] = $item['id'];
			}

			$fpc = new FirmPromoCatalog();
			$fpc_conds = Utils::prepareWhereCondsFromArray($promo_ids, 'firm_promo_id');
			$price_catalog_ids = array_keys($fpc->reader()
							->setWhere($fpc_conds['where'], $fpc_conds['params'])
							->rowsWithKey('price_catalog_id'));

			$catalog = new PriceCatalog();
			$catalog_items = $catalog->reader()->objectsByIds($price_catalog_ids);


			$_where = ['AND'];
			$_params = [];
			if ($params['id_group'] === null) {
				$id_groups = [];
				foreach ($catalog_items as $item) {
					$id_groups[$item->val('id_group')] = 1;
				}
				$filtered_conds = Utils::prepareWhereCondsFromArray(array_keys($id_groups), 'id_group');
				$_where[] = 'node_level = :node_level';
				$_params[':node_level'] = 1;
			} elseif ($params['id_group'] !== null && $params['id_subgroup'] === null) {
				$id_groups = [];
				foreach ($catalog_items as $item) {
					$id_groups[$item->val('id_subgroup')] = 1;
				}
				$filtered_conds = Utils::prepareWhereCondsFromArray(array_keys($id_groups), 'id_subgroup');
				$_where[] = 'node_level = :node_level';
				$_where[] = 'id_group = :id_group';
				$_params[':node_level'] = 2;
				$_params[':id_group'] = $params['id_group'];
			} else {
				$id_groups = [];
				foreach ($catalog_items as $item) {
					$id_groups[$item->val('id_subgroup')] = 1;
				}
				$parent = new PriceCatalog();
				$parent->setWhere(['AND', 'id_group = :id_group', 'id_subgroup = :id_subgroup', 'node_level = :node_level'], [
					':id_group' => $params['id_group'],
					':id_subgroup' => $params['id_subgroup'],
					':node_level' => 2,
				]);

				$filtered_conds = Utils::prepareWhereCondsFromArray(array_keys($id_groups), 'id_subgroup');
				$_where[] = 'node_level = :node_level';
				$_where[] = 'parent_node = :parent_node';
				$_params[':node_level'] = 3;
				$_params[':parent_node'] = $parent->id();
			}

			if ($_params) {
				$_where[] = $filtered_conds['where'];
				$_params += $filtered_conds['params'];
				$cat = new PriceCatalog();
				$promo_catalogs = $cat->reader()
						->setWhere($_where, $_params)
						->objects();

				foreach ($promo_catalogs as $cat) {
					$result[$cat->id()] = [
						'catalog' => $cat,
						'count' => 0
					];
				}
			}
		}

		foreach ($catalogs as $cat) {
			$result[$cat->id()] = [
				'catalog' => $cat,
				'count' => 0
			];
		}

		return $result;
	}

}
