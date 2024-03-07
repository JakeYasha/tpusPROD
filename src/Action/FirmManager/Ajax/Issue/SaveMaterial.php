<?php

namespace App\Action\AppAjax;

use App\Action\FirmManager\Ajax\Issue;

class SaveIssueMaterial extends \App\Action\FirmManager\Ajax\Issue {

    public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\Issue());
	}
    
	public function execute() {
		$params = app()->request()->processPostParams([
			'id_material' => ['type' => 'int'],
			'id_issue' => ['type' => 'int']
		]);
                
                $vals = $this->model()->getVals();
                $vals['id_issue'] = $params['id_issue'];
                $vals['id_material'] = $params['id_material'];
                $vals['flag_is_active'] = 1;
                
                $this->model()->insert($vals);
	}
}
