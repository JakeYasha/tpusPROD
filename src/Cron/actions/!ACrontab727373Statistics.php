<?php

class ACrontab727373Statistics extends ACrontabAction {
 
	public $start_datetime;
	public $end_datetime;
	public $limit = 10000;
	public $debug_mode = false;
        public $banned_user_ips = [];

	public function run() {
                $this->start_datetime = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
                //$this->start_datetime = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(mktime(0, 0, 0, 4, 12, 2016));
		$this->end_datetime = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(mktime(0, 0, 0, date('m'), date('d'), date('Y')));
                //$this->end_datetime = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(mktime(0, 0, 0, 4, 13, 2016));

                $this
                                ->getBannedUserIps()
				->updateAvgStatObject727373()
				->updateAvgStatFirms727373();

		return parent::run();
	}
        
        private function getBannedUserIps() {
                $this->log('получаем banned user ips');
                // ips from stat_users
                $banned_user_ips = [];
                $db = new CDbConnection();
                $user_items = $db->query()
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
                        if (!in_array($item['ip_addr'], $banned_user_ips)) {
                                $banned_user_ips []= $item['ip_addr'];
                        }
                }
                // ips from stat_requests
                $request_items = $db->query()
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
                        if (!in_array($item['ip_addr'], $banned_user_ips)) {
                                $banned_user_ips []= $item['ip_addr'];
                        }
                }

                $this->banned_user_ips = array_unique($banned_user_ips);
                $this->log("Banned user ips count: ".count($banned_user_ips));
                return $this;
        }
        
        private function isBannedUser($user_id) {
                $tmp_user = new StatUser727373($user_id);
                if (in_array($tmp_user->val('ip_addr'), $this->banned_user_ips)) {
                        return true;
                }
                return false;
        }

	private function updateAvgStatFirms727373() {
		$this->log('обновление avg_stat_firm_popular_pages');
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
			print_r("\r обработано: " . ($j * $this->limit)."\n");
			$offset++;

			$items = $stat_object
					->setLimits($limit, $limit * $offset)
					->setWhere($where, $params)
					->getAll();

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
                                
				$requests_ids[$item->val('id_stat_request')][] = ['id_firm' => $item->id_firm(), 'id_service' => $item->id_service()];
                                
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
				
                                if (!$url) {
                                        continue;
                                }                                
                                $url_hash = md5($url);
                                
                                if (!isset($requests_ids[$item->id()])) {
                                        continue;
                                }

                                foreach ($requests_ids[$item->id()] as $request) {
                                        $id_service = $request['id_service'];
                                        $id_firm = $request['id_firm'];

                                        if (!isset($data[$id_service][$id_firm]['weeks'][$datetime_week][$url_hash])) {
                                                $data[$id_service][$id_firm]['weeks'][$datetime_week][$url_hash] = [
                                                        'url' => $item->val('response_url'),
                                                        'title' => $item->val('response_title'),
                                                        'count' => 0
                                                ];
                                        }

                                        if (!isset($data[$id_service][$id_firm]['months'][$datetime_month][$url_hash])) {
                                                $data[$id_service][$id_firm]['months'][$datetime_month][$url_hash] = [
                                                        'url' => $item->val('response_url'),
                                                        'title' => $item->val('response_title'),
                                                        'count' => 0
                                                ];
                                        }

                                        if (!isset($data_user[$id_service][$id_firm]['weeks'][$datetime_week][$item->val('id_stat_user')])) {
                                                $data_user[$id_service][$id_firm]['weeks'][$datetime_week][$item->val('id_stat_user')] = 1;
                                        }

                                        if (!isset($data_user[$id_service][$id_firm]['months'][$datetime_month][$item->val('id_stat_user')])) {
                                                $data_user[$id_service][$id_firm]['months'][$datetime_month][$item->val('id_stat_user')] = 1;
                                        }

                                        $data[$id_service][$id_firm]['weeks'][$datetime_week][$url_hash]['count'] ++;
                                        $data[$id_service][$id_firm]['months'][$datetime_month][$url_hash]['count'] ++;
                                }
			}
		}

		$i = 0;

		foreach ($data as $id_service => $firms) {
			foreach ($firms as $id_firm => $date_types) {
				foreach ($date_types as $date_type => $dates) {
					foreach ($dates as $datetime => $urls) {
						foreach ($urls as $hash => $count) {
							$i++;
							$result_row = [
								'id_firm' => $id_firm,
								'id_service' => $id_service,
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
							$asfpp->setWhere($check_where, $check_params)
									->getByConds();

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

		foreach ($data_user as $id_service => $firms) {
			foreach ($firms as $id_firm => $date_types) {
				foreach ($date_types as $date_type => $dates) {
					foreach ($dates as $datetime => $users) {
						$result_row = [
							'id_firm' => $id_firm,
							'id_service' => $id_service,
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
						$asu->setWhere($check_where, $check_params)
								->getByConds();

						if ($asu->exists()) {
							$asu->update(['count' => $result_row['count'] + $asu->val('count')]);
						} else {
							$asu->insert($result_row);
						}

						foreach ($users as $id_user => $val) {
							if (isset($users_geo[$id_user])) {
								if (!isset($data_geo[$id_service][$id_firm][$date_type][$datetime][$users_geo[$id_user]['id_city'] . '|' . $users_geo[$id_user]['city_name']])) {
									$data_geo[$id_service][$id_firm][$date_type][$datetime][$users_geo[$id_user]['id_city'] . '|' . $users_geo[$id_user]['city_name']] = 0;
								}
								$data_geo[$id_service][$id_firm][$date_type][$datetime][$users_geo[$id_user]['id_city'] . '|' . $users_geo[$id_user]['city_name']] ++;
							}
						}
					}
				}
			}
		}

		foreach ($data_geo as $id_service => $firms) {
			foreach ($firms as $id_firm => $date_types) {
				foreach ($date_types as $date_type => $dates) {
					foreach ($dates as $datetime => $cities) {
						foreach ($cities as $composite_id => $count) {
							$result_city = '';
							list($id_city, $city_name) = explode('|', $composite_id);
							$asg = new AvgStatGeo727373();
							$result_row = [
								'id_firm' => $id_firm,
								'id_service' => $id_service,
								'timestamp_inserting' => $datetime,
								'count' => $count,
								'month' => ($date_type === 'months' ? 1 : 0),
								'city_name' => ''
							];

							$city_name = trim($city_name);
							if (!$city_name && $id_city) {
								$sc = new StsCity();
								$sc->setWhere(['AND', 'id_city = :id_city'], [':id_city' => $id_city])
										->getByConds();
								if ($sc->exists()) {
									$result_row['city_name'] = $sc->name();
								}
							} elseif ($city_name) {
								$result_row['city_name'] = $city_name;
							}


							$check_row = $result_row;
							unset($check_row['count']);
							$check_where = ['AND'];
							$check_params = [];
							foreach ($check_row as $k => $v) {
								$check_where[] = $k . ' = :' . $k;
								$check_params[':' . $k] = $v;
							}

							$asg->setWhere($check_where, $check_params)
									->getByConds();

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
		$this->log('обновление avg_stat_object');

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
			$items = $stat_object
					->setLimits($limit, $limit * $offset)
					->setWhere($where, $params)
					->getAll();

			if (!$items) {
				break;
			}

			foreach ($items as $item) {
                                if ($this->isBannedUser($item->val('id_stat_user'))) {
                                        continue;
                                }
                                
				$id_service = $item->id_service();
				$id_firm = $item->id_firm();
				$type = (int) $item->val('type');

				if (isset($stat_user_group[$item->id_firm() . '~' . $item->id_service() . '~' . $item->val('id_stat_user') . '~' . $item->val('type')])) {
					continue;
				}
				$stat_user_group[$item->id_firm() . '~' . $item->id_service() . '~' . $item->val('id_stat_user') . '~' . $item->val('type')] = 1;

				list($datetime_week, $datetime_month) = self::getDatetimesFromModel($item);

				if (!isset($data[$id_service][$id_firm]['weeks'][$datetime_week][$type])) {
					$data[$id_service][$id_firm]['weeks'][$datetime_week][$type] = 0;
				}
				if (!isset($data[$id_service][$id_firm]['months'][$datetime_month][$type])) {
					$data[$id_service][$id_firm]['months'][$datetime_month][$type] = 0;
				}

				$data[$id_service][$id_firm]['weeks'][$datetime_week][$type] ++;
				$data[$id_service][$id_firm]['months'][$datetime_month][$type] ++;
			}
		}

		$i = 0;
		foreach ($data as $id_service => $firms) {
			foreach ($firms as $id_firm => $date_types) {
				foreach ($date_types as $date_type => $datetimes) {
					foreach ($datetimes as $datetime => $types) {
						$result_row = [
							'id_service' => $id_service,
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
						$aso->setWhere($check_where, $check_params)
								->getByConds();

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

	private static function getDatetimesFromModel(\Sky4\Model $item) {
		$timestamp = \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_inserting'));
		$timestamp_day = mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
		$datetime = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(strtotime('last monday', strtotime('tomorrow', $timestamp_day)));
		$timestamp_month = mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp));
		$datetime_month = \Sky4\Helper\DeprecatedDateTime::fromTimestamp($timestamp_month);

		return [$datetime, $datetime_month];
	}

}
