<?php

namespace App\Action\FirmUser\Ajax\Upload;

use App\Model\FirmFile;
use Sky4\FileSystem\Dir;
use const APP_DIR_PATH;
use function app;

class FirmImages extends \App\Action\FirmUser\Ajax\Upload {

	public function execute() {
		$get = app()->request()->processGetParams([
			'id_firm' => ['type' => 'int'],
			'id_service' => ['type' => 'int']
		]);

		$dir = new Dir(APP_DIR_PATH . '/public/file');
		$dir->create();

		$this->fileUploader()->setFileDirPath($dir->getPath());

		$result = ['success' => false];
		if ($this->fileUploader()->uploadFile()) {
			$file_data = $this->fileUploader()->getFileData();
			$vals = [
				'file_dimension_height' => $file_data['dimension_height'],
				'file_dimension_size' => $file_data['dimension_size'],
				'file_dimension_width' => $file_data['dimension_width'],
				'file_extension' => $file_data['extension'],
				'file_name' => $file_data['name'],
				'file_raw_name' => $file_data['raw_name'],
				'file_subdir_name' => implode('/', $file_data['subdirs_names']),
				'id_firm' => $get['id_firm'],
				'type' => 'image',
				'flag_is_temp' => 0
			];

			$image = new FirmFile();
			$image->embededFileComponent()->setSubDirName('file');
			$image->insert($vals);

			if ($image->embeddedFile()->isImage()) {
				$this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
						->setTargetFilePath($image->embeddedFile()->path('-330x200'))
						->setTargetFileWidth(330)
						->setTargetFileHeight(200)
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
					'thumb_path' => $image->embededFileComponent()->iconLink('-330x200'),
					'image_id' => $image->id()
				];
			}
		}

		die(json_encode($result));
	}

}
