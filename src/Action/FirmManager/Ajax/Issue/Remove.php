<?php

namespace App\Action\AppAjax;

use App\Action\FirmManager\Ajax\Issue;

class Remove extends \App\Action\FirmManager\Ajax\Issue {

	public function execute($mode = 1) {
		$params = app()->request()->processPostParams([
			'model_alias' => ['type' => 'string'],
			'model_id' => ['type' => 'int']
		]);

		$object = \Sky4\Utils::getModelClass($params['model_alias']);
		$object->get($params['model_id']);

		if ($object->exists() && $object instanceof Firm) {
			app()->stat()->addObject($mode, $object)
					->fixResponse(false);
		}
        
		$this->setResultMessage('ok')
				->renderResult();
	}

}
