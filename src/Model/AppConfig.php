<?php

namespace App\Model;

use App\Classes\Assist,
	Sky4\Helper\Html,
	Sky4\Helper\StringHelper,
	Sky4\Model\Composite;

class AppConfig extends Composite {

	use Component\IdTrait,
	 Component\NameTrait,
	 Component\AdjacencyListTrait,
	 Component\TimestampActionTrait;

	public function fields() {
		$c = $this->fieldPropCreator();
		return [
			'app' => [
				'default_val' => 'app',
				'elem' => 'text_field',
				'label' => 'Приложение'
			],
			'flag_show_only_in_dev_mode' => $c->singleCheckBox('Показывать только в dev-режиме?'),
			'key' => $c->textField('Ключ'),
			'type' => $c->dropDownList_typeList('Тип', $this->types()),
			'val_boolean' => $c->singleCheckBox('Значение (булевое)'),
			'val_double' => $c->textField('Значение (дробное число)', ['rules' => ['double']]),
			'val_int' => $c->intField('Значение (целое число)'),
			'val_string' => $c->stringField('Значение (строка)'),
			'val_text' => $c->textArea('Значение (текст)', ['parser' => true])
		];
	}

	// -------------------------------------------------------------------------

	public function cols() {
		$cols = ['name', 'key'];
		if (defined('APP_IS_DEV_MODE') && APP_IS_DEV_MODE) {
			$cols[] = 'type';
		}
		$cols['val'] = [
			'label' => 'Значение',
			'name' => 'renderVal',
			'type' => 'method'
		];
		return $cols;
	}

	public function defaultCutPasteEnabled() {
		return true;
	}

	public function defaultInsertEnabled() {
		return (defined('APP_IS_DEV_MODE') && APP_IS_DEV_MODE);
	}

	public function defaultModalFormEnabled() {
		return true;
	}

	public function defaultOrder() {
		return ['name' => 'ASC'];
	}

	public function form() {
		$form = parent::form();
		if (!$this->exists()) {
			// @todo Убрать в Cms.
			$assist = new Assist();
			$object = $assist->getObject();
			if (is_object($object) && ($object instanceof AppConfig) && $object->val('key')) {
				$form->setVal('key', $object->val('key') . '.');
			}
		}
		return $form;
	}

	public function formStructure() {
		$result = [];
		if (defined('APP_IS_DEV_MODE') && APP_IS_DEV_MODE) {
			$result = [
				'app',
				'flag_show_only_in_dev_mode',
				'name',
				'key',
				'type',
				'val_string',
				'val_text',
				'val_int',
				'val_double',
				'val_boolean'
			];
		} else {
			$result[] = 'name';
			switch ($this->val('type')) {
				case 'boolean':
				case 'double':
				case 'int':
				case 'text':
					$result[] = 'val_' . $this->val('type');
					break;
				default:
					$result[] = 'val_string';
					break;
			}
			$result[] = 'key';
		}
		return $result;
	}

	public function getEditableFieldsNames() {
		if (!(defined('APP_IS_DEV_MODE') && APP_IS_DEV_MODE)) {
			$result = ['name'];
			switch ($this->val('type')) {
				case 'boolean' :
				case 'double' :
				case 'int' :
				case 'text' :
					$result[] = 'val_' . $this->val('type');
					break;
				default :
					$result[] = 'val_string';
					break;
			}
			return $result;
		}
		return parent::getEditableFieldsNames();
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Настройки';
	}

	public function types() {
		return [
			'' => 'Строка',
			'boolean' => 'Булевое значение',
			'double' => 'Дробное число',
			'int' => 'Целое число',
			'text' => 'Текст'
		];
	}

	// -------------------------------------------------------------------------

	public function getItems() {
		$result = [];
		$items = $this->reader()->rows();
		foreach ($items as $item) {
			$app = (string) $item['app'];
			if (!isset($result[$app])) {
				$result[$app] = [];
			}
			$key = StringHelper::toLower(StringHelper::trim($item['key']));
			$result[$app][$key] = [
				'name' => (string) $item['name'],
				'type' => (string) $item['type'],
				'val' => null
			];
			switch ($result[$app][$key]['type']) {
				case 'boolean':
					$result[$app][$key]['val'] = ((int) $item['val_boolean'] === 1) ? true : false;
					break;
				case 'double':
					$result[$app][$key]['val'] = (double) $item['val_double'];
					break;
				case 'int':
					$result[$app][$key]['val'] = (int) $item['val_int'];
					break;
				case 'text':
					$result[$app][$key]['val'] = (string) $item['val_text'];
					break;
				case '':
					$result[$app][$key]['val'] = (string) $item['val_string'];
					break;
			}
		}
		return $result;
	}

	public function renderVal() {
		$result = '';
		switch ($this->val('type')) {
			case 'boolean':
				$result = ((int) $this->val('val_boolean') === 1) ? 'Да' : 'Нет';
				break;
			case 'double':
				$result = (double) $this->val('val_double');
				break;
			case 'int':
				$result = (int) $this->val('val_int');
				break;
			case 'text':
				$result = Html::encode((string) $this->val('val_text'));
				break;
			case '':
				$result = Html::encode((string) $this->val('val_string'));
				break;
		}
		return $result;
	}

}
