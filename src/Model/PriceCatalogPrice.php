<?php

namespace App\Model;

class PriceCatalogPrice extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\IdFirmTrait,
	 Component\IdPriceTrait,
	 Component\IdCatalogTrait;

	public function fields() {
		$c = $this->fieldPropCreator();
		return [
			'path' => $c->stringField('ПУТЬ', 100),
			'node_level' => $c->intField('Уровень', 1)
		];
	}

	/**
	 * Получаем фирмы по вложенным категориям
	 * @return array
	 */
	public function getIdFirmsByCatalog(PriceCatalog $catalog) {
		$result = [];

		$path = $catalog->getPathString();
		$firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');
        // ВОЗМОЖНО ПРОБЛЕМНЫЙ ЗАПРОС
		$rows = $this->reader()->setSelect(['id_firm', 'COUNT(`id_price`) AS `sum`', 'COUNT(DISTINCT `id_catalog`) as `count`'])
				->setWhere(['AND', $firm_conds['where'], 'path LIKE :path'], [':path' => $path.'%'] + $firm_conds['params'])
				->setGroupBy('id_firm')
				->setOrderBy('`count` DESC, `sum` DESC')
				->rows();
        
        // ВОЗМОЖНО ПРОБЛЕМНЫЙ ЗАПРОС
		$rows_with_current_level = $this->reader()->setSelect(['id_firm'])
				->setWhere(['AND', $firm_conds['where'], 'path = :path'], [':path' => $path] + $firm_conds['params'])
				->setGroupBy('id_firm')
				->rowsWithKey('id_firm');

		$_firm = new Firm();
		$total_price_count = 0;
		foreach ($rows as $row) {
			$total_price_count += $row['sum'];
			if (!isset($rows_with_current_level[$row['id_firm']])) {
				$row['count'] ++;
			}

			$rating = $_firm->reader()->setSelect('rating')->setWhere(['AND', 'id = :id'], [':id' => $row['id_firm']])->rowByConds();
            $priority = $_firm->reader()->setSelect('priority')->setWhere(['AND', 'id = :id'], [':id' => $row['id_firm']])->rowByConds();
            if (isset($rating['rating'])) {
                $result[$row['id_firm']] = ((int) $priority['priority'] * 1000 * 10000 * 10 * 100) + ($row['count'] * 10000 * 10 * 100) + ($row['sum'] * 10 * 100) + ((int)($rating['rating'] * 100));
            } else {
                $result[$row['id_firm']] = ((int) $priority['priority'] * 1000 * 10000) + ($row['count'] * 10000) + $row['sum'];
            }
		}

		arsort($result);

		return ['data' => $result, 'total_price_count' => $total_price_count];
	}

	public function getSubCatalogs(PriceCatalog $catalog, $full_tree = false) {
		$result = [];

		$path = $catalog->getPathString();
		$firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');
        // ВОЗМОЖНО ПРОБЛЕМНЙЫЙ ЗАПРОС
		$rows = $this->reader()->setSelect(['id_firm', 'COUNT(`id_price`) AS `count`', 'id_catalog', 'path'])
				->setWhere(['AND', $firm_conds['where'], 'path LIKE :path'], [':path' => $path.'%'] + $firm_conds['params'])
				->setGroupBy('id_firm, id_catalog')
				->setOrderBy('`count` DESC')
				->rows();

		foreach ($rows as $row) {
			$row['path'] = preg_replace('~([^0-9\]\[]+)|(\[{2,})|(\]{2,})~', '', $row['path']);
			$path = str()->replace($row['path'], $catalog->getPathString(), '');
			$path = str()->sub(str()->sub($path, 0, -1), 1);
			$ids = explode('][', $path);
			if (!isset($result[$row['id_firm']])) {
				$result[$row['id_firm']] = [];
			}
			if ($ids[0]) {
				if (!isset($result[$row['id_firm']][$ids[0]])) {
					$result[$row['id_firm']][$ids[0]] = 0;
				}
				$result[$row['id_firm']][$ids[0]] += $row['count'];
			}

			if (!isset($result[$row['id_firm']][$catalog->id()])) {
				$result[$row['id_firm']][$catalog->id()] = 0;
			}

			$result[$row['id_firm']][$catalog->id()] += $row['count'];
		}

		foreach ($result as $id_firm => $catalogs) {
			arsort($result[$id_firm]);
			$result[$id_firm] = [$catalog->id() => $result[$id_firm][$catalog->id()]] + $result[$id_firm];
			if (!$full_tree) {
				$result[$id_firm] = array_slice($result[$id_firm], 0, 3, true);
			}
		}

		return $result;
	}

	public function getCatalogTagsByFirm(Firm $firm, PriceCatalog $catalog = null) {
		$where = ['AND', 'id_firm = :id_firm'];
		$params = [':id_firm' => $firm->id()];

		if ($catalog !== null && $catalog->exists()) {
			$where[] = 'path LIKE :path';
			$where[] = 'path != :path_eq';
			$params[':path'] = $catalog->getPathString().'%';
			$params[':path_eq'] = $catalog->getPathString();
		}

		$rows = $this->reader()->setSelect(['path', 'COUNT(id_price) as `count`'])
				->setWhere($where, $params)
				->setGroupBy('path')
				->rows();

		$matrix = [];
		if ($catalog !== null) {
			foreach ($rows as $row) {
				$row['path'] = preg_replace('~([^0-9\]\[]+)|(\[{2,})|(\]{2,})~', '', $row['path']);
				$path = str()->replace($row['path'], $catalog->getPathString(), '');
				$path = str()->sub(str()->sub($path, 0, -1), 1);
				$ids = explode('][', $path);
				if (!isset($matrix[$ids[0]])) {
					$matrix[$ids[0]] = 0;
				}
				$matrix[$ids[0]] += $row['count'];
			}

			arsort($matrix);
			$objects = $catalog->reader()
					->objectsByIds(array_keys($matrix), 'id');

			$result = [];
			foreach ($matrix as $id_catalog => $count) {
				$result[$id_catalog] = [
					'catalog' => $objects[$id_catalog],
					'count' => $count
				];
			}

			return $result;
		} else {
			$group_ids = [];
			$subgroup_ids = [];
			foreach ($rows as $row) {
				$row['path'] = preg_replace('~([^0-9\]\[]+)|(\[{2,})|(\]{2,})~', '', $row['path']);
				$path = str()->sub(str()->sub($row['path'], 0, -1), 1);
				$ids = explode('][', $path);
				if (isset($ids[1])) {
					if (!isset($matrix[$ids[0]])) {
						$matrix[$ids[0]] = [];
					}
					if (!isset($matrix[$ids[0]][$ids[1]])) {
						$matrix[$ids[0]][$ids[1]] = 0;
					}
					$group_ids[$ids[0]] = 1;
					$subgroup_ids[$ids[1]] = 1;
					$matrix[$ids[0]][$ids[1]] += $row['count'];
				}
			}

			$counter = [];
			foreach ($matrix as $id_group => $subgroups) {
				arsort($matrix[$id_group]);
				$counter[$id_group] = count($subgroups);
			}

			arsort($counter);

			$result = [];
			foreach ($counter as $id_group => $count) {
				$result[$id_group] = $matrix[$id_group];
			}
			$pc = new PriceCatalog();
			$groups = $pc->reader()
					->setSelect(['id', 'name'])
					->rowsByIds(array_keys($group_ids), 'id');
			$subgroups = $pc->reader()
					->setSelect(['id', 'web_many_name as name'])
					->rowsByIds(array_keys($subgroup_ids), 'id');
            
			return [$result, $groups, $subgroups];
		}
	}

	public function getPriceCount(PriceCatalog $catalog) {
		$path = $catalog->getPathString();
		$firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');
        // ВОЗМОЖНО ПРОБЛЕМНЫЙ ЗАПРОС
		return $this->reader()
						->setWhere(['AND', $firm_conds['where'], 'path LIKE :path'], [':path' => $path.'%'] + $firm_conds['params'])
						->count();
	}

	public function getPriceIds(PriceCatalog $catalog, $id_firms = [], $limit = false, $offset = 0) {
		$path = $catalog->getPathString();
		$id_firms = $id_firms ? $id_firms : app()->location()->getFirmIds();
		$firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($id_firms, 'id_firm');
		
		$rdr = $this->reader();
        // ВОЗМОЖНО ПРОБЛЕМНЫЙ ЗАПРОС
		$rdr->setSelect('id_price')
				->setWhere(['AND', $firm_conds['where'], 'path LIKE :path'], [':path' => $path.'%'] + $firm_conds['params']);

		if ($limit !== false) {
			$rdr->setLimit($limit, $offset);
		}

		return array_keys($rdr->rowsWithKey('id_price'));
	}

	public function getPrices(PriceCatalog $catalog) {
		$path = $catalog->getPathString();

		return $this->reader()->setSelect(['`id_price` as `id`', '`id_firm`'])
						->setWhere(['AND', 'path LIKE :path'], [':path' => $path.'%'])
						->rows();
	}

	public function getPriceIdsByFirm(PriceCatalog $catalog, Firm $firm) {
		$path = $catalog->getPathString();
		$firm_conds = ['where' => 'id_firm = :id_firm', 'params' => [':id_firm' => $firm->id()]];
		return array_keys($this->reader()->setSelect('DISTINCT id_price')
						->setWhere(['AND', $firm_conds['where'], 'path LIKE :path'], [':path' => $path.'%'] + $firm_conds['params'])
						->rowsWithKey('id_price'));
	}

	public function getCatalogIdsByPriceIds($price_ids, $level = null) {
		$pcp_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($price_ids, 'id_price');
		$pcp_where = ['AND', $pcp_conds['where']];
		$pcp_params = $pcp_conds['params'];
		if ($level != null) {
			$pcp_where = ['AND', 'node_level = :node_level', $pcp_conds['where']];
			$pcp_params = array_merge([':node_level' => $level], $pcp_conds['params']);
		}

		return array_keys($this->reader()
						->setSelect('id_catalog')
						->setGroupBy('id_catalog')
						->setWhere($pcp_where, $pcp_params)
						->rowsWithKey('id_catalog'));
	}

	public function getTotalPriceCount($firm_ids = null) {
		$firm_ids = $firm_ids === null ? app()->location()->getFirmIds() : $firm_ids;
		$firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($firm_ids, 'id_firm');
		return $this->reader()->setWhere(['AND', $firm_conds['where']], $firm_conds['params'])
						->count();
	}

	public function getTotalFirmCount($firm_ids = null) {
		$firm_ids = $firm_ids === null ? app()->location()->getFirmIds() : $firm_ids;
		$firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($firm_ids, 'id_firm');
		return $this->reader()->setSelect('COUNT(DISTINCT(id_firm)) as `count`')
						->setWhere(['AND', $firm_conds['where']], $firm_conds['params'])
						->rowByConds()['count'];
	}

//	public function updateRtIndex($sphinx = null) {
//		//restructure
//		return $this; //@todo
//		if ($sphinx === null) {
//			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
//		}
//
//		$price_catalog = new PriceCatalog();
//		$price_catalog->reader()->object($this->val('id_catalog'));
//
//		$grand_parent = new PriceCatalog();
//		$grand_parent->reader()->object($this->val('id_parent'));
//
//		$pcp = new PriceCatalogPrice();
//		$count_goods = $pcp->reader()
//				->setWhere(['AND', 'id_catalog = :id_catalog', 'id_firm = :id_firm'], [':id_catalog' => $this->val('id_catalog'), 'id_firm' => $this->id_firm()])
//				->count();
//
//		$row = [
//			'id' => $this->id(),
//			'id_group' => $price_catalog->val('id_group'),
//			'id_catalog' => $price_catalog->id(),
//			'id_parent' => $price_catalog->val('parent_node'),
//			'grand_parent' => $grand_parent->val('parent_node'),
//			'id_firm' => $this->id_firm(),
//			'count' => $count_goods,
//			'count_goods' => $count_goods,
//			'node_level' => $price_catalog->val('node_level'),
//			//
//			'name' => $price_catalog->val('name'),
//			'subgroup_name' => $grand_parent->val('name').' '.$grand_parent->val('web_name').' '.$grand_parent->val('web_many_name'),
//			'web_name' => $price_catalog->val('web_name'),
//			'web_many_name' => $price_catalog->val('web_name')
//		];
//
//		print_r($row);
//		exit();
//
//
//		$sphinx->replace()
//				->into(SPHINX_CATALOG_SUGGEST_INDEX)
//				->set($row)
//				->execute();
//
//		return $this;
//	}
//	public function delete($sphinx = null) {
//		$this->deleteRtIndex($sphinx);
//		return parent::delete();
//	}
//
//	public function deleteRtIndex($sphinx = null) {
//		if ($sphinx === null) {
//			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
//		}
//
//		$sphinx->delete()
//				->from(SPHINX_CATALOG_SUGGEST_INDEX)
//				->where('id', '=', intval($this->id()))
//				->execute();
//
//		return $this;
//	}

	public function getActualCatalogIds($catalog_ids, $max_level = 5) {
		$result = [];

		$firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');
		$where = ['OR'];
		$params = [];

		$cat = new PriceCatalog();
		$catalogs = $cat->reader()->setSelect(['id', 'path'])->objectsByIds($catalog_ids);

		foreach ($catalogs as $cat) {
			$where[] = 'path LIKE :path'.$cat->id();
			$params[':path'.$cat->id()] = $cat->getPathString().'%';
		}
        
		$where = ['AND', $firm_conds['where'], $where, 'node_level < :node_level'];
		$params += $firm_conds['params'] + [':node_level' => $max_level];
        // ВОЗМОЖНО ПРОБЛЕМНЫЙ ЗАПРОС (САМЫЙ)!!!!!!!!!!
		$rows = $this->reader()->setSelect(['id_catalog'])
				->setWhere($where, $params)
				->setGroupBy('id_catalog')
				->rowsWithKey('id_catalog');

		return array_keys($rows);
	}

	public function getCatalogsByIds($catalog_ids) { //возможно тут надо оптимизировать выборку (много данных запрашивается) @todo
		$result = [];

		$firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');
		$where = ['OR'];
		$params = [];

		$cat = new PriceCatalog();
		$catalogs = $cat->reader()->objectsByIds($catalog_ids);
		foreach ($catalogs as $cat) {
			$where[] = 'path LIKE :path'.$cat->id();
			$params[':path'.$cat->id()] = $cat->getPathString().'%';
		}

		$where = ['AND', $firm_conds['where'], $where, 'node_level < :node_level'];
		$params += $firm_conds['params'] + [':node_level' => 5];

		$rows = $this->reader()->setSelect(['COUNT(*) AS `count`', 'id_catalog'])
				->setWhere($where, $params)
				->setGroupBy('id_catalog')
				->setOrderBy('`count` DESC')
				->rowsWithKey('id_catalog');

		if ($rows) {
			$pc = new PriceCatalog();
			$result = $pc->reader()->objectsByIds(array_keys($rows));
		}

		return $result;
	}

	public function getCatalogsByCount(Firm $firm) {
		$catalogs = [];
		$where = ['AND', 'id_firm = :id_firm'];
		$params = ['id_firm' => $firm->id()];

		$pcp = $this->reader()
				->setSelect(['id_catalog', 'COUNT(*) as `count`', 'path'])
				->setWhere($where, $params)
				->setOrderBy('`count` DESC')
				->setGroupBy('id_catalog')
				->rowsWithKey('id_catalog');

		$catalog_ids = array_keys($pcp);

		if ($catalog_ids) {
			$pc = new PriceCatalog();
			$pc_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($catalog_ids);
			$pc_order_by = \Sky4\Model\Utils::prepareOrderByField($catalog_ids, 'id');
			$catalogs = $pc->reader()
					->setWhere($pc_conds['where'], $pc_conds['params'])
					->setOrderBy('`node_level` ASC, '.$pc_order_by['order'])
					->objects();
		}

		$result_catalogs_ids = [];
		$price_catalog_counts = [];
		foreach ($catalogs as $cat) {
			if (true) {//APP_IS_DEV_MODE) {
				$pcp[$cat->id()]['path'] = preg_replace('~([^0-9\]\[]+)|(\[{2,})|(\]{2,})~', '', $pcp[$cat->id()]['path']);
				$path = str()->sub(str()->sub($pcp[$cat->id()]['path'], 0, -1), 1);
				$ids = array_slice(explode('][', $path), 2);

				foreach ($ids as $id) {
					if (!isset($price_catalog_counts[$id])) $price_catalog_counts[$id] = ['id' => $id, 'count' => 0];
					$price_catalog_counts[$id]['count'] += (int)$pcp[$cat->id()]['count'];
				}
			}

			if ($cat->node_level() === 3 && !isset($result_catalogs_ids[$cat->id()])) {
				$result_catalogs_ids[$cat->id()] = [];
			}

			if ($cat->node_level() === 4 && isset($result_catalogs_ids[$cat->parent_node()])) {
				$result_catalogs_ids[$cat->parent_node()][] = $cat->id();
			}
		}

		if (true) {//APP_IS_DEV_MODE) {
			usort($price_catalog_counts, function($pc1, $pc2) {
				return ($pc2['count'] - $pc1['count']);
			});

			$catalog_ids = [];
			foreach ($price_catalog_counts as $price_catalog_count) {
				$catalog_ids[] = (int)$price_catalog_count['id'];
			}

			if ($catalog_ids) {
				$pc = new PriceCatalog();
				$pc_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($catalog_ids);
				$pc_order_by = \Sky4\Model\Utils::prepareOrderByField($catalog_ids, 'id');


				$catalogs = $pc->reader()
						->setWhere($pc_conds['where'], $pc_conds['params'])
						->setOrderBy('`node_level` ASC, '.$pc_order_by['order'])
						->objects();
			}

			$result_catalogs_ids = [];
			foreach ($catalogs as $cat) {
				if ($cat->node_level() === 3 && !isset($result_catalogs_ids[$cat->id()])) {
					$result_catalogs_ids[$cat->id()] = [];
				}

				if ($cat->node_level() === 4 && isset($result_catalogs_ids[$cat->parent_node()])) {
					$result_catalogs_ids[$cat->parent_node()][] = $cat->id();
				}
			}

			//var_dump($price_catalog_counts);
		}

//		foreach ($result_catalogs_ids as $l3 => $l4) {
//			if (!$l4) {
//				unset($result_catalogs_ids[$l3]);
//			}
//		}

		if (!$result_catalogs_ids && $catalogs) {
			foreach ($catalogs as $catalog_id => $catalog) {
				$parent = new PriceCatalog($catalog->parent_node());
				if (!isset($result_catalogs_ids[$parent->id()])) $result_catalogs_ids[$parent->id()] = [];
				$result_catalogs_ids[$parent->id()] [] = $catalog_id;
				$catalogs[$parent->id()] = $parent;
			}
		}

		$i = 0;
		$result = [];
		foreach ($result_catalogs_ids as $l3 => $l4) {
			$i++;
			$result[$l3] = $l4;
			if ($i === 3) {
				break;
			}
		}

		$_result = [];
		$tmp_result = $result;
		foreach ($result as $k => $v) {
			if ($v) {
				$_result[$k] = $v;
				unset($result[$k]);
			}
		}

		foreach ($result as $k => $v) {
			$_result[$k] = $v;
		}

		return ['matrix' => $_result, 'data' => $catalogs];
	}

	public function getCatalogIdsSortByCount($id_group = null) {
		$conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id_firm');
		if ($id_group !== null) {
			$group = (new PriceCatalog())->reader()->setWhere(['AND', 'id_group = :id_group', 'node_level = :node_level'], [':id_group' => $id_group, ':node_level' => 1])
					->objectByConds();
			$conds['where'] = ['AND', $conds['where'], 'path LIKE :path'];
			$conds['params'] += [':path' => '['.$group->id().'][%'];
		}
        $result = $this->reader()->setSelect(['id_catalog', 'COUNT(*) as `count`'])
                        ->setWhere($conds['where'], $conds['params'])
                        ->setGroupBy('id_catalog')
                        ->setOrderBy('count DESC')
                        ->rowsWithKey('id_catalog');

		return $result;
	}

	public static function replace(Price $price, \App\Model\PriceCatalog $catalog) {
		app()->db()->query()->setText('REPLACE INTO `price_catalog_price` SET `id_catalog` = :id_catalog, id_firm = :id_firm, id_price = :id_price, path = :path, node_level = :node_level')
				->execute([
					':id_catalog' => $catalog->id(),
					':id_firm' => $price->val('id_firm'),
					':id_price' => $price->id(),
					':path' => $catalog->getPathString(),
					':node_level' => $catalog->val('node_level')
		]);

		return;
	}

}
