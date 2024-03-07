<?php

namespace App\Action\Crontab;

use App\Classes\YandexMetrika;
use App\Model\StatSite;
use Sky4\Helper\DeprecatedDateTime;

class SiteStatistics extends \App\Action\Crontab {

	public function execute() {
		$this->log('Заполнение таблицы stat_site');
		$this->updateStatSite();
		$this->log('Обновлено строк: 1');
		$this->log('Готово');
	}

	public function getTotalVisitors() {
		$ym = new YandexMetrika();
		return $ym->setDateBeginning(DeprecatedDateTime::shiftMonths(-1))
				->setDateEnding(DeprecatedDateTime::now())
                ->getTotalVisitorsWithOAuth();
	}

	private function updateStatSite() {
		$total_towns = $this->db()->query()->setText("SELECT COUNT(DISTINCT(id_city)) as `count` FROM `firm_city`")->fetch()[0]['count'];
		$total_firms = $this->db()->query()->setText("SELECT COUNT(*) as `count` FROM `firm` WHERE `flag_is_active` = 1")->fetch()[0]['count'];
		$total_price = $this->db()->query()->setText("SELECT COUNT(*) as `count` FROM `price` WHERE `flag_is_active` = 1")->fetch()[0]['count'];
		$total_firm_shows = $this->db()->query()->setText("SELECT SUM(`t1`) as `count` FROM `avg_stat_object` WHERE `timestamp_inserting` BETWEEN DATE_FORMAT( DATE_ADD( NOW( ) , INTERVAL -1 MONTH ) , '%Y-%m-%d 00:00:00' ) AND DATE_FORMAT( NOW( ) , '%Y-%m-%d 00:00:00' )")->fetch()[0]['count'];
		$total_price_shows = $this->db()->query()->setText("SELECT SUM(`t2`) as `count` FROM `avg_stat_object` WHERE `timestamp_inserting` BETWEEN DATE_FORMAT( DATE_ADD( NOW( ) , INTERVAL -1 MONTH ) , '%Y-%m-%d 00:00:00' ) AND DATE_FORMAT( NOW( ) , '%Y-%m-%d 00:00:00' )")->fetch()[0]['count'];
		$total_visitors = $this->getTotalVisitors();

		$vals = [
			'total_towns' => $total_towns,
			'total_firms' => $total_firms,
			'total_price' => $total_price,
			'total_firm_shows' => $total_firm_shows,
			'total_price_shows' => $total_price_shows,
			'total_visitors' => (int)$total_visitors
		];
		
		$stat_site = new StatSite(1);

		if ($stat_site->exists()) {
			$stat_site->update($vals);
		} else {
			$stat_site->insert($vals);
		}

		return $this;
	}

}
