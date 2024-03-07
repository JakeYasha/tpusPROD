<?php

namespace App\Action\Crontab;

class SyncPrice extends \App\Action\Crontab {

	public function __construct() {
		parent::__construct();
		$this->file_name = 'sts_price_xml.txt';
	}

	public function execute() {
		$this->startAction()
				->setInactivePrice()
				->sync()
				->deleteFromRtIndex()
				->endAction();
	}

	public function deleteFromRtIndex() {
		$price = new \App\Model\Price();
		$offset = -1000;

		$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
		while (1) {
			$offset += 1000;

			$items = $price->reader()
					->setWhere(['AND', 'flag_is_active = :flag_is_active', 'source != :source'], [':flag_is_active' => 0, ':source' => 'yml'])
					->setLimit(1000, $offset)
					->objects();

			if ( ! $items) {
				break;
			}

			foreach ($items as $item) {
				$sphinx->delete()
						->from(SPHINX_PRICE_INDEX)
						->where('id', '=', (int)$item->id())
						->execute();
			}
		}

		return $this;
	}

	public function sync() {
		$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
		//$sphinx->query('TRUNCATE RTINDEX ' . SPHINX_PRICE_DRAFT_INDEX)->execute();
		$dump_file = new \Sky4\FileSystem\File($this->dir().'/'.$this->fileName());
		if ($dump_file->exists() && $dump_file->getSize() > 0 && $handle = fopen($dump_file->path(), "r")) {
			$this->log('Синхронизация Price');
			$i = 0;
			while (($buffer = fgets($handle)) !== false) {
				$params = [];
				$i ++;
				if ($i % 10000 == 0) {
					$this->log('обработано записей: '.$i, 1);
				}

				$row = explode('	', str_replace('\\	', ' ', $buffer));
				if (count($row) !== 30) {
					$this->log($this->file_name.' проблемы в строке №'.$i.' ('.count($row).') ', 1);
					continue;
				}

				$source = new \App\Model\Component\Source();
				$source_options = $source->fields()['source']['options'];

				if ( ! array_key_exists($row[24], $source_options)) {
					$this->log($this->file_name.' неправильный source в строке №'.$i.' ('.count($row).') ', 1);
					continue;
				}
                
                $embedded_image = '';
                if (preg_match('~img\-data="([^"]+)"~', trim($row[2]), $matches)) {
                    $embedded_image = '/var/www/sites/tovaryplus.ru/update' . str_replace([
                            '/var/www/sites/tovaryplus.ru/update',
                            '/usr/share/nginx/html/tovaryplus.new/tovaryplus.ru/app/cron'
                        ],'',$matches[1]);
                    $row[2] = preg_replace('~(<img style[^>]+>)~', '', trim($row[2]));
                }

				$vals = [
					'country_of_origin' => trim($row[0]),
					'currency' => trim($row[1]),
					'description' => trim($row[2]),
					'flag_is_active' => (int)$row[3],
					'flag_is_available' => (int)$row[4],
					'flag_is_delivery' => (int)$row[5],
					'flag_is_image_exists' => 0,
					'flag_is_referral' => 0,
					'flag_is_retail' => (int)$row[7],
					'flag_is_wholesale' => (int)$row[8],
					'id_external' => (int)$row[9],
					'id_firm' => null,
					'id_group' => (int)$row[11],
					'id_subgroup' => (int)$row[12],
					'legacy_id_city' => (int)$row[13],
					'legacy_id_firm' => (int)$row[14],
					'legacy_id_price' => (int)$row[15],
					'legacy_id_service' => (int)$row[16],
					'name' => trim($row[17]),
					'name_external' => trim($row[18]),
					'params' => trim($row[19]),
					'price' => (double)$row[20],
					'price_old' => (double)$row[21],
					'price_wholesale' => (double)$row[22],
					'price_wholesale_old' => (double)$row[23],
					'source' => $row[24],
					'timestamp_inserting' => \Sky4\Helper\DateTime::now()->format(),
					'timestamp_last_updating' => $row[26],
					'unit' => trim(str()->toLower($row[27])),
					'url' => trim($row[28]),
					'vendor' => trim($row[29]) === 'НЕ УКАЗАНО' ? '' : trim($row[29])
				];

				$price_object = new \App\Model\Price();
				$price_object->reader()->setSelect(['id', 'id_firm'])
						->setWhere(['AND', 'legacy_id_price = :id_price', 'legacy_id_service = :id_service', 'legacy_id_firm = :id_firm'], [':id_price' => $vals['legacy_id_price'], ':id_service' => $vals['legacy_id_service'], ':id_firm' => $vals['legacy_id_firm']])
						->objectByConds();

				if ($price_object->exists()) {
					unset($vals['timestamp_inserting']);
					unset($vals['flag_is_image_exists']);
					unset($vals['id_firm']);
					unset($vals['flag_is_image_exists']);
					$price_object->update($vals);
				} else {
					$firm = new \App\Model\Firm();
					$firm->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_service = :id_service'], [':id_firm' => $vals['legacy_id_firm'], ':id_service' => $vals['legacy_id_service']])
							->objectByConds();

					if ($firm->exists()) {
						$vals['id_firm'] = $firm->id();
						$price_object->insert($vals);
					}
				}

				if ($embedded_image && file_exists($embedded_image) && is_file($embedded_image)) {
					$this->setEmbeddedImage($price_object, $embedded_image);
				}

				$price_object->updateRtIndex();
			}
			$this->log('Синхронизация завершена');
		} else {
			$this->log('Файл обновления не найден');
		}

		return $this;
	}

	private function setEmbeddedImage(\App\Model\Price $price, $embedded_image) {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
        
		$file_extension = array_reverse(explode('.', $embedded_image))[0];
		$image = [
			'file_name' => \Sky4\Helper\Random::uniqueId(),
			'id_firm' => $price->id_firm(),
			'id_price' => $price->id(),
			'file_extension' => $file_extension,
			'path' => $embedded_image,
			'source' => 'auto'
		];

		$where = ['AND', 'id_firm = :id_firm', 'id_price = :id_price', 'source = :source'];
		$params = [':id_firm' => (int)$image['id_firm'], ':id_price' => (int)$image['id_price'], ':source' => 'auto'];

		$im = new \App\Model\Image();
		$im->reader()
				->setWhere($where, $params)
                ->setOrderBy('timestamp_inserting DESC')
				->objectByConds();

		//копирование и подготовка изображения
		$target_file_name = $image['file_name'];
		$target_dir = new \Sky4\FileSystem\Dir(APP_DIR_PATH.'/public/image/');
		$target_dir->setPath($target_dir->path().str()->sub($target_file_name, 0, 1).'/');
		if ( ! $target_dir->exists()) {
			$target_dir->create();
		}

		$target_dir->setPath($target_dir->path().str()->sub($target_file_name, 1, 1).'/');
		if ( ! $target_dir->exists()) {
			$target_dir->create();
		}

		$image['file_subdir_name'] = str()->sub($target_file_name, 0, 1).'/'.str()->sub($target_file_name, 1, 1);
		copy($image['path'], $target_dir->path().$target_file_name.'.'.$image['file_extension']);
		unset($image['path']);

		if ($im->exists()) {
			$file = new \Sky4\FileSystem\File(APP_DIR_PATH.'/public/image/'.$im->val('file_subdir_name').'/'.$im->val('file_name').'.'.$im->val('file_extension'));
			$file->remove();
			$im->update($image);
		} else {
			$im->insert($image);
		}
	}

	public function setInactivePrice() {
		$this->log('удаление лишних записей', 1);
		$dump_file = new \Sky4\FileSystem\File($this->dir().'/sts_price_del.txt');

		if ($dump_file->getSize() > 0) {
			if ($f = fopen($dump_file->path(), "r")) {
				$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
				while ( ! feof($f)) {
					$line = fgets($f, 4096);
					$fields = explode("\t", $line);
					if (count($fields) < 4) {
						continue;
					}
					list($id_price, $id_firm, $id_service, $datetime) = $fields;

					$price = new \App\Model\Price();
					$price->reader()
							->setWhere(['AND', 'legacy_id_firm = :id_firm', 'legacy_id_price = :id_price', 'legacy_id_service = :id_service'], [
								':id_firm' => (int)$id_firm,
								':id_price' => (int)$id_price,
								':id_service' => (int)$id_service,
							])
							->objectByConds();

					if ($price->exists()) {
						$price->update(['flag_is_active' => 0]);
						app()->db()->query()->setText('DELETE FROM `price_catalog_price` WHERE id_price = :id_price')
								->execute([':id_price' => $price->id()]);
					}
				}
				fclose($f);
			}
		}
		$this->log('завершено', 1);

		return $this;
	}

}
