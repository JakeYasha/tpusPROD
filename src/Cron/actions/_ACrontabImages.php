<?php

class ACrontabImages extends ACrontabAction {

	public function run() {
		$this->log('обновляем фотографии');
		$this->updateFromRatissFiles();

		return parent::run();
	}

	/**
	 * @deprecated
	 */
	public function updateFromOldUplDir() {
		$source_dir = new CDir('/usr/share/nginx/html/tovaryplus.ru/public/img/ratiss/upl');
		$photos = $this->db->query()->setText('SELECT * FROM `photo`')->fetch();

		$i = 0;
		foreach ($photos as $ob) {
			$file = new CFile($source_dir->path() . $ob['id_firm'] . '_' . $ob['id_service'] . '/' . $ob['filename']);
			if ($file->exists()) {
				$where = ['AND', 'id_firm = :id_firm', 'id_service = :id_service', 'id_price = :id_price'];
				$params = [':id_service' => (int) $ob['id_service'], ':id_firm' => (int) $ob['id_firm'], ':id_price' => (int) $ob['id_price']];

				$im = new Image();
				$im->setWhere($where, $params)
						->getByConds();

				if (!$im->exists()) {
					$target_file_name = CRandom::uniqueId();
					$target_dir = new CDir(APP_DIR_PATH . '/public/image/');
					$target_dir->setPath($target_dir->path() . str()->sub($target_file_name, 0, 1) . '/');
					if (!$target_dir->exists()) {
						$target_dir->create();
					}
					$target_dir->setPath($target_dir->path() . str()->sub($target_file_name, 1, 1) . '/');
					if (!$target_dir->exists()) {
						$target_dir->create();
					}

					$target_file_extension = str()->toLower(end(explode('.', $file->getPath())));
					$file_subdir_name = str()->sub($target_file_name, 0, 1) . '/' . str()->sub($target_file_name, 1, 1);
					copy($file->path(), $target_dir->path() . $target_file_name . '.' . $target_file_extension);
					$im->insert([
						'file_name' => $target_file_name,
						'id_firm' => $ob['id_firm'],
						'id_service' => $ob['id_service'],
						'id_price' => $ob['id_price'],
						'file_extension' => $target_file_extension,
						'file_subdir_name' => $file_subdir_name
					]);
				}
			}
			print_r("\rДобавлено файлов: " . $i);
		}
	}

	public function updateFromRatissFiles() {
		$source_dir = new CDir(APP_DIR_PATH . '/app/cron/update/ratiss_image');
		$files = $source_dir->getFiles();
		$images = [];
		foreach ($files as $file_name) {
			if (preg_match('~pf_([0-9]+)_([0-9]+)_([0-9]+).([a-zA-Z]+)~', $file_name, $price_matches)) {
				if (count($price_matches) === 5) {
					$images[] = [
						'file_name' => CRandom::uniqueId(),
						'id_firm' => $price_matches[2],
						'id_service' => $price_matches[1],
						'id_price' => $price_matches[3],
						'file_extension' => $price_matches[4],
						'path' => $source_dir->getPath() . '/' . $price_matches[0],
						'source' => 'ratiss'
					];
				}
			} elseif (preg_match('~ff_([0-9]+)_([0-9]+).([a-zA-Z]+)~', $file_name, $firm_matches)) {
				if (count($firm_matches) === 4) {
					$images[] = [
						'file_name' => CRandom::uniqueId(),
						'id_price' => 0,
						'id_service' => $firm_matches[1],
						'id_firm' => $firm_matches[2],
						'file_extension' => $firm_matches[3],
						'path' => $source_dir->getPath() . '/' . $firm_matches[0],
						'source' => 'ratiss'
					];
				}
			}
		}

		$i = 0;
		foreach ($images as $vals) {
			$i++;
			$where = ['AND', 'id_firm = :id_firm', 'id_service = :id_service', 'id_price = :id_price', 'source = :source'];
			$params = [':id_service' => (int) $vals['id_service'], ':id_firm' => (int) $vals['id_firm'], ':id_price' => (int) $vals['id_price'], ':source' => 'ratiss'];

			$im = new Image();
			$im->setWhere($where, $params)
                                        ->setOrderBy('timestamp_inserting DESC')
					->getByConds();

			//копирование и подготовка изображения
			$target_file_name = $vals['file_name'];
			$target_dir = new CDir(APP_DIR_PATH . '/public/image/');
			$target_dir->setPath($target_dir->path() . str()->sub($target_file_name, 0, 1) . '/');
			if (!$target_dir->exists()) {
				$target_dir->create();
			}
			$target_dir->setPath($target_dir->path() . str()->sub($target_file_name, 1, 1) . '/');
			if (!$target_dir->exists()) {
				$target_dir->create();
			}

			$vals['file_subdir_name'] = str()->sub($target_file_name, 0, 1) . '/' . str()->sub($target_file_name, 1, 1);
			copy($vals['path'], $target_dir->path() . $target_file_name . '.' . $vals['file_extension']);
			unset($vals['path']);

			if ($im->exists()) {
				$file = new CFile(APP_DIR_PATH . '/public/image/' . $im->val('file_subdir_name') . '/' . $im->val('file_name') . '.' . $im->val('file_extension'));
				$file->remove();

				$im->update($vals);
			} else {
				$im->insert($vals);
			}
		}

		$this->log(' обработано ' . $i . ' файлов');
	}

}
