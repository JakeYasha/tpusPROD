<?php

namespace App\Model;

class MenuItem extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\NameTrait,
	 Component\NestedSetTrait,
	 Component\PositionWeightTrait,
	 Component\TimestampActionTrait;

	public function alias() {
		return 'menu-item';
	}

	public function editableFieldsNames() {
		return $this->fieldsNames();
	}

	public function cols() {
		return [
			'name' => [
				'label' => 'Название'
			],
			'alias' => [
				'label' => 'Алиас'
			],
			'link' => [
				'label' => 'Ссылка'
			],
			'position_weight' => [
				'label' => 'Вес',
				''
			],
		];
	}

	public function orderableFieldsNames() {
		return array_keys($this->cols());
	}

	public function fields() {
		return array(
			'alias' => array(
				'elem' => 'text_field',
				'label' => 'Алиас',
				'params' => array('length' => array('max' => 255))
			),
			'image_link' => array(
				'elem' => 'text_field',
				'label' => 'Ссылка на изображение',
				'params' => array('length' => array('max' => 255))
			),
			'link' => array(
				'elem' => 'text_field',
				'label' => 'Ссылка',
				'params' => array('length' => array('max' => 255))
			),
			'style' => array(
				'elem' => 'text_field',
				'label' => 'Стиль',
				'params' => array('length' => array('max' => 255))
			),
			'style_class' => array(
				'elem' => 'text_field',
				'label' => 'Стилевой класс',
				'params' => array('length' => array('max' => 255))
			)
		);
	}

	public function formStructure() {
		return array(
			array('type' => 'component', 'name' => 'Name'),
			array('type' => 'field', 'name' => 'link'),
			array('type' => 'field', 'name' => 'image_link'),
			array('type' => 'field', 'name' => 'style_class'),
			array('type' => 'field', 'name' => 'style'),
			array('type' => 'field', 'name' => 'alias'),
			array('type' => 'component', 'name' => 'PositionWeight'),
			array('type' => 'component', 'name' => 'TimestampAction')
		);
	}

	public function quickViewFieldsNames() {
		return array('id', 'name', 'link');
	}

	// -------------------------------------------------------------------------

	public function findByAlias($alias) {
		return $this->reader()
						->setWhere(['AND', '`alias` = :alias'], [':alias' => $alias])
						->objectByConds();
	}

	public function getItems($link) {
		$result = array();
		if ($this->exists()) {
			$items = $this->nestedSet()->getChildren((int) $this->val('node_level') + 1, null, null, '`position_weight` DESC');
			foreach ($items as $item) {
				$result[$item->id()] = array(
					'image_link' => $item->val('image_link'),
					'is_active' => ($item->val('link') === $link),
					'link' => $item->val('link'),
					'name' => $item->val('name'),
					'style' => $item->val('style'),
					'style_class' => $item->val('style_class'),
					'alias' => $item->val('alias'),
					'subitems' => array()
				);
				$subitems = $item->nestedSet()->getChildren((int) $item->val('node_level') + 1, null, null, '`position_weight` DESC');
				foreach ($subitems as $subitem) {
					$result[$item->id()]['subitems'][$subitem->id()] = array(
						'image_link' => $subitem->val('image_link'),
						'is_active' => ($subitem->val('link') === $link),
						'link' => $subitem->val('link'),
						'name' => $subitem->val('name'),
						'style' => $subitem->val('style'),
						'style_class' => $subitem->val('style_class'),
						'alias' => $subitem->val('alias'),
						'subitems' => array()
					);
					if ($subitem->val('link') === $link) {
						$result[$item->id()]['is_active'] = true;
					}
				}
			}
		}
		return $result;
	}

	public function defaultShortcutPasteEnabled() {
		return true;
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Меню';
	}

}
