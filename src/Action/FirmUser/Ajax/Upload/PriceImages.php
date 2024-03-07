<?php

namespace App\Action\FirmUser\Ajax\Upload;

use App\Model\Image;
use Sky4\FileSystem\Dir;
use Sky4\FileSystem\File\Image as Image2;
use const APP_DIR_PATH;
use function app;

class PriceImages extends \App\Action\FirmUser\Ajax\Upload {

	public function execute() {
		$get = app()->request()->processGetParams([
			'id_firm' => ['type' => 'int'],
			'id_price' => ['type' => 'int']
		]);

		$dir = new Dir(APP_DIR_PATH.'/public/image');
		$dir->create();
		$this->fileUploader()->setFileDirPath($dir->getPath());

		$result = ['success' => false];
		if ($this->fileUploader()->uploadFile()) {
			$file_data = $this->fileUploader()->getFileData();

			$name_hash = '';
			$_price = (new \App\Model\Price($get['id_price']));
			if ($_price->exists()) $name_hash = md5($_price->name());

			$vals = [
				'file_dimension_height' => $file_data['dimension_height'],
				'file_dimension_size' => $file_data['dimension_size'],
				'file_dimension_width' => $file_data['dimension_width'],
				'file_extension' => $file_data['extension'],
				'file_name' => $file_data['name'],
				'file_raw_name' => $file_data['raw_name'],
				'file_subdir_name' => implode('/', $file_data['subdirs_names']),
				'id_firm' => $get['id_firm'],
				'id_price' => $get['id_price'],
				'source' => (int)$get['id_price'] === 0 ? 'temp' : 'client',
				'base_path' => '/image',
				'name_hash' => $name_hash
			];

			$image = new Image();
			$image->embededFileComponent()->setSubDirName('image');
			$image->insert($vals);
			$_price->update(['flag_is_image_exists' => 1]);

			if ($image->embeddedFile()->isImage()) {
				$this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
						->setTargetFilePath($image->embeddedFile()->path('-160x160'))
						->setTargetFileWidth(160)
						->setTargetFileHeight(160)
						->setWithCutoff(false)
						->resize();

				$this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
						->setTargetFilePath($image->embeddedFile()->path('-260x260'))
						->setTargetFileWidth(260)
						->setTargetFileHeight(260)
						->setWithCutoff(false)
						->resize();

				if ($image->val('file_extension') !== 'gif' && (int)$image->val('file_dimension_width') > 1000) {
					$this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
							->setTargetFilePath($image->embeddedFile()->path())
							->setTargetFileWidth(1000)
							->setTargetFileHeight(null)
							->setWithCutoff(true)
							->resize();
				}

				$images_list = $get['id_price'];
				if (true) {
					$images_list = $this->renderPriceImagesList($get['id_price']);
				}

				$result = [
					'success' => true,
					'thumb_path' => $image->embededFileComponent()->iconLink('-160x160'),
					'image_id' => $image->id(),
					'images_list' => $images_list,
					'id_price' => $get['id_price']
				];
			}
		}

		die(json_encode($result));
	}

}
