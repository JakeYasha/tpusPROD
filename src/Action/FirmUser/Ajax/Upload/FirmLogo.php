<?php

namespace App\Action\FirmUser\Ajax\Upload;

use App\Action\FirmUser\Ajax\Upload;
use App\Model\Firm;
use App\Model\Image;
use Sky4\FileSystem\Dir;
use const APP_DIR_PATH;
use function app;

class FirmLogo extends Upload {

	public function execute() {
		$get = app()->request()->processGetParams([
			'id_firm' => ['type' => 'int']
		]);

		$dir = new Dir(APP_DIR_PATH . '/public/image');
		$dir->create();

		$this->fileUploader()->setFileDirPath($dir->getPath())
				->setAllowedFileExtensions(['jpg', 'png', 'gif']);

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
				'id_price' => 0,
				'source' => 'client'
			];

			$image = new Image();
			$image->embededFileComponent()->setSubDirName('image');
			$image->insert($vals);

			if ($image->embeddedFile()->isImage()) {
				$this->imageProcessor()->setSourceFilePath($image->embeddedFile()->path())
						->setTargetFilePath($image->embeddedFile()->path())
						->setTargetFileWidth(160)
						->setTargetFileHeight(null)
						->setWithCutoff(false)
						->resize();

				$firm = new Firm();
				$firm->getByIdFirm($image->id_firm());
				$firm->update(['file_logo' => $image->embededFileComponent()->iconLink()]);

				$result = [
					'success' => true,
					'thumb_path' => $image->embededFileComponent()->iconLink(),
					'image_id' => $image->id()
				];
			}
		}

		die(json_encode($result));
	}

}
