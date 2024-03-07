<?php

namespace App\Action\FirmUser\Ajax\Upload;

use App\Model\FirmFile;
use Sky4\FileSystem\Dir;
use const APP_DIR_PATH;
use function app;

class FirmFiles extends \App\Action\FirmUser\Ajax\Upload {

	public function execute() {
		$get = app()->request()->processGetParams([
			'id_firm' => ['type' => 'int'],
			'id_service' => ['type' => 'int']
		]);

		$dir = new Dir(APP_DIR_PATH . '/public/file');
		$dir->create();

		$this->fileUploader()->setFileDirPath($dir->getPath())
				->setAllowedFileExtensions(['xls', 'xlsx', 'doc', 'docx', 'pdf', 'odt', 'ods']);

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
				'type' => 'file',
				'flag_is_temp' => 0
			];

			$file = new FirmFile();
			$file->embededFileComponent()->setSubDirName('file');
			$file->insert($vals);

			$result = [
				'success' => true,
				'name' => $file->val('file_raw_name'),
				'link' => $file->path(),
				'thumb_path' => $file->thumb(),
				'image_id' => $file->id()
			];
		} else {
			$result = [
				'success' => false,
				'error_message' => 'Файл не должен быть больше 10 Mb'
			];
		}

		die(json_encode($result));
	}

}
