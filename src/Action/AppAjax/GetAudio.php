<?php

namespace App\Action\AppAjax;

require_once APP_DIR_PATH.'/protected/kc_api/kc_service_api.php';
use KCServiceApi;

class GetAudio extends \App\Action\AppAjax {

	public function execute() {
        if (!app()->firmManager()->exists()) {
			die();
		}
        
        $params = app()->request()->processGetParams([
			'action' => ['type' => 'string', 'default' => 'get-call'], 
			'date_begin' => ['type' => 'string'],//'2019-10-18',
			'date_end' => ['type' => 'string'],//'2019-11-18', 
			'asterisk_id' => ['type' => 'string'],//'1574080315.742'
		]);
        
        $kc_service_api = new KCServiceApi();
        $kc_service_api->GetAudio($params['asterisk_id'],$params['date_begin'],$params['date_end']);
        die();
	}

}
