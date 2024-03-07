<?php

namespace App\Model;

class Page extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\ExtendedNameTrait,
	 Component\ExtendedTextTrait,
	 Component\MetadataTrait,
	 Component\NestedSetTrait,
	 Component\StateTrait,
	 Component\TimestampActionTrait;

	public function defaultCopyPasteEnabled() {
		return true;
	}

	public function defaultEyeEnabled() {
		return true;
	}

	public function beforeInsert(&$vals, $parent_object = null) {
		if (isset($vals['name_in_url']) && !$vals['name_in_url']) {
			$vals['name_in_url'] = str()->translit($vals['name']) . self::urlPostfix();
		}

		return parent::beforeInsert($vals, $parent_object);
	}

	public function beforeUpdate(&$vals) {
		if (isset($vals['name_in_url']) && !$vals['name_in_url']) {
			$vals['name_in_url'] = str()->translit($vals['name']) . self::urlPostfix();
		}

		return parent::beforeUpdate($vals);
	}

	public function editableFieldsNames() {
		return $this->fieldsNames();
	}

	public function fields() {
		return array(
			'files' => [
				'col' => [
					'default_val' => '',
					'flags' => 'not_null',
					'type' => 'string(255)'
				],
				'elem' => 'media_selector',
				'label' => 'Файлы'
			],
			'show_sub_page' => [
				'elem' => 'single_check_box',
				'label' => 'Выводить вложенные страницы',
				'default_val' => true
			]
		);
	}

	public function formStructure() {
		return array(
			array('type' => 'tab', 'name' => 'texts', 'label' => 'Тексты'),
			array('type' => 'tab', 'name' => 'files', 'label' => 'Файлы'),
			array('type' => 'tab', 'name' => 'metadata', 'label' => 'Метаданные'),
			array('type' => 'component', 'name' => 'ExtendedName'),
			array('type' => 'component', 'name' => 'State'),
			array('type' => 'field', 'name' => 'show_sub_page'),
			array('type' => 'component', 'name' => 'TimestampAction'),
			array('type' => 'field', 'name' => 'files', 'tab_name' => 'files'),
			array('type' => 'component', 'name' => 'ExtendedText', 'tab_name' => 'texts'),
			array('type' => 'component', 'name' => 'Metadata', 'tab_name' => 'metadata')
		);
	}

	private static function urlPostfix() {
		return '.htm';
	}

	public function cols() {
		return [
			'name' => [
				'label' => 'Название'
			],
			'name_in_url' => [
				'label' => 'Ссылка'
			]
		];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Страницы';
	}

	public function quickViewFieldsNames() {
		return array('id', 'name');
	}

	public function defaultShortcutCopyEnabled() {
		return true;
	}

	public function linkItem() {
		if ($this->val('name_in_url')) return '/' . $this->alias() . '/show/' . $this->val('name_in_url');
		return parent::linkItem();
	}

	public function filterFields() {
		return [
			'name' => array(
				'elem' => 'text_field',
				'field_name' => 'name',
				'label' => 'Название страницы'
			)
		];
	}

	public function filterFormStructure() {
		return [
			['type' => 'field', 'name' => 'name']
		];
	}

}
