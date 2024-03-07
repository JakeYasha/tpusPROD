<?php

namespace App\Action\Utils;

class EasyTests extends \App\Action\Utils {

	public function __construct() {
		parent::__construct();
		if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
			exit();
		}
	}

	public function execute() {
        $params = app()->request()->processGetParams([
            'firm_id' => ['type' => 'int']
        ]);

        $firm_id = (int) $params['firm_id'];

        if (!$firm_id) {
            echo 'Укажите firm_id' . '</br>';
            exit();
        }

        $firm = new \App\Model\Firm($firm_id);
        $image = new \App\Model\Image();
        $image = $image->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_price= :nil'], [':id_firm' => $firm_id, ':nil' => 0])
                ->setOrderBy('CAST(`source` AS CHAR) ASC, `timestamp_inserting` DESC')
                ->objectByConds();

        echo '<style>';
        echo 'table { border: none; min-width: 50%; }';
        echo 'tr { border: none; }';
        echo 'td, th { padding: 5px 10px; border: 1px solid #ccc; text-align: center; }';
        echo '</style>';
        echo '<h1>' . $firm->val('company_name') . '</h1>';
        echo '<h2>Логотип</h2>';
        echo '<table><tr><th>';
        echo 'Логотип текущий';
        echo '</th><th>';
        echo 'Будет после обновления';
        echo '</th><tr><td>';
        echo '<img src="' . $firm->val('file_logo') . '"/>';
        echo '</td><td>';
        echo '<img src="' . '/image/'.$image->val('file_subdir_name').'/'.$image->val('file_name').'.'.$image->val('file_extension') . '"/>';
        echo '</td></tr></table>';
        echo '<h2>Этапы изменения логотипа</h2>';
        $images = new \App\Model\Image();
        $images = $images->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_price= :nil'], [':id_firm' => $firm_id, ':nil' => 0])
                ->setOrderBy('`timestamp_inserting` ASC')
                ->objects();
        
        echo '<table><tr><th>';
        echo '№';
        echo '</th><th>';
        echo 'Дата изменения';
        echo '</th><th>';
        echo 'Источник';
        echo '</th><th>';
        echo 'Логотип';
        echo '</th>';
        $i = 1;
        foreach ($images as $image) {
            echo '<tr><td>';
            echo $i++;
            echo '</td><td>';
            echo $image->val('timestamp_inserting');
            echo '</td><td>';
            switch($image->val('source')) {
                case 'auto':
                    echo 'автоматически/yml';
                    break;
                case 'ratiss':
                    echo 'модификатор';
                    break;
                case 'client':
                    echo 'личный кабинет';
                    break;
            }
            echo '</td><td>';
            echo '<img src="' . '/image/'.$image->val('file_subdir_name').'/'.$image->val('file_name').'.'.$image->val('file_extension') . '"/>';
            echo '</td></tr>';
        }
        echo '</table>';
        
		exit();
	}

}
