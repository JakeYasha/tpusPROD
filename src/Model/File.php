<?php

namespace App\Model;

use Sky4\Model\Composite;

class File extends Composite {

	use Component\IdTrait,
	 Component\EmbeddedFileTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		return [
			'storage_id' => $this->fieldPropCreator()->intField('Хранилище')
		];
	}

	// -------------------------------------------------------------------------

	public function cols() {
		return [
			'file_raw_name',
			'file_dimension_size',
			'file_extension',
			'timestamp_inserting'
		];
	}
	
	public function defaultOrder() {
		return [
			'timestamp_inserting' => 'desc'
		];
	}

	public function defaultCutEnabled() {
		return true;
	}

	public function defaultInsertEnabled() {
		return false;
	}

	public function defaultEyeEnabled() {
		return true;
	}

	public function formStructure() {
		$c = $this->formStructureCreator();
		return [
			'storage_id',
			$c->component('EmbeddedFile'),
			$c->component('TimestampAction')
		];
	}

	public function icon() {
		return $this->fileComponent()->icon();
	}

	public function iconLink($file_postfix = null) {
		return $this->fileComponent()->iconLink($file_postfix);
	}

	public function iconTitle() {
		return $this->fileComponent()->iconTitle();
	}

	public function link($file_postfix = null) {
		return $this->fileComponent()->link($file_postfix);
	}

	public function name() {
		return $this->exists() ? $this->val('file_raw_name').'.'.$this->val('file_extension') : 'Файл';
	}

	public function orderableFieldsNames() {
		return [
			'file_dimension_size',
			'file_extension',
			'file_raw_name',
			'timestamp_inserting'
		];
	}

	public function relWithParentModel() {
		return [
			'keys' => ['storage_id' => 'id'],
			'model_alias' => 'file-storage'
		];
	}

	public function rels() {
		return [
			'file_storage' => [
				'keys' => ['storage_id' => 'id'],
				'model_alias' => 'file-storage',
				'title' => 'Файловое хранилище'
			]
		];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Файлы';
	}

}
