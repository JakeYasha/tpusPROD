<?php

namespace App\Action\FirmUser\Ajax;

class Upload extends \App\Action\FirmUser\Ajax {

	protected $file_uploader = null;
	protected $image_processor = null;

	public function execute() {
		throw new \Sky4\Exception();
	}

	/**
	 * 
	 * @return \Sky4\Component\DeprecatedFileUploader
	 */
	protected function fileUploader() {
		if ($this->file_uploader === null) {
			$this->file_uploader = new \Sky4\Component\DeprecatedFileUploader();
			$this->file_uploader->setMaxFileSize(25 * 1024 * 1024)
					->setMinFileSize(1)
					->setUseAutoSubdirs(true)
					->setVarName('files');
		}

		return $this->file_uploader;
	}

	protected function imageProcessor() {
		if ($this->image_processor === null) {
			$this->image_processor = class_exists('Imagick', false) ? new \Sky4\Component\ImageProcessor\Imagick() : new \Sky4\Component\ImageProcessor\Gd();
		}
		return $this->image_processor;
	}

}
