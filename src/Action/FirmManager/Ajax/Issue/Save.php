<?php

namespace App\Action\AppAjax;

use App\Action\FirmManager\Ajax\Issue;

class Save extends \App\Action\FirmManager\Ajax\Issue {

	public function execute($mode = 1) {
		$params = app()->request()->processPostParams([
			'model_alias' => ['type' => 'string'],
			'model_id' => ['type' => 'int']
		]);

		$object = \Sky4\Utils::getModelClass($params['model_alias']);
		$object->get($params['model_id']);
        
		$this->setResultMessage('ok')
				->renderResult();
	}

}
