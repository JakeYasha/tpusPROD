<?php

namespace App\Classes;

use CHiddenField;
use CHtml;

class Autocomplete extends \Sky4\Widget\InterfaceElem\Autocomplete {

	public function render() {
		$class = $this->getClass($this->getClassPrefix() . 'text-field');
//		if ($class) {
//			$class .= ' js-autocomplete';
//		} else {
//			$class = 'js-autocomplete';
//		}
		$field_name = $this->getParam('field_name', '');
		$model_alias = $this->getParam('model_alias', '');
		$val_mode = ($this->getParam('val_mode', 'id') === 'val') ? 'val' : 'id';
		$settings = $this->getParam('settings', '');

		$this->setAttr('class', $class)
				->setAttr('type', 'text')
				->setAttr('data-field-name', $field_name)
				->setAttr('data-model-alias', $model_alias)
				->setAttr('data-val-mode', $val_mode)
				->setAttr('data-settings', $settings);
		if ($val_mode === 'id') {
			$this->setAttr('data-name', $this->getName())
					->setExtraValToAttrs();
			$hidden_field = new CHiddenField();
			$hidden_field->setName($this->getName())
					->setVal($this->getVal());
			return CHtml::tag('input', $this->getAttrs()) . $hidden_field->render();
		} else {
			$this->setAttr('name', $this->getName())
					//->setRulesToAttrs()
					->setValToAttrs();
			return CHtml::tag('input', $this->getAttrs());
		}
	}

}
