<?php

namespace App\Action\Statistics;

use App\Classes\YandexMetrika;
use Sky4\Helper\DeprecatedDateTime;
use function app;

class Live extends \App\Action\Statistics {

	public function execute() {
		app()->breadCrumbs()
				->setElem('Статистика', '/statistics/')
				->setElem('Статистика посещаемости сайта', '/statistics/live/');

		$text = $this->text()->getByLink('statistics/live');
		$text->setVal('text', self::replaceStatText($text->val('text')));
		if ($text->exists()) {
			app()->metadata()->setFromModel($text);
		} else {
			app()->metadata()->setTitle('Статистика посещаемости сайта');
		}

		$ym = new YandexMetrika();
		$days = $ym->setDateBeginning(DeprecatedDateTime::shiftMonths(-1))
				->setDateEnding(DeprecatedDateTime::now())
                ->getSummaryByDaysWithOAuth();

		$months = $ym->setDateBeginning(DeprecatedDateTime::shiftYears(-2))
				->setDateEnding(DeprecatedDateTime::now())
                ->getSummaryByMonthsWithOAuth();

		$days_data = json_decode($days);
		$days_data_result = [];
		foreach ($days_data->data as $mdata)
		//$days_data_result[$key] =  "{ date: new Date(".date('Y', strtotime($mdata->date)).", ".(date('n', strtotime($mdata->date))-1).", ".date('j', strtotime($mdata->date))."), visits: ".$mdata->visits.", visitors: ".$mdata->visitors.", pageviews: ".$mdata->page_views.", visittime: ".$mdata->visit_time."}";
			$days_data_result[$mdata->dimensions[0]->name] = "{date: '" . date('d.m', strtotime($mdata->dimensions[0]->name)) . "', visits: " . $mdata->metrics[0] . ", visitors: " . $mdata->metrics[2] . ", pageviews: " . $mdata->metrics[1] . ", visittime: " . ((int) $mdata->metrics[3]) . "}";

		$day_data = join(',', $days_data_result);

		/* for ($i = 0; $i < count($days_data_result); $i++) {
		  $day_data .= ($i == 0) ? $days_data_result[$i] : ',' . $days_data_result[$i];
		  } */

		$month_data = json_decode($months);
		$month_data_result = [];
		foreach ($month_data->data as $mdata)
		//$month_data_result[$key] = "{date: '" . date('d.m', strtotime($mdata->date)) . "', visits: " . $mdata->visits . ", visitors: " . $mdata->visitors . ", pageviews: " . $mdata->page_views . ", visittime: " . $mdata->visit_time . "}";
			$month_data_result[explode(' - ', $mdata->dimensions[0]->name)[0]] = "{date: '" . date('d.m', strtotime(explode(' - ', $mdata->dimensions[0]->name)[0])) . "', visits: " . $mdata->metrics[0] . ", visitors: " . $mdata->metrics[2] . ", pageviews: " . $mdata->metrics[1] . ", visittime: " . ((int) $mdata->metrics[3]) . "}";

		$month_data = join(',', $month_data_result);

		/* for ($i = 0; $i < count($month_data_result); $i++) {
		  $month_data .= ($i == 0) ? $month_data_result[$i] : ',' . $month_data_result[$i];
		  } */

		$this->view()
				->setTemplate('live')
				->set('breadcrumbs', app()->breadCrumbs()->render())
				->set('text', $text)
				//
				->set('days', $day_data)
				->set('months', $month_data)
				->save();
	}

}
