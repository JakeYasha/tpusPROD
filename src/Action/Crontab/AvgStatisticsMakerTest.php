<?php

namespace App\Action\Crontab;

use App\Action\Crontab;
use App\Model\AdvertModule;
use App\Model\AvgStatBanner;
use App\Model\AvgStatFirmPopularPages;
use App\Model\AvgStatGeo;
use App\Model\AvgStatObject;
use App\Model\AvgStatPopularPages;
use App\Model\AvgStatUser;
use App\Model\FirmPromo;
use App\Model\FirmVideo;
use App\Model\StatBannerClick;
use App\Model\StatBannerShow;
use App\Model\StatObject;
use App\Model\StatRequest;
use App\Model\StatSite;
use App\Model\StatUser;
use App\Model\StsCity;
use Sky4\Helper\DeprecatedDateTime as DateTime;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model;

class AvgStatisticsMakerTest extends Crontab {

	protected $start_datetime;
	protected $end_datetime;
	protected $limit = 10000;
	protected $debug_mode = false;
	protected $banned_user_ips = [];

	public function execute() {
        echo 'AvgStatisticsMakerTest - execute';
		$this->startAction();

		$this->start_datetime = (new DateTime())->fromTimestamp(mktime(0, 0, 0, 4, 2, date('Y')));
		$this->end_datetime = (new DateTime())->fromTimestamp(mktime(0, 0, 0, 4, 9, date('Y')));

		$this->getBannedUserIps()
				->updateAvgStatBanner()
				->updateAvgStatObject()
				->updateAvgStatPopularPages()
				->updateAvgStatFirms()
				//->updatePromoStatistics()
				//->updateVideoStatistics()
				//->updateAdvertModuleStatistics();
		;

		$this->endAction();
	}

	private function getBannedUserIps() {
        echo 'getBannedUserIps';
		$this->log('Получаем забаненные IP');
		$banned_user_ips = [];
		$user_items = $this->db()->query()
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
		$request_items = $this->db()->query()
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
		$this->log("Количество забаненных IP: ".count($banned_user_ips));
		return $this;
	}

	private function isBannedUser($user_id) {
		$tmp_user = new StatUser($user_id);
		if (in_array($tmp_user->val('ip_addr'), $this->banned_user_ips)) {
			return true;
		}
		return false;
	}

	private function updateAvgStatBanner() {
		$this->log('обновление avg_stat_banner');
		$limit = $this->limit;
		$offset = -1;
		$sbs = new StatBannerShow();
		$sbc = new StatBannerClick();
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
				$id_banner = $item->val('id_banner');

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
				$id_banner = $item->val('id_banner');

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
							'id_banner' => $id_banner,
							'month' => 0
						];

						foreach ($types as $type => $count) {
							$result_row['count_'.$type] = $count;
						}

						$asb = new AvgStatBanner();
						$asb->reader()->setWhere(['AND', 'id_firm = :id_firm', 'timestamp_inserting = :timestamp_inserting', 'id_banner = :id_banner', 'month = :month'], [':id_firm' => $id_firm, ':timestamp_inserting' => $timestamp_inserting, ':id_banner' => $id_banner, ':month' => 0])
								->objectByConds();
						if ($asb->exists()) {
							$vals = $asb->getVals();
							foreach ($types as $type => $count) {
								$vals['count_'.$type] += $count;
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

		$this->log('вставлено '.$i.' записей');

		$i = 0;
		foreach ($data_month as $id_city => $firms) {
			foreach ($firms as $id_firm => $banners) {
				foreach ($banners as $id_banner => $dates) {
					foreach ($dates as $timestamp_inserting => $types) {
						$i++;
						$result_row = [
							'id_firm' => $id_firm,
							'timestamp_inserting' => $timestamp_inserting,
							'id_banner' => $id_banner,
							'month' => 1
						];

						foreach ($types as $type => $count) {
							$result_row['count_'.$type] = $count;
						}

						$asb = new AvgStatBanner();
						$asb->reader()->setWhere(['AND', 'id_firm = :id_firm', 'timestamp_inserting = :timestamp_inserting', 'id_banner = :id_banner', 'month = :month'], [':id_firm' => $id_firm, ':timestamp_inserting' => $timestamp_inserting, ':id_banner' => $id_banner, ':month' => 1])
								->objectByConds();
						if ($asb->exists()) {
							$vals = $asb->getVals();
							foreach ($types as $type => $count) {
								$vals['count_'.$type] += $count;
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

	private function updateAvgStatFirms() {
		$this->log('обновление avg_stat_firm_popular_pages');
		$limit = $this->limit;
		$offset = -1;

		$stat_object = new StatObject();

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
			foreach ($items as $item) {
				if ($this->isBannedUser($item->val('id_stat_user'))) {
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

			$stat_user = new StatUser();
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

			$sr = new StatRequest();
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
								$check_where[] = $k.' = :'.$k;
								$check_params[':'.$k] = $v;
							}
							$asfpp = new AvgStatFirmPopularPages();
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
							$check_where[] = $k.' = :'.$k;
							$check_params[':'.$k] = $v;
						}

						$asu = new AvgStatUser();
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
								if (!isset($data_geo[$id_city][$id_firm][$date_type][$datetime][$users_geo[$id_user]['id_city'].'|'.$users_geo[$id_user]['city_name']])) {
									$data_geo[$id_city][$id_firm][$date_type][$datetime][$users_geo[$id_user]['id_city'].'|'.$users_geo[$id_user]['city_name']] = 0;
								}
								$data_geo[$id_city][$id_firm][$date_type][$datetime][$users_geo[$id_user]['id_city'].'|'.$users_geo[$id_user]['city_name']] ++;
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
							$asg = new AvgStatGeo();
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
								$check_where[] = $k.' = :'.$k;
								$check_params[':'.$k] = $v;
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

		$this->log('обработано '.$i.' записей');

		return $this;
	}

	private function updateAvgStatObject() {
		$this->log('обновление avg_stat_object');

		$limit = $this->limit;
		$offset = -1;
		$i = 0;
		$stat_object = new StatObject();

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
				$type = (int)$item->val('type');

				if (isset($stat_user_group[$item->id_firm().'~'.$item->val('id_city').'~'.$item->val('id_stat_user').'~'.$item->val('type')])) {
					continue;
				}
				$stat_user_group[$item->id_firm().'~'.$item->val('id_city').'~'.$item->val('id_stat_user').'~'.$item->val('type')] = 1;

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
							$result_row['t'.$type] = $count;
						}

						$check_where = ['AND'];
						$check_params = [];
						foreach ($check_row as $k => $v) {
							$check_where[] = $k.' = :'.$k;
							$check_params[':'.$k] = $v;
						}
						$aso = new AvgStatObject();
						$aso->reader()
								->setWhere($check_where, $check_params)
								->objectByConds();

						if ($aso->exists()) {
							$vals = $aso->getVals();
							foreach ($types as $type => $count) {
								$vals['t'.$type] += $count;
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

	private function updateAvgStatPopularPages() {
		$this->log('обновление avg_stat_popular_pages');

		$limit = $this->limit;
		$offset = -1;
		$stat_request = new StatRequest();

		$where = ['AND', 'timestamp_inserting >= :start', 'timestamp_inserting < :end'];
		$params = [':start' => $this->start_datetime, ':end' => $this->end_datetime];

		$data = [];
		$i = 0;
		while (1) {
			$offset++;
			$i++;
			$items = $stat_request->reader()
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

				$id_city = $item->val('response_id_city');
				$title = $item->val('response_title');
				$url = trim($item->val('response_url'));

				if (!$url) {
					continue;
				}

				if (str()->posArray($url, ['.png', '.gif', '.jpg'])) {
					continue;
				}
				$url_hash = md5($url);
				$type = preg_match('~/catalog/[0-9]+/[0-9]*/?~', $url) ? 'catalog' : 'page';
				list($datetime_week, $datetime_month) = self::getDatetimesFromModel($item);

				if (!isset($data[$id_city]['week'][$datetime_week][$url_hash])) {
					$data[$id_city]['week'][$datetime_week][$url_hash] = ['count' => 0, 'title' => $title, 'type' => $type, 'url' => $url];
				}

				if (!isset($data[$id_city]['months'][$datetime_month][$url_hash])) {
					$data[$id_city]['months'][$datetime_month][$url_hash] = ['count' => 0, 'title' => $title, 'type' => $type, 'url' => $url];
				}

				$data[$id_city]['week'][$datetime_week][$url_hash]['count'] ++;
				$data[$id_city]['months'][$datetime_month][$url_hash]['count'] ++;
			}
		}

		$i = 0;
		foreach ($data as $id_city => $date_types) {
			foreach ($date_types as $date_type => $datetimes) {
				foreach ($datetimes as $datetime => $urls) {
					foreach ($urls as $url => $count) {
						$i++;
						$result_row = [
							'id_city' => $id_city,
							'timestamp_inserting' => $datetime,
							'type' => $count['type'],
							'title' => $count['title'],
							'count' => $count['count'],
							'url' => $count['url'],
							'month' => ($date_type === 'months') ? 1 : 0
						];

						$check_row = $result_row;
						unset($check_row['count']);
						unset($check_row['title']);

						$check_where = ['AND'];
						$check_params = [];
						foreach ($check_row as $k => $v) {
							$check_where[] = $k.' = :'.$k;
							$check_params[':'.$k] = $v;
						}
						$aspp = new AvgStatPopularPages();
						$aspp->reader()
								->setWhere($check_where, $check_params)
								->objectByConds();

						if ($aspp->exists()) {
							$aspp->update(['count' => $aspp->val('count') + $result_row['count']]);
						} else {
							$aspp->insert($result_row);
						}
					}
				}
			}
		}

		return $this;
	}

	private function updateAdvertModuleStatistics() {
		$this->log('обновление просмотров рекламных модулей');
		$site_stat = new StatSite(1);

		$so = new StatObject();
		$items = $so->query()
				->setSelect(['id_city', 'id_firm', 'id_stat_request', 'id_stat_user', 'model_alias', 'model_id', 'name', 'type'])
				->setFrom([$so->table()])
				->setWhere(['AND', 'type = :type', 'timestamp_inserting > :datetime'], [':type' => StatObject::ADVERT_MODULE_SHOW, ':datetime' => $site_stat->val('timestamp_last_updating')])
				->setGroupBy(['id_city', 'id_firm', 'id_stat_request', 'id_stat_user', 'model_alias', 'model_id', 'name', 'type'])
				->select();

		foreach ($items as $it) {
			if ($this->isBannedUser($it['id_stat_user'])) {
				continue;
			}
			$advert_module = new AdvertModule($it['model_id']);
			$advert_module->writer()->updateCounter('total_views', +1);
		}

		$this->log('обработано '.count($items).' записей');

		$this->log('обновление кликов рекламных модулей');
		$so = new StatObject();

		$items = $so->query()
				->setSelect(['id_city', 'id_firm', 'id_stat_request', 'id_stat_user', 'model_alias', 'model_id', 'name', 'type'])
				->setFrom([$so->table()])
				->setWhere(['AND', 'type = :type', 'timestamp_inserting > :datetime'], [':type' => StatObject::ADVERT_MODULE_CLICK, ':datetime' => $site_stat->val('timestamp_last_updating')])
				->setGroupBy(['id_city', 'id_firm', 'id_stat_request', 'id_stat_user', 'model_alias', 'model_id', 'name', 'type'])
				->select();

		foreach ($items as $it) {
			if ($this->isBannedUser($it['id_stat_user'])) {
				continue;
			}
			$advert_module = new AdvertModule($it['model_id']);
			$advert_module->writer()->updateCounter('total_clicks', 1);
		}

		$this->log('обработано '.count($items).' записей');

		return $this;
	}

	private function updatePromoStatistics() {
		$this->log('обновление просмотров акций');
		$site_stat = new StatSite(1);

		$so = new StatObject();

		$items = $so->query()
				->setSelect(['id_city', 'id_firm', 'id_stat_request', 'id_stat_user', 'model_alias', 'model_id', 'name', 'type'])
				->setFrom([$so->table()])
				->setWhere(['AND', 'type = :type', 'timestamp_inserting > :datetime'], [':type' => 5, ':datetime' => $site_stat->val('timestamp_last_updating')])
				->setGroupBy(['id_city', 'id_firm', 'id_stat_request', 'id_stat_user', 'model_alias', 'model_id', 'name', 'type'])
				->select();

		foreach ($items as $it) {
			if ($this->isBannedUser($it['id_stat_user'])) {
				continue;
			}
			$firm_promo = new FirmPromo($it['model_id']);
			$firm_promo->writer()->updateCounter('total_views', +1);
		}

		$this->log('обработано '.count($items).' записей');

		return $this;
	}

	private function updateVideoStatistics() {
		$this->log('обновление просмотров видеороликов');
		$site_stat = new StatSite(1);

		$so = new StatObject();

		$items = $so->query()
				->setSelect(['id_city', 'id_firm', 'id_stat_request', 'id_stat_user', 'model_alias', 'model_id', 'name', 'type'])
				->setFrom([$so->table()])
				->setWhere(['AND', 'type = :type', 'timestamp_inserting > :datetime'], [':type' => 7, ':datetime' => $site_stat->val('timestamp_last_updating')])
				->setGroupBy(['id_city', 'id_firm', 'id_stat_request', 'id_stat_user', 'model_alias', 'model_id', 'name', 'type'])
				->select();

		foreach ($items as $it) {
			if ($this->isBannedUser($it['id_stat_user'])) {
				continue;
			}
			$firm_video = new FirmVideo($it['model_id']);
			$firm_video->update(['total_views' => (int)$firm_video->val('total_views') + 1]);
		}

		$this->log('обработано '.count($items).' записей');

		return $this;
	}

//

	private static function getDatetimesFromModel(Model $item) {
		$timestamp = DeprecatedDateTime::toTimestamp($item->val('timestamp_inserting'));
		$timestamp_day = mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
		$datetime = DeprecatedDateTime::fromTimestamp(strtotime('last monday', strtotime('tomorrow', $timestamp_day)));
		$timestamp_month = mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp));
		$datetime_month = DeprecatedDateTime::fromTimestamp($timestamp_month);

		return [$datetime, $datetime_month];
	}

}
