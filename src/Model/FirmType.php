<?php

namespace App\Model;

class FirmType extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\AdjacencyListTrait,
	 Component\TextTrait,
	 Component\NameTrait,
	 Component\PositionWeightTrait,
	 Component\MetadataTrait;

	public function cols() {
		if ($this->val('node_level') < 2) {
			return [
				'name' => ['label' => $this->val('node_level') === 1 ? 'Подтип' : 'Тип'],
				'position_weight' => ['label' => 'Сортировка'],
				'case' => ['label' => 'Падеж города']
			];
		} else {
			return [
				'name' => ['label' => 'Ключевое слово'],
				'flag_is_suggest' => ['label' => 'Подсказка', 'type' => 'flag']
			];
		}
	}

	public function formStructure() {
		$structure = [
			['type' => 'field', 'name' => 'name'],
			['type' => 'field', 'name' => 'position_weight'],
			['type' => 'field', 'name' => 'case'],
			['type' => 'field', 'name' => 'advert_restrictions'],
		];
		if ($this->val('node_level') < 3) {
			$structure[] = ['type' => 'field', 'name' => 'text', 'label' => 'Информация о типе'];
			$structure[] = ['type' => 'field', 'name' => 'text_bottom', 'label' => 'Нижний текст'];
			$structure[] = ['type' => 'tab', 'name' => 'meta', 'label' => 'Метатеги'];
			$structure[] = ['type' => 'component', 'name' => 'Metadata', 'tab_name' => 'meta'];
		} else {
			$structure[] = ['type' => 'field', 'name' => 'flag_is_suggest'];
		}

		return $structure;
	}

	public function fields() {
		return [
			'count' => [
				'col' => [
					'flags' => 'unsigned',
					'type' => 'int_4'
				],
				'elem' => 'text_field',
				'label' => 'Количество фирм',
				'params' => [
					'rules' => ['int']
				]
			],
			'flag_is_suggest' => [
				'elem' => 'single_check_box',
				'label' => 'В автодополнении поиска',
				'default_val' => 0
			],
			'text' => [
				'attrs' => ['rows' => '10'],
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_4'
				],
				'elem' => 'tiny_mce',
				'label' => 'Текст',
				'params' => [
					'parser' => true
				]
			],
			'text_bottom' => [
				'attrs' => ['rows' => '10'],
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_4'
				],
				'elem' => 'tiny_mce',
				'label' => 'Нижний текст',
				'params' => [
					'parser' => true
				]
			],
			'case' => [
				'col' => [
					'flags' => 'unsigned',
					'type' => 'int_1'
				],
				'elem' => 'drop_down_list',
				'label' => 'Падеж города',
				'params' => [
					'rules' => ['int']
				],
				'options' => self::cases()
			],
			'advert_restrictions' => [
				'col' => [
					'flags' => 'not_null',
					'name' => 'advert_restrictions',
					'type' => 'int_4',
				],
				'elem' => 'drop_down_list',
				'label' => 'Рекламные ограничения',
				'options' => \Sky4\Container::getList('AdvertRestrictions'),
			]
		];
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Типы фирм';
	}

	public static function cases($id = null) {
		$array = [
			1 => 'Ярославля',
			2 => 'в Ярославле'
		];

		if ($id !== null) return $array[$id];
		return $array;
	}

	public function linkTag() {
		if ((int)$this->val('parent_node') === 0) {
			return app()->link('/firm/bytype/'.$this->id().'/');
		}
		return app()->link('/firm/bytype/'.$this->val('parent_node').'/'.$this->id().'/');
	}

	public function link($query = null) {
		if ($query !== null) {
			return app()->linkFilter('/search/firms/', ['query' => $query, 'id_type' => $this->id()]);
		}
		if ((int)$this->val('parent_node') === 0) {
			return '/firm/bytype/'.$this->id().'/';
		}
		return '/firm/bytype/'.$this->val('parent_node').'/'.$this->id().'/';
	}

	public function getByFirm(Firm $firm) {
		$res = [];

		$fft = new FirmFirmType();
		$firm_type_ids = $fft->getByFirm($firm);

		if ($firm_type_ids) {
			$res = $this->reader()
					->setOrderBy('`count` DESC')
					->objectsByIds(array_keys($firm_type_ids));
		}

		return $res;
	}

	public function name() {
		return $this->val('name');
	}

	public function delete() {
		$this->deleteRtIndex();
		return parent::delete();
	}

	public function afterUpdate(&$vals) {
		$result = parent::afterUpdate($vals);
		$this->updateRtIndex();
		return $result;
	}

	public function afterInsert(&$vals, $parent_object = null) {
		$result = parent::afterInsert($vals, $parent_object);
		$this->updateRtIndex();
		return $result;
	}

	public function updateRtIndex($sphinx = null) {
		if ($sphinx === null) {
			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
		}

		$row = [
			'id' => $this->id(),
			'name' => $this->val('name'),
			'text' => $this->val('text'),
			'text_bottom' => $this->val('text_bottom'),
			'node_level' => $this->val('node_level'),
			'parent_node' => $this->val('parent_node')
		];

		$sphinx->replace()
				->into(SPHINX_FIRM_CATALOG_INDEX)
				->set($row)
				->execute();

		return $this;
	}

	public function deleteRtIndex($sphinx = null) {
		if ($sphinx === null) {
			$sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
		}

		$sphinx->delete()
				->from(SPHINX_FIRM_CATALOG_INDEX)
				->where('id', '=', (int)$this->id())
				->execute();

		return $this;
	}

}
