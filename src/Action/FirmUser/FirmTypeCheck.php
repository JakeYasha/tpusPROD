<?php

namespace App\Action\FirmUser;

use App\Action\FirmUser;
use App\Model\AdvertModule;
use App\Model\AdvertModuleFirmType;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Helper\Word;
use Sky4\Model\Utils;
use function app;

class FirmTypeCheck extends FirmUser {

	public function execute() {
		$params = app()->request()->processPostParams([
			'entity_type' => ['type' => 'string']
		]);

		switch ($params['entity_type']) {
			case 'AdvertModule':
			default:
				return $this->advertModuleFirmTypeCheck();
		}
	}

	private function advertModuleFirmTypeCheck() {
		$result = ['success' => true, 'message' => 'ok'];
		$params = app()->request()->processPostParams([
			'firm_type_id' => ['type' => 'int']
		]);

		$am = new AdvertModule();
		$am_conds = $this->firm()->getWhereConds();
		$am_where = [
			'AND',
			'timestamp_ending >= :timestamp',
			$am_conds['where']
		];
		$am_params = [':timestamp' => DeprecatedDateTime::now()] + $am_conds['params'];

		$advert_modules = $am->reader()
				->setSelect(['id'])
				->setWhere($am_where, $am_params)
				->rowsWithKey('id');

		if ($advert_modules) {
			$amg = new AdvertModuleFirmType();
			$amg_conds = Utils::prepareWhereCondsFromArray(array_keys($advert_modules), 'id_advert_module');

			$amg_where = [
				'AND',
				'id_firm_type = :firm_type_id',
				$amg_conds['where']
			];
			$amg_params = [':firm_type_id' => $params['firm_type_id']] + $amg_conds['params'];

			$countOfSubgroupAdvertModules = $amg->reader()
					->setWhere($amg_where, $amg_params)
					->count();

			$default_count = (int) app()->config()->get('app.firmuser.count_of_fitm_type_advert_modules', 2);
			if ($countOfSubgroupAdvertModules >= $default_count) {
				$result = ['success' => false, 'message' => 'Можно добавлять только ' . $default_count . ' ' . Word::ending($default_count, ['рекламный модуль', 'рекламных модуля', 'рекламных модулей']) . ' в один тип фирм'];
			}
		}

		die(json_encode($result));
	}

}
