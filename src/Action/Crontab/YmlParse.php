<?php

namespace App\Action\Crontab;

class YmlParse extends \App\Action\Crontab {

	public function execute() {
		//отключаем так как yml больше не парсим
		// $yml = new \App\Model\Yml();

		// $yml->reader()->setSelect(['id'])
		// 		->setWhere(['AND', 'status = :status', 'error_code = :error_code'], [':status' => 'processing', ':error_code' => 0])
		// 		->objectByConds();

		// if ($yml->exists()) {
		// 	$yml_log = new \App\Model\YmlLog();
		// 	$yml_log->reader()->setWhere(['AND', 'id_yml = :id'], [':id' => $yml->id()])
		// 			->setOrderBy('timestamp_last_updating DESC')
		// 			->objectByConds();

		// 	if (time() - (new \Sky4\Helper\DateTime($yml_log->val('timestamp_last_updating')))->timestamp() > 60 * 60 * 6) {
		// 		$yml->update(['status' => 'complete_success']);
		// 	} else {
		// 		return $this;
		// 	}
		// }

		// $yml = new \App\Model\Yml();
		// $yml->reader()->setWhere(['AND', 'status = :status', 'error_code = :error_code'], [':status' => '', ':error_code' => 0])
		// 		->setOrderBy('timestamp_last_updating ASC')
		// 		->objectByConds();

		// if ($yml->exists()) {
        //     $firm = new \App\Model\Firm($yml->id_firm());
        //     if ($firm->exists() && $firm->isBlocked()) {
        //         $yml->update(['status' => 'complete_success']);
        //         return $this;
        //     }
            
		// 	$yml_log = $this->startYmlLog($yml);
        //     $this->log('Парсим yml фирмы ' . $yml->id_firm());
		// 	$this->parse($yml);
		// 	$this->catalogCounter($yml);
		// 	$this->endYmlLog($yml_log, $yml);
		// 	app()->db()->query()->setText('UPDATE `yml` SET `status` = :status WHERE id = :id')
		// 			->execute([
		// 				':id' => $yml->id(),
		// 				':status' => 'complete_success',
		// 	]);
		// } else {
		// 	$this->refreshYml();
		// }
	}

	private function startYmlLog(\App\Model\Yml $yml) {
		$yml_log = new \App\Model\YmlLog();
		$yml_log->insert([
			'id_yml' => $yml->id(),
			'id_firm' => $yml->id_firm(),
			'url' => $yml->val('url')
		]);

		return $yml_log;
	}

	private function endYmlLog(\App\Model\YmlLog $yml_log, \App\Model\Yml $yml) {
		$price = new \App\Model\Price();
		$yml_log->update([
			'offers_count' => $yml->val('offers_count'),
			'offers_count_loaded' => $yml->val('offers_count_loaded'),
			'offers_count_active' => $price->reader()->setWhere(['AND', 'flag_is_active = :flag_is_active', 'source = :source', 'id_firm = :id_firm'], [':flag_is_active' => 1, ':source' => 'yml', ':id_firm' => $yml->id_firm()])->count()
		]);

		return $yml_log;
	}

	private function parse(\App\Model\Yml $yml) {
		$yml->update(['status' => 'processing']);

		$yml_parser = new \App\Classes\YmlParser();
		$data = @file_get_contents($yml->val('url'));
		if ($data) {
            $this->log('Данные есть');
			$yml->update(['data' => $data]);
			$yml_parser->setXmlDataFromYmlObject($yml);
			$yml_parser->setNameFormat(explode(',', $yml->val('name_format')));
			$yml_parser->setFlagIsReferral($yml->val('flag_is_referral'));
			$yml_parser->parse();
		} else {
			$this->log('Данные по URL не получены');
		}
		return $this;
	}

	private function catalogCounter(\App\Model\Yml $yml) {
		$this->startAction();
		$this->log('Перерасчитываем каталог');
		$cat = new \App\Classes\Catalog(true);
		$cat->execute($yml->id_firm());
		$this->log('Готово');
		$this->endAction();
		return $this;
	}

	private function refreshYml() {
		$datetime_to_update_days = app()->config()->get('app.yml.refresh.days', 3);
		$timestamp_to_update = time() - $datetime_to_update_days * 60 * 60 * 24;
		$datetime_to_update = (new \Sky4\Helper\DateTime())->fromTimestamp($timestamp_to_update);

		app()->db()->query()->setText("UPDATE yml SET `status` = :status WHERE `timestamp_last_updating` <= :datetime_to_update")
				->execute([
					':status' => '',
					':datetime_to_update' => $datetime_to_update->format()
		]);

		return $this;
	}

}
