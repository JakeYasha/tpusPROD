<?php

namespace App\Action\FirmManager\Ajax\Issue;

use App\Action\FirmManager\Ajax\Issue;

use App\Model\IssueMaterial as IssueMaterial;
use App\Model\Material as MaterialModel;

class Sis extends \App\Action\FirmManager\Ajax\Issue {
        public function __construct() {
            parent::__construct();
        }
        
	public function execute() {
            if (app()->FirmManager()->isNewsEditor()) {
		$params = app()->request()->processPostParams([
			'id_material' => ['type' => 'int'],
			'id_issue' => ['type' => 'int'],
			'type' => ['type' => 'string']
		]);
                
                $material = new MaterialModel($params['id_material']);
                if ($material->exists()) {
                    if (app()->FirmManager()->id_service() == $material->get_id_service()){
                        $issue = new IssueMaterial();
                        switch ($params['type']){
                            case 'add':{
                                if (count($issue->setWhere(
                                        ['AND', '`id_issue` = :id_issue', '`id_material` = :id_material'], [':id_material' => $params['id_material'],':id_issue' => $params['id_issue']]
                                        )->getList()) == 0){
                                    // значит запись первая и уникальная иначе - запись уже была и добавлять не надо
                                    $vals['id_issue'] = $params['id_issue'];
                                    $vals['id_material'] = $params['id_material'];
                                    $vals['flag_is_active'] = 1;
                                    $vals['timestamp_inserting'] = date("Y-m-d H:i:s");
                                    $issue->insert($vals);
                                    return true;
                                }
                            };break;
                            case 'del':{
                                if (count($issue->setWhere(
                                        ['AND', '`id_issue` = :id_issue', '`id_material` = :id_material'], [':id_material' => $params['id_material'],':id_issue' => $params['id_issue']]
                                        )->getList()) != 0){
                                    $issue->deleteAll(['AND', '`id_issue` = :id_issue', '`id_material` = :id_material'], null, null, null, [':id_material' => $params['id_material'],':id_issue' => $params['id_issue']]);
                                    return true;
                                }
                            };break;
                        }
                        return false;
                    }
                }
                
                return false;
                
            }else{
                //это не редактор
                return false;
            }
	}
}
