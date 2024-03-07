<?php

namespace App\Presenter;

use App\Classes\Pagination;
use App\Model\AvgStatFirm727373PopularPages;
use App\Model\AvgStatGeo727373;
use App\Model\AvgStatObject727373;
use App\Model\AvgStatUser727373;
use App\Model\StatObject727373;
use Sky4\Helper\DeprecatedDateTime;
use function app;
use function str;


class FirmUser727373Statistics extends Presenter {
    
    public function findSummary($filters = []) {
		$aso = new AvgStatObject727373();
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

		$types = StatObject727373::getTypeNames();
		$select_conds = [];
		foreach ($types as $k => $v) {
			$select_conds[] = 'SUM(t'.$k.') as t'.$k;
			$types['t'.$k] = ['name' => $v, 'stat_group' => StatObject727373::getStatGroupNameByType($k)];
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

		$template = $filters['html_mode'] ? 'presenter_stat_summary_items727373_pdf' : 'presenter_stat_summary_items727373';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findGeo($filters = []) {
		$asg = new AvgStatGeo727373();

		$where = ['AND',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
			'month = :month',
			'city_name != :city_name'
		];
		$params = [
			':id_firm' => app()->firmUser()->id_firm(),
			':timestamp_start' => (new \Sky4\Helper\DateTime())->fromTimestamp($filters['t_start'])->format(),
			':timestamp_end' => (new \Sky4\Helper\DateTime())->fromTimestamp($filters['t_end'])->format(),
			':month' => $filters['group'] === 'months' ? 1 : 0,
			':city_name' => ''
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

		$template = $filters['html_mode'] ? 'presenter_stat_geo_items727373_pdf' : 'presenter_stat_geo_items727373';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findDynamic($filters = []) {
		$aso = new AvgStatObject727373();
		$asu = new AvgStatUser727373();

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

		$types = StatObject727373::getTypeNames();
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

		$template = $filters['html_mode'] ? 'presenter_stat_dynamic_items727373_pdf' : 'presenter_stat_dynamic_items727373';
		$this->setItemsTemplate($template);

		return $this;
	}

	public function findPages($filters = []) {
		$asfpp = new AvgStatFirm727373PopularPages();
		$where = ['AND',
			'id_firm = :id_firm',
			'timestamp_inserting >= :timestamp_start',
			'timestamp_inserting < :timestamp_end',
			'month = :month'
		];

		$params = [
			':id_firm' => app()->firmUser()->id_firm(),
			':timestamp_start' => (new \Sky4\Helper\DateTime())->fromTimestamp($filters['t_start'])->format(),
			':timestamp_end' => (new \Sky4\Helper\DateTime())->fromTimestamp($filters['t_end'])->format(),
			':month' => $filters['group'] === 'months' ? 1 : 0
		];

		$no_page_filters = $filters;
		unset($no_page_filters['page']);
		$this->setLimit($filters['html_mode'] ? 99999 : 15);
		$this->pagination()
				->setTotalRecords($asfpp->reader()->setWhere($where, $params)->count())
				->setLimit($this->getLimit())
				->setPage($this->getPage())
				->setLink(\App\Controller\FirmUser::link('/online-statistics/'))
				->setLinkParams($no_page_filters)
				->calculateParams()
				->renderElems();


		$rows = $asfpp->query()
				->setSelect(['*', 'SUM(count) as `count`'])
				->setFrom($asfpp->table())
				->setLimit($this->pagination()->getLimit(), $this->pagination()->getOffset())
				->setWhere($where, $params)
				->setGroupBy(['url'])
				->setOrderBy("`count` DESC, REPLACE(REPLACE(`url`, '/question/', ''), '/', '')*1 DESC")
				->select();

		foreach ($rows as $row) {
			if ((int)$row['count'] > 0) {
				$this->items[] = [
					'name' => $row['title'],
					'url' => $row['url'],
					'count' => (int)$row['count']
				];
				$counts[] = (int)$row['count'];
			}
		}

		if ($this->items) {
			array_multisort($counts, SORT_DESC, $this->items);
		}

		$template = $filters['html_mode'] ? 'presenter_stat_pages_items727373_pdf' : 'presenter_stat_pages_items727373';
		$this->setItemsTemplate($template);

		return $this;
	}

	// -------------------------------------------------------------------------

	public function __construct() {
		parent::__construct();
		$this->setLimit(10);
		$this->setModel(new AvgStatObject727373());
		$this
				->setItemsTemplateSubdirName('firmuser');

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

}
