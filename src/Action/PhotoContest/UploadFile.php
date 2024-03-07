<?php

namespace App\Action\PhotoContest;

class UploadFile extends \App\Action\PhotoContest {

	public function execute() {
		$get = app()->request()->processGetParams([
			'id_contest' => ['type' => 'int'],
		]);

		$this->model()->reader()->object($get['id_contest']);

		if ($this->model()->exists() && $this->model()->isWorking()) {
			$dir = new \Sky4\FileSystem\Dir(APP_DIR_PATH . '/public/uploaded/photo-contest');
			$dir->create();

			$file_uploader = new \Sky4\Component\DeprecatedFileUploader();
			$file_uploader->setFileDirPath($dir->getPath())
					->setMaxFileSize(10 * 1024 * 1024)
					->setMinFileSize(1)
					->setUseAutoSubdirs(true)
					->setVarName('files');

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
					'source' => 'temp',
					'base_path' => '/uploaded/photo-contest'
				];

				$image = new \App\Model\Image();
				$image->embededFileComponent()->setSubDirName('uploaded/photo-contest');
				$image->insert($vals);

				if ($image->embeddedFile()->isImage()) {
					$image_processor = class_exists('Imagick', false) ? new \Sky4\Component\ImageProcessor\Imagick() : new \Sky4\Component\ImageProcessor\Gd();
					$image_processor->setSourceFilePath($image->embeddedFile()->path());
					if ($image_processor->getSourceFileWidth() > 1920) {
						$image_processor->setSourceFilePath($image->embeddedFile()->path())
								->setTargetFilePath($image->embeddedFile()->path())
								->setTargetFileWidth(1920)
								->setTargetFileHeight(null)
								->setWithCutoff(false)
								->resize();
					}

					$image_processor->setSourceFilePath($image->embeddedFile()->path())
							->setTargetFilePath($image->embeddedFile()->path('-160x160'))
							->setTargetFileWidth(160)
							->setTargetFileHeight(160)
							->setWithCutoff(true)
							->resize();

					$result = [
						'success' => true,
						'thumb_path' => $image->path('-160x160'),
						'image_id' => $image->id(),
						'selector' => '.js-photo-contest-image-holder',
						'content' => '<img src="/uploaded/photo-contest/' . $image->val('file_subdir_name') . '/' . $image->val('file_name') . '-160x160.' . $image->val('file_extension') . '" />'
					];

					$_SESSION['photo-contest']['photos'][] = $image->id();
				}
			}
		}



		die(json_encode($result));
	}

}
