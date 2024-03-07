<?php

namespace App\Presenter;

use App\Action\FirmManager;
use App\Action\FirmUser;
use App\Classes\Pagination;
use App\Model\AdvertModule;
use App\Model\AdvertModuleFirmType;
use App\Model\AdvertModuleGroup;
use App\Model\AvgStatBanner;
use App\Model\AvgStatBanner727373;
use App\Model\AvgStatFirmPopularPages;
use App\Model\AvgStatFirm727373PopularPages;
use App\Model\AvgStatGeo;
use App\Model\AvgStatGeo727373;
use App\Model\AvgStatObject;
use App\Model\AvgStatObject727373;
use App\Model\AvgStatUser;
use App\Model\AvgStatUser727373;
use App\Model\Banner;
use App\Model\BannerGroup;
use App\Model\BannerCatalog;
use App\Model\Firm;
use App\Model\FirmManager as FirmManager2;
use App\Model\FirmType;
use App\Model\PriceCatalog;
use App\Model\StatBannerClick;
use App\Model\StatBanner727373Click;
use App\Model\StatObject;
use App\Model\StatObject727373;
use App\Model\StsHistCalls;
use App\Model\StsHistExportDetail;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model\Utils;
use function app;
use function str;

class FirmUserStatistics extends Presenter {

	public function findSummary($filters = []) {
		$aso = new AvgStatObject();
		$where = ['AND',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
			'month = :month'
		];

		$params = [
			':id_firm' => app()->firmUser()->id_firm(),
			':timestamp_start' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
			':timestamp_end' => DeprecatedDateTime::fromTimestamp($filters['t_end']),
			':month' => $filters['group'] === 'months' ? 1 : 0
		];

		$types = StatObject::getTypeNames();
		$select_conds = [];
		foreach ($types as $k => $v) {
			$select_conds[] = 'SUM(t'.$k.') as t'.$k;
			$types['t'.$k] = ['name' => $v, 'stat_group' => StatObject::getStatGroupNameByType($k)];
			unset($types[$k]);
		}

		$row = $aso->query()
				->setSelect($select_conds)
				->setFrom($aso->table())
				->setWhere($where, $params)
				->setOrderBy('`timestamp_inserting` DESC')
				->selectRow();

		foreach ($row as $field_name => $count) {
			if ((int)$count > 0) {
				$this->items[] = [
					'name' => $types[$field_name]['name'],
					'stat_group' => $types[$field_name]['stat_group'],
					'count' => $count
				];
				$counts[] = $count;
			}
		}

		if ($this->items) {
			array_multisort($counts, SORT_DESC, $this->items);
		}

		$template = $filters['html_mode'] ? 'presenter_stat_summary_items_pdf' : 'presenter_stat_summary_items';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findGeo($filters = []) {
		$asg = new AvgStatGeo();

		$where = ['AND',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
			'month = :month'
		];
		$params = [
			':id_firm' => app()->firmUser()->id_firm(),
			':timestamp_start' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
			':timestamp_end' => DeprecatedDateTime::fromTimestamp($filters['t_end']),
			':month' => $filters['group'] === 'months' ? 1 : 0
		];

		$rows = $asg->query()
				->setSelect('`city_name`, SUM(`count`) as `count`')
				//->setLimit($filters['html_mode'] ? 99999 : 10)
				->setLimit(10)
				->setFrom($asg->table())
				->setWhere($where, $params)
				->setGroupBy('city_name')
				->setOrderBy('`count` DESC, `city_name` ASC')
				->select();

		$this->items = ['chart_items' => [], 'data' => []];
		foreach ($rows as $k => $row) {
			$this->items['data'][] = [
				'name' => $row['city_name'],
				'count' => $row['count']
			];

			$this->items['chart_items'][$row['city_name']] = [
				'count' => $row['count'],
				'link' => ''
			];
		}

		$template = $filters['html_mode'] ? 'presenter_stat_geo_items_pdf' : 'presenter_stat_geo_items';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findDynamic($filters = []) {
		$aso = new AvgStatObject();
		$asu = new AvgStatUser();

		$where = ['AND',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
			'month = :month'
		];
		$params = [
			':id_firm' => app()->firmUser()->id_firm(),
			':timestamp_start' => DeprecatedDateTime::shiftMonths(-6),
			':timestamp_end' => DeprecatedDateTime::now(),
			':month' => $filters['group'] === 'months' ? 1 : 0
		];

		$types = StatObject::getTypeNames();
		$select_conds = [];
		foreach ($types as $k => $v) {
			$select_conds[] = 't'.$k;
		}

		$shows = $aso->query()
				->setSelect('('.implode('+', $select_conds).') as `total`, `timestamp_inserting`')
				->setFrom($aso->table())
				->setWhere($where, $params)
				->setGroupBy($filters['group'] === 'months' ? 'YEAR(timestamp_inserting), MONTH(timestamp_inserting)' : 'YEAR(timestamp_inserting), MONTH(timestamp_inserting), WEEK(timestamp_inserting)')
				->setOrderBy('timestamp_inserting DESC')
				->select();

		$users = $asu->query()
				->setSelect('SUM(count) as `total`, `timestamp_inserting`')
				->setFrom($asu->table())
				->setWhere($where, $params)
				->setGroupBy($filters['group'] === 'months' ? 'YEAR(timestamp_inserting), MONTH(timestamp_inserting)' : 'YEAR(timestamp_inserting), MONTH(timestamp_inserting), WEEK(timestamp_inserting)')
				->setOrderBy('timestamp_inserting DESC')
				->select();

		$data = [];
		foreach ($shows as $row) {
			if (!isset($data[$row['timestamp_inserting']])) {
				$data[$row['timestamp_inserting']] = ['shows' => 0, 'users' => 0];
			}
			$data[$row['timestamp_inserting']]['shows'] = $row['total'];
		}
		foreach ($users as $row) {
			$data[$row['timestamp_inserting']]['users'] = $row['total'];
		}

		$prepared = [];
		$chart_items = [];
		$chart_items['columns'][] = 'Просмотры';
		$chart_items['columns'][] = 'Пользователи';

		foreach ($data as $timestamp => $types) {
			$prepared[] = [
				'name' => $filters['group'] === 'months' ? str()->firstCharToUpper(DeprecatedDateTime::monthName($timestamp)) : 'c '.date('d ', DeprecatedDateTime::toTimestamp($timestamp)).DeprecatedDateTime::monthName($timestamp, 1),
				'shows' => $types['shows'],
				'users' => $types['users']
			];
		}

		$rdata = array_reverse($data);
		foreach ($rdata as $timestamp => $types) {
			$chart_items['data'][$filters['group'] === 'months' ? str()->firstCharToUpper(DeprecatedDateTime::monthName($timestamp)) : 'c '.date('d ', DeprecatedDateTime::toTimestamp($timestamp)).DeprecatedDateTime::monthName($timestamp, 1)][0] = $types['shows'];
			$chart_items['data'][$filters['group'] === 'months' ? str()->firstCharToUpper(DeprecatedDateTime::monthName($timestamp)) : 'c '.date('d ', DeprecatedDateTime::toTimestamp($timestamp)).DeprecatedDateTime::monthName($timestamp, 1)][1] = $types['users'];
		}

		$this->items = ['cols' => [$filters['group'] === 'months' ? 'Месяц' : 'Неделя', 'Посетители', 'Просмотры'], 'data' => $prepared, 'chart_items' => $chart_items];

		$template = $filters['html_mode'] ? 'presenter_stat_dynamic_items_pdf' : 'presenter_stat_dynamic_items';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findExport($filters = [], $old_format = true) {
        $firm_id = $old_format ? app()->firmUser()->firm()->val('id_firm') : app()->firmUser()->firm()->id();
        $service_id = app()->firmUser()->firm()->val('id_service');
        
		$model = new StsHistExportDetail();
		$where = ['AND',
			'id_firm = :id_firm',
			'id_service = :id_service',
			'datetime >= :timestamp_start',
			'datetime < :timestamp_end'
		];
		$params = [
			':id_firm' => $firm_id,
			':id_service' => $service_id,
			':timestamp_start' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
			':timestamp_end' => DeprecatedDateTime::fromTimestamp($filters['t_end'])
		];

		$no_page_filters = $filters;
		unset($no_page_filters['page']);

		$total_records = $model->query()
				->setSelect('COUNT(*) as `total`, COUNT(DISTINCT id_hist_export) as `total_rows`')
				->setFrom($model->table())
				->setWhere($where, $params)
				->selectRow();

		$this->pagination()
				->setTotalRecords($total_records['total'])
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmUser::link('/export/'))
				->setLinkParams($no_page_filters)
				->calculateParams()
				->renderElems();

		$rows = $model->query()
				->setSelect(['id_hist_export', 'id_export_type', 'datetime', 'name', 'pack', 'unit'])
				->setFrom($model->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($where, $params)
				->setOrderBy('sts_hist_export_detail.`datetime` ASC')
				->select();

		$items = [];
		foreach ($rows as $row) {
			if (!isset($items[$row['id_hist_export']])) {
				$items[$row['id_hist_export']] = [];
			}
			$timestamp = DeprecatedDateTime::toTimestamp($row['datetime']);
			$items[$row['id_hist_export']][] = [
				'date' => date('d.m.Y', $timestamp),
				'time' => date('H:i:s', $timestamp),
				'name' => $row['name'],
				'pack' => $row['pack'],
				'id_export_type' => $row['id_export_type'] == 3 ? 'email' : ($row['id_export_type'] == 5 ? 'sms' : 'не указано'),
				'unit' => $row['unit']
			];
		}

		$this->items = ['total_rows' => $total_records['total_rows'], 'total_prices' => $total_records['total'], 'items' => $items];
		$template = $filters['html_mode'] ? 'presenter_stat_emails_items_pdf' : 'presenter_stat_emails_items';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findCalls($filters = [], $old_format = true) {
        $firm_id = $old_format ? app()->firmUser()->firm()->val('id_firm') : app()->firmUser()->firm()->id();
        $service_id = app()->firmUser()->firm()->val('id_service');
        $dispatcher_mode = $old_format ? 'IS' : 'IS NOT';
        
		$model = new StsHistCalls();
		$where = ['AND',
			'sts_hist_answer.id_firm = :id_firm',
			'sts_hist_answer.id_service = :id_service',
            'sts_hist_calls.dispatcher ' . $dispatcher_mode . ' :dispatcher',
			'sts_hist_calls.datetime >= :timestamp_start',
			'sts_hist_calls.datetime < :timestamp_end'
		];
		$params = [
			':id_firm' => $firm_id,
			':id_service' => $service_id,
            ':dispatcher' => null,
			':timestamp_start' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
			':timestamp_end' => DeprecatedDateTime::fromTimestamp($filters['t_end'])
		];

		$no_page_filters = $filters;
		unset($no_page_filters['page']);

		$id_firm = isset($filters['id_firm']) ? $filters['id_firm'] : $firm_id;
		$id_service = isset($filters['id_service']) ? $filters['id_service'] : $service_id;

		$total_records = $model->query()
				->setSelect('COUNT(*) as `total`, COUNT(DISTINCT(sts_hist_calls.id_hist_calls)) as `total_rows`')
				->setFrom($model->table())
				->setWhere($where, $params)
				->setInnerJoin('sts_hist_answer', 'sts_hist_calls.id_hist_calls = sts_hist_answer.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_answer.from_id_service')
				//->setGroupBy('sts_hist_answer.name')
				->selectRow();

		$this->pagination()
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmUser::link('/calls/'))
				->setLinkParams($no_page_filters)
				->setTotalRecords($total_records['total'])
				->calculateParams()
				->renderElems();

		$rows = $model->query()
				->setSelect(['sts_hist_calls.id_hist_calls', 'sts_hist_calls.datetime', 'sts_hist_calls.datetime_finish', 'sts_hist_answer.name', 'sts_hist_answer.pack', 'sts_hist_answer.unit', 'sts_hist_answer.manufacture', 'sts_hist_readdress.phone', 'sts_hist_readdress.readdress'])
				->setFrom($model->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($where, $params)
				->setLeftJoin('sts_hist_readdress', 'sts_hist_calls.id_hist_calls = sts_hist_readdress.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_readdress.from_id_service and sts_hist_readdress.id_firm='.$id_firm.' and sts_hist_readdress.id_service='.$id_service)
				->setInnerJoin('sts_hist_answer', 'sts_hist_calls.id_hist_calls = sts_hist_answer.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_answer.from_id_service')
				->setOrderBy('sts_hist_calls.`datetime` ASC')
				->select();

		$this->items = ['total_rows' => $total_records['total_rows'], 'total_prices' => $total_records['total'], 'items' => []];
		foreach ($rows as $row) {
			$timestamp = DeprecatedDateTime::toTimestamp($row['datetime']);
			if (!isset($this->items['items'][$timestamp])) {
				$this->items['items'][$timestamp] = [];
			}
			$this->items['items'][$timestamp][] = [
				'name' => $row['name'],
				'manufacture' => $row['manufacture'],
				'pack' => $row['pack'],
				'readdress' => $row['readdress'],
				'phone' => $row['phone'],
			];
		}

		$template = $filters['html_mode'] ? 'presenter_stat_calls_items_pdf' : 'presenter_stat_calls_items';
		$this->setItemsTemplate($template);

		return $this;
	}
    
    public function findOnlineCalls($filters = []) {
		$id_firm = $filters['id_firm'];
		$id_service = $filters['id_service'];
		$id_manager = $filters['id_manager'];
        
        $log = [];
        
        $log []= ['time' => date('H:i:s'), 'msg' => 'Старт поиска звонков'];
        
		$model = new StsHistCalls();
        if($id_manager) {
            $log []= ['time' => date('H:i:s'), 'msg' => 'Поиск с фильтром по менеджеру'];
            $firm_manager = new FirmManager2($id_manager);
            if (!$firm_manager->exists()) {
                throw new Exception();
            }
            $__conds = Utils::prepareWhereCondsFromArray($firm_manager->getManagerUserIds(), 'id_manager');
            $_where = [
				'AND',
                'flag_is_active = :1',
                $__conds['where']
			];
			$_params = [
                ':1' => 1
            ] + $__conds['params'];
            
            $firm_ids = [];
            if ($id_firm) {
                $firm_ids = [$id_firm];
            } else {
                $firm = new Firm();
                $firm_ids = array_keys($firm->reader()
                        ->setWhere($_where, $_params)
                        ->rowsWithKey('id'));
            }
            if ($firm_ids) {
                $_params = [];
                $_params_names = [];
                foreach ($firm_ids as $id) {
                    $_params[':id_firm_' . $id] = $id;
                    $_params_names[] = ':id_firm_' . $id;
                }
                $conds = [
                    'where' => '`sts_hist_answer`.`id_firm` IN (' . implode(',', $_params_names) . ')',
                    'params' => $_params
                ];
                $where = ['AND',
                    'sts_hist_calls.dispatcher > :dispatcher',
                    'sts_hist_calls.datetime >= :timestamp_start',
                    'sts_hist_calls.datetime < :timestamp_end',
                    $conds['where']
                ];
                $params = [
                    ':dispatcher' => 0,
                    ':timestamp_start' => $filters['t_start'] . ' 00:00:00',
                    ':timestamp_end' => $filters['t_end'] . ' 23:59:59'
                ] + $conds['params'];
            } else {
                $where = ['AND',
                    'sts_hist_calls.dispatcher > :dispatcher',
                    'sts_hist_calls.datetime >= :timestamp_start',
                    'sts_hist_calls.datetime < :timestamp_end'
                ];
                $params = [
                    ':dispatcher' => 0,
                    ':timestamp_start' => $filters['t_start'] . ' 00:00:00',
                    ':timestamp_end' => $filters['t_end'] . ' 23:59:59'
                ];
            }
        } else if ($id_firm && $id_service) {
            $log []= ['time' => date('H:i:s'), 'msg' => 'Поиск с фильтром по фирме'];
            $where = ['AND',
                'sts_hist_answer.id_firm = :id_firm',
                'sts_hist_answer.id_service = :id_service',
                'sts_hist_calls.dispatcher > :dispatcher',
                'sts_hist_calls.datetime >= :timestamp_start',
                'sts_hist_calls.datetime < :timestamp_end'
            ];
            $params = [
                ':id_firm' => $id_firm,
                ':id_service' => $id_service,
                ':dispatcher' => 0,
                ':timestamp_start' => $filters['t_start'] . ' 00:00:00',
                ':timestamp_end' => $filters['t_end'] . ' 23:59:59'
            ];
        } else {
            $where = ['AND',
                'sts_hist_calls.dispatcher > :dispatcher',
                'sts_hist_calls.datetime >= :timestamp_start',
                'sts_hist_calls.datetime < :timestamp_end'
            ];
            $params = [
                ':dispatcher' => 0,
                ':timestamp_start' => $filters['t_start'] . ' 00:00:00',
                ':timestamp_end' => $filters['t_end'] . ' 23:59:59'
            ];
        }
        
		$no_page_filters = $filters;
		unset($no_page_filters['page']);
        $log []= ['time' => date('H:i:s'), 'msg' => 'Вычисление счетчиков по ответам/переадрессациям'];
        if ($filters['readdress_only'] == 'on') {
            $total_records = $model->query()
                    ->setSelect('COUNT(*) as `total`, COUNT(DISTINCT(sts_hist_calls.id_hist_calls)) as `total_rows`')
                    ->setFrom($model->table())
                    ->setWhere($where, $params)
                    ->setInnerJoin('sts_hist_answer', 'sts_hist_calls.id_hist_calls = sts_hist_answer.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_answer.from_id_service')
                    ->setRightJoin('sts_hist_readdress', 'sts_hist_calls.id_hist_calls = sts_hist_readdress.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_readdress.from_id_service AND sts_hist_readdress.id_firm = sts_hist_answer.id_firm')
                    //->setGroupBy('sts_hist_answer.name')
                    ->selectRow();
        } else {
            $total_records = $model->query()
                    ->setSelect('COUNT(*) as `total`, COUNT(DISTINCT(sts_hist_calls.id_hist_calls)) as `total_rows`')
                    ->setFrom($model->table())
                    ->setWhere($where, $params)
                    ->setInnerJoin('sts_hist_answer', 'sts_hist_calls.id_hist_calls = sts_hist_answer.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_answer.from_id_service')
                    ->setLeftJoin('sts_hist_readdress', 'sts_hist_calls.id_hist_calls = sts_hist_readdress.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_readdress.from_id_service AND sts_hist_readdress.id_firm = sts_hist_answer.id_firm')
                    //->setGroupBy('sts_hist_answer.name')
                    ->selectRow();
        }
        $log []= ['time' => date('H:i:s'), 'msg' => 'Рендерим'];
		$this->pagination()
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmManager::link('/calls/'))
				->setLinkParams($no_page_filters)
				->setTotalRecords($total_records['total'])
				->calculateParams()
				->renderElems();
        $log []= ['time' => date('H:i:s'), 'msg' => 'Отрендерили'];
        $log []= ['time' => date('H:i:s'), 'msg' => 'Поиск'];
        if ($filters['readdress_only'] == 'on') {
            $rows = $model->query()
                    ->setSelect([
                        'sts_hist_calls.id_hist_calls', 
                        'sts_hist_calls.datetime', 
                        'sts_hist_calls.dispatcher', 
                        'sts_hist_calls.asterisk_id', 
                        'sts_hist_calls.datetime_finish', 
                        'sts_hist_answer.name', 
                        'firm.company_name as company_name', 
                        'sts_hist_answer.id_firm', 
                        'sts_hist_answer.id_price', 
                        'sts_hist_answer.id_service', 
                        'sts_hist_answer.pack', 
                        'sts_hist_answer.unit', 
                        'sts_hist_answer.manufacture', 
                        'sts_hist_readdress.phone', 
                        'sts_hist_readdress.readdress'
                    ])
                    ->setFrom($model->table())
                    ->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
                    ->setWhere($where, $params)
                    ->setInnerJoin('sts_hist_answer', 'sts_hist_calls.id_hist_calls = sts_hist_answer.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_answer.from_id_service')
                    ->setRightJoin('sts_hist_readdress', 'sts_hist_calls.id_hist_calls = sts_hist_readdress.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_readdress.from_id_service AND sts_hist_readdress.id_firm = sts_hist_answer.id_firm')
                    ->setLeftJoin('firm', 'sts_hist_answer.id_firm = firm.id')
                    ->setOrderBy('sts_hist_calls.`datetime` DESC')
                    ->select();
        } else {
            $rows = $model->query()
                    ->setSelect([
                        'sts_hist_calls.id_hist_calls', 
                        'sts_hist_calls.datetime', 
                        'sts_hist_calls.dispatcher', 
                        'sts_hist_calls.asterisk_id', 
                        'sts_hist_calls.datetime_finish', 
                        'sts_hist_answer.name', 
                        'firm.company_name as company_name', 
                        'sts_hist_answer.id_firm', 
                        'sts_hist_answer.id_price', 
                        'sts_hist_answer.id_service', 
                        'sts_hist_answer.pack', 
                        'sts_hist_answer.unit', 
                        'sts_hist_answer.manufacture', 
                        'sts_hist_readdress.phone', 
                        'sts_hist_readdress.readdress'
                    ])
                    ->setFrom($model->table())
                    ->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
                    ->setWhere($where, $params)
                    ->setInnerJoin('sts_hist_answer', 'sts_hist_calls.id_hist_calls = sts_hist_answer.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_answer.from_id_service')
                    ->setLeftJoin('sts_hist_readdress', 'sts_hist_calls.id_hist_calls = sts_hist_readdress.id_hist_calls AND sts_hist_calls.from_id_service = sts_hist_readdress.from_id_service AND sts_hist_readdress.id_firm = sts_hist_answer.id_firm')
                    ->setLeftJoin('firm', 'sts_hist_answer.id_firm = firm.id')
                    ->setOrderBy('sts_hist_calls.`datetime` DESC')
                    ->select();
        }
        $log []= ['time' => date('H:i:s'), 'msg' => 'Поиск завершен'];
        
		$this->items = ['total_rows' => $total_records['total_rows'], 'total_prices' => $total_records['total'], 'items' => []];
        
        $db = new \Sky4\Db\Connection('727373_dev');
        $dispatchers = $db->query()
                ->setText("SELECT `id`,`name_full` FROM `dispatcher`")
                ->fetch();
        $log []= ['time' => date('H:i:s'), 'msg' => 'Заполняем данные диспетчеров'];
        $dispatcher = [];
        foreach ($dispatchers as $_dispatcher) {
            $dispatcher[$_dispatcher['id']] = $_dispatcher['name_full'];
        }
        
		foreach ($rows as $row) {
			$timestamp = DeprecatedDateTime::toTimestamp($row['datetime']);
			if (!isset($this->items['items'][$timestamp])) {
				$this->items['items'][$timestamp] = [];
			}
            
			if (!isset($this->items['items'][$timestamp][$row['id_firm']])) {
				$this->items['items'][$timestamp][$row['id_firm']] = [];
			}
            
			$this->items['items'][$timestamp][$row['id_firm']][] = [
				'name' => $row['name'],
				'manufacture' => $row['manufacture'],
                'firm_name' => $row['company_name'],
                'dispatcher' => $dispatcher[$row['dispatcher']],
                'asterisk_id' => $row['asterisk_id'],
				'pack' => $row['pack'],
				'readdress' => $row['readdress'],
				'phone' => $row['phone'],
			];
		}
        
        $log []= ['time' => date('H:i:s'), 'msg' => 'Финальная отрисовка'];

		$template = $filters['html_mode'] ? 'presenter_stat_calls_items_short_pdf' : 'presenter_stat_calls_items_short';
		$this->setItemsTemplate($template);
        //var_dump($log);
		return $this;
	}
    
    /*public function findOnlineCallSubgroups($filters = []) {
		$subgroup_ids = is_array($filters['id_subgroup']) ? $filters['id_subgroup'] : [$filters['id_subgroup']];
		$id_service = $filters['id_service'];
        
		$model = new StsHistAnswer();

        if ($subgroup_ids) {
            $_params = [];
            $_params_names = [];
            foreach ($subgroup_ids as $id) {
                $_params[':id_subgroup_' . $id] = $id;
                $_params_names[] = ':id_subgroup_' . $id;
            }
            $_conds = [
                'where' => '`id_subgroup` IN (' . implode(',', $subgroup_ids) . ')',
                'params' => $_params
            ];
            $where = [
				'AND',
                '`sts_hist_answer`.`from_id_service` = :from_id_service',
                '`sts_hist_answer`.`dispatcher` > :dispatcher',
                '`sts_hist_answer`.`datetime` >= :timestamp_start',
                '`sts_hist_answer`.`datetime` < :timestamp_end',
                $_conds['where']
			];
			$params = [
                ':from_id_service' => $id_service,
                ':dispatcher' => 0,
                ':timestamp_start' => $filters['t_start'] . ' 00:00:00',
                ':timestamp_end' => $filters['t_end'] . ' 23:59:59'
            ] + $_conds['params'];
        }
        
		$no_page_filters = $filters;
		unset($no_page_filters['page']);
        
        $total_records = count($model->query()
                ->setSelect('`sts_hist_answer`.`id_subgroup`, `sts_subgroup`.`name`, `sts_hist_answer`.`id_city`, COUNT(`sts_hist_answer`.`id_hist_answer`) AS `count`')
                ->setFrom($model->table())
                ->setWhere($where, $params)
                ->setLeftJoin('sts_subgroup', '`sts_subgroup`.`id_subgroup` = `sts_hist_answer`.`id_subgroup`')
                ->setGroupBy('`sts_hist_answer`.`id_subgroup`')
                ->rows());

		$this->pagination()
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmManager::link('/call-subgroups/'))
				->setLinkParams($no_page_filters)
				->setTotalRecords($total_records['total'])
				->calculateParams()
				->renderElems();


        $rows = $model->query()
                ->setSelect('`sts_hist_answer`.`id_subgroup` AS `id_subgroup`, `sts_subgroup`.`name` AS `name`, `sts_hist_answer`.`id_city` AS `id_city`, COUNT(`sts_hist_answer`.`id_hist_answer`) AS `count`')
                ->setFrom($model->table())
                ->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
                ->setWhere($where, $params)
                ->setOrderBy('`sts_subgroup`.`name` ASC')
                ->select();
        
		$this->items = ['total_rows' => $total_records['total_rows'], 'total_prices' => $total_records['total'], 'items' => []];
        
		$template = $filters['html_mode'] ? 'presenter_stat_calls_items_short_pdf' : 'presenter_stat_calls_items_short';
		$this->setItemsTemplate($template);

		return $this;
	}*/

	public function findPages($filters = []) {
		$asfpp = new AvgStatFirmPopularPages();
        $id_firm = isset($filters['id_firm']) && (int)$filters['id_firm'] ? $filters['id_firm'] : app()->firmUser()->id_firm();
		$where = ['AND',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
			'month = :month'
		];

		$params = [
			':id_firm' => $id_firm,
			':timestamp_start' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
			':timestamp_end' => DeprecatedDateTime::fromTimestamp($filters['t_end']),
			':month' => $filters['group'] === 'months' ? 1 : 0
		];

		$no_page_filters = $filters;
		unset($no_page_filters['page']);
		$this->setLimit($filters['html_mode'] ? 99999 : 15);
		$this->pagination()
				->setTotalRecords($asfpp->reader()->setWhere($where, $params)->count())
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmUser::link('/statistics/'))
				->setLinkParams($no_page_filters)
				->calculateParams()
				->renderElems();

		$rows = $asfpp->query()
				->setSelect(['*', 'SUM(count) as `count`'])
				->setFrom($asfpp->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($where, $params)
				->setGroupBy(['url'])
				->setOrderBy('`count` DESC, `title` ASC')
				->select();

		$firm = new Firm();
		$firm->getByIdFirm($id_firm);

		if (isset($filters['stat_group'])) {
            $i = 0;
			foreach ($rows as $row) {
                $i++;
				$stat_group = StatObject::getStatGroupNameByUrl($row['url']);
				if ((int)$row['count'] > 0 && $filters['stat_group'] == $stat_group) {
					$this->items[] = [
                        'num' => $i + (20 * ($this->getPage() - 1)),
						'name' => $row['title'],
                        'firm' => $firm,
						'url' => $this->renderLink($row['url'], $firm->val('id_city')),
						'stat_group' => $stat_group,
                        'type' => '',
                        'site' => 'tovaryplus.ru',
						'count' => (int)$row['count']
					];
					$counts[] = (int)$row['count'];
				}
			}
		} else {
            $i = 0;
			foreach ($rows as $row) {
                $i++;
				if ((int)$row['count'] > 0) {
					$this->items[] = [
                        'num' => $i + (20 * ($this->getPage() - 1)),
						'name' => $row['title'],
                        'firm' => $firm,
						'url' => $this->renderLink($row['url'], $firm->val('id_city')),
						'stat_group' => StatObject::getStatGroupNameByUrl($row['url']),
                        'type' => '',
                        'site' => 'tovaryplus.ru',
						'count' => (int)$row['count']
					];
					$counts[] = (int)$row['count'];
				}
			}
		}

		if ($this->items) {
			array_multisort($counts, SORT_DESC, $this->items);
		}

		$template = $filters['html_mode'] ? 'presenter_stat_pages_items_pdf' : 'presenter_stat_pages_items';
		$this->setItemsTemplate($template);

		return $this;
	}
    
    public function find727373Pages($filters = []) {
		$asfpp = new AvgStatFirm727373PopularPages();
        $id_firm = isset($filters['id_firm']) && (int)$filters['id_firm'] ? $filters['id_firm'] : app()->firmUser()->id_firm();
		$where = ['AND',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
			'month = :month'
		];

		$params = [
			':id_firm' => $id_firm,
			':timestamp_start' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
			':timestamp_end' => DeprecatedDateTime::fromTimestamp($filters['t_end']),
			':month' => $filters['group'] === 'months' ? 1 : 0
		];

		$no_page_filters = $filters;
		unset($no_page_filters['page']);
		$this->setLimit($filters['html_mode'] ? 99999 : 15);
		$this->pagination()
				->setTotalRecords($asfpp->reader()->setWhere($where, $params)->count())
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmUser::link('/statistics/'))
				->setLinkParams($no_page_filters)
				->calculateParams()
				->renderElems();


		$rows = $asfpp->query()
				->setSelect(['*', 'SUM(count) as `count`'])
				->setFrom($asfpp->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($where, $params)
				->setGroupBy(['url'])
				->setOrderBy('`count` DESC, `title` ASC')
				->select();

		$firm = new Firm();
		$firm->getByIdFirm($id_firm);

		if (isset($filters['stat_group'])) {
            $i = 0;
			foreach ($rows as $row) {
                $i++;
				$stat_group = StatObject727373::getStatGroupNameByUrl($row['url']);
				if ((int)$row['count'] > 0 && $filters['stat_group'] == $stat_group) {
                    $url = $this->renderLink($row['url'], $firm->val('id_city'));
                    if (strpos($url,'/organization/away/') !== FALSE) {
                        $row['title'] = 'Переход по ссылке Фирмы';
                    }
					$this->items[] = [
                        'num' => $i + (20 * ($this->getPage() - 1)),
						'name' => $row['title'],
                        'firm' => $firm,
						'url' => $url,
						'stat_group' => $stat_group,
                        'type' => '',
                        'site' => '727373.ru',
						'count' => (int)$row['count']
					];
					$counts[] = (int)$row['count'];
				}
			}
		} else {
            $i = 0;
			foreach ($rows as $row) {
                $i++;
				if ((int)$row['count'] > 0) {
                    $url = $this->renderLink($row['url'], $firm->val('id_city'));
                    if (strpos($url,'/organization/away/') !== FALSE) {
                        $row['title'] = 'Переход по ссылке Фирмы';
                    }
					$this->items[] = [
                        'num' => $i + (20 * ($this->getPage() - 1)),
						'name' => $row['title'],
                        'firm' => $firm,
						'url' => $url,
						'stat_group' => StatObject727373::getStatGroupNameByUrl($row['url']),
                        'type' => '',
                        'site' => '727373.ru',
						'count' => (int)$row['count']
					];
					$counts[] = (int)$row['count'];
				}
			}
		}

		if ($this->items) {
			array_multisort($counts, SORT_DESC, $this->items);
		}

		$template = $filters['html_mode'] ? 'presenter_stat_pages_items_pdf' : 'presenter_stat_pages_items';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findBanners($filters = []) {
		$asb = new AvgStatBanner();
		$where = ['AND',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
			'month = :month'
		];
		$params = [
			':id_firm' => app()->firmUser()->id_firm(),
			':timestamp_start' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
			':timestamp_end' => DeprecatedDateTime::fromTimestamp($filters['t_end']),
			':month' => $filters['group'] === 'months' ? 1 : 0
		];


		$no_page_filters = $filters;
		unset($no_page_filters['page']);
		$this->pagination()
				->setTotalRecords($asb->reader()->setWhere($where, $params)->setGroupBy('id_banner')->count())
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmUser::link('/adv/'))
				->setLinkParams($no_page_filters)
				->calculateParams()
				->renderElems();

		$rows = $asb->query()
				->setSelect(['SUM(count_shows) as `count_shows`', 'SUM(count_clicks) as `count_clicks`', 'id_banner'])
				->setFrom($asb->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($where, $params)
				->setOrderBy('`count_clicks` DESC, `count_shows` DESC')
				//->setGroupBy($filters['group'] === 'months' ? 'id_banner, YEAR(timestamp_inserting), MONTH(timestamp_inserting)' : 'id_banner, YEAR(timestamp_inserting), MONTH(timestamp_inserting), WEEK(timestamp_inserting)')
				->setGroupBy('id_banner')
				->select();

		$sbc = new StatBannerClick();
		$sbc_where = $where;
		$sbc_params = $params;
		unset($sbc_where[4]);
		unset($sbc_params[':month']);

		$_rows_clicks = $sbc->query()
				->setSelect('COUNT(*) as `count`, id_banner')
				->setFrom($sbc->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($sbc_where, $sbc_params)
				->setGroupBy('id_banner')
				->select();
		$clicks = [];
		foreach ($_rows_clicks as $row) {
			$clicks[$row['id_banner']] = $row['count'];
		}

		$banner_ids = [];
		foreach ($rows as $row) {
			$banner_ids[] = $row['id_banner'];
		}

		$banner = new Banner();
		$banners = $banner->reader()->objectsByIds($banner_ids);

		$subgroups = [];
		$bg = new BannerGroup();
		foreach ($banners as $ban) {
			$id_subgroups = $bg->getSubgroupIdsByBannerId($ban->id());
			if ($id_subgroups) {
				$cat = new PriceCatalog();
				$cat_conds = Utils::prepareWhereCondsFromArray($id_subgroups, 'id_subgroup');
				$cat_where = ['AND', $cat_conds['where'], 'node_level = :node_level'];
				$cat_params = [':node_level' => 2] + $cat_conds['params'];
				$subgroups[$ban->id()] = $cat->reader()->setWhere($cat_where, $cat_params)->objects();
			}
		}

		$catalogs = [];
		$bc = new BannerCatalog();
		foreach ($banners as $ban) {
			$id_catalogs = $bc->getCatalogIdsByBannerId($ban->id());
			if ($id_catalogs) {
				$cat = new PriceCatalog();
				$cat_conds = Utils::prepareWhereCondsFromArray($id_catalogs, 'id');
				$cat_where = ['AND', $cat_conds['where'], 'node_level > :node_level'];
				$cat_params = [':node_level' => 2] + $cat_conds['params'];
				$catalogs[$ban->id()] = $cat->reader()->setWhere($cat_where, $cat_params)->objects();
			}
		}

		foreach ($rows as $row) {
			$ban = $banners[$row['id_banner']];
			if (!($ban instanceof Banner)) {
				continue;
			}
			$filters['id'] = $ban->id();

			$this->items[] = [
				'id' => $ban->id(),
				'name' => $ban->name(),
				'url' => $ban->link(),
				'count_shows' => (int)$row['count_shows'],
				//'count_clicks' => isset($clicks[$ban->id()]) ? (int) $clicks[$ban->id()] : 0,
				'count_clicks' => (int)$row['count_clicks'],
				//'ctr' => (int) $row['count_shows'] > 0 ? round((isset($clicks[$ban->id()]) ? (int) $clicks[$ban->id()] : 0) / (int) $row['count_shows'] * 100, 2) : 0,
				'ctr' => (int)$row['count_shows'] > 0 ? round((int)$row['count_clicks'] / (int)$row['count_shows'] * 100, 2) : 0,
				'clicks_link' => FirmUser::link('/adv/', $filters),
				'image' => $ban->getImage()->embeddedFile()->link(),
				'block' => app()->adv()->renderBanner($ban, $filters['html_mode'] ? true : false),
				'extension' => $ban->getImage()->val('file_extension'),
				'subgroups' => $subgroups[$ban->id()] ?? [],
				'catalogs' => $catalogs[$ban->id()] ?? [],
				'keywords' => $ban->val('keywords_string'),
				'end_date' => DeprecatedDateTime::toTimestamp($ban->val('timestamp_ending')),
				'period' => date('d.m.Y', DeprecatedDateTime::toTimestamp($ban->val('timestamp_beginning'))).' - '.date('d.m.Y', DeprecatedDateTime::toTimestamp($ban->val('timestamp_ending'))),
				'target_site' => $ban->val('url')
			];
			$counts[] = (int)$row['count_clicks'];
		}

		if ($this->items) {
			array_multisort($counts, SORT_DESC, $this->items);
		}

		$template = $filters['html_mode'] ? 'presenter_banner_items_pdf' : 'presenter_banner_items';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findBanners727373($filters = []) {
		$asb = new AvgStatBanner727373();
		$where = ['AND',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
			'month = :month'
		];
		$params = [
			':id_firm' => app()->firmUser()->id_firm(),
			':timestamp_start' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
			':timestamp_end' => DeprecatedDateTime::fromTimestamp($filters['t_end']),
			':month' => $filters['group'] === 'months' ? 1 : 0
		];

		$no_page_filters = $filters;
		unset($no_page_filters['page']);
		$this->pagination()
				->setTotalRecords($asb->reader()->setWhere($where, $params)->setGroupBy('id_banner_727373')->count())
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmUser::link('/adv-online/'))
				->setLinkParams($no_page_filters)
				->calculateParams()
				->renderElems();

		$rows = $asb->query()
				->setSelect(['SUM(count_shows) as `count_shows`', 'SUM(count_clicks) as `count_clicks`', 'id_banner_727373'])
				->setFrom($asb->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($where, $params)
				->setOrderBy('`count_clicks` DESC, `count_shows` DESC')
				//->setGroupBy($filters['group'] === 'months' ? 'id_banner, YEAR(timestamp_inserting), MONTH(timestamp_inserting)' : 'id_banner, YEAR(timestamp_inserting), MONTH(timestamp_inserting), WEEK(timestamp_inserting)')
				->setGroupBy('id_banner_727373')
				->select();

		$sbc = new StatBanner727373Click();
		$sbc_where = $where;
		$sbc_params = $params;
		unset($sbc_where[4]);
		unset($sbc_params[':month']);

		$_rows_clicks = $sbc->query()
				->setSelect('COUNT(*) as `count`, cml_banner_id')
				->setFrom($sbc->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($sbc_where, $sbc_params)
				->setGroupBy('cml_banner_id')
				->select();
		$clicks = [];
		foreach ($_rows_clicks as $row) {
			$clicks[$row['cml_banner_id']] = $row['count'];
		}

		$banner_ids = [];
		foreach ($rows as $row) {
			$banner_ids[] = $row['id_banner_727373'];
		}
        
        if ($banner_ids) {
            $banner = new Banner();
            $banner_conds = Utils::prepareWhereCondsFromArray($banner_ids, 'id');
            $banner_where = ['AND', $banner_conds['where'], 'site = :site'];
            $banner_params = [':site' => '727373'] + $banner_conds['params'];

            $banners = $banner->reader()
                    ->setWhere($banner_where, $banner_params)
                    ->objects();

            $subgroups = [];
            $bg = new BannerGroup();
            foreach ($banners as $ban) {
                $id_subgroups = $bg->getSubgroupIdsByBannerId($ban->id());
                if ($id_subgroups) {
                    $cat = new PriceCatalog();
                    $cat_conds = Utils::prepareWhereCondsFromArray($id_subgroups, 'id_subgroup');
                    $cat_where = ['AND', $cat_conds['where'], 'node_level = :node_level'];
                    $cat_params = [':node_level' => 2] + $cat_conds['params'];
                    $subgroups[$ban->id()] = $cat->reader()->setWhere($cat_where, $cat_params)->objects();
                }
            }

            $catalogs = [];
            $bc = new BannerCatalog();
            foreach ($banners as $ban) {
                $id_catalogs = $bc->getCatalogIdsByBannerId($ban->id());
                if ($id_catalogs) {
                    $cat = new PriceCatalog();
                    $cat_conds = Utils::prepareWhereCondsFromArray($id_catalogs, 'id');
                    $cat_where = ['AND', $cat_conds['where'], 'node_level > :node_level'];
                    $cat_params = [':node_level' => 2] + $cat_conds['params'];
                    $catalogs[$ban->id()] = $cat->reader()->setWhere($cat_where, $cat_params)->objects();
                }
            }
        }

        foreach ($rows as $row) {
            if (!isset($banners[$row['id_banner_727373']])) {
                continue;
            }
            $ban = $banners[$row['id_banner_727373']];
            if (!($ban instanceof Banner)) {
                continue;
            }

            $filters['id'] = $ban->id();

            $this->items[] = [
                'id' => $ban->id(),
                'name' => $ban->name(),
                'url' => $ban->link(),
                'count_shows' => (int)$row['count_shows'],
                //'count_clicks' => isset($clicks[$ban->id()]) ? (int) $clicks[$ban->id()] : 0,
                'count_clicks' => (int)$row['count_clicks'],
                //'ctr' => (int) $row['count_shows'] > 0 ? round((isset($clicks[$ban->id()]) ? (int) $clicks[$ban->id()] : 0) / (int) $row['count_shows'] * 100, 2) : 0,
                'ctr' => (int)$row['count_shows'] > 0 ? round((int)$row['count_clicks'] / (int)$row['count_shows'] * 100, 2) : 0,
                'clicks_link' => FirmUser::link('/adv-online/', $filters),
                'image' => $ban->getImage()->embeddedFile()->link(),
                'block' => app()->adv()->renderBanner($ban, $filters['html_mode'] ? true : false),
                'extension' => $ban->getImage()->val('file_extension'),
                'subgroups' => $subgroups[$ban->id()] ?? [],
                'catalogs' => $catalogs[$ban->id()] ?? [],
                'keywords' => $ban->val('keywords_string'),
				'end_date' => DeprecatedDateTime::toTimestamp($ban->val('timestamp_ending')),
                'period' => date('d.m.Y', DeprecatedDateTime::toTimestamp($ban->val('timestamp_beginning'))).' - '.date('d.m.Y', DeprecatedDateTime::toTimestamp($ban->val('timestamp_ending'))),
                'target_site' => $ban->val('url')
            ];
            $counts[] = (int)$row['count_clicks'];
        }

		if ($this->items) {
			array_multisort($counts, SORT_DESC, $this->items);
		}

		$template = $filters['html_mode'] ? 'presenter_banner_727373_items_pdf' : 'presenter_banner_727373_items';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findBannersByService($id_service, $filters = []) {
		$banner = new Banner();

		$bg = new BannerGroup();
        $bc = new BannerCatalog();
		$banner_ids = [];
        if ($filters['id_catalog'] !== 0) {
            $banner_ids = array_keys($bc->reader()->setWhere(['AND', 'id_catalog = :id_catalog'], [':id_catalog' => $filters['id_catalog']])
							->rowsWithKey('id_banner'));
        } elseif ($filters['id_subgroup'] !== 0) {
			$banner_ids = array_keys($bg->reader()->setWhere(['AND', 'id_subgroup = :id_subgroup'], [':id_subgroup' => $filters['id_subgroup']])
							->rowsWithKey('id_banner'));
		} elseif ($filters['id_group'] !== 0) {
			$banner_ids = array_keys($bg->reader()->setWhere(['AND', 'id_group = :id_group'], [':id_group' => $filters['id_group']])
							->rowsWithKey('id_banner'));
		}

		$firm = new Firm();
		$firm_ids_conds = Utils::prepareWhereCondsFromArray(array_keys($firm->reader()->setSelect('id')->setWhere(['AND', 'id_service = :id_service'], [':id_service' => app()->firmManager()->id_service()])->rowsWithKey('id')), 'id_firm');
		$where = ['AND', $firm_ids_conds['where']];
		$params = $firm_ids_conds['params'];

		$firm_ids = [];

		if ($banner_ids) {
			$conds = Utils::prepareWhereCondsFromArray($banner_ids);
			$where[] = $conds['where'];
			$params += $conds['params'];
		}

		if ($filters['id_manager'] !== 0) {
			$firm_manager = new FirmManager2($filters['id_manager']);
			if ($firm_manager->exists()) {
				$user_ids = $firm_manager->getManagerUserIds();
				$firm_conds = Utils::prepareWhereCondsFromArray($user_ids, 'id_manager');
				$firm = new Firm();
				$firm_ids = $firm->reader()->setWhere(['AND', $firm_conds['where']], $firm_conds['params'])
						->rowsWithKey('id');
				$firm_banner_conds = Utils::prepareWhereCondsFromArray(array_keys($firm_ids), 'id_firm');
				$where[] = $firm_banner_conds['where'];
				$params += $firm_banner_conds['params'];
			}
		}
		if ($filters['id_firm'] !== 0) {
			$where[] = 'id_firm = :id_firm';
			$params += [':id_firm' => $filters['id_firm']];
		}

		if ($filters['type'] !== 'all') {
			$where[] = 'type = :type';
			$params += [':type' => $filters['type'] == 'context' ? '' : $filters['type']];
		}

		if ($filters['max_count'] === 1) {
			$where[] = 'max_count = :max_count';
			$params += [':max_count' => 0];
		} elseif ($filters['max_count'] === 2) {
			$where[] = 'max_count > :max_count';
			$params += [':max_count' => 0];
		}

		if ($filters['active'] === 1) {
			$where[] = 'timestamp_ending > :now';
			$where[] = 'flag_is_active = :flag_is_active';
			$params += [':now' => DeprecatedDateTime::now(), ':flag_is_active' => 1];
		} elseif ($filters['active'] === 2) {
			$where[] = ['OR', 'timestamp_ending <= :now', 'flag_is_active = :flag_is_active'];
			$params += [':now' => DeprecatedDateTime::now(), ':flag_is_active' => 0];
		}

		$this->pagination()
				->setTotalRecords($banner->reader()->setWhere($where, $params)->count())
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmManager::link('/banners/'))
				->setLinkParams($filters)
				->calculateParams()
				->renderElems();

		$banners = $banner->reader()
				->setWhere($where, $params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('flag_is_active DESC, timestamp_ending '.(($filters['active'] === 2) ? 'DESC' : 'ASC'))
				->objects();

		$subgroups = [];
		$catalogs = [];
		foreach ($banners as $ban) {
			$bg = new BannerGroup();
			$id_subgroups = $bg->getSubgroupIdsByBannerId($ban->id());
			if ($id_subgroups) {
				$cat = new PriceCatalog();
				$cat_conds = Utils::prepareWhereCondsFromArray($id_subgroups, 'id_subgroup');
				$cat_where = ['AND', $cat_conds['where'], 'node_level = :node_level'];
				$cat_params = [':node_level' => 2] + $cat_conds['params'];
				$subgroups[$ban->id()] = $cat->reader()
						->setWhere($cat_where, $cat_params)
						->objects();
			}
			$bc = new BannerCatalog();
			$id_catalogs = $bc->getCatalogIdsByBannerId($ban->id());
			if ($id_catalogs) {
				$cat = new PriceCatalog();
				$cat_conds = Utils::prepareWhereCondsFromArray($id_catalogs, 'id');
				$cat_where = ['AND', $cat_conds['where'], 'node_level > :node_level'];
				$cat_params = [':node_level' => 2] + $cat_conds['params'];
				$catalogs[$ban->id()] = $cat->reader()->setWhere($cat_where, $cat_params)->objects();
			}
		}

		foreach ($banners as $ban) {
			$firm = new Firm();
			$firm->getByIdFirm($ban->id_firm());
			$this->items[] = [
				'id' => $ban->id(),
				'name' => $ban->name(),
				'firm' => $firm,
				'url' => $ban->link(),
				'banner_url' => $ban->val('url'),
                'site' => $ban->val('site'),
				'count_shows' => 0,
				'count_clicks' => 0,
				'clicks_link' => 0,
				'image' => $ban->getImage()->embeddedFile()->link(),
				'block' => app()->adv()->renderBanner($ban, $filters['html_mode'] ? true : false),
				'extension' => $ban->getImage()->val('file_extension'),
				'subgroups' => isset($subgroups[$ban->id()]) ? $subgroups[$ban->id()] : [],
				'catalogs' => isset($catalogs[$ban->id()]) ? $catalogs[$ban->id()] : [],
				'keywords' => $ban->val('keywords_string'),
				'end_date' => DeprecatedDateTime::toTimestamp($ban->val('timestamp_ending')),
				'period' => date('d.m.Y', DeprecatedDateTime::toTimestamp($ban->val('timestamp_beginning'))).' - '.date('d.m.Y', DeprecatedDateTime::toTimestamp($ban->val('timestamp_ending'))),
				'is_active' => DeprecatedDateTime::toTimestamp($ban->val('timestamp_ending')) > DeprecatedDateTime::toTimestamp(DeprecatedDateTime::now()) && (bool)$ban->val('flag_is_active')
			];
		}

		$template = $filters['html_mode'] ? 'presenter_banner_items_pdf' : 'presenter_banner_items';
		$this
				->setItemsTemplateSubdirName('firmmanager')
				->setItemsTemplate($template);

		return $this;
	}

	public function findBannerClicks(Banner $banner, $filters = []) {
/*        $banned_user_ips = [];
		$user_items = app()->db()->query()
				->setText("SELECT * FROM ("
						." SELECT `id`,`ip_addr`, `timestamp_beginning`, SUBSTRING_INDEX(`timestamp_beginning`, :tstmp, 2) as `without_seconds`, COUNT(`id`) as `users_per_minute` "
						." FROM `stat_user` "
						." WHERE `timestamp_beginning` >= :start_timestamp "
						." AND `timestamp_beginning` < :end_timestamp "
						." GROUP BY `ip_addr`, `without_seconds` ORDER BY `users_per_minute` DESC"
						.") s WHERE s.`users_per_minute` > :max_users_per_minute")
				->setParams([':start_timestamp' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
					':end_timestamp' => DeprecatedDateTime::fromTimestamp($filters['t_end']),
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
				->setParams([':start_timestamp' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
					':end_timestamp' => DeprecatedDateTime::fromTimestamp($filters['t_end']),
					':max_requests_per_minute' => 50,
					':tstmp' => ':'])
				->fetch();

		foreach ($request_items as $item) {
			if (!in_array($item['ip_addr'], $banned_user_ips)) {
				$banned_user_ips [] = $item['ip_addr'];
			}
		}
        
        //$uid_conds = Utils::prepareWhereCondsFromArray($banned_user_ips, 'ip_addr', 'NOT IN');
        $_params = [];
		$_params_names = [];
        $j = 0;
		foreach (array_unique($banned_user_ips) as $ip) {
            $j++;
			$_params[':ip_addr_' . $j] = $ip;
			$_params_names[] = ':ip_addr_' . $j;
		}
		$uid_conds = [
			'where' => '`ip_addr` NOT IN (' . implode(',', $_params_names) . ')',
			'params' => $_params
		];*/
        
        $sbc = new StatBannerClick();
		$where = ['AND',
			'id_banner = :id_banner',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
            //$uid_conds['where']
		];
		$params = [
			':id_banner' => $banner->id(),
			':id_firm' => app()->firmUser()->id_firm(),
			':timestamp_start' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
			':timestamp_end' => DeprecatedDateTime::fromTimestamp($filters['t_end'])
		];// + $uid_conds['params'];

		$no_page_filters = $filters;
		unset($no_page_filters['page']);
		$filters['id'] = $banner->id();
		$this->pagination()
				->setTotalRecords($sbc->reader()->setWhere($where, $params)->count())
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmUser::link('/adv/', $filters))
				->setLinkParams($no_page_filters)
				->calculateParams()
				->renderElems();

		$rows = $sbc->query()
				->setFrom($sbc->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($where, $params)
				->setOrderBy('`timestamp_inserting` DESC')
				->select();

		foreach ($rows as $row) {
			$timestamp = DeprecatedDateTime::toTimestamp($row['timestamp_inserting']);
			$this->items[] = [
				'id' => $row['id_banner'],
				'date' => date('d.m.Y', $timestamp),
				'time' => date('H:i:s', $timestamp),
				'ip' => $row['ip_addr'],
				'url' => $row['banner_url'],
				'page_url' => $row['response_url'],
			];
		}

		$template = $filters['html_mode'] ? 'presenter_banner_clicks_items_pdf' : 'presenter_banner_clicks_items';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findBanner727373Clicks(Banner $banner, $filters = []) {
        /*$banned_user727373_ips = [];
        $user_items = app()->db()->query()
                ->setText("SELECT * FROM ("
                        . " SELECT `id`,`ip_addr`, `timestamp_beginning`, SUBSTRING_INDEX(`timestamp_beginning`, :tstmp, 2) as `without_seconds`, COUNT(`id`) as `users_per_minute` "
                        . " FROM `stat_user727373` "
                        . " WHERE `timestamp_beginning` >= :start_timestamp "
                        . " AND `timestamp_beginning` < :end_timestamp "
                        . " GROUP BY `ip_addr`, `without_seconds` ORDER BY `users_per_minute` DESC"
                        . ") s WHERE s.`users_per_minute` > :max_users_per_minute")
                ->setParams([':start_timestamp' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
                    ':end_timestamp' => DeprecatedDateTime::fromTimestamp($filters['t_end']),
                    ':max_users_per_minute' => 20,
                    ':tstmp' => ':'])
                ->fetch();

        foreach ($user_items as $item) {
            if (!in_array($item['ip_addr'], $banned_user727373_ips)) {
                $banned_user727373_ips [] = $item['ip_addr'];
            }
        }
        // ips from stat_requests
        $request_items = app()->db()->query()
                ->setText("SELECT * FROM ("
                        . " SELECT sr.`id`, SUBSTRING_INDEX(`timestamp_inserting`, :tstmp, 2) as `without_seconds`, COUNT(sr.`id`) as `requests_per_minute`, su.`ip_addr` "
                        . " FROM `stat_request727373` sr "
                        . " LEFT JOIN `stat_user727373` su ON su.`id` = sr.`id_stat_user`"
                        . " WHERE sr.`timestamp_inserting` >= :start_timestamp AND sr.`timestamp_inserting` < :end_timestamp"
                        . " GROUP BY su.`ip_addr`, `without_seconds` ORDER BY `requests_per_minute` DESC) s WHERE s.`requests_per_minute` > :max_requests_per_minute")
                ->setParams([':start_timestamp' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
                    ':end_timestamp' => DeprecatedDateTime::fromTimestamp($filters['t_end']),
                    ':max_requests_per_minute' => 50,
                    ':tstmp' => ':'])
                ->fetch();

        foreach ($request_items as $item) {
            if (!in_array($item['ip_addr'], $banned_user727373_ips)) {
                $banned_user727373_ips [] = $item['ip_addr'];
            }
        }

        //$uid_conds = Utils::prepareWhereCondsFromArray($banned_user_ips, 'ip_addr', 'NOT IN');
        $_params = [];
		$_params_names = [];
        $j = 0;
		foreach (array_unique($banned_user727373_ips) as $ip) {
            $j++;
			$_params[':ip_addr_' . $j] = $ip;
			$_params_names[] = ':ip_addr_' . $j;
		}
		$uid_conds = [
			'where' => '`ip_addr` NOT IN (' . implode(',', $_params_names) . ')',
			'params' => $_params
		];*/
        
		$sbc = new StatBanner727373Click();
		$where = ['AND',
			'id_banner = :id_banner',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
            //$uid_conds['where']
		];
		$params = [
			':id_banner' => $banner->id(),
			':id_firm' => app()->firmUser()->id_firm(),
			':timestamp_start' => DeprecatedDateTime::fromTimestamp($filters['t_start']),
			':timestamp_end' => DeprecatedDateTime::fromTimestamp($filters['t_end'])
		];// + $uid_conds['params'];

		$no_page_filters = $filters;
		unset($no_page_filters['page']);
		$filters['id'] = $banner->id();
		$this->pagination()
				->setTotalRecords($sbc->reader()->setWhere($where, $params)->count())
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmUser::link('/adv-online/', $filters))
				->setLinkParams($no_page_filters)
				->calculateParams()
				->renderElems();

		$rows = $sbc->query()
				->setFrom($sbc->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($where, $params)
				->setOrderBy('`timestamp_inserting` DESC')
				->select();

		foreach ($rows as $row) {
			$timestamp = DeprecatedDateTime::toTimestamp($row['timestamp_inserting']);
			$this->items[] = [
				'id' => $row['cml_banner_id'],
				'date' => date('d.m.Y', $timestamp),
				'time' => date('H:i:s', $timestamp),
				'ip' => $row['ip_addr'],
				'url' => $row['banner_url'],
				'page_url' => $row['response_url'],
			];
		}

		$template = $filters['html_mode'] ? 'presenter_banner_727373_clicks_items_pdf' : 'presenter_banner_727373_clicks_items';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findAdvertModulesByService($filters = []) {
		$advert_module = new AdvertModule();

		$amg = new AdvertModuleGroup();
		$advert_module_ids = [];
		if ($filters['id_subgroup'] !== 0) {
			$advert_module_ids = array_keys($amg->reader()->setWhere(['AND', 'id_subgroup = :id_subgroup'], [':id_subgroup' => $filters['id_subgroup']])
							->rowsWithKey('id_advert_module'));
		} elseif ($filters['id_group'] !== 0) {
			$advert_module_ids = array_keys($amg->reader()->setWhere(['AND', 'id_group = :id_group'], [':id_group' => $filters['id_group']])
							->rowsWithKey('id_advert_module'));
		}

		$sts_service = new \App\Model\StsService();
		$id_city = $sts_service->reader()
						->setSelect('id_city')
						->setWhere(['AND', 'id_service = :id_service', 'exist = :exist'], [':id_service' => app()->firmManager()->id_service(), ':exist' => 1])
						->setLimit(1)
						->rows()[0]['id_city'];

		$firm_location_conds = Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds($id_city), 'id_firm');
		$where = ['AND', $firm_location_conds['where']];
		$params = $firm_location_conds['params'];

		$firm_ids = [];

		if ($advert_module_ids) {
			$conds = Utils::prepareWhereCondsFromArray($advert_module_ids);
			$where[] = $conds['where'];
			$params += $conds['params'];
		}

		if ($filters['id_manager'] !== 0) {
			$firm_manager = new FirmManager2($filters['id_manager']);
			if ($firm_manager->exists()) {
				$user_ids = $firm_manager->getManagerUserIds();
				$firm_conds = Utils::prepareWhereCondsFromArray($user_ids, 'id_manager');
				$firm = new Firm();
				$firm_ids = $firm->reader()->setWhere(['AND', $firm_conds['where']], $firm_conds['params'])
						->rowsWithKey('id_firm');
				$firm_advert_module_conds = Utils::prepareWhereCondsFromArray(array_keys($firm_ids), 'id_firm');
				$where[] = $firm_advert_module_conds['where'];
				$params += $firm_advert_module_conds['params'];
			}
		}
		if ($filters['id_firm'] !== 0) {
			$where[] = 'id_firm = :id_firm';
			$params += [':id_firm' => $filters['id_firm']];
		}

		if ($filters['type'] !== 'all') {
			$where[] = 'type = :type';
			$params += [':type' => $filters['type'] == 'context' ? '' : $filters['type']];
		}

		if ($filters['active'] === 1) {
			$where[] = 'timestamp_ending > :now';
			$where[] = 'flag_is_active = :flag_is_active';
			$params += [':now' => DeprecatedDateTime::now(), ':flag_is_active' => 1];
		} elseif ($filters['active'] === 2) {
			$where[] = ['OR', 'timestamp_ending <= :now', 'flag_is_active = :flag_is_active'];
			$params += [':now' => DeprecatedDateTime::now(), ':flag_is_active' => 0];
		}

		$this->pagination()
				->setTotalRecords($advert_module->reader()->setWhere($where, $params)->count())
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmManager::link('/advert-modules/'))
				->setLinkParams($filters)
				->calculateParams()
				->renderElems();

		$advert_modules = $advert_module->reader()
				->setWhere($where, $params)
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('flag_is_active DESC, timestamp_ending '.(($filters['active'] === 2) ? 'DESC' : 'ASC'))
				->objects();

		$subgroups = [];
		$firmtypes = [];
		foreach ($advert_modules as $advm) {
			$amg = new AdvertModuleGroup();
			$id_subgroups = $amg->getSubgroupIdsByAdvertModuleId($advm->id());
			if ($id_subgroups) {
				$cat = new PriceCatalog();
				$cat_conds = Utils::prepareWhereCondsFromArray($id_subgroups, 'id_subgroup');
				$cat_where = ['AND', $cat_conds['where'], 'node_level = :node_level'];
				$cat_params = [':node_level' => 2] + $cat_conds['params'];
				$subgroups[$advm->id()] = $cat->reader()
						->setWhere($cat_where, $cat_params)
						->objects();
			}

			$amft = new AdvertModuleFirmType();
			$id_firm_types = $amft->getFirmTypeIdsByAdvertModuleId($advm->id());
			if ($id_firm_types) {
				$ft = new FirmType();
				$ft_conds = Utils::prepareWhereCondsFromArray($id_firm_types, 'id');
				$ft_where = ['AND', $ft_conds['where']];
				$ft_params = $ft_conds['params'];
				$firmtypes[$advm->id()] = $ft->reader()
						->setWhere($ft_where, $ft_params)
						->objects();
			}
		}

		foreach ($advert_modules as $advm) {
			$firm = new Firm();
			$firm->getByIdFirm($advm->id_firm());
			$this->items[] = [
				'id' => $advm->id(),
				'name' => $advm->name(),
				'header' => $advm->header(),
				'firm' => $firm,
				'url' => $advm->link(),
				'advert_module_url' => $advm->val('url'),
				'advert_module_more_url' => $advm->val('more_url'),
				'full_image' => $advm->getFullImage()->link(),
				'adv_text' => $advm->val('adv_text'),
				'about_string' => $advm->val('about_string'),
				'email' => $advm->val('email'),
				'phone' => $advm->val('phone'),
				'firmtypes' => isset($firmtypes[$advm->id()]) ? $firmtypes[$advm->id()] : [],
				'subgroups' => isset($subgroups[$advm->id()]) ? $subgroups[$advm->id()] : [],
				'end_date' => DeprecatedDateTime::toTimestamp($advm->val('timestamp_ending')),
				'period' => date('d.m.Y', DeprecatedDateTime::toTimestamp($advm->val('timestamp_beginning'))).' - '.date('d.m.Y', DeprecatedDateTime::toTimestamp($advm->val('timestamp_ending'))),
				'is_active' => DeprecatedDateTime::toTimestamp($advm->val('timestamp_ending')) > DeprecatedDateTime::toTimestamp(DeprecatedDateTime::now()) && (bool)$advm->val('flag_is_active'),
				'total_views' => $advm->val('total_views'),
				'total_clicks' => $advm->val('total_clicks')
			];
		}

		$template = $filters['html_mode'] ? 'presenter_advert_module_items_pdf' : 'presenter_advert_module_items';
		$this
				->setItemsTemplateSubdirName('firmmanager')
				->setItemsTemplate($template);

		return $this;
	}
    
    public function findStatistics($filters = []) {
        $so = new StatObject();

        if ($filters['id_firm']) {
            $where = ['AND',
                'id_firm = :id_firm',
                'timestamp_inserting >= :timestamp_start',
                'timestamp_inserting < :timestamp_end'
            ];
            $params = [
                ':id_firm' => $filters['id_firm'],
                ':timestamp_start' => $filters['t_start'] . ' 00:00:00',
                ':timestamp_end' => $filters['t_end'] . ' 23:59:59'
            ];
        }
        
        $this->pagination()
				->setTotalRecords(count($so->reader()
                        ->setWhere($where, $params)
                        ->setGroupBy('`name`,`model_id`,`type`')
                        ->rows()))
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmManager::link('/statistics/'))
				->setLinkParams($filters)
				->calculateParams()
				->renderElems();

		$stat_objects = $so->reader()
                ->setSelect('*, COUNT(`id`) AS `count`')
				->setWhere($where, $params)
                ->setGroupBy('`name`, `type`')
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('`name` ASC')
				->rows();
        
        $i = 0;
        foreach ($stat_objects as $stat_object) {
            $i++;
			$firm = new Firm();
			$firm->getByIdFirm($stat_object['id_firm']);
			$this->items[] = [
				'num' => $i + (20 * ($this->getPage() - 1)),
				'name' => $stat_object['name'] ? $stat_object['name'] : $firm->name(),
				'firm' => $firm,
                'type' => StatObject::getTypeNames()[$stat_object['type']],
                'site' => 'tovaryplus.ru',
                'count' => $stat_object['count'],
			];
		}
        
		$template = $filters['html_mode'] ? 'presenter_statistic_items_pdf' : 'presenter_statistic_items';
		$this
				->setItemsTemplateSubdirName('firmmanager')
				->setItemsTemplate($template);

		return $this;
    }
    
    public function findStatistics727373($filters = []) {
        $so727373 = new StatObject727373();

        if ($filters['id_firm']) {
            $where = ['AND',
                'id_firm = :id_firm',
                'timestamp_inserting >= :timestamp_start',
                'timestamp_inserting < :timestamp_end'
            ];
            $params = [
                ':id_firm' => $filters['id_firm'],
                ':timestamp_start' => $filters['t_start'] . ' 00:00:00',
                ':timestamp_end' => $filters['t_end'] . ' 23:59:59'
            ];
        }
        
        $this->pagination()
				->setTotalRecords(count($so727373->reader()
                        ->setWhere($where, $params)
                        ->setGroupBy('`name`,`model_id`,`type`')
                        ->rows()))
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(FirmManager::link('/statistics727373/'))
				->setLinkParams($filters)
				->calculateParams()
				->renderElems();

		$stat_objects = $so727373->reader()
                ->setSelect('*, COUNT(`id`) AS `count`')
				->setWhere($where, $params)
                ->setGroupBy('`name`, `type`')
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setOrderBy('`name` ASC')
				->rows();
        
        $i = 0;
        foreach ($stat_objects as $stat_object) {
            $i++;
			$firm = new Firm();
			$firm->getByIdFirm($stat_object['id_firm']);
			$this->items[] = [
				'num' => $i + (20 * ($this->getPage() - 1)),
				'name' => $stat_object['name'] ? $stat_object['name'] : $firm->name(),
				'firm' => $firm,
                'type' => StatObject727373::getTypeNames()[$stat_object['type']],
                'site' => '727373.ru',
                'count' => $stat_object['count'],
			];
		}
        
		$template = $filters['html_mode'] ? 'presenter_statistic727373_items_pdf' : 'presenter_statistic727373_items';
		$this
				->setItemsTemplateSubdirName('firmmanager')
				->setItemsTemplate($template);

		return $this;
    }

	// -------------------------------------------------------------------------

	public function __construct() {
		parent::__construct();
		$this->setItemsTemplateSubdirName('firmuser')
				->setLimit(10)
				->setModel(new \App\Model\FirmUser());

		return true;
	}

	/**
	 * @return Pagination
	 */
	public function pagination() {
		if ($this->pagination === null) {
			$this->pagination = new Pagination();
		}
		return $this->pagination;
	}

	public function getPage() {
		$params = app()->request()->processGetParams(['page' => 'int']);
		if ($params['page']) return $params['page'];
		return 1;
	}

	private function renderLink($url, $id_city) {
		$result = $url;
		if (str()->pos($url, '/price/show/') === false && str()->pos($url, '/firm/show/') === false) {
			$result = '/'.$id_city.$url;
		}

		return $result;
	}

}
