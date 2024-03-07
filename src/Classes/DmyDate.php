<?php

namespace App\Classes;

use CDate;
use CDateTime;
use CHtml;

class DmyDate extends \Sky4\Widget\InterfaceElem {

	public function processInputVal($field_name, &$field_props, &$vals) {
		$field_name = (string) $field_name;
		if (is_array($field_props) && is_array($vals)) {
			if (isset($vals[$field_name])) {
				if (preg_match('/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/iu', $vals[$field_name])) {
					$field_props['val'] = CDate::transformFrom($vals[$field_name]);
				} else {
					$field_props['val'] = $vals[$field_name];
				}
			} else {
				$field_props['val'] = CDate::nil();
			}
		}

		return $this;
	}

	public function processOutputVal($field_name, &$field_props, &$vals) {
		if (is_array($field_props) && is_array($vals)) {
			if (isset($vals[$field_name])) {
				$field_props['val'] = date('d.m.Y', CDateTime::toTimestamp($vals[$field_name]));
			} elseif (isset($field_props['default_val'])) {
				$field_props['val'] = CDate::transformTo($field_props['default_val']);
			} else {
				$field_props['val'] = date('d.m.Y');
			}
		}
		return $this;
	}

	public function render() {
		$class = $this->getClass($this->getClassPrefix() . 'text-field');
		if ($class) {
			$class .= ' js-date-field';
		} else {
			$class = 'js-date-field';
		}
		$this->setAttr('class', $class)
				->setAttr('name', $this->getName())
				->setAttr('type', 'text')
				->setValToAttrs()
				->setRulesToAttrs();
		return CHtml::tag('input', $this->getAttrs());
	}

}
