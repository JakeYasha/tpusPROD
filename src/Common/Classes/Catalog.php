<?php

namespace App\Classes;

use App\Model\PriceCatalog;
use App\Model\PriceCatalogPrice;
use Foolz\SphinxQL\SphinxQL;
use Sky4\Model\Utils;
use const SPHINX_MAX_INT;
use const SPHINX_PRICE_DRAFT_INDEX;
use const SPHINX_PRICE_INDEX;
use function app;
use function str;

class Catalog extends \App\Action\Crontab {

	protected $tmp_mode = false;
	protected $table = 'price_catalog_price';

	public function setTable($table) {
		$this->table = $table;
		return $this;
	}

	public function getTable() {
		return $this->table;
	}

	public function setTmpMode() {
		$this->tmp_mode = true;
		$this->createTempTable('price_catalog_price');
		$this->setTable('tmp_price_catalog_price');
		return $this;
	}

	public function fullCatalogRebuild() {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		app()->db()->query()->setText('TRUNCATE TABLE '.$this->getTable())->execute();

		$cat = new PriceCatalog();

		$levels = [2, 3, 4, 5, 6, 7];
		$i = 0;
		$catalog_objects = [];
		$catalog_index = -1;
		foreach ($levels as $level) {
			if ($level === 2) {
				echo PHP_EOL.'start level 2';
				$_catalogs = $cat->reader()
						->setSelect(['id', 'id_subgroup', 'node_level', 'parent_node', 'flag_is_strict', 'name', 'path'])
						->setWhere(['AND', 'node_level = :node_level', 'flag_is_catalog = :flag_is_catalog'], [':node_level' => $level, ':flag_is_catalog' => 1])
						->setOrderBy('id ASC')
						->objects();

				$catalogs = [];
				foreach ($_catalogs as $catalog) {
					$catalogs[$catalog->val('id_subgroup')] = $catalog;
				}

				$limit = 200000;
				$offset = -$limit;
				while (1) {
					$offset += $limit;
					$price = new \App\Model\Price();
					$_id_prices = $price->reader()->setSelect(['id', 'id_firm', 'id_subgroup'])
							->setWhere(['AND', 'flag_is_active = :flag_is_active'], [':flag_is_active' => 1])
							->setLimit($limit, $offset)
							->setOrderBy('id DESC')
							->rowsWithKey();

					if ( ! $_id_prices) {
						break;
					}

					$j = 0;
					foreach ($_id_prices as $sprice) {
						$j ++;
						if (isset($catalogs[$sprice['id_subgroup']])) {
							$catalog_sgr = $catalogs[$sprice['id_subgroup']];
							app()->db()->query()->setText('REPLACE INTO `'.$this->getTable().'` SET `id_catalog` = :id_catalog, id_firm = :id_firm, id_price = :id_price, path = :path, node_level = :node_level')
									->execute([
										':id_catalog' => $catalog_sgr->id(),
										':id_firm' => $sprice['id_firm'],
										':id_price' => $sprice['id'],
										':path' => $catalog_sgr->getPathString(),
										':node_level' => $catalog_sgr->val('node_level')
							]);
						}
					}
				}
			} else {
				echo PHP_EOL.'start level '.$level;
				$catalogs = $cat->reader()
						->setSelect(['id', 'id_subgroup', 'node_level', 'parent_node', 'flag_is_strict', 'name', 'path'])
						->setWhere(['AND', 'node_level = :node_level', 'flag_is_catalog = :flag_is_catalog'], [':node_level' => $level, ':flag_is_catalog' => 1])
						->setOrderBy('id ASC')
						->objects();

				$j = 0;
				foreach ($catalogs as $id_catalog => $catalog) {
					$i ++;
					$j ++;
					$catalog_index ++;
					$level = (int)$catalog->val('node_level');
					$ids_prices = [];

					$this->setSphinxParams($sphinx, $catalog, true);
					$sphinx = $sphinx->enqueue();
					$catalog_objects[$catalog_index] = $catalog;

					if ($i % 200 === 0) {
						$j = 0;
                        try {
                            app()->log("\r".date('H:i:s').' executeBatch', 1);
                            $ids_prices = $sphinx->executeBatch();
                        } catch (\Sky4\Exception $exc) {
                            app()->log("\r".date('H:i:s').' ERROR ' . $exc->getMessage(), 1);
                        }
						$sphinx = SphinxQL::create(app()->getSphinxConnection());
						foreach ($ids_prices as $key => $chunk) {
							//$path = $catalog_objects[$key]->getPathString();
							$this->setPriceCatalog($chunk, $catalog_objects[$key]);
						}

						app()->log("\r".date('H:i:s').' '.$i.'          ', 1);
						$catalog_index = -1;
						$catalog_objects = [];
					}
				}

				if ($j !== 0) {
					$ids_prices = $sphinx->executeBatch();
					$sphinx = SphinxQL::create(app()->getSphinxConnection());
					foreach ($ids_prices as $key => $chunk) {
						$this->setPriceCatalog($chunk, $catalog_objects[$key]);
					}

					$catalog_index = -1;
					$catalog_objects = [];
				}
			}
			app()->log('Level '.$level.' end'.PHP_EOL, 1);
		}
		$this->storeYml();
		app()->log('готово', 1);

		if ($this->tmp_mode) {
			$this->flipTable('price_catalog_price');
		}

		return $this;
	}

	public function rebuildCatalogBySubgroup($id_subgroup) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$cat = new PriceCatalog();

		$levels = [2, 3, 4, 5, 6, 7];
		$i = 0;
		$catalog_objects = [];
		$catalog_index = -1;
		foreach ($levels as $level) {
			if ($level === 2) {
				$_catalogs = $cat->reader()
						->setSelect(['id', 'id_subgroup', 'node_level', 'parent_node', 'flag_is_strict', 'name', 'path'])
						->setWhere(['AND', 'node_level = :node_level', 'flag_is_catalog = :flag_is_catalog', 'id_subgroup = :id_subgroup'], [':node_level' => $level, ':flag_is_catalog' => 1, ':id_subgroup' => $id_subgroup])
						->setOrderBy('id ASC')
						->objects();

				$catalogs = [];
				foreach ($_catalogs as $catalog) {
					$catalogs[$catalog->val('id_subgroup')] = $catalog;
				}

				$limit = 200000;
				$offset = -$limit;
				while (1) {
					$offset += $limit;
					$price = new \App\Model\Price();
					$_id_prices = $price->reader()->setSelect(['id', 'id_firm', 'id_subgroup'])
							->setLimit($limit, $offset)
							->setOrderBy('id DESC')
							->rowsWithKey();

					if ( ! $_id_prices) {
						break;
					}

					$j = 0;
					foreach ($_id_prices as $sprice) {
						$j ++;
						if (isset($catalogs[$sprice['id_subgroup']])) {
							$catalog_sgr = $catalogs[$sprice['id_subgroup']];
							app()->db()->query()->setText('REPLACE INTO `'.$this->getTable().'` SET `id_catalog` = :id_catalog, id_firm = :id_firm, id_price = :id_price, path = :path, node_level = :node_level')
									->execute([
										':id_catalog' => $catalog_sgr->id(),
										':id_firm' => $sprice['id_firm'],
										':id_price' => $sprice['id'],
										':path' => $catalog_sgr->getPathString(),
										':node_level' => $catalog_sgr->val('node_level')
							]);
						}
					}
				}
			} else {
				$catalogs = $cat->reader()
						->setSelect(['id', 'id_subgroup', 'node_level', 'parent_node', 'flag_is_strict', 'name', 'path'])
						->setWhere(['AND', 'node_level = :node_level', 'flag_is_catalog = :flag_is_catalog', 'id_subgroup = :id_subgroup'], [':node_level' => $level, ':flag_is_catalog' => 1, ':id_subgroup' => $id_subgroup])
						->setOrderBy('id ASC')
						->objects();

				$j = 0;
				foreach ($catalogs as $id_catalog => $catalog) {
					$i ++;
					$j ++;
					$catalog_index ++;
					$level = (int)$catalog->val('node_level');
					$ids_prices = [];

					$this->setSphinxParams($sphinx, $catalog, true);
					$sphinx = $sphinx->enqueue();
					$catalog_objects[$catalog_index] = $catalog;

					if ($i % 200 === 0) {
						$j = 0;
						$ids_prices = $sphinx->executeBatch();
						$sphinx = SphinxQL::create(app()->getSphinxConnection());
						foreach ($ids_prices as $key => $chunk) {
							$path = $catalog_objects[$key]->getPathString();
							$this->setPriceCatalog($chunk, $catalog_objects[$key]);
						}

						app()->log("\r".date('H:i:s').' '.$i.'          ', 1);
						$catalog_index = -1;
						$catalog_objects = [];
					}
				}

				if ($j !== 0) {
					$ids_prices = $sphinx->executeBatch();
					$sphinx = SphinxQL::create(app()->getSphinxConnection());
					foreach ($ids_prices as $key => $chunk) {
						$this->setPriceCatalog($chunk, $catalog_objects[$key]);
					}

					$catalog_index = -1;
					$catalog_objects = [];
				}
			}
		}
		return $this;
	}

	public function fullCatalogRebuildForFirm(\App\Model\Firm $firm) {
		if ($firm->exists()) {
			$sphinx = SphinxQL::create(app()->getSphinxConnection());
			$cat = new PriceCatalog();
			$id_subgroups = [];
			$_id_subgroups = $sphinx->select('id_subgroup')
					->from(SPHINX_PRICE_INDEX)
					->where('id_firm', '=', (int)$firm->id())
					->limit(0, SPHINX_MAX_INT)
					->groupBy('id_subgroup')
					->option('max_matches', SPHINX_MAX_INT)
					->execute();

			foreach ($_id_subgroups as $ssgr) {
				$id_subgroups[] = $ssgr['id_subgroup'];
			}

			$id_subgroups_conds = Utils::prepareWhereCondsFromArray($id_subgroups, 'id_subgroup');


			$levels = [2, 3, 4, 5, 6, 7];
			$i = 0;
			$catalog_objects = [];
			$catalog_index = -1;
			foreach ($levels as $level) {
				if ($level === 2) {
					$_catalogs = $cat->reader()
							->setSelect(['id', 'id_subgroup', 'node_level', 'parent_node', 'flag_is_strict', 'name', 'path'])
							->setWhere(['AND', 'node_level = :node_level', 'flag_is_catalog = :flag_is_catalog', $id_subgroups_conds['where']], [':node_level' => $level, ':flag_is_catalog' => 1] + $id_subgroups_conds['params'])
							->setOrderBy('id ASC')
							->objects();

					$catalogs = [];
					foreach ($_catalogs as $catalog) {
						$catalogs[$catalog->val('id_subgroup')] = $catalog;
					}

					$limit = 10000;
					$offset = -$limit;
					while (1) {
						$offset += $limit;
						$_id_prices = $sphinx->select(['id', 'id_firm', 'id_subgroup'])
								->from(SPHINX_PRICE_INDEX)
								->where('id_firm', '=', (int)$firm->id())
								->limit($offset, $limit)
								->option('max_matches', 1000000)
								->execute();

						if ( ! $_id_prices) {
							break;
						}

						foreach ($_id_prices as $sprice) {
							if (isset($catalogs[$sprice['id_subgroup']])) {
								$catalog = $catalogs[$sprice['id_subgroup']];
								app()->db()->query()->setText('REPLACE INTO `'.$this->getTable().'` SET `id_catalog` = :id_catalog, id_firm = :id_firm, id_price = :id_price, path = :path, node_level = :node_level')
										->execute([
											':id_catalog' => $catalog->id(),
											':id_firm' => $sprice['id_firm'],
											':id_price' => $sprice['id'],
											':path' => $catalog->getPathString(),
											':node_level' => $catalog->val('node_level')
								]);
							}
						}
					}
				} else {
					$catalogs = $cat->reader()
							->setSelect(['id', 'id_subgroup', 'node_level', 'parent_node', 'flag_is_strict', 'name', 'path'])
							->setWhere(['AND', 'node_level = :node_level', 'flag_is_catalog = :flag_is_catalog', $id_subgroups_conds['where']], [':node_level' => $level, ':flag_is_catalog' => 1] + $id_subgroups_conds['params'])
							->setOrderBy('id ASC')
							->objects();

					$j = 0;
					foreach ($catalogs as $id_catalog => $catalog) {
						$i ++;
						$j ++;
						$catalog_index ++;
						$level = (int)$catalog->val('node_level');
						$ids_prices = [];
						$this->setSphinxParams($sphinx, $catalog, true, $firm->id());
						$sphinx = $sphinx->enqueue();
						$catalog_objects[$catalog_index] = $catalog;

						if ($i % 200 === 0) {
							$j = 0;
							$ids_prices = $sphinx->executeBatch();
							$sphinx = SphinxQL::create(app()->getSphinxConnection());
							foreach ($ids_prices as $key => $chunk) {
								$this->setPriceCatalog($chunk, $catalog_objects[$key]);
							}

							$catalog_index = -1;
							$catalog_objects = [];
						}
					}

					if ($j !== 0) {
						$ids_prices = $sphinx->executeBatch();
						$sphinx = SphinxQL::create(app()->getSphinxConnection());
						foreach ($ids_prices as $key => $chunk) {
							$this->setPriceCatalog($chunk, $catalog_objects[$key]);
						}
					}
				}
			}
		}

		return $this;
	}

	public function execute($id_firm = null, $id_catalog = null, $refresh_offers = null) {
		if ($id_firm === null && $id_catalog === null) {
			$cat = new PriceCatalog();
			$id_subgroups = [];
			app()->resetSphinxConnection();
			$sphinx = SphinxQL::create(app()->getSphinxConnection());
			$_id_subgroups = $sphinx->select('id_subgroup')
					->from(SPHINX_PRICE_DRAFT_INDEX)
					->limit(0, SPHINX_MAX_INT)
					->groupBy('id_subgroup')
					->option('max_matches', SPHINX_MAX_INT)
					->execute();

			print_r(PHP_EOL.count($_id_subgroups));


			foreach ($_id_subgroups as $ssgr) {
				$id_subgroups[] = $ssgr['id_subgroup'];
			}
			$id_subgroups_conds = Utils::prepareWhereCondsFromArray($id_subgroups, 'id_subgroup');

			if ( ! $id_subgroups) {
				return;
			}


			$levels = [2, 3, 4, 5, 6, 7];
			$i = 0;
			$catalog_objects = [];
			$catalog_index = -1;
			foreach ($levels as $level) {
				if ($level === 2) {
					$_catalogs = $cat->reader()
							->setSelect(['id', 'id_subgroup', 'node_level', 'parent_node', 'flag_is_strict', 'name'])
							->setWhere(['AND', 'node_level = :node_level', 'flag_is_catalog = :flag_is_catalog', $id_subgroups_conds['where']], [':node_level' => $level, ':flag_is_catalog' => 1] + $id_subgroups_conds['params'])
							->setOrderBy('id ASC')
							->objects();

					$catalogs = [];
					foreach ($_catalogs as $catalog) {
						$catalogs[$catalog->val('id_subgroup')] = $catalog;
					}

					$limit = 10000;
					$offset = -$limit;
					while (1) {
						$offset += $limit;
						echo PHP_EOL.$offset;
						$_id_prices = $sphinx->select(['id', 'id_firm', 'id_subgroup'])
								->from(SPHINX_PRICE_DRAFT_INDEX)
								->limit($offset, $limit)
								->option('max_matches', SPHINX_MAX_MATCHES_FULL)
								->execute();

						if ( ! $_id_prices) {
							break;
						}

						foreach ($_id_prices as $sprice) {
							if (isset($catalogs[$sprice['id_subgroup']])) {
								$catalog = $catalogs[$sprice['id_subgroup']];
								app()->db()->query()->setText('REPLACE INTO `'.$this->getTable().'` SET `id_catalog` = :id_catalog, id_firm = :id_firm, id_price = :id_price, path = :path, node_level = :node_level')
										->execute([
											':id_catalog' => $catalog->id(),
											':id_firm' => $sprice['id_firm'],
											':id_price' => $sprice['id'],
											':path' => $catalog->getPathString(),
											':node_level' => $catalog->val('node_level')
								]);
							}
						}
					}
				} else {
					$catalogs = $cat->reader()
							->setSelect(['id', 'id_subgroup', 'node_level', 'parent_node', 'flag_is_strict', 'name'])
							->setWhere(['AND', 'node_level = :node_level', 'flag_is_catalog = :flag_is_catalog', $id_subgroups_conds['where']], [':node_level' => $level, ':flag_is_catalog' => 1] + $id_subgroups_conds['params'])
							->setOrderBy('id ASC')
							->objects();

					$j = 0;
					foreach ($catalogs as $id_catalog => $catalog) {
						$i ++;
						$j ++;
						$catalog_index ++;
						$level = (int)$catalog->val('node_level');
						$ids_prices = [];

						$this->setSphinxParams($sphinx, $catalog, true, NULL, SPHINX_PRICE_DRAFT_INDEX);
						$sphinx = $sphinx->enqueue();
						$catalog_objects[$catalog_index] = $catalog;

						if ($i % 200 === 0) {
							$j = 0;
							$ids_prices = $sphinx->executeBatch();
							$sphinx = SphinxQL::create(app()->getSphinxConnection());
							foreach ($ids_prices as $key => $chunk) {
								$this->setPriceCatalog($chunk, $catalog_objects[$key]);
							}

							app()->log("\r".date('H:i:s').' '.$i.'          ', 1);

							$catalog_index = -1;
							$catalog_objects = [];
						}
					}

					if ($j !== 0) {
						$ids_prices = $sphinx->executeBatch();
						$sphinx = SphinxQL::create(app()->getSphinxConnection());
						foreach ($ids_prices as $key => $chunk) {
							$this->setPriceCatalog($chunk, $catalog_objects[$key]);
						}
					}
				}
				app()->log('Level '.$level.'end'.PHP_EOL, 1);
			}

			app()->resetSphinxConnection();
			$sphinx = SphinxQL::create(app()->getSphinxConnection());
			app()->log('готово', 1);
		} else {
			if ($id_catalog === null) {
				$this->storeYml($id_firm, NULL, $this->getTable());
			} else {
				app()->log('Пересчитываем каталог ID '.$id_catalog);
				$this->storeYml($id_firm, $id_catalog, $this->getTable(), true);
				app()->log('Готово');
			}
		}
	}

	public function storeYml($id_firm = null, $id_catalog = null, $table = null, $refresh_offers = null) {
		$yml_offer = new \App\Model\YmlOffer();
		$yml_cat = new \App\Model\YmlCategory();

		$cats_where = ['AND', 'flag_is_fixed = :flag_is_fixed'];
		$cats_params = [':flag_is_fixed' => 1];
		if ($id_firm !== null) {
			$cats_where[] = 'id_firm = :id_firm';
			$cats_params[':id_firm'] = $id_firm;
		}


		if ($id_catalog === null) {
			$cats_where[] = 'id_catalog != :id_catalog';
			$cats_params[':id_catalog'] = 0;
		} else {
			$cats_where[] = 'id_catalog = :id_catalog';
			$cats_params[':id_catalog'] = $id_catalog;
		}

		$i = 0;
		$cats = $yml_cat->reader()
				->setWhere($cats_where, $cats_params)
				->objects();

		$cnt_offers = 0;
		$time = time();
		foreach ($cats as $ycat) {
			$yml_offer = new \App\Model\YmlOffer();
			$offers = $yml_offer->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_yml_category = :id_yml_category', 'status != :status'], [':id_yml_category' => $ycat->id(), ':status' => 'deleted', ':id_firm' => $ycat->id_firm()])
					->objects();

			$cat = new \App\Model\PriceCatalog($ycat->val('id_catalog'));
			if ($cat->exists()) {
				$path = $cat->getPathString();
				if ($offers) {
					$cnt_offers += count($offers);
					foreach ($offers as $offer) {
						$i ++;
						$price = new \App\Model\Price();
						if ($refresh_offers) {
							$offer->update(['id_catalog' => $cat->id()]);
							$price->refreshByYmlOffer($offer);
						}
						if ( ! $price->exists()) {
							$price->objectByYmlOffer($offer);
						}
						if ($price->activeComponent()->isActive()) {
							app()->db()->query()->setText('REPLACE INTO `'.$this->getTable().'` SET `id_catalog` = :id_catalog, id_firm = :id_firm, id_price = :id_price, path = :path, node_level = :node_level')
									->execute([
										':id_catalog' => $cat->id(),
										':id_firm' => $price->id_firm(),
										':id_price' => $price->id(),
										':path' => $path,
										':node_level' => $cat->val('node_level')
							]);
						}
					}
				}
			}
		}

		return $this;
	}

	public function onCatalogUpdate(PriceCatalog $catalog) {
		if ($catalog->isNameChanged()) {
			return $this->rebuildCatalogBySubgroup($catalog->val('id_subgroup'));
		}

		return $this;
	}

	public function onCatalogInsert(PriceCatalog $catalog) {
		$sphinx = SphinxQL::create(app()->getSphinxConnection());
		$this->setSphinxParams($sphinx, $catalog, true);
		$prices = $sphinx->execute();
		$this->setPriceCatalog($prices, $catalog);

		return $this;
	}

	public function onCatalogDelete(PriceCatalog $catalog) {
		if ((int)$catalog->val('node_level') >= 3) {
			return $this->rebuildCatalogBySubgroup($catalog->val('id_subgroup'));
		}

		return $this;
	}

	private function setSphinxParams(SphinxQL &$sphinx, PriceCatalog $catalog, $id_subgroup = false, $id_firm = null, $sphinx_index = SPHINX_PRICE_INDEX) {
		$word = str()->replace($catalog->val('name'), '-', ' ');
		$word = str()->replace($word, '/', ' ');

		if ((int)$catalog->val('flag_is_strict') === 1) {
			$sphinx->select('id', 'id_firm', SphinxQL::expr('WEIGHT() AS weight'))
					->where('weight', '>', 1550)
					->option('field_weights', ['wname' => 10, 'w2name' => 4, 'name' => 1]);
		} else {
			$sphinx->select('id', 'id_firm');
		}

		$sphinx->from($sphinx_index)
				->where('yml', '=', 0)
				->limit(0, SPHINX_MAX_INT)
				->match('name', SphinxQL::expr($word))
				->option('ranker', SphinxQL::expr("bm25"))
				->option('max_matches', SPHINX_MAX_INT);

		if ($id_subgroup) {
			$sphinx->where('id_subgroup', '=', (int)$catalog->val('id_subgroup'));
		}
		if ($id_firm) {
			$sphinx->where('id_firm', '=', (int)$id_firm);
		}

		return $this;
	}

	private function setPriceCatalog($prices, \App\Model\PriceCatalog $catalog) {
		foreach ($prices as $key => $price) {
			$catalog->query()->setText('REPLACE INTO `'.$this->getTable().'` SET `id_catalog` = :id_catalog, id_firm = :id_firm, id_price = :id_price, path = :path, node_level = :node_level')
					->execute([
						':id_catalog' => $catalog->id(),
						':id_firm' => $price['id_firm'],
						':id_price' => $price['id'],
						':path' => $catalog->getPathString(),
						':node_level' => $catalog->val('node_level')
			]);
		}

		return $this;
	}

	public function emptyPriceCatalogForFirm($id_firm, $id_catalog) {
		app()->db()->query()->setText('DELETE FROM `price_catalog_price` WHERE id_firm = :id_firm AND id_catalog = :id_catalog')
				->execute([':id_firm' => $id_firm, ':id_catalog' => $id_catalog]);
		return $this;
	}

}
