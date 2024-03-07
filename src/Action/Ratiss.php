<?php

namespace App\Action;

class Ratiss extends \App\Classes\Action {

	public function execute() {
		$this->text()->getByLink('/ratiss/');
		app()->metadata()->setFromModel($this->text());
		app()->breadCrumbs()->setElem($this->text()->name(), '');

		$services = app()->db()->query()
				->setText("SELECT s.id_city AS idcity, c.`name` AS scity, s.`name` AS sname, s.`web` AS swebsites, s.`id_service` AS idservice, COUNT(f.`id_firm`) AS scount_firms FROM `sts_service` s
                                    LEFT JOIN `firm` f ON f.id_service = s.id_service AND f.flag_is_active = 1
                                    LEFT JOIN `sts_city` c ON s.id_city = c.id_city
                                    WHERE s.`exist` = 1 
                                    GROUP BY 1,2,3
                                    ORDER BY c.`name`")
				->fetch();

		$count = 0;
		$rows = [];
		foreach ($services as $service) {
			$sites = array();
			foreach (preg_split('~[;,]~', $service['swebsites']) as $site) {
				if ($site === 'tovaryplus.ru') {
					$sites [] = trim($site);
				} else {
					$sites [] = '<a href="' . app()->away(trim($site)) . '"  rel="nofollow" target="_blank">' . str()->toLower($site) . '</a>';
				}
			}
			$rows[] = [
				$service['scount_firms'] > 0 && $service['idcity'] != '76004' ? '<a href="/' . $service['idcity'] . '" title="TovaryPlus.ru ' . str()->firstCharToUpper(str()->toLower($service['scity'])) . '">' . str()->firstCharToUpper(str()->toLower($service['scity'])) . '</a>' : str()->firstCharToUpper(str()->toLower($service['scity'])),
				$service['sname'],
				str()->toLower($service['swebsites']) === 'tovaryplus.ru' ? str()->toLower($service['swebsites']) : ($service['scount_firms'] > 0 ? join(', ', $sites) : str()->toLower($service['swebsites'])),
				$service['scount_firms'] !== NULL ? $service['scount_firms'] : 0
			];
			$count++;
		}
		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render())
				->set('cities', $rows)
				->set('item', $this->text())
				->set('count_cities', $count)
				->setTemplate('index')
				->save();
	}

}
