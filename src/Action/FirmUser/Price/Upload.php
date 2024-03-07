<?php

namespace App\Action\FirmUser\Price;

class Upload extends \App\Action\FirmUser\Price {

	public function execute() {
		app()->breadCrumbs()->setElem('Загрузка YML-файла', '/firm-user/price/upload/');
		app()->metadata()->setTitle('Загрузка YML-файла');
		$get = app()->request()->processGetParams([
			'error_code' => ['type' => 'int']
		]);

		$yml = new \App\Model\Yml();
		$_items = $yml->reader()
				->setSelect(['id', 'status', 'type', 'url', 'error_code', 'timestamp_yml', 'offers_count', 'offers_count_loaded', 'hash', 'name_format', 'flag_is_referral'])
				->setWhere(['AND', 'id_firm = :id_firm'], [':id_firm' => app()->firmUser()->id_firm()])
				->setOrderBy('timestamp_inserting DESC')
				->objects();

		$items = $yml->prepare($_items);
		foreach ($items as $yml_id => $vals) {
			$logs = (new \App\Model\YmlLog())->reader()->setWhere(['AND', 'id_yml = :id_yml', 'offers_count != :nil'], [':id_yml' => $yml_id, ':nil' => 0])
					->setLimit(3)
					->setOrderBy('id DESC')
					->objects();

			$items[$yml_id]['logs'] = [];
			foreach ($logs as $log) {
				$items[$yml_id]['logs'][] = [
					'offers_count' => $log->val('offers_count'),
					'offers_count_loaded' => $log->val('offers_count_loaded'),
					'offers_count_active' => $log->val('offers_count_active'),
					'timestamp_inserting' => (new \Sky4\Helper\DateTime($log->val('timestamp_inserting')))->format('d.m.Y H:i:s'),
					'timestamp_last_updating' => (new \Sky4\Helper\DateTime($log->val('timestamp_last_updating')))->format('H:i:s'),// gmdate('m', (new \Sky4\Helper\DateTime($log->val('timestamp_last_updating')))->timestamp() - (new \Sky4\Helper\DateTime($log->val('timestamp_inserting')))->timestamp()),
				];
			}
		}

		$yml_parser = new \App\Classes\YmlParser();
		$this->view()
				->set('items', $items)
				->set('error_message', $yml_parser->setErrorCode($get['error_code'])->getErrorMessage())
				->setTemplate('price_upload', 'firmuser')
				->save();
	}

}
