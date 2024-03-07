<?php

namespace App\Action\FirmManager\Ajax\Issue;

use App\Action\FirmManager\Ajax\Issue;

use App\Model\IssueMaterial;
//use App\Model\Material;

class AddMaterial extends \App\Action\FirmManager\Ajax\Issue {
        public function __construct() {
            parent::__construct();
        }
        
	public function execute() {
            //if (app()->FirmManager()->isNewsEditor()) {
		$params = app()->request()->processPostParams([
			'id_material' => ['type' => 'int'],
			'id_issue' => ['type' => 'int']
		]);
                //$material = new Material($params['id_material']);
                //var_dump($material->get_id_service());
                //if (app()->FirmManager()->id_service() == $material->get_id_service()){
                    $issue = new IssueMaterial();
                    $vals['id_issue'] = $params['id_issue'];
                    $vals['id_material'] = $params['id_material'];
                    $vals['flag_is_active'] = 1;
                    $vals['timestamp_inserting'] = date("Y-m-d H:i:s");

                    $issue->insert($vals);
                    return true;
                //}else{
                //    return false;
                //}
                
            //}else{
                //это не редактор
                //return false;
            //}
	}
}
