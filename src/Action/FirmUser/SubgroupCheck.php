<?php

namespace App\Action\FirmUser;

use App\Model\AdvertModule;
use App\Model\AdvertModuleGroup;
use App\Model\FirmPromo;
use App\Model\FirmPromoCatalog;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Helper\Word;
use Sky4\Model\Utils;
use function app;

class SubgroupCheck extends \App\Action\FirmUser {

	public function execute() {
		$params = app()->request()->processPostParams([
			'entity_type' => ['type' => 'string']
		]);

		switch ($params['entity_type']) {
			case 'AdvertModule':
				return $this->advertModuleSubgroupCheck();
			case 'FirmPromo':
			default:
				return $this->firmPromoSubgroupCheck();
		}
	}

	protected function advertModuleSubgroupCheck() {
		$result = ['success' => true, 'message' => 'ok'];
		$params = app()->request()->processPostParams([
			'price_catalog_id' => ['type' => 'int']
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
			$amg = new AdvertModuleGroup();
			$amg_conds = Utils::prepareWhereCondsFromArray(array_keys($advert_modules), 'id_advert_module');

			$amg_where = [
				'AND',
				'id_subgroup = :price_catalog_id',
				$amg_conds['where']
			];
			$amg_params = [':price_catalog_id' => $params['price_catalog_id']] + $amg_conds['params'];

			$countOfSubgroupAdvertModules = $amg->reader()
					->setWhere($amg_where, $amg_params)
					->count();

			$default_count = (int) app()->config()->get('app.firmuser.count_of_rubric_advert_modules', 2);
			if ($countOfSubgroupAdvertModules >= $default_count) {
				$result = ['success' => false, 'message' => 'Можно добавлять только ' . $default_count . ' ' . Word::ending($default_count, ['рекламный модуль', 'рекламных модуля', 'рекламных модулей']) . ' в одну подгруппу'];
			}
		}

		die(json_encode($result));
	}

	private function firmPromoSubgroupCheck() {
		$result = ['success' => true, 'message' => 'ok'];
		$params = app()->request()->processPostParams([
			'price_catalog_id' => ['type' => 'int']
		]);

		$fp = new FirmPromo();
		$fp_conds = $this->firm()->getWhereConds();
		$fp_where = [
			'AND',
			'timestamp_ending >= :timestamp',
			$fp_conds['where']
		];
		$fp_params = [':timestamp' => DeprecatedDateTime::now()] + $fp_conds['params'];

		$promos = $fp->reader()
				->setSelect(['id'])
				->setWhere($fp_where, $fp_params)
				->rowsWithKey('id');

		if ($promos) {
			$fpc = new FirmPromoCatalog();
			$fpc_conds = Utils::prepareWhereCondsFromArray(array_keys($promos), 'firm_promo_id');

			$fpc_where = [
				'AND',
				'price_catalog_id = :price_catalog_id',
				$fpc_conds['where']
			];
			$fpc_params = [':price_catalog_id' => $params['price_catalog_id']] + $fpc_conds['params'];

			$countOfSubgroupPromos = $fpc->reader()
					->setWhere($fpc_where, $fpc_params)
					->count();

			$default_count = (int) app()->config()->get('app.firmuser.count_of_subgroup_promos', 2);
			if ($countOfSubgroupPromos >= $default_count) {
				$result = ['success' => false, 'message' => 'Можно добавлять только ' . $default_count . ' ' . Word::ending($default_count, ['акция', 'акции', 'акций']) . ' в одну подгруппу'];
			}
		}

		die(json_encode($result));
	}

}
