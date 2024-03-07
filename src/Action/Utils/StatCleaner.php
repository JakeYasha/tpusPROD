<?php

namespace App\Action\Utils;

use Foolz\SphinxQL\SphinxQL;
use \App\Model\Firm;
use App\Model\StatObject;
use App\Model\StatRequest;
use App\Model\StatUser;
use function app;

class StatCleaner extends \App\Action\Utils {
    public function __construct() {
        parent::__construct();
        if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
            exit();
        }
    }

	public function execute() {
        $this->params = app()->request()->processGetParams([
			'id_firm' => ['type' => 'int'],
			'type' => ['type' => 'int'],
			'start_date' => ['type' => 'string'],
			'end_date' => ['type' => 'string'],
		]);
        
        if (!isset($this->params['id_firm']) || !(int)$this->params['id_firm']) {
            echo 'Укажите id Фирмы [id_firm]';
            die();
        }
        
        $id_firm = (int)$this->params['id_firm'];

        if (!$this->params['start_date']) {
            echo 'Укажите дату начала отбора данных [start_date]';
            die();
        }
        
        $start_date = $this->params['start_date'] . ' 00:00:00';

        $firm = new Firm();
        $firm->getByIdFirm($id_firm);
        
        if (!$firm->exists()) {
            echo 'Фирмы с таким id не существует';
            die();
        }
        
        $where = ['AND','id_firm = :id_firm', 'timestamp_inserting >= :start_date'];
        $params = [':id_firm' => $id_firm, ':start_date' => $start_date];
        
        $end_date = isset($this->params['end_date']) ? $this->params['end_date'] . ' 00:00:00' : false;
        
        if ($end_date) {
            $where = array_merge($where, ['timestamp_inserting < :end_date']);
            $params += [':end_date' => $end_date];
        }
        
        $type = isset($this->params['type']) ? (int)$this->params['type'] : 0;
        
        if ($type) {
            $where = array_merge($where, ['type = :type']);
            $params += [':type' => $type];
        }
        
        $_so = new StatObject();
        $stat_objects = $_so->reader()->setWhere($where, $params)->setOrderBy('timestamp_inserting DESC')->objects();

        $i = 1;
        foreach($stat_objects as $stat_object) {
            $stat_request = new StatRequest($stat_object->val('id_stat_request'));
            if ($stat_request->exists() && strpos($stat_request->val('request_url'), 'brand=') !== FALSE) {
                echo $stat_request->val('timestamp_inserting') . ' (' . $i++ . '): ' . $stat_request->val('request_url') . '<br/>';
            }
        }
        
        exit();
	}

}
