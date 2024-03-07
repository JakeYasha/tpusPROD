<?php

namespace App\Action;

class Statistics extends \App\Classes\Action {

	public function execute() {
		app()->breadCrumbs()
				->setElem('Статистика', '/statistics/');

		$text = $this->text()->getByLink('/statistics/');
		app()->metadata()->setFromModel($text);
		$text->setVal('text', self::replaceStatText(preg_replace('~>\s+<~', '><', $text->val('text'))));

		$this->view()->setTemplate('index')
				->set('breadcrumbs', app()->breadCrumbs()->render())
				->set('text', $text)
				->save();
	}

	protected static function replaceStatText($text) {
		$ss = new \App\Model\StatSite();
		$ss->reader()
				->setOrderBy('timestamp_last_updating DESC')
				->objectByConds();

		$crc = new \App\Model\CurrentRegionCity();
		$crc->reader()
				->setWhere(['AND', 'id_city = :id_city'], [':id_city' => 76004])
				->objectByConds();

		$update_date = $ss->val('timestamp_last_updating');

		$search_array = [
			'%cur_day',
			'%cur_month_name',
			'%cur_year',
			'%count_towns',
			'%count_firms',
			'%count_prices',
			'%yar_count_firms',
			'%yar_count_prices',
			'%stat_date_start'
		];

		$replace_array = [
			\Sky4\Helper\DeprecatedDateTime::day($update_date),
			\Sky4\Helper\DeprecatedDateTime::monthName($update_date, 1),
			\Sky4\Helper\DeprecatedDateTime::year($update_date),
			'<strong>' . str()->addSpaces($ss->val('total_towns')) . '</strong> ' . \CWord::ending($ss->val('total_towns'), ['город', 'города', 'городов']),
			'<strong>' . str()->addSpaces($ss->val('total_firms')) . '</strong> ' . \CWord::ending($ss->val('total_firms'), ['фирма', 'фирмы', 'фирм']),
			'<strong>' . str()->addSpaces($ss->val('total_price')) . '</strong> ' . \CWord::ending($ss->val('total_prices'), ['товар и услуга', 'товара и услуги', 'товаров и услуг']),
			'<strong>' . str()->addSpaces($crc->val('count_firms')) . '</strong> ' . \CWord::ending($ss->val('count_firms'), ['фирма', 'фирмы', 'фирм']),
			'<strong>' . str()->addSpaces($crc->val('count_goods')) . '</strong> ' . \CWord::ending($ss->val('count_goods'), ['товар и услуга', 'товара и услуги', 'товаров и услуг']),
			date('d.m.Y', mktime(0, 0, 0, date('m') - 1, date('d')))
		];

		return str()->replace($text, $search_array, $replace_array);
	}

}
