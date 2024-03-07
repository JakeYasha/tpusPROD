<?php

namespace App\Presenter;

use App\Model\Firm;
use App\Model\FirmPromo;
use App\Model\FirmPromoCatalog;
use App\Model\PriceCatalog;
use Sky4\Helper\DeprecatedDateTime as DateTime;
use Sky4\Model\Utils;

class FirmPromoItems extends Presenter {

    public $path = '';
    public $rubrics = [];

    
	public function __construct() {
		parent::__construct();
		$this->setItemsTemplate('firm_promo_presenter_items')
				->setItemsTemplateSubdirName('firmpromo')
				->setModelName('FirmPromo');
		return true;
	}

	/**
	 * @return \\App\Classes\Pagination
	 */
	public function pagination() {
		if ($this->pagination === null) {
			$this->pagination = new \App\Classes\Pagination();
		}
		return $this->pagination;
	}

	public function getPage() {
		$params = app()->request()->processGetParams(['page' => 'int']);
		if ($params['page']) return $params['page'];
		return 1;
	}

	public function find($params) {
		$firm = new Firm();
		$conds_city_id = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
		$_where = [
			'AND',
			//['OR',
			//'`flag_is_infinite` = :flag',
			['AND', 'timestamp_ending > :now'],
			//],
			['AND', 'flag_is_active = :flag', $conds_city_id['where']]
		];

		$_params = array_merge([':flag' => 1, ':now' => DateTime::now()], $conds_city_id['params']);

		if ($params['id_catalog'] !== null) {
			$cat = new PriceCatalog($params['id_catalog']);
			$childs = $cat->adjacencyListComponent()->getChildren();

			$ids = [$cat->id()];
			foreach ($childs as $child) {
				$ids[] = $child->id();
			}

			$catalog_conds = Utils::prepareWhereCondsFromArray($ids, 'price_catalog_id');
			$fpc = new FirmPromoCatalog();
			$promo_ids = $fpc->reader()
					->setWhere($catalog_conds['where'], $catalog_conds['params'])
					->rowsWithKey('firm_promo_id');

			if ($promo_ids) {
				$promo_conds = Utils::prepareWhereCondsFromArray(array_keys($promo_ids), 'id');
				$_where[] = $promo_conds['where'];
				$_params += $promo_conds['params'];
			}
		}

		$this->pagination()
				->setLimit(20)
				->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count())
				->setLink(app()->link('/firm-promo/'))
				->setPage($this->getPage())
				->calculateParams()
				->renderElems();

		$_items = $this->model()->reader()
				->setWhere($_where, $_params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		$files = [];
		$_catalog_ids = [];
		foreach ($_items as $item) {
			$files[] = Utils::getFirstCompositeId($item->val('image'));
			$catalog_ids = explode(',', $item->val('catalog_ids'));
			$catalog_ids = array_filter($catalog_ids);
			foreach ($catalog_ids as $cid) {
				$_catalog_ids[] = $cid;
			}
		}

		$catalog = new PriceCatalog();
		$catalogs = $catalog->reader()->objectsByIds($_catalog_ids);
		foreach ($catalogs as $cat) {
			app()->adv()->setAdvertRestrictions($cat->val('advert_restrictions'));
		}

		$files = Utils::getObjectsByIds($files);

		$items = [];
		foreach ($_items as $item) {
			$image_key = Utils::getFirstCompositeId($item->val('image'));
			$firm = new Firm();
			$firm->getByIdFirm($item->id_firm());
			if ($firm->isBlocked()) continue;
			$items[] = FirmPromo::prepare($item, isset($files[$image_key]) ? $files[$image_key]->iconLink('-320x180') : false);
		}

		$this->items = $items;

		return $this;
	}

	public function findSameFirmPromos($id_firm, $id_promo) {
		$_where = [
			'AND',
			/* ['OR',
			  '`flag_is_infinite` = :flag', */
			['AND', /* '`timestamp_beginning`< :now', */ 'timestamp_ending > :now'],
			/* ], */
			['AND', '`flag_is_active` = :flag', '`id_firm` = :id_firm', '`id` != :id']
		];

		$_params = [':flag' => 1, ':now' => DateTime::now(), ':id_firm' => $id_firm, ':id' => $id_promo];

		$this->pagination()
				->setLimit(20)
				->setTotalRecords($this->model()->reader()->setWhere($_where, $_params)->count());

		$_items = $this->model()->reader()
				->setWhere($_where, $_params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('RAND()')
				->objects();

		$files = [];
		foreach ($_items as $item) {
			$files[] = Utils::getFirstCompositeId($item->val('image'));
		}

		$files = Utils::getObjectsByIds($files);

		$items = [];
		foreach ($_items as $item) {
			$image_key = Utils::getFirstCompositeId($item->val('image'));
			$items[] = FirmPromo::prepare($item, isset($files[$image_key]) ? $files[$image_key]->iconLink('-320x180') : false);
		}

		$this->items = $items;
		$this->setItemsTemplate('firm_promo_presenter_same_firm_items');

		return $this;
	}

	public function findFirmPromos($id_group = null, $id_subgroup = null, $template = 'firm_promo_presenter_catalog_promo_block') {
		$pc = new PriceCatalog();

		$pc_where = ['AND', '`node_level` = :node_level'];
		$pc_params = [':node_level' => 2];

		if (((int) $id_group === 44 || (int) $id_group === 22 || $id_group !== null) && $id_group !== 0) {
			$pc_where[] = '`id_group` = :id_group';
			$pc_params[':id_group'] = $id_group;
		} elseif ($id_group === 0) {
			$pc_where[] = '`id_group` != :id_group1';
			$pc_where[] = '`id_group` != :id_group2';
			$pc_params[':id_group1'] = 22;
			$pc_params[':id_group2'] = 44;
		}

		if ($id_subgroup !== null) {
			$pc_where[] = '`id_subgroup` = :id_subgroup';
			$pc_params[':id_subgroup'] = $id_subgroup;
		}

		$pc_ids = $pc->reader()
				->setSelect(['id'])
				->setWhere($pc_where, $pc_params)
				->rowsWithKey('id');

		$fp_ids = [];
		if ($pc_ids) {
			$fpc = new FirmPromoCatalog();
			$fpc_conds = Utils::prepareWhereCondsFromArray(array_keys($pc_ids), 'price_catalog_id');
			$fp_ids = $fpc->reader()
					->setSelect('firm_promo_id')
					->setWhere($fpc_conds['where'], $fpc_conds['params'])
					->rowsWithKey('firm_promo_id');

			$fp = new FirmPromo();
			$fp_conds = Utils::prepareWhereCondsFromArray(array_keys($fp_ids), 'id');
			$fp_city_conds = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
		}

		if ($fp_ids) {
			$_where = [
				'AND',
				'timestamp_ending > :now',
				'`flag_is_active` = :flag',
				$fp_conds['where'],
				$fp_city_conds['where']
			];

			$_params = [':flag' => 1, ':now' => DateTime::now()] + $fp_conds['params'] + $fp_city_conds['params'];
			$_items = $fp->reader()
					->setWhere($_where, $_params)
					->setOrderBy('RAND()')
					->objects();

			$files = [];
			foreach ($_items as $item) {
				$files[] = Utils::getFirstCompositeId($item->val('image'));
			}

			$files = Utils::getObjectsByIds($files);

			$items = [];
			foreach ($_items as $item) {
				$firm = new Firm($item->id_firm());
				if ($firm->isBlocked()) {
					continue;
				}

				$image_key = Utils::getFirstCompositeId($item->val('image'));
				$items[] = FirmPromo::prepare($item, isset($files[$image_key]) ? $files[$image_key]->iconLink('-320x180') : false);
			}

			$this->items = $items;
			$this->setItemsTemplate($template);
		}

		return $this;
	}
    
    public function findIndexFirmPromos() {
        $fp = new FirmPromo();
        $fp_city_conds = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');

        $_where = [
            'AND',
            'timestamp_ending > :now',
            '`flag_is_active` = :flag',
            $fp_city_conds['where']
        ];

        $_params = [':flag' => 1, ':now' => DateTime::now()] + $fp_city_conds['params'];
        $_items = $fp->reader()
                ->setWhere($_where, $_params)
                ->setOrderBy('RAND()')
                ->setLimit(4)
                ->objects();

        $files = [];
        foreach ($_items as $item) {
            $files[] = Utils::getFirstCompositeId($item->val('image'));
        }

        $files = Utils::getObjectsByIds($files);

        $items = [];
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
            $firm = new Firm($item->id_firm());
            if ($firm->isBlocked()) {
                continue;
            }

            $image_key = Utils::getFirstCompositeId($item->val('image'));
            $_item = FirmPromo::prepare($item, isset($files[$image_key]) ? $files[$image_key]->iconLink('-320x180') : false);
            foreach($rubrics as $name => $rubric) {
                if (count(array_intersect($rubric['data'], explode(",", $item->val('catalog_ids'))))) {
                    $_item['rubric'] = $name;
                }
            }
            $items[] = $_item;
        }

        $this->items = $items;
        $this->setTemplateSubdirName('views3/firmpromo')
                ->setItemsTemplate('../../views3/firmpromo/firm_promo_presenter_index_promo_block');

        return $this;
	}
    
    public function findFirmPromoByCatalog($path = '', $template = 'firm_promo_presenter_catalog_promo_block') {
        $active_catalog_ids = [];
        foreach($this->rubrics as $promo_rubric_name => $promo_rubric) {
            if ($path == md5($promo_rubric_name)) {
                $active_catalog_ids = $promo_rubric['data'];
            }
        }

        $pc_ids_conds = [];
        if($active_catalog_ids) {
            $pc_ids_conds = Utils::prepareWhereCondsFromArray($active_catalog_ids);
        }
        
        $pc_ids = [];
        $pc = new PriceCatalog();
        if ($active_catalog_ids) {
            $pc_ids = $pc->reader()
                    ->setSelect(['id'])
                    ->setWhere($pc_ids_conds['where'], $pc_ids_conds['params'])
                    ->rowsWithKey('id');
        }

		$fp_ids = [];
		if ($pc_ids) {
			$fpc = new FirmPromoCatalog();
			$fpc_conds = Utils::prepareWhereCondsFromArray(array_keys($pc_ids), 'price_catalog_id');
			$fp_ids = $fpc->reader()
					->setSelect('firm_promo_id')
					->setWhere($fpc_conds['where'], $fpc_conds['params'])
					->rowsWithKey('firm_promo_id');

			$fp = new FirmPromo();
			$fp_conds = Utils::prepareWhereCondsFromArray(array_keys($fp_ids), 'id');
			$fp_city_conds = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
		} else {
			$fpc = new FirmPromoCatalog();
			$fp_ids = $fpc->reader()
					->setSelect('firm_promo_id')
					->rowsWithKey('firm_promo_id');

			$fp = new FirmPromo();
			$fp_conds = Utils::prepareWhereCondsFromArray(array_keys($fp_ids), 'id');
			$fp_city_conds = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
        }

		if ($fp_ids) {
			$_where = [
				'AND',
				'timestamp_ending > :now',
				'`flag_is_active` = :flag',
				$fp_conds['where'],
				$fp_city_conds['where']
			];

			$_params = [':flag' => 1, ':now' => DateTime::now()] + $fp_conds['params'] + $fp_city_conds['params'];
			$_items = $fp->reader()
					->setWhere($_where, $_params)
					->setOrderBy('RAND()')
					->objects();

			$files = [];
			foreach ($_items as $item) {
				$files[] = Utils::getFirstCompositeId($item->val('image'));
			}

			$files = Utils::getObjectsByIds($files);

			$items = [];
			foreach ($_items as $item) {
				$firm = new Firm($item->id_firm());
				if ($firm->isBlocked()) {
					continue;
				}

				$image_key = Utils::getFirstCompositeId($item->val('image'));
				$items[] = FirmPromo::prepare($item, isset($files[$image_key]) ? $files[$image_key]->iconLink('-320x180') : false);
			}

			$this->items = $items;
			$this->setItemsTemplate($template);
		}

		return $this;
	}
    

	public function findPromosByFirm(Firm $firm, $filters = []) {
		$fp = new FirmPromo();
		$where = ['AND', 'id_firm = :id_firm'];
		$params = [':id_firm' => $firm->id()];

		$this->pagination()
				->setTotalRecords($fp->reader()->setWhere($where, $params)->count())
				->setLink('/firm-user/promo/')
				->setLinkParams($filters)
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->calculateParams();

		$_items = $fp->reader()
				->setWhere($where, $params)
				->setLimit($this->pagination()->getOffset(), $this->pagination()->getLimit())
				->setOrderBy('flag_is_active DESC, timestamp_inserting DESC')
				->objects();

		$files = [];
		foreach ($_items as $item) {
			$files[] = Utils::getFirstCompositeId($item->val('image'));
		}

		$files = Utils::getObjectsByIds($files);

		$items = [];
		foreach ($_items as $item) {
			$image_key = Utils::getFirstCompositeId($item->val('image'));
			$items[] = FirmPromo::prepare($item, isset($files[$image_key]) ? $files[$image_key]->iconLink('-320x180') : false);
		}

		$this->items = $items;
		$this->setItemsTemplate('firm_promo_presenter');

		return $this;
	}

}
