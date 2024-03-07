<?php

use Foolz\SphinxQL\SphinxQL;
use Foolz\SphinxQL\Connection;
use Foolz\SphinxQL\Exception;

class ACrontabCatalogCounter extends ACrontabAction {

	private $synonyms, $sphinx, $index = 0;

	public function __construct() {
		parent::__construct();
		$this->sphinx = SphinxQL::create(App::getSphinxConnection());
		$syn = new \App\Model\Synonym();
		$this->synonyms = $syn->getList();
	}

	public function run() {
//		$this->log('проверяем соответствие групп');
//		$this->checkGroupIds();
//		exit();
		$this->log('начинаем расчет параметров для каталога');
		$this->createTempTable();

		$cat = new PriceCatalog();
		$catalogs = $cat
				->setWhere(['AND', 'flag_is_catalog = :1', 'node_level = :3'], [':1' => 1, ':3' => 3])
				//->setWhere(['AND', 'node_level = :2'], [':2' => 2])
				->getAll();

		$this->db->query()->setText('ALTER TABLE `tmp_price_catalog_price` DISABLE KEYS');
		$this->db->query()->setText('ALTER TABLE `tmp_price_catalog_count` DISABLE KEYS');
		$this->store($catalogs);
		$this->db->query()->setText('ALTER TABLE `tmp_price_catalog_price` ENABLE KEYS');
		$this->db->query()->setText('ALTER TABLE `tmp_price_catalog_count` ENABLE KEYS');
		$this->flipTables();
		$this->log('готово');
	}

	private function checkGroupIds() {
		$cat = new PriceCatalog();
		$catalogs = $cat
				->setWhere(['AND', 'node_level = :2'], [':2' => 2])
				->getAll();
		$all_count = count($catalogs);
		$i = 0;
		foreach ($catalogs as $cat) {
			$i++;
			$this->recursiveCheck($cat, $cat->id_group(), $cat->id_subgroup());
			echo "\r" . $i . "/" . $all_count;
		}
	}

	private function recursiveCheck($cat, $id_group, $id_subgroup) {
		$children = $cat->adjacencyListComponent()->getChildren();
		foreach ($children as $child) {
			if ($child->id_group() !== $id_group || $child->id_subgroup() !== $id_subgroup) {
				$child->update([
					'id_group' => $id_group,
					'id_subgroup' => $id_subgroup
				]);
				print_r(PHP_EOL . 'error: ' . $child->id());
			}
			$this->recursiveCheck($child, $id_group, $id_subgroup);
		}
	}

	/**
	 * 
	 * @param PriceCatalog[] $catalogs
	 */
	public function store($catalogs, $check_before_insert = null) {
		$sphinx = $this->sphinx;
		foreach ($catalogs as $id_catalog => $catalog) {
			$word = str()->replace($catalog->val('name'), '-', ' ');
			$search = $word;

			$syn = ''; //isset($this->synonyms[trim($catalog->val('name'))]) ? $this->synonyms[trim($catalog->val('name'))] : FALSE;
			if ($syn) {
				$synonym = implode(' ', $syn);
				$search .= '|' . $synonym;
			}
			//echo $search;

			$res = [];

			try {
				if ($catalog->val('flag_is_strict')) {
					$res = $sphinx->select('id', 'id_firm', 'id_service', 'id_city', SphinxQL::expr('COUNT(*) as `count`'), SphinxQL::expr('WEIGHT() AS weight'))
							->offset(0)
							->from(App::SPHINX_PRICE_INDEX)
							->where('id_subgroup', '=', intval($catalog->val('id_subgroup')))
							->where('weight', '>', 1550)
							->groupBy('id_firm')
							->groupBy('id_service')
							->match('name', SphinxQL::expr($search))
							->option('max_matches', 50000)
							->option('field_weights', ['wname' => 10, 'w2name' => 4, 'name' => 1])
							->option('ranker', SphinxQL::expr("bm25"))
							->execute();
				} else {
					$res = $sphinx->select('id', 'id_firm', 'id_service', 'id_city', SphinxQL::expr('COUNT(*) as `count`'))
							->offset(0)
							->from(App::SPHINX_PRICE_INDEX)
							->where('id_subgroup', '=', intval($catalog->val('id_subgroup')))
							->groupBy('id_firm')
							->groupBy('id_service')
							->match('name', SphinxQL::expr($search))
							->option('max_matches', 50000)
							->execute();
				}
			} catch (\Sky4\Exception $exc) {
				$this->log('ошибка: ' . $catalog->id());
			}

			$res_price_ids = [];
			if ($catalog->val('flag_is_strict')) {
				$res_price_ids = $sphinx->select('id', 'id_firm', 'id_service', 'id_city', SphinxQL::expr('WEIGHT() AS weight'))
						->offset(0)
						->from(App::SPHINX_PRICE_INDEX)
						->where('id_subgroup', '=', intval($catalog->val('id_subgroup')))
						->where('weight', '>', 1550)
						->match('name', $search, true)
						->option('max_matches', 50000)
						->option('field_weights', ['wname' => 10, 'w2name' => 4, 'name' => 1])
						->option('ranker', SphinxQL::expr("bm25"))
						->execute();
			} else {
				$res_price_ids = $sphinx->select('id', 'id_firm', 'id_service', 'id_city')
						->offset(0)
						->from(App::SPHINX_PRICE_INDEX)
						->where('id_subgroup', '=', intval($catalog->val('id_subgroup')))
						->match('name', $search, true)
						->option('max_matches', 50000)
						->execute();
			}

			foreach ($res_price_ids as $price_row) {
				$pcp = new PriceCatalogPrice();
				if ($check_before_insert === null) {
					$pcp->setTable('tmp_price_catalog_price');
				}
				$price_id = (int) $price_row['id'];
				if ($price_id) {
					$pcp->insert([
						'price_id' => $price_id,
						'catalog_id' => $id_catalog,
						'node_level' => $catalog->val('node_level'),
						'id_city' => $price_row['id_city'],
						'id_firm' => $price_row['id_firm'],
						'id_service' => $price_row['id_service']
					]);
				}
			}

			if (isset($res[0])) {
				$pcc = new App\Model\PriceCatalogCount();
				if ($check_before_insert === null) {
					$pcc->setTable('tmp_price_catalog_count');
				}

				foreach ($res as $row) {
					if ($check_before_insert === true) {
						$c_where = ['AND', 'id_catalog = :id_catalog', 'id_parent = :id_parent', 'id_city = :id_city', 'id_firm = :id_firm'];
						$c_params = [
							':id_catalog' => $id_catalog,
							':id_parent' => $catalog->val('parent_node'),
							':id_city' => $row['id_city'],
							':id_firm' => $row['id_firm']
						];
						$pcc_check = new PriceCatalogCount();
						$pcc_check->setWhere($c_where, $c_params)->getByConds();
						if ($pcc_check->exists()) {
							$pcc_check->delete();
						}
					}
					$pcc->insert([
						'id_catalog' => $id_catalog,
						'id_parent' => $catalog->val('parent_node'),
						'id_city' => $row['id_city'],
						'id_firm' => $row['id_firm'],
						'id_service' => $row['id_service'],
						'count' => $row['count']
					]);
				}

				$cat = new PriceCatalog();
				$next_catalogs = $cat
						->setWhere(['AND', 'flag_is_catalog = :1', 'parent_node = :parent_node'], [':1' => 1, ':parent_node' => $catalog->id()])
						->getAll();

				if ($next_catalogs) {
					$this->index++;
					//print_r("\r" . $this->index . '==' . $catalog->val('node_level') . ': ' . $catalog->id());
					$this->store($next_catalogs, $check_before_insert);
				}
			}
		}
	}

	protected function createTempTable() {
		try {
			App::db()->query()->dropTable('tmp_price_catalog_count');
		} catch (\Sky4\Exception $exc) {
			;
		}
		try {
			App::db()->query()->dropTable('tmp_price_catalog_price');
		} catch (\Sky4\Exception $exc) {
			;
		}
		App::db()->query()->copyTable('price_catalog_count', 'tmp_price_catalog_count');
		App::db()->query()->copyTable('price_catalog_price', 'tmp_price_catalog_price');

		return $this;
	}

	protected function flipTables() {
		App::db()->query()->renameTable('price_catalog_count', 'del_price_catalog_count');
		App::db()->query()->renameTable('tmp_price_catalog_count', 'price_catalog_count');
		App::db()->query()->dropTable('del_price_catalog_count');

		App::db()->query()->renameTable('price_catalog_price', 'del_price_catalog_price');
		App::db()->query()->renameTable('tmp_price_catalog_price', 'price_catalog_price');
		App::db()->query()->dropTable('del_price_catalog_price');

		return $this;
	}

}
