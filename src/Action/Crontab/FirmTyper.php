<?php

namespace App\Action\Crontab;

use App\Model\CurrentRegionCity;
use App\Model\Firm;
use App\Model\FirmFirmType;
use App\Model\FirmType;
use App\Model\FirmTypeCity;
use Foolz\SphinxQL\SphinxQL;
use Sky4\Model\Utils;

class FirmTyper extends \App\Action\Crontab {

	public function execute() {
		$this->startAction();
		$this->log('заполнение таблицы firm_firm_type');
		$this->createTempTable('firm_firm_type');
		$this->db()->query()->truncateTable('tmp_firm_firm_type');

		$firm_type = new FirmType();
		$firm = new Firm();
		$types = $firm_type->reader()->setWhere('`node_level` = :3', [':3' => 3])->rows();

		$i = 0;
		$total_inserted_rows = 0;
		$cnt = count($types);
		$all_array = [];
		$firm_types = [];

		$sphinx = SphinxQL::create(app()->getSphinxConnection());

		foreach ($types as $type) {
			$i++;
			$level1 = new FirmType($type['parent_node']);
			if (!$level1->exists()) continue;
			$level1 = $level1->id();

			$level0 = $this->db()->query()->setSelect('parent_node')->setFrom(['firm_type'])->setWhere('`id` = :id', [':id' => $level1])->selectRow()['parent_node'];
			if (!isset($firm_types[$level0])) {
				$firm_types[$level0] = [];
			}


			$sphinx->reset();
			$words_list = explode(' ', preg_replace('~ {2,}~', ' ', trim($type['name']))); 
			$search_expression = '"'.implode(' ', $words_list).'" ='.implode('<<=', $words_list);
			$result = $sphinx->select('*')
					->from(SPHINX_FIRM_INDEX)
					->match('(company_name,company_activity)', $search_expression, true)
					->limit(0, SPHINX_MAX_INT)
					->option('ranker', 'none')
					->option('max_matches', SPHINX_MAX_INT)
					->execute();

			$firm_ids = [];
			foreach ($result as $row) {
				$firm_ids[] = $row['id'];
			}

			if ($firm_ids) {
				$id_where_conds = Utils::prepareWhereCondsFromArray($firm_ids);
				$out_array = $firm->reader()->setSelect(['id', 'id_city', 'flag_is_active'])->setWhere($id_where_conds['where'], $id_where_conds['params'])->rows();
				foreach ($out_array as $row) {
					$link = new FirmFirmType();
					$link->setWhere(['AND', '`id_firm` = :row_id', '`id_type` = :type_id'], [':row_id' => $row['id'], ':type_id' => $type['id']])->getByConds();

					if (!$link->exists()) {
                        $firm = new Firm($row['id']);
                        $firm_branches = $firm->getFirmBranches(false);
                        if ($firm_branches) {
                            foreach($firm_branches as $id_city => $items) {
                                $firm_firm_type = new FirmFirmType();
                                $firm_firm_type->setTable('tmp_firm_firm_type');
                                $firm_firm_type->insert([
                                    'id_firm' => $row['id'],
                                    'id_type' => $type['parent_node'],
                                    'id_city' => $id_city,
                                    'flag_is_active' => $row['flag_is_active']
                                ]);
                                $total_inserted_rows++;
                            }
                        }
						$firm_firm_type = new FirmFirmType();
						$firm_firm_type->setTable('tmp_firm_firm_type');
						$firm_firm_type->insert([
							'id_firm' => $row['id'],
							'id_type' => $type['parent_node'],
							'id_city' => $row['id_city'],
							'flag_is_active' => $row['flag_is_active']
						]);
						$total_inserted_rows++;

						if (in_array($row['id'], $firm_types[$level0])) {
							continue;
						} else {
							$firm_types[$level0][] = $row['id'];
						}
					}
				}
				if (0) {
					$process = ceil($i / $cnt * 100);
					if ($process % 20 == 0) {
						$this->log(ceil($i / $cnt * 100)."%");
					}
				}
			}
		}
		$this->flipTable('firm_firm_type');

		$this->log('вставлено строк: '.$total_inserted_rows);

		$this->log('заполнение таблицы firm_type_city');
		$this->createTempTable('firm_type_city');
		$this->db()->query()->truncateTable('tmp_firm_type_city');

		$cr_city = new CurrentRegionCity();
		$firm_type = new FirmType();
		$firm_firm_type = new FirmFirmType();

		$cities = $cr_city->query()->setFrom(['current_region_city'])->setSelectDistinct(['id_city'])->setWhere('`id_city` > :nil', [':nil' => 0])->select();
		$types = $firm_type->query()->setFrom(['firm_type'])->setSelectDistinct(['id'])->setWhere('`node_level` = :2', [':2' => 2])->select();

		$total_inserted_rows = 0;
		foreach ($types as $type) {
			foreach ($cities as $city) {
				$count = $firm_firm_type->reader()
						->setWhere(['AND', '`id_city` = :city_id_city', '`id_type` = :type_id', '`flag_is_active` = :one'], [':city_id_city' => $city['id_city'], ':type_id' => $type['id'], ':one' => 1])
						->count();
				if ($count > 0) {
					$firm_type_city = new FirmTypeCity();
					$firm_type_city->setTable('tmp_firm_type_city');
					$firm_type_city->insert([
						'id_type' => $type['id'],
						'id_city' => $city['id_city'],
						'cnt' => $count
					]);
					$total_inserted_rows++;
				}
			}
		}

		$types = $firm_type->query()->setFrom(['firm_type'])->setSelectDistinct(['id'])->setWhere('`node_level` = :nil', [':nil' => 0])->select();
		foreach ($types as $type) {
			foreach ($cities as $city) {
				$this->db()->query()
						->setText("SELECT COUNT(DISTINCT(id_firm)) as `cnt` FROM `firm_firm_type` WHERE `id_type` IN (SELECT `id` FROM `firm_types` WHERE parent_node = :type_id) AND `id_city` = :city_id_city AND `flag_is_active` = :one")
						->setParams([':type_id' => $type['id'], ':city_id_city' => $city['id_city'], ':one' => 1])
						->selectRow();

				if ($row['cnt'] > 0) {
					$firm_type_city = new FirmTypeCity();
					$firm_type_city->setTable('tmp_firm_type_city');
					$firm_type_city->insert([
						'id_type' => $type['id'],
						'id_city' => $city['id_city'],
						'cnt' => $row['cnt']
					]);
					$total_inserted_rows++;
				}
			}
		}

		$this->flipTable('firm_type_city');
		$this->log('вставлено строк: '.$total_inserted_rows);
	}

}
