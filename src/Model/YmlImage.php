<?php

namespace App\Model;

use App\Model\Component\EmbeddedFileTrait;
use App\Model\Component\IdFirmTrait;
use App\Model\Component\IdTrait;
use App\Model\Component\TimestampActionTrait;
use Sky4\FileSystem\File;

class YmlImage extends \Sky4\Model\Composite {

	use IdTrait,
	 IdFirmTrait,
	 TimestampActionTrait,
	 EmbeddedFileTrait;

	public function fields() {
		$c = $this->fieldPropCreator();
		return [
			'id_yml' => $c->intField('ID предложения из YML', 8),
			'base_path' => [
				'elem' => 'text_field',
				'label' => 'Базовая папка',
				'default_val' => '/yml_image'
			]
		];
	}

	public function path($postfix = '') {
		return '/yml_image/' . $this->val('file_subdir_name') . '/' . $this->val('file_name') . $postfix . '.' . $this->val('file_extension');
	}

	public function link($postfix = '') {
		return $this->val('base_path') . '/' . $this->val('file_subdir_name') . '/' . $this->val('file_name') . $postfix . '.' . $this->val('file_extension');
	}

	public function iconLink() {
		return $this->link('-thumb');
	}

	public function thumb() {
		return $this->link('-160x160');
	}

	public function delete() {
		$this->embededFileComponent()->setSubDirName('yml_image');
		$file1 = new File($this->embededFileComponent()->path());
		$file2 = new File($this->embededFileComponent()->path('-160'));
		$file3 = new File($this->embededFileComponent()->path('-160x160'));
		$file4 = new File($this->embededFileComponent()->path('-260x260'));
		$file5 = new File($this->embededFileComponent()->path('-1000'));

		$file1->remove();
		$file2->remove();
		$file3->remove();
		$file4->remove();
		$file5->remove();

		return parent::delete();
	}

}
