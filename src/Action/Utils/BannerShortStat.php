<?php

namespace App\Action\Utils;

use \Foolz\SphinxQL\SphinxQL;

class BannerShortStat extends \App\Action\Utils {

	protected $start_datetime;
	protected $end_datetime;
	protected $banned_user_ips = [];

    public function __construct() {
        parent::__construct();
        if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
            exit();
        }
    }

    public function execute() {
        $params = app()->request()->processGetParams([
            'banner_id' => ['type' => 'int']
        ]);

        $banner_id = (int) $params['banner_id'];

        if (!$banner_id) {
            echo 'Укажите banner_id' . '</br>';
            exit();
        }

        $banner = new \App\Model\Banner($banner_id);

        if (!$banner->exists()) {
            echo 'Нет такого баннера' . '</br>';
            exit();
        }
        
        $this->start_datetime = '2019-02-11 00:00:00';
        $this->end_datetime = '2019-02-18 00:00:00';
        $this->getBannedUserIps();
       

        $asb = new \App\Model\AvgStatBanner();
        $avg_stat_banners = $asb->reader()
                ->setWhere(
                        [
                            'AND', 
                            'id_banner = :id_banner', 
                            'timestamp_inserting >= :timestamp_start',
                            'timestamp_inserting < :timestamp_end'
                        ], 
                        [
                            ':id_banner' => $banner_id, 
                            ':timestamp_start' => $this->start_datetime,
                            ':timestamp_end' => $this->end_datetime
                        ]
                )
                ->setOrderBy('timestamp_inserting')
                ->objects();

        echo '<h1 style="text-align:center;">Статистика по баннеру ' . $banner_id . ' c ' . $this->start_datetime . ' по ' . $this->end_datetime . '</h1>';
        
        echo '<h2 style="text-align:center;">Статистика AVG</h2>';
        echo '<table width="100%" border="1">';
        echo '<tr>';
        echo '<th>Неделя</th>';
        echo '<th>Фирма</th>';
        echo '<th>Баннер</th>';
        echo '<th>Показы</th>';
        echo '<th>Клики</th>';
        echo '</tr>';
        foreach ($avg_stat_banners as $avg_stat_banner) {
            $firm = new \App\Model\Firm($avg_stat_banner->val('id_firm'));
            $image = $banner->getImage();
            echo '<tr>';
            echo '<td style="text-align:center;">' . $avg_stat_banner->val('timestamp_inserting') . '</td>';
            echo '<td style="text-align:center;">' . $firm->name() . '</td>';
            echo '<td style="text-align:center;">' . ($image->exists() ? '<img src="' . $image->link() . '"/>' : 'Нет изображения') . '</td>';
            echo '<td style="text-align:center;">' . $avg_stat_banner->val('count_shows') . '</td>';
            echo '<td style="text-align:center;">' . $avg_stat_banner->val('count_clicks') . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        
        $sbs = new \App\Model\StatBannerShow();
        $stat_banners_show = $sbs->reader()
                ->setWhere(
                        [
                            'AND', 
                            'id_banner = :id_banner', 
                            'timestamp_inserting >= :timestamp_start',
                            'timestamp_inserting < :timestamp_end'
                        ], 
                        [
                            ':id_banner' => $banner_id, 
                            ':timestamp_start' => $this->start_datetime,
                            ':timestamp_end' => $this->end_datetime
                        ]
                )
                ->setOrderBy('timestamp_inserting')
                ->objects();

        $table1 = '<table width="100%" border="1">';
        $table1 .= '<tr>';
        $table1 .= '<th>№</th>';
        $table1 .= '<th>Дата показа</th>';
        $table1 .= '<th>ID посетителя</th>';
        $table1 .= '<th>IP посетителя</th>';
        $table1 .= '</tr>';
        $i = 0;
        $j = 0;
        foreach ($stat_banners_show as $stat_banner_show) {
            $banned = $this->isBannedUser($stat_banner_show->val('id_stat_user'));
            $firm = new \App\Model\Firm($stat_banner_show->val('id_firm'));
            $user = new \App\Model\StatUser($stat_banner_show->val('id_stat_user'));
            if ($banned) {
                $j++;
                $table1 .= '<tr style="color:#ff0012;">';
            } else {
                $table1 .= '<tr>';
            }
            $table1 .= '<td style="text-align:center;">' . ++$i . '</td>';
            $table1 .= '<td style="text-align:center;">' . $stat_banner_show->val('timestamp_inserting') . '</td>';
            $table1 .= '<td style="text-align:center;">' . ($user->exists() ? $user->id() : 'Не определен') . '</td>';
            $table1 .= '<td style="text-align:center;">' . ($user->exists() ? $user->val('ip_addr') : 'Не определен') . '</td>';
            $table1 .= '</tr>';
        }

        $table1 .= '</table>';
        
        echo '<h2 style="text-align:center;">Статистика показов - ' . (count($stat_banners_show) - $j) . '/' . count($stat_banners_show) . '</h2>';
        echo $table1;

        
        $sbc = new \App\Model\StatBannerClick();
        $stat_banners_click = $sbc->reader()
                ->setWhere(
                        [
                            'AND', 
                            'id_banner = :id_banner', 
                            'timestamp_inserting >= :timestamp_start',
                            'timestamp_inserting < :timestamp_end'
                        ], 
                        [
                            ':id_banner' => $banner_id, 
                            ':timestamp_start' => $this->start_datetime,
                            ':timestamp_end' => $this->end_datetime
                        ]
                )
                ->setOrderBy('timestamp_inserting')
                ->objects();

        $table2 = '<table width="100%" border="1">';
        $table2 .= '<tr>';
        $table2 .= '<th>№</th>';
        $table2 .= '<th>Дата показа</th>';
        $table2 .= '<th>ID посетителя</th>';
        $table2 .= '<th>IP посетителя</th>';
        $table2 .= '</tr>';
        $i = 0;
        $j = 0;
        foreach ($stat_banners_click as $stat_banner_click) {
            $banned = $this->isBannedUser($stat_banner_click->val('id_stat_user'));
            $firm = new \App\Model\Firm($stat_banner_click->val('id_firm'));
            $user = new \App\Model\StatUser($stat_banner_click->val('id_stat_user'));
            if ($banned) {
                $j++;
                $table2 .= '<tr style="color:#ff0012;">';
            } else {
                $table2 .= '<tr>';
            }
            $table2 .= '<td style="text-align:center;">' . ++$i . '</td>';
            $table2 .= '<td style="text-align:center;">' . $stat_banner_click->val('timestamp_inserting') . '</td>';
            $table2 .= '<td style="text-align:center;">' . ($user->exists() ? $user->id() : 'Не определен') . '</td>';
            $table2 .= '<td style="text-align:center;">' . ($user->exists() ? $user->val('ip_addr') : 'Не определен') . '</td>';
            $table2 .= '</tr>';
        }

        $table2 .= '</table>';

        echo '<h2 style="text-align:center;">Статистика кликов - ' . (count($stat_banners_click) - $j) . '/' . count($stat_banners_click) . '</h2>';
        echo $table2;

        exit();
    }
    
    private function getBannedUserIps() {
		$banned_user_ips = [];
		$user_items = app()->db()->query()
				->setText("SELECT * FROM ("
						." SELECT `id`,`ip_addr`, `timestamp_beginning`, SUBSTRING_INDEX(`timestamp_beginning`, :tstmp, 2) as `without_seconds`, COUNT(`id`) as `users_per_minute` "
						." FROM `stat_user` "
						." WHERE `timestamp_beginning` >= :start_timestamp "
						." AND `timestamp_beginning` < :end_timestamp "
						." GROUP BY `ip_addr`, `without_seconds` ORDER BY `users_per_minute` DESC"
						.") s WHERE s.`users_per_minute` > :max_users_per_minute")
				->setParams([':start_timestamp' => $this->start_datetime,
					':end_timestamp' => $this->end_datetime,
					':max_users_per_minute' => 20,
					':tstmp' => ':'])
				->fetch();

		foreach ($user_items as $item) {
			if (!in_array($item['ip_addr'], $banned_user_ips)) {
				$banned_user_ips [] = $item['ip_addr'];
			}
		}
		// ips from stat_requests
		$request_items = app()->db()->query()
				->setText("SELECT * FROM ("
						." SELECT sr.`id`, SUBSTRING_INDEX(`timestamp_inserting`, :tstmp, 2) as `without_seconds`, COUNT(sr.`id`) as `requests_per_minute`, su.`ip_addr` "
						." FROM `stat_request` sr "
						." LEFT JOIN `stat_user` su ON su.`id` = sr.`id_stat_user`"
						." WHERE sr.`timestamp_inserting` >= :start_timestamp AND sr.`timestamp_inserting` < :end_timestamp"
						." GROUP BY su.`ip_addr`, `without_seconds` ORDER BY `requests_per_minute` DESC) s WHERE s.`requests_per_minute` > :max_requests_per_minute")
				->setParams([':start_timestamp' => $this->start_datetime,
					':end_timestamp' => $this->end_datetime,
					':max_requests_per_minute' => 50,
					':tstmp' => ':'])
				->fetch();

		foreach ($request_items as $item) {
			if (!in_array($item['ip_addr'], $banned_user_ips)) {
				$banned_user_ips [] = $item['ip_addr'];
			}
		}

		$this->banned_user_ips = array_unique($banned_user_ips);
		return $this;
	}

	private function isBannedUser($user_id) {
		$tmp_user = new \App\Model\StatUser($user_id);
		if (in_array($tmp_user->val('ip_addr'), $this->banned_user_ips)) {
			return true;
		}
		return false;
	}

}
