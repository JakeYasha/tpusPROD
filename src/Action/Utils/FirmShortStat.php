<?php

namespace App\Action\Utils;
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

use \Foolz\SphinxQL\SphinxQL;

class FirmShortStat extends \App\Action\Utils {

    protected $start_datetime;
    protected $end_datetime;
    protected $banned_user_ips = [];
    protected $banned_user727373_ips = [];
    protected $limit = 10000;

    public function __construct() {
        parent::__construct();
        if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
            exit();
        }
    }

    public function execute() {
        ini_set('max_execution_time', -1);
        $params = app()->request()->processGetParams([
            'firm_id' => ['type' => 'int']
        ]);

        $firm_id = (int) $params['firm_id'];

        if (!$firm_id) {
            echo 'Укажите firm_id' . '</br>';
            exit();
        }

        $firm = new \App\Model\Firm($firm_id);

        if (!$firm->exists()) {
            echo 'Нет такой фирмы' . '</br>';
            exit();
        }

        $this->start_datetime = '2018-11-01 00:00:00';
        $this->end_datetime = '2018-11-13 00:00:00';
        $this->getBannedUserIps();

        $limit = $this->limit;
        $offset = -1;
        $stat_request = new \App\Model\StatRequest();
        $where = ['AND', 'timestamp_inserting >= :start', 'timestamp_inserting < :end', 'response_id_city = :response_id_city'];
        $params = [':start' => $this->start_datetime, ':end' => $this->end_datetime, ':response_id_city' => 76004];
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
                if (trim($item->val('response_url')) == '/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/') {
                    echo $item->val('timestamp_inserting') . ': ' . trim($item->val('response_url')) . '<br/>';
                }

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
                
                if ($url != '/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/') continue;
                echo $item->val('timestamp_inserting') . ': ' . trim($item->val('response_url')) . '<br/>';
                
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
                        if ($count['url'] != '/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/') continue;
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
                            $check_where[] = $k . ' = :' . $k;
                            $check_params[':' . $k] = $v;
                        }
                        $aspp = new \App\Model\AvgStatPopularPages();
                        $aspp->reader()
                                ->setWhere($check_where, $check_params)
                                ->objectByConds();

                        if ($aspp->exists()) {
                            echo "UPDATE " .
                            ",id_city = " . $result_row['id_city'] .
                            ",timestamp_inserting = " . $result_row['timestamp_inserting'] .
                            ",type = " . $result_row['type'] .
                            ",title = " . $result_row['title'] .
                            ",count = " . ($aspp->val('count') + $result_row['count']) . " (old[" . $aspp->id() . "]: " . $aspp->val('count') . ")" .
                            ",url = " . $result_row['url'] .
                            ",month = " . $result_row['month'] .
                            " <br/>";
                            //$aspp->update(['count' => $aspp->val('count') + $result_row['count']]);
                        } else {
                            //$aspp->insert($result_row);
                            echo "INSERT " .
                            ",id_city = " . $result_row['id_city'] .
                            ",timestamp_inserting = " . $result_row['timestamp_inserting'] .
                            ",type = " . $result_row['type'] .
                            ",title = " . $result_row['title'] .
                            ",count = " . $result_row['count'] .
                            ",url = " . $result_row['url'] .
                            ",month = " . $result_row['month'] .
                            " <br/>";
                        }
                    }
                }
            }
        }
        
        echo " <br/>";
        echo " <br/>";
        
        $limit = $this->limit;
        $offset = -1;
        
        $stat_object = new StatObject();

		$data = [];
		$data_user = [];
		$data_geo = [];
		$j = 0;

		$where = ['AND', 'timestamp_inserting >= :start', 'timestamp_inserting < :end', 'id_firm = :id_firm'];
		$params = [':start' => $this->start_datetime, ':end' => $this->end_datetime, ':id_firm' => $firm->id()];

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
                            if ($count['url'] != '/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/') continue;
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
                                echo "UPDATE " .
                                ",timestamp_inserting = " . $result_row['timestamp_inserting'] .
                                ",title = " . $result_row['title'] .
                                ",count = " . ($asfpp->val('count') + $result_row['count']) . " (old[" . $asfpp->id() . "]: " . $asfpp->val('count') . ")" .
                                ",url = " . $result_row['url'] .
                                ",month = " . $result_row['month'] .
                                " <br/>";
                                //$aspp->update(['count' => $aspp->val('count') + $result_row['count']]);
                            } else {
                                //$aspp->insert($result_row);
                                echo "INSERT " .
                                ",timestamp_inserting = " . $result_row['timestamp_inserting'] .
                                ",title = " . $result_row['title'] .
                                ",count = " . $result_row['count'] .
                                ",url = " . $result_row['url'] .
                                ",month = " . $result_row['month'] .
                                " <br/>";
                            }
						}
					}
				}
			}
		}



        /* $aso = new \App\Model\AvgStatObject();
          $avg_stat_objects = $aso->reader()
          ->setWhere(
          [
          'AND',
          'id_firm = :id_firm',
          'timestamp_inserting >= :timestamp_start',
          'timestamp_inserting < :timestamp_end'
          ], [
          ':id_firm' => $firm_id,
          ':timestamp_start' => $this->start_datetime,
          ':timestamp_end' => $this->end_datetime
          ]
          )
          ->setOrderBy(['month ASC', 'timestamp_inserting DESC'])
          ->objects();

          echo '<h1 style="text-align:center;">Статистика по фирме ' . $firm->name() . ' c ' . $this->start_datetime . ' по ' . $this->end_datetime . '</h1>';

          echo '<h2 style="text-align:center;">Статистика AVG_STAT_OBJECT</h2>';
          echo '<table width="100%" border="1">';
          echo '<tr>';
          echo '<th>Неделя/Месяц</th>';
          echo '<th>Показ карточки фирмы</th>';
          echo '<th>Показ карточек товара</th>';
          echo '<th>Показ страниц прайс-листа</th>';
          echo '<th>Переход по ссылке на сайт фирмы</th>';
          echo '</tr>';
          $t1 = 0;
          $t2 = 0;
          $t3 = 0;
          $t9 = 0;
          foreach ($avg_stat_objects as $avg_stat_object) {
          if ((int) $avg_stat_object->val('month') != 1) {
          $t1 += (int) $avg_stat_object->val('t1');
          $t2 += (int) $avg_stat_object->val('t2');
          $t3 += (int) $avg_stat_object->val('t3');
          $t9 += (int) $avg_stat_object->val('t9');
          echo '<tr>';
          echo '<td style="text-align:center;">' . $avg_stat_object->val('timestamp_inserting') . '</td>';
          echo '<td style="text-align:center;">' . $avg_stat_object->val('t1') . '</td>';
          echo '<td style="text-align:center;">' . $avg_stat_object->val('t2') . '</td>';
          echo '<td style="text-align:center;">' . $avg_stat_object->val('t3') . '</td>';
          echo '<td style="text-align:center;">' . $avg_stat_object->val('t9') . '</td>';
          echo '</tr>';
          } else {
          echo '<tr>';
          echo '<td style="text-align:center;color:#aa0012;">' . $avg_stat_object->val('timestamp_inserting') . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . $t1 . '/' . $avg_stat_object->val('t1') . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . $t2 . '/' . $avg_stat_object->val('t2') . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . $t3 . '/' . $avg_stat_object->val('t3') . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . $t9 . '/' . $avg_stat_object->val('t9') . '</td>';
          echo '</tr>';
          $t1 = 0;
          $t2 = 0;
          $t3 = 0;
          $t9 = 0;
          }
          }

          echo '</table>'; */

        /* $aso727373 = new \App\Model\AvgStatObject727373();
          $avg_stat_objects727373 = $aso727373->reader()
          ->setWhere(
          [
          'AND',
          'id_firm = :id_firm',
          'timestamp_inserting >= :timestamp_start',
          'timestamp_inserting < :timestamp_end'
          ], [
          ':id_firm' => $firm_id,
          ':timestamp_start' => $this->start_datetime,
          ':timestamp_end' => $this->end_datetime
          ]
          )
          ->setOrderBy(['month ASC', 'timestamp_inserting DESC'])
          ->objects();

          echo '<h1 style="text-align:center;">Статистика 727373 по фирме ' . $firm->name() . ' c ' . $this->start_datetime . ' по ' . $this->end_datetime . '</h1>';

          echo '<h2 style="text-align:center;">Статистика AVG_STAT_OBJECT727373</h2>';
          echo '<table width="100%" border="1">';
          echo '<tr>';
          echo '<th>Неделя/Месяц</th>';
          echo '<th>Показ карточки фирмы</th>';
          echo '<th>Переход по ссылке на сайт фирмы</th>';
          echo '</tr>';
          $t1 = 0;
          $t4 = 0;
          foreach ($avg_stat_objects727373 as $avg_stat_object727373) {
          if ((int) $avg_stat_object727373->val('month') != 1) {
          $t1 += (int) $avg_stat_object727373->val('t1');
          $t4 += (int) $avg_stat_object727373->val('t4');
          echo '<tr>';
          echo '<td style="text-align:center;">' . $avg_stat_object727373->val('timestamp_inserting') . '</td>';
          echo '<td style="text-align:center;">' . $avg_stat_object727373->val('t1') . '</td>';
          echo '<td style="text-align:center;">' . $avg_stat_object727373->val('t4') . '</td>';
          echo '</tr>';
          } else {
          echo '<tr>';
          echo '<td style="text-align:center;color:#aa0012;">' . $avg_stat_object727373->val('timestamp_inserting') . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . $t1 . '/' . $avg_stat_object727373->val('t1') . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . $t4 . '/' . $avg_stat_object727373->val('t4') . '</td>';
          echo '</tr>';
          $t1 = 0;
          $t4 = 0;
          }
          }

          echo '</table>'; */

        /* $asfp = new \App\Model\AvgStatFirmPopularPages();
          $avg_stat_firm_popular_pages = $asfp->reader()
          ->setSelect(['month', 'timestamp_inserting', 'SUM(`count`) as `count`', "CASE WHEN LOCATE('mode=price',`url`) THEN 'price-list' WHEN LOCATE('price/show',`url`) THEN 'price' ELSE 'firm' END AS `res_url`"])
          ->setWhere(
          [
          'AND',
          'id_firm = :id_firm',
          'timestamp_inserting >= :timestamp_start',
          'timestamp_inserting < :timestamp_end',
          ['OR', 'url = :url1', 'url LIKE :url2', 'url LIKE :url3']
          ], [
          ':id_firm' => $firm_id,
          ':timestamp_start' => $this->start_datetime,
          ':timestamp_end' => $this->end_datetime,
          ':url1' => '/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/',
          ':url2' => '/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/?%mode=price%',
          ':url3' => '/price/show/%',
          ]
          )
          ->setGroupBy(['month', 'timestamp_inserting', 'res_url'])
          ->setOrderBy(['`timestamp_inserting` DESC', '`month` ASC', '`count` DESC'])
          ->rows();

          echo '<h1 style="text-align:center;">Статистика популярных страниц по фирме ' . $firm->name() . ' c ' . $this->start_datetime . ' по ' . $this->end_datetime . '</h1>';

          echo '<h2 style="text-align:center;">Статистика AVG_STAT_FIRM_POPULAR_PAGES</h2>';
          echo '<table width="100%" border="1">';
          echo '<tr>';
          echo '<th>Неделя/месяц</th>';
          echo '<th>Показ карточки фирмы</th>';
          echo '<th>Показ карточек товара</th>';
          echo '<th>Показ страниц прайс листа</th>';
          echo '</tr>';
          $td = [];
          $td['month'] = [];
          foreach ($avg_stat_firm_popular_pages as $avg_stat_firm_popular_page) {
          $page_type = '';
          switch ($avg_stat_firm_popular_page['res_url']) {
          case 'price-list':
          $page_type = 'Показ страниц прайс листа';
          break;
          case 'price':
          $page_type = 'Показ карточек товара';
          break;
          case 'firm':
          $page_type = 'Показ карточки фирмы';
          break;
          }
          if (!isset($td[$avg_stat_firm_popular_page['timestamp_inserting'] . '-' . $avg_stat_firm_popular_page['month']])) {
          $td[$avg_stat_firm_popular_page['timestamp_inserting'] . '-' . $avg_stat_firm_popular_page['month']] = [];
          }
          if (!isset($td['month'][$avg_stat_firm_popular_page['res_url']])) {
          $td['month'][$avg_stat_firm_popular_page['res_url']] = 0;
          }

          if ((int) $avg_stat_firm_popular_page['month'] == 0) {
          $td['month'][$avg_stat_firm_popular_page['res_url']] += (int) $avg_stat_firm_popular_page['count'];
          $td[$avg_stat_firm_popular_page['timestamp_inserting'] . '-' . $avg_stat_firm_popular_page['month']][$avg_stat_firm_popular_page['res_url']] = ['timestamp_inserting' => $avg_stat_firm_popular_page['timestamp_inserting'], 'count' => (int) $avg_stat_firm_popular_page['count']];
          } else {
          $td[$avg_stat_firm_popular_page['timestamp_inserting'] . '-' . $avg_stat_firm_popular_page['month']][$avg_stat_firm_popular_page['res_url']] = ['timestamp_inserting' => $avg_stat_firm_popular_page['timestamp_inserting'], 'count' => $td['month'][$avg_stat_firm_popular_page['res_url']] . '/' . (int) $avg_stat_firm_popular_page['count']];
          $td['month'][$avg_stat_firm_popular_page['res_url']] = 0;
          }
          }
          unset($td['month']);

          foreach ($td as $timestamp => $row) {
          $month = explode('-', strrev($timestamp));
          $month = $month[0];
          if ((int) $month == 0) {
          $_datetime = isset($row['firm']) ? $row['firm']['timestamp_inserting'] : (isset($row['price']) ? $row['price']['timestamp_inserting'] : '-');
          echo '<tr>';
          echo '<td style="text-align:center;">' . $_datetime . '</td>';
          echo '<td style="text-align:center;">' . (isset($row['firm']) ? $row['firm']['count'] : 0) . '</td>';
          echo '<td style="text-align:center;">' . (isset($row['price']) ? $row['price']['count'] : 0) . '</td>';
          echo '<td style="text-align:center;">' . (isset($row['price-list']) ? $row['price-list']['count'] : 0) . '</td>';
          echo '</tr>';
          } else {
          echo '<tr>';
          echo '<td style="text-align:center;color:#aa0012;">' . $_datetime . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . (isset($row['firm']) ? $row['firm']['count'] : 0) . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . (isset($row['price']) ? $row['price']['count'] : 0) . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . (isset($row['price-list']) ? $row['price-list']['count'] : 0) . '</td>';
          echo '</tr>';
          }
          }

          echo '</table>';

          $so = new \App\Model\StatObject();
          $stat_objects = $so->reader()
          ->setWhere(
          [
          'AND',
          'id_firm = :id_firm',
          'timestamp_inserting >= :timestamp_start',
          'timestamp_inserting < :timestamp_end',
          'type IN (1,2,3,9)'
          ], [
          ':id_firm' => $firm_id,
          ':timestamp_start' => $this->start_datetime,
          ':timestamp_end' => $this->end_datetime
          ]
          )
          ->setOrderBy(['timestamp_inserting ASC'])
          ->objects();

          $t1 = 0;
          $t2 = 0;
          $t3 = 0;
          $t9 = 0;

          $tb1 = 0;
          $tb2 = 0;
          $tb3 = 0;
          $tb9 = 0;

          $weeks = [];

          $date = date("Y-m-d 00:00:00", strtotime("2018-01-01 00:00:00"));
          $cache = [];
          foreach ($stat_objects as $stat_object) {
          if (isset($cache[$stat_object->val('id_firm').'~'.$stat_object->val('id_city').'~'.$stat_object->val('id_stat_user').'~'.$stat_object->val('type')])) {
          continue;
          } else {
          $cache[$stat_object->val('id_firm').'~'.$stat_object->val('id_city').'~'.$stat_object->val('id_stat_user').'~'.$stat_object->val('type')] = 1;
          }

          $_date = date("Y-m-d 00:00:00", strtotime($stat_object->val('timestamp_inserting')));
          if ($this->isNextWeek($date, $_date)) {
          $date = $_date;
          $weeks[$date] = [];
          }

          if (!isset($weeks[$date][$stat_object->val('type')])) {
          $weeks[$date][$stat_object->val('type')] = 0;
          }

          if ($this->isBannedUser($stat_object->val('id_stat_user'))) {
          $weeks[$date][$stat_object->val('type')] += 1;
          } else {
          $weeks[$date][$stat_object->val('type')] += 1;
          switch ((int) $stat_object->val('type')) {
          case 1: $t1++;
          break;
          case 2: $t2++;
          break;
          case 3: $t3++;
          break;
          case 9: $t9++;
          break;
          }
          }
          }

          echo '<h1 style="text-align:center;">Статистика по фирме ' . $firm->name() . ' c ' . $this->start_datetime . ' по ' . $this->end_datetime . '</h1>';

          echo '<h2 style="text-align:center;">Статистика STAT_OBJECT</h2>';
          echo '<table width="100%" border="1">';
          echo '<tr>';
          echo '<th>Неделя</th>';
          echo '<th>Показ карточки фирмы</th>';
          echo '<th>Показ карточек товара</th>';
          echo '<th>Показ страниц прайс-листа</th>';
          echo '<th>Переход по ссылке на сайт фирмы</th>';
          echo '</tr>';
          $first_data = '';
          $weeks = array_reverse($weeks);
          foreach ($weeks as $data => $week) {
          if (!$first_data) {
          $first_data = $data;
          }
          $tb1 += (isset($week[1]) ? $week[1] : 0);
          $tb2 += (isset($week[2]) ? $week[2] : 0);
          $tb3 += (isset($week[3]) ? $week[3] : 0);
          $tb9 += (isset($week[9]) ? $week[9] : 0);
          echo '<tr>';
          echo '<td style="text-align:center;">' . $data . '</td>';
          echo '<td style="text-align:center;">' . (isset($week[1]) ? $week[1] : 0) . '</td>';
          echo '<td style="text-align:center;">' . (isset($week[2]) ? $week[2] : 0) . '</td>';
          echo '<td style="text-align:center;">' . (isset($week[3]) ? $week[3] : 0) . '</td>';
          echo '<td style="text-align:center;">' . (isset($week[9]) ? $week[9] : 0) . '</td>';
          echo '</tr>';
          }
          echo '<tr>';
          echo '<td style="text-align:center;color:#aa0012;">' . $first_data . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . $t1 . '/' . $tb1 . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . $t2 . '/' . $tb2 . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . $t3 . '/' . $tb3 . '</td>';
          echo '<td style="text-align:center;color:#aa0012;">' . $t9 . '/' . $tb9 . '</td>';
          echo '</tr>';

          echo '</table>'; */

        exit();
    }

    private function getBannedUserIps() {
        $banned_user_ips = [];
        $user_items = app()->db()->query()
                ->setText("SELECT * FROM ("
                        . " SELECT `id`,`ip_addr`, `timestamp_beginning`, SUBSTRING_INDEX(`timestamp_beginning`, :tstmp, 2) as `without_seconds`, COUNT(`id`) as `users_per_minute` "
                        . " FROM `stat_user` "
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
                $banned_user_ips [] = $item['ip_addr'];
            }
        }
        // ips from stat_requests
        $request_items = app()->db()->query()
                ->setText("SELECT * FROM ("
                        . " SELECT sr.`id`, SUBSTRING_INDEX(`timestamp_inserting`, :tstmp, 2) as `without_seconds`, COUNT(sr.`id`) as `requests_per_minute`, su.`ip_addr` "
                        . " FROM `stat_request` sr "
                        . " LEFT JOIN `stat_user` su ON su.`id` = sr.`id_stat_user`"
                        . " WHERE sr.`timestamp_inserting` >= :start_timestamp AND sr.`timestamp_inserting` < :end_timestamp"
                        . " GROUP BY su.`ip_addr`, `without_seconds` ORDER BY `requests_per_minute` DESC) s WHERE s.`requests_per_minute` > :max_requests_per_minute")
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

    private function isBannedUser727373($user_id) {
        $tmp_user = new \App\Model\StatUser727373($user_id);
        if (in_array($tmp_user->val('ip_addr'), $this->banned_user727373_ips)) {
            return true;
        }
        return false;
    }

    private function isNextWeek($cur_date, $cmp_date) {
        $result = (strtotime(date("Y-m-d 00:00:00", strtotime($cmp_date))) - strtotime(date("Y-m-d 00:00:00", strtotime($cur_date)))) / (60 * 60 * 24);
        if ($result >= 7) {
            return true;
        }

        return false;
    }

    private static function getDatetimesFromModel(Model $item) {
        $timestamp = \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_inserting'));
        $timestamp_day = mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
        $datetime = \Sky4\Helper\DeprecatedDateTime::fromTimestamp(strtotime('last monday', strtotime('tomorrow', $timestamp_day)));
        $timestamp_month = mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp));
        $datetime_month = \Sky4\Helper\DeprecatedDateTime::fromTimestamp($timestamp_month);

        return [$datetime, $datetime_month];
    }

}
