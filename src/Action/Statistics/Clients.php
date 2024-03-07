<?php

namespace App\Action\Statistics;

use App\Action\Statistics;
use App\Model\Firm;
use Sky4\Helper\DeprecatedDateTime;
use function app;

class Clients extends Statistics {

	public function execute() {
		app()->breadCrumbs()
				->setElem('Статистика', '/statistics/')
				->setElem('Наши постоянные клиенты', '/statistics/clients/');

		$text = $this->text()->getByLink('statistics/clients');
		$text->setVal('text', self::replaceStatText($text->val('text')));
		if ($text->exists()) {
			app()->metadata()->setFromModel($text);
		} else {
			app()->metadata()->setTitle('Наши постоянные клиенты');
		}

		$firm = new Firm();
		$firms = $firm->reader()
				->setSelect(['YEAR(timestamp_inserting) AS `year_input`', 'company_name', 'company_activity', 'id', 'id_firm', 'id_service', 'flag_is_active'])
				->setWhere(['AND', 'priority > :priority', 'flag_is_active = :flag_is_active', 'timestamp_inserting < :timestamp', 'id_service = :id_service'], [':priority' => 0 ,':flag_is_active' => 1, 'timestamp' => DeprecatedDateTime::shiftYears(-3), ':id_service' => 10])
				->setOrderBy('`year_input`, `company_name`')
				->rows();

		$items = [];
		foreach ($firms as $row) {
			if ((int) $row['year_input'] === 0) {
				continue;
			}
			if (!isset($items[$row['year_input']])) {
				$items[$row['year_input']] = [];
			}
			$firm = new Firm();
			$firm->setVals($row);
			$items[$row['year_input']][] = $firm;
		}

		$this->view()
				->setTemplate('clients')
				->set('breadcrumbs', app()->breadCrumbs()->render())
				->set('text', $text)
				//
				->set('items', $items)
				->save();
	}

}
