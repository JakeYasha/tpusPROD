<?php

namespace App\Action\Crontab;

class SyncFirm extends \App\Action\Crontab {

	public function __construct() {
		parent::__construct();
		$this->file_name = 'sts_firm.txt';
	}

	public function execute() {
		$result = $this->startAction()
				->setInactiveFirms()
				->sync();
		$this->endAction();

		return $result;
	}

	protected function setInactiveFirms() {
		$this->file_name = 'sts_firm_del.txt';
		$dump_file = new \Sky4\FileSystem\File($this->dir().'/'.$this->fileName());
		if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
			$inactive_array = [];
			if ($f = fopen($dump_file->path(), "r")) {
				while (!feof($f)) {
					$line = fgets($f, 4096);
					$fields = explode("\t", $line);
					if (count($fields) < 3) {
						continue;
					}
					list($id_firm, $id_service, $datetime) = $fields;
					if (!isset($inactive_array[$id_service])) {
						$inactive_array[$id_service] = [];
					}
					$inactive_array[$id_service][] = $id_firm;
				}
				fclose($f);
			}

			if (!empty($inactive_array)) {
				$this->log('Удаляем данные, связанные с удаленными фирмами', 1);
				foreach ($inactive_array as $id_service => $id_firms) {
					$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
					foreach ($id_firms as $id_firm) {
						$firm = new \App\Model\Firm();
						$firm->getByIdFirmAndIdService($id_firm, $id_service);

						if ($firm->exists()) {
							$firm->update(['flag_is_active' => 0]);
							app()->db()->query()->setText('UPDATE `price` SET `flag_is_active` = :flag_is_active WHERE id_firm = :id_firm')
									->execute([':flag_is_active' => 0, ':id_firm' => $firm->id()]);
							app()->db()->query()->setText('DELETE FROM `price_catalog_price` WHERE id_firm = :id_firm')
									->execute([':id_firm' => $firm->id()]);

							$sphinx->delete()
									->from(SPHINX_PRICE_INDEX)
									->where('id_firm', '=', (int)$firm->id())
									->execute();

							$sphinx->delete()
									->from(SPHINX_FIRM_INDEX)
									->where('id', '=', (int)$firm->id())
									->execute();
						}

						// Проверим наличие банеров, если есть и фирма сейчас будет заблокирована - необходимо уведомить менеджера
						if (!$firm->isBlocked()) {
							$sb = new \App\Model\Banner();
							$dt = new \Sky4\Helper\DateTime();
							$today = $dt->fromTimestamp(mktime(23, 59, 59, date('m'), date('d')));
							$banners = $sb->reader()
									->setWhere(['AND', 'timestamp_ending > :today', 'id_firm = :id_firm', 'flag_is_active = :flag_is_active'], [':today' => $today->format(), ':id_firm' => $firm->id(), ':flag_is_active' => 1])
									->objects();

							foreach ($banners as $banner) {
								if ($firm->id_service() === 10) {
									$manager = new \App\Model\FirmManager();
									$manager->getByFirm($firm);
									if ($manager->exists()) {
										$this->sendBlockedFirmBannerNotice($manager->val('email'), $firm, $banner);
									}
								} else {
									$service = new \App\Model\StsService();
									$service->reader()
											->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $firm->id_service()])
											->objectByConds();

									if ($service->exists()) {
										$this->sendBlockedFirmBannerNotice($service->val('email'), $firm, $banner);
									}
								}
							}
						}
					}
				}
			}
		}

		return $this;
	}

	private function sendBlockedFirmBannerNotice($email, \App\Model\Firm $firm, \App\Model\Banner $banner) {
		app()->email()
				->setSubject('Уточнение сроков размещения банера для заблокированноый фирмы '.$firm->name())
				->setTo($email)
				->setModel($banner)
				->setTemplate('email_blocked_firm_banner_notice', 'firmmanager')
				->sendToQuery();

		return $this;
	}

	protected function sync() {
		$this->file_name = 'sts_firm.txt';
		$dump_file = new \Sky4\FileSystem\File($this->dir().'/'.$this->fileName());
		if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
			$this->log('Синхронизация Firm');
			$table_name = 'ratiss_firm';
			app()->db()->query()->truncateTable($table_name);

			$this->loadData('sts_firm', $table_name);

			$firms = $this->db()->query()
					->setFrom($table_name)
					->setWhere('`id_service` != :nil', [':nil' => 0])
					->select();

			foreach ($firms as $ob) {
				$firm_where = ['AND', '`id_firm` = :id_firm', 'id_service = :id_service'];
				$firm_params = [':id_firm' => $ob['id_firm'], ':id_service' => $ob['id_service']];

				$firm = new \App\Model\Firm();
				$firm->getByIdFirmAndIdService($ob['id_firm'], $ob['id_service']);

				$res = [
					'id_firm' => $ob['id_firm'],
					'id_parent' => $ob['id_parent'],
					'id_contract' => 0,
					'id_manager' => $ob['id_manager'],
					'id_firm_user' => 0,
					'id_service' => $ob['id_service'],
					'company_activity' => $ob['business'] ? $ob['business'] : '',
					'company_address' => $ob['address'] ? $ob['address'] : '',
					'company_cell_phone' => $ob['pager'] ? $ob['pager'] : '',
					'company_email' => $ob['email'] ? $ob['email'] : '',
					'company_fax' => $ob['fax'] ? $ob['fax'] : '',
					'company_map_address' => $ob['mapaddress'] ? $ob['mapaddress'] : '',
					'company_name' => $ob['name'] ? $ob['name'] : $ob['name_ratiss'],
					'company_name_ratiss' => $ob['name_ratiss'] ? $ob['name_ratiss'] : '',
					'company_name_jure' => $ob['jure'] ? $ob['jure'] : '',
					'company_phone' => $ob['phone'] ? $ob['phone'] : '',
					'company_phone_readdress' => $ob['readdres_phone'] ? $ob['readdres_phone'] : '',
					'company_web_site_url' => $ob['web'] ? $ob['web'] : '',
					'mode_work' => $ob['mode_work'] ? $ob['mode_work'] : '',
					'text' => $ob['info'] ? $ob['info'] : '',
					'flag_is_producer' => $ob['producer'],
					'flag_is_active' => $ob['blocked'] ? 0 : 1,
					'file_logo' => '',
					'file_description' => $ob['id_description'] ? $ob['id_description'] : '',
					'path' => $ob['path'] ? $ob['path'] : '',
					'priority' => $ob['priority'] ? $ob['priority'] : '',
					'rating' => 0,
					'id_city' => $ob['id_city'],
					'id_country' => $ob['id_country'],
					'id_region_country' => $ob['id_region_country'],
					'id_region_city' => $ob['id_region_city'],
					'timestamp_inserting' => $ob['date_input'],
					'timestamp_ratiss_updating' => $ob['datetime'],
				];

				$image = new \App\Model\Image();
				$images = $image->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_price = :nil', 'source = :source'], [':id_firm' => $firm->id(), ':nil' => 0, ':source' => 'ratiss'])
						->objects();
				foreach ($images as $image) {
					if ($image->exists()) {
						$image->delete();
					}
				}

				$image = new \App\Model\Image();
				$image->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_price= :nil'], [':id_firm' => $firm->id(), ':nil' => 0])
						->setOrderBy('CAST(`source` AS CHAR) ASC, `timestamp_inserting` DESC')
						->objectByConds();

				if ($image->exists()) {
					$res['file_logo'] = '/image/'.$image->val('file_subdir_name').'/'.$image->val('file_name').'.'.$image->val('file_extension');
				}

				if ($firm->exists()) {
					unset($res['rating']);
					$firm->update($res);
				} else {
					$firm->insert($res);
				}

				$contract = new \App\Model\FirmContract();
				$contract->getByFirm($firm);

				if (!$contract->exists()) {
					if ($ob['dog_number']) {
						$contract->insert([
							'id_firm' => $firm->id(),
							'name' => $ob['dog_number'] ? $ob['dog_number'] : '',
							'timestamp_beginning' => $ob['dog_begin_date'],
							'timestamp_ending' => $ob['dog_end_date']
						]);
					}
				}
				$contract_id = $contract->id();

				$user = new \App\Model\FirmUser();
				$user->getByFirm($firm);

				$user_id = $user->exists() ? $user->id() : 0;

				$firm->update(['id_contract' => $contract_id, 'id_firm_user' => $user_id]);
			}

			$firm = new \App\Model\Firm();
			$firms = $firm->reader()->setSelect(['id', 'id_city', 'flag_is_active'])->rows();

			$i = 0;
			foreach ($firms as $firm) {
				$i++;
                if ($firm['flag_is_active'] == 1) {
                    app()->db()->query()->setText('REPLACE INTO `firm_city` SET `id_firm` = :id_firm, `id_city` = :id_city')
                            ->execute([
                                ':id_firm' => $firm['id'],
                                ':id_city' => $firm['id_city']
                    ]);
                } else {
                    app()->db()->query()->setText('DELETE FROM `firm_city` WHERE `id_firm` = :id_firm AND `id_city` = :id_city')
                            ->execute([
                                ':id_firm' => $firm['id'],
                                ':id_city' => $firm['id_city']
                    ]);
                }
			}

			$this->log('Обновили/добавили данные по фирмам', 1);

			$price_updates_file = new \App\Model\File($this->dir().'/sts_firm_price_update.txt');
			if ($price_updates_file->exists() && $price_updates_file->getSize() > 0) {
				if ($f = fopen($price_updates_file->path(), "r")) {
					while (!feof($f)) {
						$line = fgets($f, 4096);
						if (!$line) {
							break;
						}
						$row = explode("\t", $line);
						if (count($row) < 3) {
							break;
						}
						list($id_firm, $id_service, $dateupdate) = $row;
						$this->db()->query()->setText('UPDATE `price` SET `timestamp_last_updating` = :datetime WHERE `legacy_id_service` = :id_service AND `legacy_id_firm` = :id_firm')->execute([':datetime' => $dateupdate, ':id_service' => $id_service, ':id_firm' => $id_firm]);
					}
					fclose($f);
				}
			}
			$this->log('Обновили прайсы по обновленным фирмам', 1);
			$this->log('Синхронизация завершена');
			return true;
		} else {
			$this->log('Файл обновления не найден');
			return false;
		}

		return false;
	}

}
