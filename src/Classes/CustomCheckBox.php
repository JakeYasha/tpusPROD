<?php

namespace App\Classes;

class CustomCheckBox extends \Sky4\Widget\InterfaceElem {

	public function getColProps() {
		return array(
			'default_val' => '0',
			'flags' => 'not_null unsigned',
			'type' => 'int_1'
		);
	}

	public function render() {
		$this->setAttr('class', $this->getClass($this->getClassPrefix() . 'check-box'))
				->setAttr('name', $this->getName())
				->setAttr('type', 'checkbox')
				->setValToAttrs();
		return '<label><input' . \Sky4\Helper\Html::renderAttrs($this->getAttrs()) . ' />' . $this->getLabel() . '</label>';
	}

}
