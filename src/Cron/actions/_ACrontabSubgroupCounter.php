<?php

class ACrontabSubgroupCounter extends ACrontabAction {

	public function run() {
		$this->log('заполнение таблицы sts_subgroup_count');
		$total_inserted_rows = 0;
		$time = time();

		$sp = new StsPrice();
		$cities = $sp->reader()
				->setSelect(['DISTINCT(id_city) as `id_city`'])
				->setWhere(['AND', '`blocked` = :0'], [':0' => 0])
				->rows();
		$all = count($cities);

		//$this->db->query()->copyTable('subgroup_count', 'subgroup_count_3', true);
		$this->db->query()->truncateTable('subgroup_count');

		$i = 0;
		foreach ($cities as $city) {
			$i++;
			$groups = $sp->reader()
					->setSelect(['DISTINCT(id_group) as `id`'])
					->setWhere(['AND', '`id_city` = :id_city', '`blocked` = :0'], [':0' => 0, ':id_city' => $city['id_city']])
					->rows();

			foreach ($groups as $group) {
				$subgroups = $sp->query()
						->setSelect(['DISTINCT(id_subgroup) as `id`', 'COUNT(id_subgroup) as `count`'])
						->setFrom(['sts_price'])
						->setWhere(['AND', '`id_city` = :id_city', '`id_group` = :id_group', '`blocked` = :0'], [':0' => 0, ':id_city' => $city['id_city'], ':id_group' => $group['id']])
						->setGroupBy(['id_subgroup'])
						->select();

				foreach ($subgroups as $subgroup) {
					$sc = new SubgroupCount();
					$sc->setWhere(['AND', '`id_group` = :id_group', '`id_subgroup` = :id_subgroup', '`id_city` = :id_city'], [
						':id_group' => $group['id'],
						':id_subgroup' => $subgroup['id'],
						':id_city' => $city['id_city']
					])->getByConds();

					if (!$sc->exists()) {
						$sc->insert([
							'id_group' => $group['id'],
							'id_subgroup' => $subgroup['id'],
							'id_city' => $city['id_city'],
							'count_goods' => $subgroup['count']
						]);
					}
				}
			}

			//echo "\rПрогресс: " . round(($i / $all) * 100, 1) . "% за " . date("H:i:s", time() - $time - 4 * 3600) . " (RAM: " . round(memory_get_usage() / 1024, 1) . "Kb)   ";
		}

		//удаляем из sts_subgroup_count количество строк фирмы Справочник, т.к. фирма на показывается в товарах
		$this->cleaning();


		$this->log('завершено');
	}

	private function cleaning() {
		$sp = new StsPrice();
		$sc = new SubgroupCount();

		$f_cnt = $sp->reader()
				->setWhere(['AND', '`id_service` = :id_service', '`id_firm` = :id_firm', '`blocked` = :0'], [':id_service' => 10, ':id_firm' => 191, ':0' => 0])
				->count();

		$s_cnt = $sc->setWhere(['AND', '`id_city` = :id_city', '`id_subgroup` = :id_subgroup', '`id_group` = :id_group'], [':id_city' => 76004, ':id_subgroup' => 206, ':id_group' => 35])
						->getByConds()->val('count_goods');


		$s_cnt = $s_cnt ? (int) $s_cnt : 0;

		if ($s_cnt - $f_cnt <= 0) {
			$this->db->query()
					->setDeleteFrom(['subgroup_count'])
					->setWhere(['AND', '`id_city` = :id_city', '`id_subgroup` = :id_subgroup', '`id_group` = :id_group'], [':id_city' => 76004, ':id_subgroup' => 206, ':id_group' => 35])
					->delete();
		} else {
			$sc = new SubgroupCount();
			$sc->setWhere(['AND', '`id_city` = :id_city', '`id_subgroup` = :id_subgroup', '`id_group` = :id_group'], [':id_city' => 76004, ':id_subgroup' => 206, ':id_group' => 35])
					->getByConds();

			if ($sc->exists()) {
				$sc->update([
					'count_goods' => $s_cnt - $f_cnt
				]);
			}
		}
	}

}
