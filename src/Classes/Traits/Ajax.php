<?php

namespace App\Classes\Traits;

trait Ajax {

	public $result = [
		'error_code' => 0,
		'error_message' => ''
	];

	public function getObjectByParams($params, $allowed_models_aliases = []) {
		$object = null;
		$_params = \CApp::request()->processParams($params, [
			'model_alias' => ['type' => 'string'],
			'modelAlias' => ['type' => 'string'],
			'object_id' => ['type' => 'int'],
			'objectId' => ['type' => 'int']
		]);
		$model_alias = null;
		$object_id = null;
		if ($_params['modelAlias'] && $_params['objectId']) {
			$model_alias = $_params['modelAlias'];
			$object_id = $_params['objectId'];
		} elseif ($_params['model_alias'] && $_params['object_id']) {
			$model_alias = $_params['model_alias'];
			$object_id = $_params['object_id'];
		}
		if (!$model_alias || !$object_id) {
			$this->setError('Переданы неправильные данные');
		} else {
			$object = \Sky4\Utils::getModelClass($model_alias);
			if ($allowed_models_aliases && !in_array($object->alias(), $allowed_models_aliases)) {
				$this->setError('Неправильный объект');
			} else {
				$object->reader()->object($object_id);
				if (!$object->exists()) {
					$this->setError('Объект отсутствует');
				}
			}
		}
		return $object;
	}

	public function hasErrors() {
		return $this->result['error_code'] ? true : false;
	}

	public function renderResult() {
		echo json_encode($this->result);
		exit();
	}

	public function setError($message, $code = 1) {
		$this->result['error_code'] = (int) $code;
		$this->result['error_message'] = (string) $message;
		return $this;
	}

	public function setResultAction($action) {
		$this->result['result'] = [
			'action' => (string) $action,
			'type' => 'action'
		];
		return $this;
	}

	public function setResultData($data) {
		$this->result['data'] = (array) $data;
		return $this;
	}

	public function setResultMessage($message) {
		$this->result['result'] = [
			'message' => (string) $message,
			'type' => 'alert'
		];
		return $this;
	}

	public function setResultRedirect($link) {
		$this->result['result'] = [
			'link' => (string) $link,
			'type' => 'redirect'
		];
		return $this;
	}

	public function setResultReload() {
		$this->result['result'] = [
			'type' => 'reload'
		];
		return $this;
	}

}
