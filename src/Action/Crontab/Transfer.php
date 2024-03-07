<?php

namespace App\Action\Crontab;

class Transfer extends \App\Action\Crontab {

	private $old_db = null;

	/**
	 * 
	 * @return \Sky4\Db\Connection
	 */
	public function oldDb() {
		if ($this->old_db === null) {
			$this->old_db = new \Sky4\Db\Connection('old');
		}
		return $this->old_db;
	}

	public function execute() {
//		$this->transfer(new \App\Model\AvgStatFirm727373PopularPages());
//		$this->transfer(new \App\Model\AvgStatGeo());
//		$this->transfer(new \App\Model\AvgStatObject727373());
//		$this->transfer(new \App\Model\AvgStatUser727373());
//		$this->transfer(new \App\Model\StatRequest727373());
//		$this->transfer(new \App\Model\StatUser727373());
//		$this->transfer(new \App\Model\StatObject727373());
//		$this->transfer(new \App\Model\AdvertModuleGroup());
//		$this->transfer(new \App\Model\AdvertModuleRegion());
//		$this->transfer(new \App\Model\AdvertModuleRequest());
//		$this->transfer(new \App\Model\AdvertModuleRequest());
//		$this->transfer(new \App\Model\AvgStatBanner());
//		$this->transfer(new \App\Model\AvgStatBanner727373());

//		$this->transfer(new \App\Model\FirmFile());
//		$this->transfer(new \App\Model\File());

//		$this->transfer(new \App\Model\Banner());
//		$this->transfer(new \App\Model\BannerCatalog(), 1000, 'id_banner');
//		$this->transfer(new \App\Model\BannerGroup());
//		$this->transfer(new \App\Model\BannerRegion());
//		$this->transfer(new \App\Model\AdvertModule());
//		$this->transfer(new \App\Model\AdvertModuleFirm());
//		$this->transfer(new \App\Model\AdvertModuleFirmType());
//		$this->transfer(new \App\Model\AdvertModuleGroup());
//		$this->transfer(new \App\Model\AdvertModuleRegion());
//		$this->transfer(new \App\Model\AdvertModuleRequest());
//		$this->transfer(new \App\Model\FirmPromo());
//		$this->transfer(new \App\Model\FirmPromoCatalog(), 10000, 'firm_promo_id');
//		$this->transfer(new \App\Model\FirmReview());
//		$this->transfer(new \App\Model\FirmVideo());
//		$this->transfer(new \App\Model\Image());
//		//
//		$this->transfer(new \App\Model\AvgStatBanner(), 100000);
//		$this->transfer(new \App\Model\AvgStatFirm727373PopularPages(), 100000);
//		$this->transfer(new \App\Model\AvgStatFirmPopularPages(), 100000);
//		$this->transfer(new \App\Model\AvgStatGeo(), 100000);
//		$this->transfer(new \App\Model\AvgStatGeo727373(), 100000);
//		$this->transfer(new \App\Model\AvgStatObject(), 100000);
//		$this->transfer(new \App\Model\AvgStatObject727373(), 100000);
//		$this->transfer(new \App\Model\AvgStatPopularPages(), 100000);
//		$this->transfer(new \App\Model\AvgStatUser(), 100000);
//		$this->transfer(new \App\Model\AvgStatUser727373(), 100000);
		//
		{
//			$this->transfer(new \App\Model\StatBannerClick(), 100000);
//			$this->transfer(new \App\Model\StatBannerShow(), 100000);
//			$this->transfer(new \App\Model\StatObject(), 100000);
//			$this->transfer(new \App\Model\StatRequest(), 100000);
//			$this->transfer(new \App\Model\StatSite(), 100000);
//			$this->transfer(new \App\Model\StatUser(), 100000);
//			$this->transfer(new \App\Model\StatObject727373(), 100000);
//			$this->transfer(new \App\Model\StatRequest727373(), 100000);
//			$this->transfer(new \App\Model\StatUser727373(), 100000);
		}
		//
//		$this->transfer(new \App\Model\BannerGroup());
//		$this->transfer(new \App\Model\BannerRegion());
//		$this->transfer(new \App\Model\Consumer());
//		$this->transfer(new \App\Model\File());
//		$this->transfer(new \App\Model\FileStorage());
//		$this->transfer(new \App\Model\FirmCoords());
//		$this->transfer(new \App\Model\FirmDescription());
//		$this->transfer(new \App\Model\FirmFeedback());
//		$this->transfer(new \App\Model\FirmFile());
//		
//		$this->transfer(new \App\Model\FirmReview());
//		$this->transfer(new \App\Model\FirmType());
////		$this->transfer(new \App\Model\Image());
//		$this->transfer(new \App\Model\GuestBook());
//		$this->transfer(new \App\Model\PhotoContest());
//		$this->transfer(new \App\Model\PhotoContestItem());
//		$this->transfer(new \App\Model\PhotoContestNomination());
//		$this->transfer(new \App\Model\PriceRequest());
//		$this->transfer(new \App\Model\RedirectRule());
//		$this->transfer(new \App\Model\Request());
//		$this->transfer(new \App\Model\Synonym());
//		$this->transfer(new \App\Model\Text());
//		$this->transfer(new \App\Model\UserDataChanging());
//		$this->transfer(new \App\Model\WordException());
//
////		$this->transferFirms();
//		$this->transfer(new \App\Model\AdvertModule());
//		$this->transfer(new \App\Model\AdvertModuleFirm());
//		$this->transfer(new \App\Model\Banner());
//		$this->transfer(new \App\Model\DraftFirm());
//		$this->transfer(new \App\Model\FirmDelivery());
//		$this->transfer(new \App\Model\FirmDescription());
//		$this->transfer(new \App\Model\FirmFile());
//		$this->transfer(new \App\Model\FirmFeedback());
////		$this->transfer(new \App\Model\FirmManager());
//		$this->transfer(new \App\Model\FirmPromo());
////		$this->transfer(new \App\Model\FirmRank());
//		$this->transfer(new \App\Model\FirmReview());
////		$this->transfer(new \App\Model\FirmUser());
////		$this->transfer(new \App\Model\FirmUserTimestamp());
//		$this->transfer(new \App\Model\FirmVideo());
////		$this->transfer(new \App\Model\Image());
//		$this->transfer(new \App\Model\PriceRequest());
//		$this->transfer(new \App\Model\FirmHotlead());
//		$this->transfer(new \App\Model\StatBannerClick());
//		$this->transfer(new \App\Model\StatBannerShow(), 100000);
//		$this->transfer(new \App\Model\AvgStatBanner(), 50000);
//		$this->transfer(new \App\Model\AvgStatObject(), 50000);
//		$this->transfer(new \App\Model\StsHistAnswer(), 10000, 'id_hist_answer');
//		$this->transfer(new \App\Model\AvgStatFirmPopularPages(), 100000);
//		$this->transfer(new \App\Model\AvgStatUser(), 100000);
//
//
//		$this->transfer(new \App\Model\StatObject(), 50000); //table locks, manual
//        $this->transfer(new \App\Model\StsHistCalls(), 100000, 'id_hist_calls');
//        $this->transfer(new \App\Model\StsHistAnswer(), 100000, 'id_hist_answer');
//        $this->transfer(new \App\Model\StsHistReaddress(), 100000, 'id_hist_readdress');
//        $this->transfer(new \App\Model\StsHistExportDetail(), 100000, 'id_hist_detail');
	}

	private function transferFirms() {
		$rows = $this->oldDb()->query()->setText('SELECT * FROM `firm` ORDER BY `id` ASC')
				->fetch();

		$i = 0;
		foreach ($rows as $row) {
			$firm = new \App\Model\Firm();
			$firm->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_service = :id_service'], [':id_firm' => $row['id_firm'], ':id_service' => $row['id_service']])
					->objectByConds();

			unset($row['id']);
			if ($firm->exists()) {
				$firm->update($row);
			} else {
				$firm->insert($row);
			}

			echo "\r".++$i;
		}

		return $this;
	}

	private function transfer(\Sky4\Model $object, $chunk = 1000, $id_field = 'id') {
		echo PHP_EOL.$object->table().PHP_EOL;
		$offset = 0;
		$count_rows = 0;
		app()->db()->query()->truncateTable($object->table());
		app()->db()->query()->setText('ALTER TABLE `'.$object->table().'` DISABLE KEYS')->execute();
		while (1) {
			$rows = $this->oldDb()->query()->setText('SELECT * FROM `'.$object->table().'` ORDER BY `'.$id_field.'` ASC LIMIT '.$offset.', '.$chunk)
					->fetch();

			if (!$rows) {
				print_r(PHP_EOL.'end: '.$count_rows);
				break;
			}

			foreach ($rows as $row) {
				if (isset($row['id_firm']) && isset($row['id_service'])) {
					$firm_id = $this->db()->query()->setText('SELECT id FROM `firm` WHERE id_firm = '.$row['id_firm'].' AND id_service = '.$row['id_service'])->fetch();
					if (isset($firm_id[0]) && $firm_id[0]) {
						if ($object instanceof \App\Model\Image) {
							$row['legacy_id_price'] = $row['id_price'];
							$row['legacy_id_firm'] = $row['id_firm'];
							$row['legacy_id_service'] = $row['id_service'];

							if ($row['id_price']) {
								$price_id = $this->db()->query()->setText('SELECT id FROM `price` WHERE legacy_id_price = '.$row['id_price'].' AND legacy_id_service = '.$row['id_service'].' AND `legacy_id_firm` = '.$row['id_firm'])->fetch();
								if (isset($price_id[0]) && $price_id[0]) {
									$row['id_price'] = $price_id[0]['id'];
								}
							}
						}

						if (isset($row['id_service']) && !$object->fieldExists('id_service')) {
							unset($row['id_service']);
						}
						if (isset($row['id_city']) && !$object->fieldExists('id_city')) {
							unset($row['id_city']);
						}
						if (isset($row['virtual_id_firm'])) {
							unset($row['virtual_id_firm']);
						}
						$row['id_firm'] = $firm_id[0]['id'];
						$_set = [];
						foreach ($row as $field_name => $val) {
							$_set['`'.(string)$field_name.'`'] = $val;
						}
						app()->db()->query()
								->setInsertInto('`'.(string)$object->table().'`')
								->setSet($_set)
								->insert();

						$count_rows++;
					}
				} else {
					foreach ($row as $field_name => $val) {
						$_set['`'.(string)$field_name.'`'] = $val;
					}
					app()->db()->query()
							->setInsertInto('`'.(string)$object->table().'`')
							->setSet($_set)
							->insert();
					$count_rows++;
				}
				if ($count_rows % 1000 === 0) {
					print_r("\r".$count_rows);
				}
			}
			$offset += $chunk;
		}
		app()->db()->query()->setText('ALTER TABLE `'.$object->table().'` ENABLE KEYS')->execute();

		$rows = null;

		return $this;
	}

}
