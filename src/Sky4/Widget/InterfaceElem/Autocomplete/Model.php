<?php

namespace Sky4\Widget\InterfaceElem\Autocomplete;

use Sky4\Helper\Html,
	Sky4\Model\Utils as ModelUtils,
	Sky4\Utils,
	Sky4\Widget\InterfaceElem;

class Model extends InterfaceElem {

	public function getColProps() {
		return [
			'default_val' => 0,
			'flags' => 'not_null unsigned',
			'type' => 'int_4'
		];
	}

	public function render() {
		$class = $this->getClass($this->getClassPrefix().'text-field');
		if ($class) {
			$class .= ' js-model-autocomplete';
		} else {
			$class = 'js-model-autocomplete';
		}

		$field_name = $this->getParam('field_name', '');
		$model_alias = $this->getParam('model_alias', '');

		$this->setAttr('class', $class)
				->setAttr('name', $this->getName())
				->setAttr('type', 'text')
				->setAttr('data-field-name', $field_name)
				->setAttr('data-model-alias', $model_alias)
				->setAttr('data-rel-model-alias', $this->getParam('rel_model_alias', ''))
				->setAttr('data-rel-field-name-1', $this->getParam('rel_field_name_1', ''))
				->setAttr('data-rel-field-name-2', $this->getParam('rel_field_name_2', ''))
				->setRulesToAttrs()
				->setPrefill();

		return Html::tag('input', $this->getAttrs());
	}

	public function saveRels($field_name, &$field_props, &$vals) {
		$rel_model = Utils::getModelClass($this->getParam('rel_model_alias', ''));
		$rel_field_name_1 = $this->getParam('rel_field_name_1', '');
		$rel_field_name_2 = $this->getParam('rel_field_name_2', '');
		if ($rel_model->fieldExists($rel_field_name_1) && $rel_model->fieldExists($rel_field_name_2) && isset($vals[$field_name])) {
			$rel_model->deleteAll('`'.$rel_field_name_1.'` = :id', null, null, null, [':id' => $this->model()->id()]);
			$ids = explode(',', trim($vals[$field_name], ','));
			foreach ($ids as $i => $id) {
				if (!$id) {
					unset($ids[$i]);
				}
			}
			$ids = array_values($ids);
			if ($ids) {
				foreach ($ids as $id) {
					$rel_model->resetId();
					$rel_model->insert([
						$rel_field_name_1 => $this->model()->id(),
						$rel_field_name_2 => $id
					]);
				}
			}
		}
	}

	public function setPrefill() {
		$rel_model = Utils::getModelClass($this->getParam('rel_model_alias', ''));
		$rel_field_name_1 = $this->getParam('rel_field_name_1', '');
		$rel_field_name_2 = $this->getParam('rel_field_name_2', '');
		$rel_model_field_id = $this->getParam('rel_model_field_id', 'id');
		$field_name = $this->getParam('field_name', '');

		if ($rel_field_name_1 === $rel_field_name_2) {
			$ids = array_filter(explode(',', $this->getVal()));
			if ($ids) {
				$model = Utils::getModelClass($this->getParam('model_alias', ''));
				$items = $model->reader()->objectsByIds(array_filter(explode(',', $this->getVal())));
				$json = [];
				foreach ($items as $item) {
					$json[] = [
						'id' => $item->id(),
						'name' => $item->name()
					];
				}
				$this->setAttr('data-prefill', json_encode($json));
			}
		} else {
			if ($rel_model->fieldExists($rel_field_name_1) && $rel_model->fieldExists($rel_field_name_2)) {
				$rels = $rel_model->reader()
						->setSelect('`'.$rel_field_name_2.'` AS `object_id`')
						->setWhere('`'.$rel_field_name_1.'` = :id', [':id' => $this->model()->id()])
						->rowsWithKey('object_id');

				if ($rels) {
					if ($field_name === 'web_many_name_for_subgroups') {
						$model = Utils::getModelClass($this->getParam('model_alias', ''));
						$conds = ModelUtils::prepareWhereCondsFromArray(array_keys($rels), $rel_model_field_id);
						$items = $model->reader()
								->setWhere(['AND','node_level = :node_level', $conds['where']], [':node_level' => 2] + $conds['params'])
								->objects();

						$json = [];
						foreach ($items as $item) {
							$json[] = [
								'id' => $item->val('id_subgroup'),
								'name' => $item->name()
							];
						}
					} else {
						$model = Utils::getModelClass($this->getParam('model_alias', ''));
						$conds = ModelUtils::prepareWhereCondsFromArray(array_keys($rels), $rel_model_field_id);
						$items = $model->reader()
								->setWhere($conds['where'], $conds['params'])
								->objects();

						$json = [];
						foreach ($items as $item) {
							$json[] = [
								'id' => $item->id(),
								'name' => $item->name()
							];
						}
					}


					$this->setAttr('data-prefill', json_encode($json));
				}
			}
		}


		return $this;
	}

}
