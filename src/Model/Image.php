<?php

namespace App\Model;

use App\Model\Component\EmbeddedFileTrait;
use App\Model\Component\IdFirmTrait;
use App\Model\Component\IdTrait;
use App\Model\Component\TimestampActionTrait;
use Sky4\FileSystem\File;
use CFile;

class Image extends \Sky4\Model\Composite {

	use IdTrait,
	 IdFirmTrait,
	 TimestampActionTrait,
	 EmbeddedFileTrait;

	public function fields() {
		$c = $this->fieldPropCreator();

		return [
			'id_price' => [
				'col' => [
					'default_val' => '0',
					'flags' => 'not_null unsigned',
					'name' => 'id_price',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'id_price'
			],
			'source' => [
				'col' => [
					'flags' => 'not_null',
					'type' => "list('ratiss','client','temp','user')"
				],
				'elem' => 'radio_buttons',
				'label' => 'Источник данных',
				'options' => ['ratiss' => 'РАТИСС', 'client' => 'Кабинет клиента', 'temp' => 'Временный', 'user' => 'Пользователь']
			],
			'base_path' => [
				'elem' => 'text_field',
				'label' => 'Базовая папка',
				'default_val' => '/image'
			],
			'legacy_id_service' => $c->intField('тлен', 2, ['rules' => ['int']], ['flags' => 'not_null key']),
			'legacy_id_price' => $c->intField('тлен', 8, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
			'legacy_id_firm' => $c->intField('тлен', 4, ['rules' => ['int']], ['flags' => 'not_null key unsigned']),
			'name_hash' => $c->stringField('Hash', 255)
		];
	}

	public function path($postfix = '') {
		return '/image/' . $this->val('file_subdir_name') . '/' . $this->val('file_name') . $postfix . '.' . $this->val('file_extension');
	}

	public function link($postfix = '') {
		return $this->val('base_path') . '/' . $this->val('file_subdir_name') . '/' . $this->val('file_name') . $postfix . '.' . $this->val('file_extension');
	}

	public function iconLink() {
		if ($this->val('base_path') === '/uploaded/photo-contest') {
			return $this->link('-160');
		}

		return $this->link('-thumb');
	}

	public function thumb() {
		return $this->link('-160x160');
	}

	public function delete() {
		$has_clones = $this->reader()
				->setWhere(['AND', 'file_name = :file_name', 'id != :id'], [':file_name' => $this->val('file_name'), ':id' => $this->id()])
				->count();

		if (!$has_clones) {
			$this->embededFileComponent()->setSubDirName('image');
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
		}

		return parent::delete();
	}

}
