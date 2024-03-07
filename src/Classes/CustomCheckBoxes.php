<?php

namespace App\Classes;

class CustomCheckBoxes extends \Sky4\Widget\InterfaceElem {

	public function getColProps() {
		return array(
			'default_val' => '',
			'flags' => 'not_null',
			'type' => 'string(255)'
		);
	}

	public function processInputVal($field, &$field_props, &$vals) {
		$field = (string) $field;
		if (is_array($field_props) && is_array($vals)) {
			$field_props['val'] = isset($vals[$field]) ? $this->implodeVals($vals[$field]) : '';
		} else {
			throw new \Sky4\Exception();
		}
		return $this;
	}

	public function processOutputVal($field, &$field_props, &$vals) {
		$field = (string) $field;
		if (is_array($field_props) && is_array($vals)) {
			if (isset($vals[$field])) {
				$field_props['val'] = is_array($vals[$field]) ? $vals[$field] : $this->explodeVals($vals[$field]);
			}
		} else {
			throw new \Sky4\Exception();
		}
		return $this;
	}

	public function render() {
		$attrs = $this->getAttrs();
		$name = $this->getName() . '[]';
		$options = $this->getOptions();
		$vals = $this->getVal();
		if (!is_array($vals)) {
			$vals = $this->explodeVals($vals);
		}

		$block_attrs = array();
		if (isset($attrs['block_class']) && (string) $attrs['block_class']) {
			$block_attrs['class'] = $this->getClassPrefix() . 'check-boxes';
			$block_attrs['class'] .= ' ' . (string) $attrs['block_class'];
			unset($attrs['block_class']);
		} else {
			$block_attrs['class'] = $this->getClassPrefix() . 'check-boxes';
		}
		if (isset($attrs['block_style'])) {
			$block_attrs['style'] = $attrs['block_style'];
			unset($attrs['block_style']);
		}

		$result = \Sky4\Helper\Html::openTag('div', $block_attrs) . '<ul>';
		foreach ($options as $val => $label) {
			$check_box = new \App\Classes\CustomCheckBox();
			$check_box->setAttrs($attrs)
					->setLabel($label)
					->setName($name)
					->setVal($val);
			if (in_array($val, $vals)) {
				$check_box->setAttr('checked', 'true');
			}
			$result .= '<li>' . $check_box->render() . '</li>';
		}
		return $result . '</ul></div>';
	}

	// -------------------------------------------------------------------------

	protected function explodeVals($vals) {
		$vals = str()->trim($vals);
		if ($vals) {
			return explode(',', $vals);
		}
		return array();
	}

	protected function implodeVals($vals) {
		return implode(',', (array) $vals);
	}

}
