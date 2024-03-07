<?php

namespace App\Action\Crontab;

use App\Action\Crontab;
use App\Model\CurrentRegionCity;
use App\Model\Firm;
use App\Model\FirmPromo;
use App\Model\StsCity;
use App\Model\StsPrice;
use App\Model\StsRegionCountry;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;

class CurrentRegionStats extends Crontab {

	private $current_firm_ids = [];

	public function execute() {
		$this->log('заполнение таблицы current_region_city')
				->copyTable()
				->fillTable()
				->changeTables()
				->log('список городов получен')
				->log('сбор статистики по городам')
				->getStats();
	}

	private function getStats() {
		$crc = new CurrentRegionCity();
		$all_cities = $crc->reader()
				->setWhere(['AND', '`id_city` != :0'], [':0' => 0])
				->objects();

		$i = 0;
		$regions = [];
		foreach ($all_cities as $city) {
			$i++;
			$stat_firms = $this->getStatFirms($city->val('id_city'));
			$stat_discounts = $this->getStatDiscounts($city->val('id_city'));
			$stat_goods = $this->getStatGoods($city->val('id_city'));
			$stat_goods_1 = $this->getStatGoodsType1($city->val('id_city'));
			$stat_goods_2 = $this->getStatGoodsType2($city->val('id_city'));
			$stat_goods_3 = $this->getStatGoodsType3($city->val('id_city'));
			$stat_videos = $this->getStatVideos($city->val('id_city'));

			$city->update([
				'count_discounts' => $stat_discounts,
				'count_firms' => $stat_firms,
				'count_goods' => $stat_goods,
				'count_goods_1' => $stat_goods_1,
				'count_goods_2' => $stat_goods_2,
				'count_goods_3' => $stat_goods_3,
				'count_videos' => $stat_videos,
			]);

			if (!isset($regions[$city->val('id_region')])) {
				$regions[$city->val('id_region')] = ['count_discounts' => 0, 'count_firms' => 0, 'count_goods' => 0, 'count_videos' => 0];
			}

			$regions[$city->val('id_region')]['count_discounts'] += $stat_discounts;
			$regions[$city->val('id_region')]['count_firms'] += $stat_firms;
			$regions[$city->val('id_region')]['count_goods'] += $stat_goods;
			$regions[$city->val('id_region')]['count_videos'] += $stat_videos;
		}

		foreach ($regions as $id_region => $row) {
			$region = new CurrentRegionCity();
			$region->setWhere(['AND', '`id_city` = :0', '`id_region` = :id_region'], [':0' => 0, ':id_region' => $id_region])
					->getByConds();
			if ($region->exists()) {
				$region->update($row);
			}
		}

		return $this;
	}

	private function getStatGoods($id_city) {
		if (!$this->current_firm_ids) {
			return 0;
		}

		$price = new \App\Model\Price();
		$firm_ids_conds = Utils::prepareWhereCondsFromArray($this->current_firm_ids, 'id_firm');
		return $price->reader()
						->setWhere(['AND', $firm_ids_conds['where'], '`flag_is_active` = :flag_is_active'], [':flag_is_active' => 1] + $firm_ids_conds['params'])
						->count();
	}

	//товары
	private function getStatGoodsType1($id_city) {
		if (!$this->current_firm_ids) {
			return 0;
		}

		$price = new \App\Model\Price();
		$firm_ids_conds = Utils::prepareWhereCondsFromArray($this->current_firm_ids, 'id_firm');
		return $price->reader()
						->setWhere(['AND', $firm_ids_conds['where'], '`flag_is_active` = :flag_is_active', '`id_group` != :id_usl', '`id_group` != :id_oborud'], [':flag_is_active' => 1, ':id_usl' => 44, ':id_oborud' => 22] + $firm_ids_conds['params'])
						->count();
	}

	//услуги
	private function getStatGoodsType2($id_city) {
		if (!$this->current_firm_ids) {
			return 0;
		}

		$price = new \App\Model\Price();
		$firm_ids_conds = Utils::prepareWhereCondsFromArray($this->current_firm_ids, 'id_firm');
		return $price->reader()
						->setWhere(['AND', $firm_ids_conds['where'], '`flag_is_active` = :flag_is_active', '`id_group` = :id_usl'], [':flag_is_active' => 1, ':id_usl' => 44] + $firm_ids_conds['params'])
						->count();
	}

	//оборудование
	private function getStatGoodsType3($id_city) {
		if (!$this->current_firm_ids) {
			return 0;
		}

		$price = new \App\Model\Price();
		$firm_ids_conds = Utils::prepareWhereCondsFromArray($this->current_firm_ids, 'id_firm');
		return $price->reader()
						->setWhere(['AND', $firm_ids_conds['where'], '`flag_is_active` = :flag_is_active', '`id_group` = :id_oborud'], [':flag_is_active' => 1, ':id_oborud' => 22] + $firm_ids_conds['params'])
						->count();
	}

	public function getStatVideos($id_city) {
		if (!$this->current_firm_ids) {
			return 0;
		}
		$fv = app()->db()->query()
						->setText("SELECT COUNT(*) as `count_videos` FROM `firm_video` fv "
								."LEFT JOIN `firm` f ON f.id = fv.id_firm "
								."WHERE fv.`id_firm` IN (".implode(',', $this->current_firm_ids).") AND f.`flag_is_active` = 1")
						->fetch()[0];

		return $fv['count_videos'];
	}

	private function getStatFirms($id_city) {
		$fc = new \App\Model\FirmCity();
		$firm_ids = array_keys($fc->reader()
						->setWhere(['AND', '`id_city` = :id_city'], [':id_city' => $id_city])
						->rowsWithKey('id_firm'));

		$firm_ids_conds = Utils::prepareWhereCondsFromArray($firm_ids, 'id');

		if ($firm_ids) {
			$this->current_firm_ids = $firm_ids;
			$firm = new Firm();
			return $firm->reader()->setWhere(['AND', 'flag_is_active = :flag_is_active', $firm_ids_conds['where']], [':flag_is_active' => 1] + $firm_ids_conds['params'])
							->count();
		} else {
			$this->current_firm_ids = [];
		}

		return 0;
	}

	private function getStatDiscounts($id_city) {
		if (!$this->current_firm_ids) {
			return 0;
		}

		$firm_ids_conds = Utils::prepareWhereCondsFromArray($this->current_firm_ids, 'id_firm');

		$fp = new FirmPromo();
		$_where = [
			'AND',
			//['OR',
			//'`flag_is_infinite` = :flag',
			['AND', /* '`timestamp_beginning`< :now', */ 'timestamp_ending > :now'],
			//],
			['AND', 'flag_is_active = :flag', $firm_ids_conds['where']]
		];

		$_params = [':flag' => 1, ':now' => DeprecatedDateTime::now()] + $firm_ids_conds['params'];

		return $fp->reader()
						->setWhere($_where, $_params)
						->count();
	}

	private function copyTable() {
		$this->db()->query()->copyTable('current_region_city', 'current_region_city_tmp');
		$this->db()->query()->truncateTable('current_region_city_tmp');
		return $this;
	}

	private function fillTable() {
		$city_ids = [];

		$firm_id_city = $this->db()->query()
				->setSelect('DISTINCT(`id_city`) as `city`')
				->setFrom('firm_city')
				->select();

		foreach ($firm_id_city as $row) {
			$city_ids[] = $row['city'];
		}

		$city_ids = array_filter($city_ids);

		$sc = new StsCity();
		$city_ids_conds = Utils::prepareWhereCondsFromArray($city_ids, 'id_city');
		$cities = $sc->reader()
				->setWhere($city_ids_conds['where'], $city_ids_conds['params'])
				->rowsWithKey();
		$city_names = $cities;

		$region_ids = [];
		foreach ($cities as $city) {
			$region_ids[] = $city['id_region_country'];
		}
		$region_ids = array_filter($region_ids);

		$src = new StsRegionCountry();
		$region_ids_conds = Utils::prepareWhereCondsFromArray($region_ids, 'id_region_country');
		$region_names = $src->reader()->setWhere($region_ids_conds['where'], $region_ids_conds['params'])->rowsWithKey();

		$total_inserted_rows = 0;
		foreach ($region_names as $id => $region) {
			$total_inserted_rows++;
			$crs = new CurrentRegionCity();
			$crs->setTemporaryTableMode(true);
			$crs->insert([
				'id_country' => $region['id_country'],
				'id_region' => $region['id_region_country'],
				'id_city' => 0,
				'name' => $region['name']
			]);
		}
		foreach ($city_names as $k => $city) {
			$total_inserted_rows++;
			$crs = new CurrentRegionCity();
			$crs->setTemporaryTableMode(true);
			$crs->insert([
				'id_country' => $city['id_country'],
				'id_region' => $city['id_region_country'],
				'id_city' => $k,
				'name' => $city['name']
			]);
		}

		return $this;
	}

	private function changeTables() {
		$this->db()->query()->renameTable('current_region_city', 'current_region_city_del');
		$this->db()->query()->renameTable('current_region_city_tmp', 'current_region_city');
		$this->db()->query()->dropTable('current_region_city_del');

		return $this;
	}

}
