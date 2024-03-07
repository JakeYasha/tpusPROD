<?php

namespace App\Action\Crontab;

use App\Action\Crontab;
use App\Model\AvgStatBanner727373;
use App\Model\AvgStatFirm727373PopularPages;
use App\Model\AvgStatGeo727373;
use App\Model\AvgStatObject727373;
use App\Model\AvgStatUser727373;
use App\Model\StatBanner727373Click;
use App\Model\StatBanner727373Show;
use App\Model\StatObject727373;
use App\Model\StatRequest727373;
use App\Model\StatUser727373;
use Sky4\Helper\DeprecatedDateTime as DateTime;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model;

class AvgStatistics727373Maker extends Crontab {

    protected $start_datetime;
    protected $end_datetime;
    protected $limit = 10000;
    protected $debug_mode = false;
    protected $banned_user727373_ips = [];

    public function execute() {
        $this->startAction();

        $this->start_datetime = (new DateTime())->fromTimestamp(mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
        $this->end_datetime = (new DateTime())->fromTimestamp(mktime(0, 0, 0, date('m'), date('d'), date('Y')));

        $this->getBannedUser727373Ips()
                ->updateAvgStatBanner727373()
                ->updateAvgStatObject727373()
                ->updateAvgStatFirms727373()
        ;

        $this->endAction();
    }

    private function getBannedUser727373Ips() {
        $this->log('Получаем забаненные IP');
        $banned_user727373_ips = [];
        $user_items = $this->db()->query()
                ->setText("SELECT * FROM ("
                        . " SELECT `id`,`ip_addr`, `timestamp_beginning`, SUBSTRING_INDEX(`timestamp_beginning`, :tstmp, 2) as `without_seconds`, COUNT(`id`) as `users_per_minute` "
                        . " FROM `stat_user727373` "
                        . " WHERE `timestamp_beginning` >= :start_timestamp "
                        . " AND `timestamp_beginning` < :end_timestamp "
                        . " GROUP BY `ip_addr`, `without_seconds` ORDER BY `users_per_minute` DESC"
                        . ") s WHERE s.`users_per_minute` > :max_users_per_minute")
                ->setParams([':start_timestamp' => $this->start_datetime,
                    ':end_timestamp' => $this->end_datetime,
                    ':max_users_per_minute' => 20,
                    ':tstmp' => ':'])
                ->fetch();

        foreach ($user_items as $item) {
            if (!in_array($item['ip_addr'], $banned_user727373_ips)) {
                $banned_user727373_ips [] = $item['ip_addr'];
            }
        }
        // ips from stat_requests
        $request_items = $this->db()->query()
                ->setText("SELECT * FROM ("
                        . " SELECT sr.`id`, SUBSTRING_INDEX(`timestamp_inserting`, :tstmp, 2) as `without_seconds`, COUNT(sr.`id`) as `requests_per_minute`, su.`ip_addr` "
                        . " FROM `stat_request727373` sr "
                        . " LEFT JOIN `stat_user727373` su ON su.`id` = sr.`id_stat_user`"
                        . " WHERE sr.`timestamp_inserting` >= :start_timestamp AND sr.`timestamp_inserting` < :end_timestamp"
                        . " GROUP BY su.`ip_addr`, `without_seconds` ORDER BY `requests_per_minute` DESC) s WHERE s.`requests_per_minute` > :max_requests_per_minute")
                ->setParams([':start_timestamp' => $this->start_datetime,
                    ':end_timestamp' => $this->end_datetime,
                    ':max_requests_per_minute' => 50,
                    ':tstmp' => ':'])
                ->fetch();

        foreach ($request_items as $item) {
            if (!in_array($item['ip_addr'], $banned_user727373_ips)) {
                $banned_user727373_ips [] = $item['ip_addr'];
            }
        }

        $this->banned_user727373_ips = array_unique($banned_user727373_ips);
        $this->log("Количество забаненных IP: " . count($banned_user727373_ips));
        return $this;
    }

    private function isBannedUser($user_id) {
        $tmp_user = new StatUser727373($user_id);
        if (in_array($tmp_user->val('ip_addr'), $this->banned_user727373_ips)) {
            return true;
        }
        return false;
    }

    private function updateAvgStatBanner727373() {
        $this->log('обновление avg_stat_banner727373');
        $limit = $this->limit;
        $offset = -1;
        $sbs = new StatBanner727373Show();
        $sbc = new StatBanner727373Click();
        $data = [];
        $data_month = [];

        while (1) {
            $offset++;
            $items = $sbs->reader()
                    ->setLimit($limit, $limit * $offset)
                    ->setWhere(['AND', 'timestamp_inserting >= :start', 'timestamp_inserting < :end'], [':start' => $this->start_datetime, ':end' => $this->end_datetime])
                    ->objects();
            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                if ($this->isBannedUser($item->val('id_stat_user'))) {
                    continue;
                }

                $id_city = $item->val('id_city');
                $id_firm = $item->id_firm();
                $timestamp = DeprecatedDateTime::toTimestamp($item->val('timestamp_inserting'));
                $timestamp_day = mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
                $datetime = DeprecatedDateTime::fromTimestamp(strtotime('last monday', strtotime('tomorrow', $timestamp_day)));
                $timestamp_month = mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp));
                $datetime_month = DeprecatedDateTime::fromTimestamp($timestamp_month);
                $id_banner = $item->val('cml_banner_id');

                if (!isset($data[$id_city][$id_firm][$id_banner][$datetime]['shows'])) {
                    $data[$id_city][$id_firm][$id_banner][$datetime]['shows'] = 0;
                }

                if (!isset($data_month[$id_city][$id_firm][$id_banner][$datetime_month]['shows'])) {
                    $data_month[$id_city][$id_firm][$id_banner][$datetime_month]['shows'] = 0;
                }

                $data[$id_city][$id_firm][$id_banner][$datetime]['shows'] ++;
                $data_month[$id_city][$id_firm][$id_banner][$datetime_month]['shows'] ++;
            }
        }

        $offset = -1;
        while (1) {
            $offset++;
            $items = $sbc->reader()
                    ->setLimit($limit, $limit * $offset)
                    ->setWhere(['AND', 'timestamp_inserting >= :start', 'timestamp_inserting < :end'], [':start' => $this->start_datetime, ':end' => $this->end_datetime])
                    ->objects();
            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                if ($this->isBannedUser($item->val('id_stat_user'))) {
                    continue;
                }

                $id_city = $item->val('id_city');
                $id_firm = $item->id_firm();
                $timestamp = DeprecatedDateTime::toTimestamp($item->val('timestamp_inserting'));
                $timestamp_day = mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
                $datetime = DeprecatedDateTime::fromTimestamp(strtotime('last monday', strtotime('tomorrow', $timestamp_day)));
                $timestamp_month = mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp));
                $datetime_month = DeprecatedDateTime::fromTimestamp($timestamp_month);
                $id_banner = $item->val('cml_banner_id');

                if (!isset($data[$id_city][$id_firm][$id_banner][$datetime]['clicks'])) {
                    $data[$id_city][$id_firm][$id_banner][$datetime]['clicks'] = 0;
                }

                if (!isset($data_month[$id_city][$id_firm][$id_banner][$datetime_month]['clicks'])) {
                    $data_month[$id_city][$id_firm][$id_banner][$datetime_month]['clicks'] = 0;
                }

                $data[$id_city][$id_firm][$id_banner][$datetime]['clicks'] ++;
                $data_month[$id_city][$id_firm][$id_banner][$datetime_month]['clicks'] ++;
            }
        }

        $i = 0;
        foreach ($data as $id_city => $firms) {
            foreach ($firms as $id_firm => $banners) {
                foreach ($banners as $id_banner => $dates) {
                    foreach ($dates as $timestamp_inserting => $types) {
                        $i++;
                        $result_row = [
                            'id_firm' => $id_firm,
                            'timestamp_inserting' => $timestamp_inserting,
                            'id_banner_727373' => $id_banner,
                            'month' => 0
                        ];

                        foreach ($types as $type => $count) {
                            $result_row['count_' . $type] = $count;
                        }

                        $asb = new AvgStatBanner727373();
                        $asb->reader()->setWhere(['AND', 'id_firm = :id_firm', 'timestamp_inserting = :timestamp_inserting', 'id_banner_727373 = :id_banner', 'month = :month'], [':id_firm' => $id_firm, ':timestamp_inserting' => $timestamp_inserting, ':id_banner' => $id_banner, ':month' => 0])
                                ->objectByConds();
                        if ($asb->exists()) {
                            $vals = $asb->getVals();
                            foreach ($types as $type => $count) {
                                $vals['count_' . $type] += $count;
                            }
                            $vals['timestamp_inserting'] = $timestamp_inserting;
                            $asb->update($vals);
                        } else {
                            $asb->insert($result_row);
                        }
                    }
                }
            }
        }

        $this->log('вставлено ' . $i . ' записей');

        $i = 0;
        foreach ($data_month as $id_city => $firms) {
            foreach ($firms as $id_firm => $banners) {
                foreach ($banners as $id_banner => $dates) {
                    foreach ($dates as $timestamp_inserting => $types) {
                        $i++;
                        $result_row = [
                            'id_firm' => $id_firm,
                            'timestamp_inserting' => $timestamp_inserting,
                            'id_banner_727373' => $id_banner,
                            'month' => 1
                        ];

                        foreach ($types as $type => $count) {
                            $result_row['count_' . $type] = $count;
                        }

                        $asb = new AvgStatBanner727373();
                        $asb->reader()->setWhere(['AND', 'id_firm = :id_firm', 'timestamp_inserting = :timestamp_inserting', 'id_banner_727373 = :id_banner', 'month = :month'], [':id_firm' => $id_firm, ':timestamp_inserting' => $timestamp_inserting, ':id_banner' => $id_banner, ':month' => 1])
                                ->objectByConds();
                        if ($asb->exists()) {
                            $vals = $asb->getVals();
                            foreach ($types as $type => $count) {
                                $vals['count_' . $type] += $count;
                            }
                            $vals['timestamp_inserting'] = $timestamp_inserting;
                            $asb->update($vals);
                        } else {
                            $asb->insert($result_row);
                        }
                    }
                }
            }
        }

        return $this;
    }

    private function updateAvgStatFirms727373() {
        $this->log('обновление avg_stat_firm727373_popular_pages');
        $limit = $this->limit;
        $offset = -1;

        $stat_object = new StatObject727373();

        $data = [];
        $data_user = [];
        $data_geo = [];
        $j = 0;

        $where = ['AND', 'timestamp_inserting >= :start', 'timestamp_inserting < :end'];
        $params = [':start' => $this->start_datetime, ':end' => $this->end_datetime];

        while (1) {
            $j++;
            $offset++;

            $items = $stat_object->reader()
                    ->setLimit($limit, $limit * $offset)
                    ->setWhere($where, $params)
                    ->objects();

            if (!$items) {
                break;
            }

            $requests_ids = [];
            $user_ids = [];
            $cache = [];
            foreach ($items as $item) {
                if ($this->isBannedUser($item->val('id_stat_user'))) {
                    continue;
                }
                
                if (isset($cache[$item->id_firm().'~'.$item->val('id_city').'~'.$item->val('id_stat_user').'~'.$item->val('type')])) {
                    continue;
                } else {
                    $cache[$item->id_firm().'~'.$item->val('id_city').'~'.$item->val('id_stat_user').'~'.$item->val('type')] = 1;
                }
                
                // Отсеиваем личный кабинет и пр.
                if ($this->isBadStatRequest727373($item->val('id_stat_request'))) {
                    continue;
                }

                if (!isset($requests_ids[$item->val('id_stat_request')])) {
                    $requests_ids[$item->val('id_stat_request')] = [];
                }

                $requests_ids[$item->val('id_stat_request')][] = ['id_firm' => $item->id_firm(), 'id_city' => $item->val('id_city')];

                if (!isset($user_ids[$item->val('id_stat_user')])) {
                    $user_ids[$item->val('id_stat_user')] = [];
                }

                $user_ids[$item->val('id_stat_user')][] = $item->val('id_stat_request');
            }

            $stat_user = new StatUser727373();
            $users = $stat_user->reader()->objectsByIds(array_keys($user_ids));
            foreach ($user_ids as $id_user => $id_requests) {
                if (!isset($users[$id_user])) {
                    foreach ($id_requests as $id) {
                        unset($requests_ids[$id]);
                    }
                } else {
                    $users_geo[$id_user] = ['id_city' => $users[$id_user]->val('id_city'), 'city_name' => $users[$id_user]->val('user_city_name')];
                }
            }

            $sr = new StatRequest727373();
            $requests = $sr->reader()->objectsByIds(array_keys($requests_ids));

            foreach ($requests as $item) {
                $url = trim($item->val('response_url'));
                list($datetime_week, $datetime_month) = self::getDatetimesFromModel($item);

                if (str()->posArray($url, ['.png', '.gif', '.jpg']) || !$url) {
                    continue;
                }
                $url_hash = md5($url);

                if (!isset($requests_ids[$item->id()])) {
                    continue;
                }

                foreach ($requests_ids[$item->id()] as $request) {
                    $id_city = $request['id_city'];
                    $id_firm = $request['id_firm'];

                    if (!isset($data[$id_city][$id_firm]['weeks'][$datetime_week][$url_hash])) {
                        $data[$id_city][$id_firm]['weeks'][$datetime_week][$url_hash] = [
                            'url' => $item->val('response_url'),
                            'title' => $item->val('response_title'),
                            'count' => 0
                        ];
                    }

                    if (!isset($data[$id_city][$id_firm]['months'][$datetime_month][$url_hash])) {
                        $data[$id_city][$id_firm]['months'][$datetime_month][$url_hash] = [
                            'url' => $item->val('response_url'),
                            'title' => $item->val('response_title'),
                            'count' => 0
                        ];
                    }

                    if (!isset($data_user[$id_city][$id_firm]['weeks'][$datetime_week][$item->val('id_stat_user')])) {
                        $data_user[$id_city][$id_firm]['weeks'][$datetime_week][$item->val('id_stat_user')] = 1;
                    }

                    if (!isset($data_user[$id_city][$id_firm]['months'][$datetime_month][$item->val('id_stat_user')])) {
                        $data_user[$id_city][$id_firm]['months'][$datetime_month][$item->val('id_stat_user')] = 1;
                    }

                    $data[$id_city][$id_firm]['weeks'][$datetime_week][$url_hash]['count'] ++;
                    $data[$id_city][$id_firm]['months'][$datetime_month][$url_hash]['count'] ++;
                }
            }
        }

        $i = 0;
        foreach ($data as $id_city => $firms) {
            foreach ($firms as $id_firm => $date_types) {
                foreach ($date_types as $date_type => $dates) {
                    foreach ($dates as $datetime => $urls) {
                        foreach ($urls as $hash => $count) {
                            $i++;
                            if ($count['title'] == '') {
                                if (str()->posArray($url, ['organization/away'])) {
                                    $count['title'] = 'Переход на сайт фирмы';
                                }
                                if (str()->posArray($url, ['callback/submit'])) {
                                    $count['title'] = 'Отправка сообщения фирме';
                                }
                            }
                            $result_row = [
                                'id_firm' => $id_firm,
                                'timestamp_inserting' => $datetime,
                                'title' => $count['title'],
                                'count' => $count['count'],
                                'url' => $count['url'],
                                'month' => ($date_type === 'months' ? 1 : 0)
                            ];

                            $check_row = $result_row;
                            unset($check_row['count']);
                            unset($check_row['title']);

                            $check_where = ['AND'];
                            $check_params = [];
                            foreach ($check_row as $k => $v) {
                                $check_where[] = $k . ' = :' . $k;
                                $check_params[':' . $k] = $v;
                            }
                            $asfpp = new AvgStatFirm727373PopularPages();
                            $asfpp->reader()
                                    ->setWhere($check_where, $check_params)
                                    ->objectByConds();

                            if ($asfpp->exists()) {
                                $asfpp->update(['count' => $result_row['count'] + $asfpp->val('count')]);
                            } else {
                                $asfpp->insert($result_row);
                            }
                        }
                    }
                }
            }
        }

        $l = 0;
        foreach ($data_user as $id_city => $firms) {
            foreach ($firms as $id_firm => $date_types) {
                foreach ($date_types as $date_type => $dates) {
                    foreach ($dates as $datetime => $users) {
                        $l++;
                        $result_row = [
                            'id_firm' => $id_firm,
                            'timestamp_inserting' => $datetime,
                            'count' => count($users),
                            'month' => ($date_type === 'months' ? 1 : 0)
                        ];

                        $check_row = $result_row;
                        unset($check_row['count']);
                        $check_where = ['AND'];
                        $check_params = [];
                        foreach ($check_row as $k => $v) {
                            $check_where[] = $k . ' = :' . $k;
                            $check_params[':' . $k] = $v;
                        }

                        $asu = new AvgStatUser727373();
                        $asu->reader()
                                ->setWhere($check_where, $check_params)
                                ->objectByConds();

                        if ($asu->exists()) {
                            $asu->update(['count' => $result_row['count'] + $asu->val('count')]);
                        } else {
                            $asu->insert($result_row);
                        }

                        foreach ($users as $id_user => $val) {
                            if (isset($users_geo[$id_user])) {
                                if (!isset($data_geo[$id_city][$id_firm][$date_type][$datetime][$users_geo[$id_user]['id_city'] . '|' . $users_geo[$id_user]['city_name']])) {
                                    $data_geo[$id_city][$id_firm][$date_type][$datetime][$users_geo[$id_user]['id_city'] . '|' . $users_geo[$id_user]['city_name']] = 0;
                                }
                                $data_geo[$id_city][$id_firm][$date_type][$datetime][$users_geo[$id_user]['id_city'] . '|' . $users_geo[$id_user]['city_name']] ++;
                            }
                        }
                    }
                }
            }
        }

        $l = 0;
        foreach ($data_geo as $id_city => $firms) {
            foreach ($firms as $id_firm => $date_types) {
                foreach ($date_types as $date_type => $dates) {
                    foreach ($dates as $datetime => $cities) {
                        foreach ($cities as $composite_id => $count) {
                            $result_city = '';
                            list($_id_city, $_city_name) = explode('|', $composite_id);
                            $asg = new AvgStatGeo727373();
                            $result_row = [
                                'id_firm' => $id_firm,
                                'timestamp_inserting' => $datetime,
                                'count' => $count,
                                'month' => ($date_type === 'months' ? 1 : 0),
                                'city_name' => ''
                            ];

                            $_city_name = trim($_city_name);
                            if (!$_city_name && $_id_city) {
                                $sc = new StsCity();
                                $sc->reader()
                                        ->setWhere(['AND', 'id_city = :id_city'], [':id_city' => $_id_city])
                                        ->objectByConds();
                                if ($sc->exists()) {
                                    $result_row['city_name'] = $sc->name();
                                }
                            } elseif ($_city_name) {
                                $result_row['city_name'] = $_city_name;
                            }


                            $check_row = $result_row;
                            unset($check_row['count']);
                            $check_where = ['AND'];
                            $check_params = [];
                            foreach ($check_row as $k => $v) {
                                $check_where[] = $k . ' = :' . $k;
                                $check_params[':' . $k] = $v;
                            }

                            $asg->reader()
                                    ->setWhere($check_where, $check_params)
                                    ->objectByConds();

                            if ($asg->exists()) {
                                $asg->update(['count' => $result_row['count'] + $asg->val('count')]);
                            } else {
                                $asg->insert($result_row);
                            }
                        }
                    }
                }
            }
        }

        $this->log('обработано ' . $i . ' записей');

        return $this;
    }

    private function updateAvgStatObject727373() {
        $this->log('обновление avg_stat_object727373');

        $limit = $this->limit;
        $offset = -1;
        $i = 0;
        $stat_object = new StatObject727373();

        $data = [];
        $stat_user_group = [];

        $where = ['AND', 'timestamp_inserting >= :start', 'timestamp_inserting < :end'];
        $params = [':start' => $this->start_datetime, ':end' => $this->end_datetime];

        while (1) {
            $i++;
            $offset++;
            $items = $stat_object->reader()
                    ->setLimit($limit, $limit * $offset)
                    ->setWhere($where, $params)
                    ->objects();

            if (!$items) {
                break;
            }

            foreach ($items as $item) {
                if ($this->isBannedUser($item->val('id_stat_user'))) {
                    continue;
                }

                $id_city = $item->val('id_city');
                $id_firm = $item->id_firm();
                $type = (int) $item->val('type');

                if (isset($stat_user_group[$item->id_firm() . '~' . $item->val('id_city') . '~' . $item->val('id_stat_user') . '~' . $item->val('type')])) {
                    continue;
                }
                
                // Отсеиваем личный кабинет и пр.
                if ($this->isBadStatRequest727373($item->val('id_stat_request'))) {
                    continue;
                }
                
                $stat_user_group[$item->id_firm() . '~' . $item->val('id_city') . '~' . $item->val('id_stat_user') . '~' . $item->val('type')] = 1;

                list($datetime_week, $datetime_month) = self::getDatetimesFromModel($item);

                if (!isset($data[$id_city][$id_firm]['weeks'][$datetime_week][$type])) {
                    $data[$id_city][$id_firm]['weeks'][$datetime_week][$type] = 0;
                }
                if (!isset($data[$id_city][$id_firm]['months'][$datetime_month][$type])) {
                    $data[$id_city][$id_firm]['months'][$datetime_month][$type] = 0;
                }

                $data[$id_city][$id_firm]['weeks'][$datetime_week][$type] ++;
                $data[$id_city][$id_firm]['months'][$datetime_month][$type] ++;
            }
        }

        $i = 0;
        foreach ($data as $id_city => $firms) {
            foreach ($firms as $id_firm => $date_types) {
                foreach ($date_types as $date_type => $datetimes) {
                    foreach ($datetimes as $datetime => $types) {
                        $result_row = [
                            'id_firm' => $id_firm,
                            'timestamp_inserting' => $datetime,
                            'month' => ($date_type === 'months' ? 1 : 0)
                        ];

                        $check_row = $result_row;
                        foreach ($types as $type => $count) {
                            $result_row['t' . $type] = $count;
                        }

                        $check_where = ['AND'];
                        $check_params = [];
                        foreach ($check_row as $k => $v) {
                            $check_where[] = $k . ' = :' . $k;
                            $check_params[':' . $k] = $v;
                        }
                        $aso = new AvgStatObject727373();
                        $aso->reader()
                                ->setWhere($check_where, $check_params)
                                ->objectByConds();

                        if ($aso->exists()) {
                            $vals = $aso->getVals();
                            foreach ($types as $type => $count) {
                                $vals['t' . $type] += $count;
                            }
                            $aso->update($vals);
                        } else {
                            $aso->insert($result_row);
                        }
                    }
                }
            }
        }

        return $this;
    }

    private static function getDatetimesFromModel(Model $item) {
        $timestamp = DeprecatedDateTime::toTimestamp($item->val('timestamp_inserting'));
        $timestamp_day = mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
        $datetime = DeprecatedDateTime::fromTimestamp(strtotime('last monday', strtotime('tomorrow', $timestamp_day)));
        $timestamp_month = mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp));
        $datetime_month = DeprecatedDateTime::fromTimestamp($timestamp_month);

        return [$datetime, $datetime_month];
    }

    private function isBadStatRequest727373($stat_request_id) {
        $_bad_urls = [ '/user-office/' ];
        $_r = new StatRequest727373($stat_request_id);
        foreach ($_bad_urls as $_bad_url) {
            if (strpos($_r->val('request_url'), $_bad_url) !== FALSE) {
                return true;
            }
        }
        
        return false;
    }
}
