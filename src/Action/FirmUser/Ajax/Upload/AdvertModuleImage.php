<?php

namespace App\Action\FirmUser\Ajax\Upload;

use App\Model\AdvertModule;
use App\Model\FirmFile;
use Sky4\Exception;
use Sky4\FileSystem\Dir;
use const APP_DIR_PATH;
use function app;

class AdvertModuleImage extends \App\Action\FirmUser\Ajax\Upload {

	public function execute() {
		$get = app()->request()->processGetParams([
			'id' => ['type' => 'int'],
		]);

		$am = new AdvertModule($get['id']);
		if (!app()->firmUser()->exists() || ($am->exists() && !($am->id_firm() === app()->firmUser()->id_firm() && $am->id_service() === app()->firmUser()->id_service()))) {
			throw new Exception();
		}

		$dir = new Dir(APP_DIR_PATH . '/public/file');
		$dir->create();
		$this->fileUploader()->setFileDirPath($dir->getPath());

		$result = ['success' => false];
		if ($file_uploader->uploadFile()) {
			$file_data = $file_uploader->getFileData();
			$vals = [
				'file_dimension_height' => $file_data['dimension_height'],
				'file_dimension_size' => $file_data['dimension_size'],
				'file_dimension_width' => $file_data['dimension_width'],
				'file_extension' => $file_data['extension'],
				'file_name' => $file_data['name'],
				'file_raw_name' => $file_data['raw_name'],
				'file_subdir_name' => implode('/', $file_data['subdirs_names']),
				'type' => 'advert-module-image',
				'id_firm' => app()->firmUser()->id_firm(),
				'flag_is_temp' => 1
			];

			$image = new FirmFile();
			$image->embededFileComponent()->setSubDirName('file');
			$image->insert($vals);

			if ($image->embeddedFile()->isImage()) {

				$this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
						->setTargetFilePath($image->embeddedFile()->path('-thumb'))
						->setTargetFileWidth(120)
						->setTargetFileHeight(90)
						->setWithCutoff(false)
						->resize();

				if ($image->val('file_extension') !== 'gif' && (int) $image->val('file_dimension_width') > 1000) {
					$this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
							->setTargetFilePath($image->embeddedFile()->path())
							->setTargetFileWidth(1000)
							->setTargetFileHeight(null)
							->setWithCutoff(true)
							->resize();
				}

				$result = [
					'success' => true,
					'thumb_path' => $image->embededFileComponent()->iconLink('-thumb'),
					'image_id' => $image->id(),
					'composite_id' => 'firm-file~' . $image->id(),
					'image_type' => 'advert-module-image'
				];
			}
		}

		die(json_encode($result));
	}

}
