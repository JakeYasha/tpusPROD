<?php

namespace App\Action\Utils;

use App\Model\PriceCatalog;
use App\Model\YmlCategory;
use Foolz\SphinxQL\SphinxQL;
use function app;

class YmlShortStat extends \App\Action\Utils {
    public function __construct() {
        parent::__construct();
        if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
            exit();
        }
    }

	public function execute() {
        $yml_bad = app()->db()->query()
				->setText("SELECT y.`id`,f.`company_name`,y.`id_firm`,y.`offers_count`,y.`offers_count_loaded`,y.`status`,y.`timestamp_inserting`,y.`timestamp_last_updating`,y.`timestamp_yml` FROM `yml` y LEFT JOIN `firm` f ON f.`id` = y.`id_firm` WHERE y.`offers_count_loaded` = 0 ORDER BY y.`timestamp_last_updating` DESC")
                ->fetch();
        $yml_good = app()->db()->query()
				->setText("SELECT y.`id`,f.`company_name`,y.`id_firm`,y.`offers_count`,y.`offers_count_loaded`,y.`status`,y.`timestamp_inserting`,y.`timestamp_last_updating`,y.`timestamp_yml` FROM `yml` y LEFT JOIN `firm` f ON f.`id` = y.`id_firm` WHERE y.`offers_count_loaded` != 0 ORDER BY y.`timestamp_last_updating` DESC")
                ->fetch();
        
		echo '<h2>Загружены с ошибкой</h2>';
		echo '<table border="1" cellpadding="5">';
        echo '<tr>'
        . '<th>ID yml</th>'
        . '<th>Наименование фирмы</th>'
        . '<th>ID firm</th>'
        . '<th>ЗАЯВЛЕНО</th>'
        . '<th>ОБРАБОТАНО</th>'
        . '<th>ЗАГРУЖЕНО</th>'
        . '<th>Статус</th>'
        . '<th>Дата создания</th>'
        . '<th>Дата загрузки</th>'
        . '</tr>';
		foreach ($yml_bad as $_yml) {
            $count = app()->db()->query()
				->setText("SELECT COUNT(*) as `count` FROM `yml_offer` WHERE id_firm = {$_yml['id_firm']}")
                ->fetch();
                
			echo '<tr>'
            . '<td>' . $_yml['id'] . '</td>'
            . '<td>' . $_yml['company_name'] . '</td>'
            . '<td>' . $_yml['id_firm'] . '</td>'
            . '<td>' . $_yml['offers_count'] . '</td>'
            . '<td>' . $_yml['offers_count_loaded'] . '</td>'
            . '<td>' . $count[0]['count'] . '</td>'
            . '<td>' . $_yml['status'] . '</td>'
            . '<td>' . $_yml['timestamp_inserting'] . '</td>'
            . '<td>' . $_yml['timestamp_last_updating'] . '</td>'
            //. '<td>' . $_yml['timestamp_yml'] . '</td>'
            . '</tr>';
		}
		echo '</table>';
		echo '<h2>Загружены без ошибок</h2>';
		echo '<table border="1" cellpadding="5">';
        echo '<tr>'
        . '<th>ID yml</th>'
        . '<th>Наименование фирмы</th>'
        . '<th>ID firm</th>'
        . '<th>ЗАЯВЛЕНО</th>'
        . '<th>ОБРАБОТАНО</th>'
        . '<th>ЗАГРУЖЕНО</th>'
        . '<th>Статус</th>'
        . '<th>Дата создания</th>'
        . '<th>Дата загрузки</th>'
        . '</tr>';
		foreach ($yml_good as $_yml) {
            $count = app()->db()->query()
				->setText("SELECT COUNT(*) as `count` FROM `yml_offer` WHERE id_firm = {$_yml['id_firm']}")
                ->fetch();
			echo '<tr>'
            . '<td>' . $_yml['id'] . '</td>'
            . '<td>' . $_yml['company_name'] . '</td>'
            . '<td>' . $_yml['id_firm'] . '</td>'
            . '<td>' . $_yml['offers_count'] . '</td>'
            . '<td>' . $_yml['offers_count_loaded'] . '</td>'
            . '<td>' . $count[0]['count'] . '</td>'
            . '<td>' . $_yml['status'] . '</td>'
            . '<td>' . $_yml['timestamp_inserting'] . '</td>'
            . '<td>' . $_yml['timestamp_last_updating'] . '</td>'
            //. '<td>' . $_yml['timestamp_yml'] . '</td>'
            . '</tr>';
		}
		echo '</table>';
		exit();
	}

}
