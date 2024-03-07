<?php

namespace App\Action\Crontab;

use App\Model\Firm;
use App\Model\Image;
use App\Model\Price;
use Sky4\FileSystem\Dir;
use Sky4\FileSystem\File;
use Sky4\Helper\Random;
use const APP_DIR_PATH;
use function str;

class SyncImages extends \App\Action\Crontab {

	public function execute() {
		$this->startAction()->log('Обработка изображений');

		$source_dir = new Dir($this->imagesDir());
		$files = $source_dir->getFiles();
		$images = [];
		foreach ($files as $file_name) {
			if (preg_match('~pf_([0-9]+)_([0-9]+)_([0-9]+).([a-zA-Z]+)~', $file_name, $price_matches)) {
				if (count($price_matches) === 5) {
					$images[] = [
						'file_name' => Random::uniqueId(),
						'legacy_id_firm' => $price_matches[2],
						'legacy_id_service' => $price_matches[1],
						'legacy_id_price' => $price_matches[3],
						'file_extension' => $price_matches[4],
						'path' => $source_dir->getPath().'/'.$price_matches[0],
						'source' => 'ratiss'
					];
				}
			} elseif (preg_match('~ff_([0-9]+)_([0-9]+).([a-zA-Z]+)~', $file_name, $firm_matches)) {
				if (count($firm_matches) === 4) {
					$images[] = [
						'file_name' => Random::uniqueId(),
						'legacy_id_price' => 0,
						'legacy_id_service' => $firm_matches[1],
						'legacy_id_firm' => $firm_matches[2],
						'file_extension' => $firm_matches[3],
						'path' => $source_dir->getPath().'/'.$firm_matches[0],
						'source' => 'ratiss'
					];
				}
			}
		}

		$i = 0;
		foreach ($images as $vals) {
			$i++;
			$where = ['AND', 'legacy_id_firm = :id_firm', 'legacy_id_service = :id_service', 'legacy_id_price = :id_price', 'source = :source'];
			$params = [':id_service' => (int)$vals['legacy_id_service'], ':id_firm' => (int)$vals['legacy_id_firm'], ':id_price' => (int)$vals['legacy_id_price'], ':source' => 'ratiss'];

			$im = new Image();
			$im->reader()->setWhere($where, $params)
					->setOrderBy('timestamp_inserting DESC')
					->objectByConds();

			//копирование и подготовка изображения
			$target_file_name = $vals['file_name'];
			$target_dir = new Dir(APP_DIR_PATH.'/public/image/');
			$target_dir->setPath($target_dir->path().str()->sub($target_file_name, 0, 1).'/');
			if (!$target_dir->exists()) {
				$target_dir->create();
			}
			$target_dir->setPath($target_dir->path().str()->sub($target_file_name, 1, 1).'/');
			if (!$target_dir->exists()) {
				$target_dir->create();
			}

			$vals['file_subdir_name'] = str()->sub($target_file_name, 0, 1).'/'.str()->sub($target_file_name, 1, 1);
			copy($vals['path'], $target_dir->path().$target_file_name.'.'.$vals['file_extension']);
			unset($vals['path']);

			//replace identifiers
			if ((int)$vals['legacy_id_price'] !== 0) {
				$pr = new Price();
				$pr->reader()->setWhere(['AND', 'legacy_id_firm = :id_firm', 'legacy_id_price = :id_price', 'legacy_id_service = :id_service'], [
					':id_firm' => $vals['legacy_id_firm'],
					':id_price' => $vals['legacy_id_price'],
					':id_service' => $vals['legacy_id_service'],
				])->objectByConds();

				$vals['id_firm'] = $pr->id_firm();
				$vals['id_price'] = $pr->id();
				$pr->update(['flag_is_image_exists' => 1]);
			} else {
				$firm = new Firm();
				$firm->getByIdFirmAndIdService($vals['legacy_id_firm'], $vals['legacy_id_service']);
				$vals['id_firm'] = $firm->id();
				$vals['id_price'] = 0;
			}

			if ($im->exists()) {
				$file = new File(APP_DIR_PATH.'/public/image/'.$im->val('file_subdir_name').'/'.$im->val('file_name').'.'.$im->val('file_extension'));
				$file->remove();
				$im->update($vals);
			} else {
				$im->insert($vals);
			}

			if ((int)$vals['legacy_id_price'] === 0) {
                $_image = new \App\Model\Image();
				$_image->reader()->setWhere(['AND', 'id_firm = :id_firm', 'id_price= :nil', 'source = :source'], [':id_firm' => $firm->id(), ':nil' => 0, ':source' => 'client'])
						->setOrderBy('`timestamp_inserting` DESC')
						->objectByConds();

				if (!$_image->exists()) {
					$firm->update(['file_logo' => '/image/'.$im->val('file_subdir_name').'/'.$im->val('file_name').'.'.$im->val('file_extension')]);
				}
			}
		}

		$this->log('Обработано '.$i.' файлов');

		$this->endAction();
	}

}
