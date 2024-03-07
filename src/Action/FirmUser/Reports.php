<?php

namespace App\Action\FirmUser;

use App\Action\FirmUser\Adv;
use App\Action\FirmUser\AdvOnline;
use App\Action\FirmUser\Calls;
use App\Action\FirmUser\Export;
use App\Action\FirmUser\InfoPdf;
use App\Action\FirmUser\OnlineStatistics;
use CDateTime;
use CString;
use const APP_DIR_PATH;
use function app;

require_once APP_DIR_PATH . '/protected/mpdf/src/functions.php';

class Reports extends \App\Action\FirmUser {

	public function execute() {
		$this->params = app()->request()->processPostParams([
			'date' => ['type' => 'int']
		]);

		if ($this->params['date'] === null) {
			app()->metadata()->setTitle('Личный кабинет - отчеты');
			app()->breadCrumbs()
					->setElem($this->firm()->name(), $this->firm()->link(), ['style' => 'color: red', 'target' => '_blank'])
					->setElem('Отчеты', '/firm-user/reports/');

			$dates = [];
			for ($i = 0; $i < 6; $i++) {
				$timestamp = mktime(0, 0, 0, date('m') - $i, 1, date('Y'));
				$dates[$timestamp] = CString::firstCharToUpper(CDateTime::monthName(CDateTime::fromTimestamp($timestamp)) . ' ' . date('Y', $timestamp));
			}

			$this->view()
					->set('bread_crumbs', app()->breadCrumbs()->render(true))
					->set('dates', $dates)
					->setTemplate('reports_index')
					->save();
		} else {
			$types = app()->request()->processPostParams(['type' => ['type' => 'array']])['type'];
			if (!$types) {
				app()->response()->redirect(self::link('/reports/?empty'));
			}

			$date = $this->params['date'];
			$result_html = app()->chunk()->set('firm', $this->firm())->set('period', CString::firstCharToUpper(CDateTime::monthName(CDateTime::fromTimestamp($date))) . ' ' . date('Y', $date))->render('firmuser.report_header_pdf');
			foreach ($types as $type) {
				$result_html .= $this->getReportByType($type, $date);
			}
			$result_html .= app()->chunk()->render('firmuser.report_footer_pdf');
			$mpdf = new \Mpdf\Mpdf(['utf-8', 'A4', '10', 'Arial', 10, 10, 7, 7, 10, 10]);
			$stylesheet = file_get_contents(APP_DIR_PATH . '/public/css/pdf.css');
			$mpdf->WriteHTML($stylesheet, 1);

			$mpdf->list_indent_first_level = 0;
			$mpdf->WriteHTML($result_html, 2); /* формируем pdf */
			$mpdf->Output('отчет.pdf', 'I');
			exit();
		}
	}

	protected function getReportByType($type, $date) {
		$result = '';
		$filters = self::initFilters(['mode' => '', 't_start' => $date, 't_end' => mktime(0, 0, 0, date('m', $date) + 1, 1, date('Y', $date)), 'group' => 'months', 'html_mode' => true]);

		switch ((int) $type) {
			case 100 :
				$action = new InfoPdf();
				$result = $action->setHtmlMode(true)
						->execute();
				break;
			case 210 : $action = new Statistics();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			//case 220 : $filters['mode'] = 'pages';
			//	$result = $this->actionStatistics(true, $filters);
			//	break;
			case 222 : $filters['mode'] = 'pages';
				$filters['stat_group'] = 'main_stat';
				$action = new Statistics();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 224 : $filters['mode'] = 'pages';
				$filters['stat_group'] = 'additional_stat';
				$action = new Statistics();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 230 : $filters['mode'] = 'dynamic';
				$action = new Statistics();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 240 : $filters['mode'] = 'cities';
				$action = new Statistics();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
            case 610 : $action = new OnlineStatistics();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 620 : $filters['mode'] = 'pages';
				$action = new OnlineStatistics();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 630 : $filters['mode'] = 'dynamic';
				$action = new OnlineStatistics();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 640 : $filters['mode'] = 'cities';
				$action = new OnlineStatistics();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 300 :
				$action = new Adv();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 310 : $filters['id'] = 0;
				$action = new Adv();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 400 : $filters['id'] = 0;
				$action = new Calls();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 500 : $filters['id'] = 0;
				$action = new Export();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
            case 700 :
				$action = new AdvOnline();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
			case 710 : $filters['id'] = 0;
				$action = new AdvOnline();
				$result = $action->setHtmlMode(true)
						->setFilters($filters)
						->execute();
				break;
		}

		return $result;
	}

}
