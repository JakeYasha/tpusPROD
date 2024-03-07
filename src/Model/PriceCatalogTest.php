<?php

namespace App\Model;

class PriceCatalogTest extends \Sky4\Model\Composite {

	use Component\IdTrait,
	 Component\AdjacencyListTrait,
	 Component\MetadataTrait;

	public function defaultOrder() {
		return ['name' => 'ASC'];
	}

	public function defaultCopyPasteEnabled() {
		return true;
	}

	public function orderableFieldsNames() {
		return ['name', 'web_name', 'web_many_name'];
	}

	public function parent_node() {
		return (int) $this->val('parent_node');
	}

	public function cols() {
		if ((int) $this->val('node_level') > 1) {
			$cols['web_many_name'] = ['label' => 'Мн.ч', 'type' => 'text_field'];
			//$cols['web_name'] = ['label' => 'Ед.ч', 'type' => 'text_field'];
			$cols['name'] = ['label' => 'Ключ', 'type' => 'text_field'];
			$cols['flag_is_catalog'] = ['label' => 'Показывать в каталоге?', 'type' => 'flag'];
		} else {
			$cols = [
				'web_many_name' => [
					'label' => 'Название',
					'type' => 'text_field'
				]
			];
		}


		return $cols;
	}

	public function beforeInsert(&$vals, $parent_object = null) {
		if (is_array($vals) && $parent_object instanceof PriceCatalogTest) {
			$vals['id_group'] = $parent_object->val('id_group');
			$vals['id_subgroup'] = $parent_object->val('id_subgroup');
		}

		foreach ($vals as $k => $v) {
			$vals[$k] = trim($v);
		}

		return parent::beforeInsert($vals, $parent_object);
	}

	public function beforeUpdate(&$vals) {
		if (is_array($vals)) {
			foreach ($vals as $k => $v) {
				$vals[$k] = trim($v);
			}
		}

		return parent::beforeUpdate($vals);
	}

	public function formStructure() {
		if ($this->id() && $this->val('node_level') < 3) {
			return [
				['type' => 'field', 'name' => 'web_many_name'],
				['type' => 'field', 'name' => 'advert_restrictions'],
				['type' => 'field', 'name' => 'agelimit'],
				//
				['type' => 'tab', 'name' => 'text_tab', 'label' => 'Тексты'],
				['type' => 'field', 'name' => 'text1', 'tab_name' => 'text_tab'],
				['type' => 'field', 'name' => 'text2', 'tab_name' => 'text_tab'],
				//
				['type' => 'tab', 'name' => 'metadata_tab', 'label' => 'Метаданные'],
				['type' => 'component', 'name' => 'Metadata', 'tab_name' => 'metadata_tab'],
			];
		} else {
			return [
				['type' => 'label', 'text' => 'Названия'],
				['type' => 'field', 'name' => 'name'],
				['type' => 'field', 'name' => 'web_name'],
				['type' => 'field', 'name' => 'web_many_name'],
				['type' => 'field', 'name' => 'advert_restrictions'],
				['type' => 'field', 'name' => 'agelimit'],
				['type' => 'field', 'name' => 'flag_is_catalog'],
				//
				['type' => 'tab', 'name' => 'text_tab', 'label' => 'Тексты'],
				['type' => 'field', 'name' => 'text1', 'tab_name' => 'text_tab'],
				['type' => 'field', 'name' => 'text2', 'tab_name' => 'text_tab'],
				//
				['type' => 'tab', 'name' => 'metadata_tab', 'label' => 'Метаданные'],
				['type' => 'component', 'name' => 'Metadata', 'tab_name' => 'metadata_tab'],
			];
		}
	}

	public function fields() {
		return [
			'advert_restrictions' => [
				'col' => [
					'flags' => 'not_null',
					'name' => 'advert_restrictions',
					'type' => 'int_4',
				],
				'elem' => 'drop_down_list',
				'label' => 'Рекламные ограничения',
				'options' => \Sky4\Container::getList('AdvertRestrictions'),
			],
			'id_group' => [
				'col' => [
					'flags' => 'not_null',
					'name' => 'id_group',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'Группа'
			],
			'id_subgroup' => [
				'col' => [
					'flags' => 'not_null',
					'name' => 'id_subgroup',
					'type' => 'int_4',
				],
				'elem' => 'text_field',
				'label' => 'Подгруппа'
			],
			'name' => [
				'col' => [
					'flags' => 'not_null',
					'name' => 'name',
					'type' => 'string(100)',
				],
				'elem' => 'text_field',
				'label' => 'Название'
			],
			'web_name' => [
				'col' => [
					'flags' => 'not_null',
					'name' => 'web_name',
					'type' => 'string(100)',
				],
				'elem' => 'text_field',
				'label' => 'Ед.ч.'
			],
			'web_many_name' => [
				'col' => [
					'flags' => 'not_null',
					'name' => 'web_many_name',
					'type' => 'string(100)',
				],
				'elem' => 'text_field',
				'label' => 'Мн.ч.'
			],
			'agelimit' => [
				'col' => [
					'flags' => '',
					'name' => 'agelimit',
					'type' => 'int_4',
				],
				'elem' => 'drop_down_list',
				'label' => 'Возрастные ограничения',
				'options' => \Sky4\Container::getList('AdvertAgeLimit'),
			],
			'text1' => [
				'attrs' => ['rows' => '10', 'style' => 'height: 100px;'],
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_4'
				],
				'elem' => 'tiny_mce',
				'label' => 'Текст до списка',
				'params' => [
					'parser' => true,
					'rules' => []
				]
			],
			'text2' => [
				'attrs' => ['rows' => '10', 'style' => 'height: 100px;'],
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_4'
				],
				'elem' => 'tiny_mce',
				'label' => 'Текст после списка',
				'params' => [
					'parser' => true,
					'rules' => []
				]
			],
			'flag_is_catalog' => [
				'elem' => 'single_check_box',
				'label' => 'Показывать в каталоге',
				'default_val' => 1
			]
		];
	}

	public function defaultEyeEnabled() {
		return true;
	}

	public function title() {
		return $this->exists() ? $this->name() : 'Каталог товаров';
	}

	public static function staticLink($id_group, $id_subgroup, $id_catalog, $name) {
		return '/catalog/' . $id_group . '/' . $id_subgroup . '/' . $id_catalog . '/' . str()->translit(trim($name)) . '.htm';
	}

	public function link() {
		if (!$this->val('id_subgroup')) return '/catalog/' . $this->val('id_group') . '/';
		if (!$this->val('flag_is_catalog')) return '/catalog/' . $this->val('id_group') . '/' . $this->val('id_subgroup') . '/';

		$link = '/catalog/' . $this->val('id_group') . '/' . $this->val('id_subgroup') . '/' . $this->id() . '/' . str()->translit(trim($this->val('web_many_name'))) . '.htm';
		if (APP_SUB_SYSTEM_NAME === 'CMS') {
			$link = '/76004' . $link;
		}

		return $link;
	}

	public function linkPriceList(Firm $firm) {
		return '/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/?id_catalog=' . $this->id() . '&mode=price';
	}

	public function name($mode = 'classic') {
		if ($mode === 'original') {
			$name = $this->val('web_many_name') ? $this->val('web_many_name') : $this->val('web_name');
		} else {
			$name = str()->firstCharToUpper($this->val('web_many_name') ? $this->val('web_many_name') : $this->val('web_name'));
		}
		if ($mode === 'short') {
			return str()->crop($name, 20);
		}
		return $name;
	}

	public function nameToLower() {
		return str()->toLower($this->name());
	}

	public function nameToFirstCharLower() {
		return str()->firstCharToLower($this->name());
	}

	public function getTopText($id_group = null, $id_subgroup = null, $filters = []) {
		$subgroups_with_limited_top_text = [386];
		$mode = $id_group == 44 ? 'services' : 'goods';
		$text = '';
		if (!in_array($id_subgroup, $subgroups_with_limited_top_text)) $text = 'За более подробной информацией ' . ($mode == 'goods' ? 'о товарах' : 'об услугах') . ' категории &quot;' . (app()->metadata()->getHeader() . '' . app()->metadata()->getFilterString($filters)) . '&quot;, по вопросам ' . ($mode == 'goods' ? 'заказа, покупки и доставки товара, по текущим ценам и наличию товара' : 'заказа услуги, по текущим ценам') . ', пожалуйста, обращайтесь в фирмы, предлагающие ' . ($mode == 'goods' ? 'заинтересовавший вас товар' : 'заинтересовавшую вас услугу') . '.';

		if ($this->val('text1')) {
			$text1 = app()->metadata()->replaceLocationTemplates($this->val('text1'));
			$text = $text . $text1;
		}

		return $text;
	}

	public function getBottomText() {
		return $this->val('text2') ? str()->replace($this->val('text2'), ['_Cp_', '_Cg_', '_L_', '_Ci_'], [app()->location()->currentName('prepositional'), app()->location()->currentName('genitive'), app()->location()->currentId(), app()->location()->currentName()]) : '';
	}

	public function filterFields() {
		return [
			'name' => array(
				'elem' => 'text_field',
				'field_name' => 'name',
				'label' => 'Название'
			)
		];
	}

	public function filterFormStructure() {
		return [
			['type' => 'field', 'name' => 'name']
		];
	}

	public function suggest($q, $field_name = 'web_many_name', $rel_fields = []) {
		$q = str()->trim($q);
		$field_name = (string) $field_name;
		if ($q && $field_name) {
			$id_fields_names = $this->idFieldsNames();
			if ($q && is_array($id_fields_names) && (count($id_fields_names) === 1) && isset($id_fields_names[0]) && isset($this->vals[$field_name])) {
				$_select = ['`' . $id_fields_names[0] . '` AS `key`', '`' . $field_name . '` AS `val`'];
				$_where = ['AND', '`' . $field_name . '` LIKE :' . $field_name, '`node_level` = :node_level'];
				$_params = [':' . $field_name => '%' . $q . '%', ':node_level' => 2];
				foreach ($rel_fields as $rel_field_name => $rel_field_val) {
					$_where[] = '`' . $rel_field_name . '` = :' . $rel_field_name;
					$_params[':' . $rel_field_name] = $rel_field_val;
				}
				return $this->reader()
								->setSelect($_select)
								->setWhere($_where, $_params)
								->setLimit(20)
								->rows();
			}
		}
		return [];
	}

	public function suggestSubgroups($q) {
		$q = str()->trim($q);

		$field_name = 'web_many_name';
		$_select = ['id', 'id_group', 'id_subgroup', 'web_many_name', 'node_level'];
		$_where = ['AND', '`' . $field_name . '` LIKE :' . $field_name, '`node_level` = :node_level'];
		$_params = [':' . $field_name => '%' . $q . '%', ':node_level' => 2];

		$_items = $this->reader()
				->setSelect($_select)
				->setWhere($_where, $_params)
				->setLimit(20)
				->rows();

		$items = [];
		if ($_items) {
			$_groups = [];
			foreach ($_items as $it) {
				if (!isset($_groups[$it['id_group']])) {
					$_groups[$it['id_group']] = 1;
				}
			}

			$group_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(array_keys($_groups), 'id_group');
			$g_where = ['AND', $group_conds['where'], ':node_level = :node_level', '`id_subgroup` = :0'];
			$g_params = $group_conds['params'];
			$g_params[':node_level'] = 1;
			$g_params[':0'] = 0;

			$groups = $this->reader()
					->setSelect($_select)
					->setWhere($g_where, $g_params)
					->rowsWithKey('id_group');

			$items = [];
			foreach ($_items as $it) {
				$items[] = ['key' => $it['id_subgroup'], 'val' => $it['web_many_name'] . '<span style="color:#ccc;display:block;">' . $groups[$it['id_group']]['web_many_name'] . '</span>'];
			}
		}

		return $items;
	}

	public function getGroup($id_group) {
		$this->reader()
				->setWhere(['AND', 'id_group = :id_group', 'id_subgroup = :id_subgroup', 'node_level = :node_level'], [':id_group' => (int) $id_group, ':node_level' => 1, ':id_subgroup' => 0])
				->objectByConds();

		return $this;
	}

	public function getSubGroup($id_group, $id_subgroup) {
		$this->reader()
				->setWhere(['AND', 'id_group = :id_group', 'id_subgroup = :id_subgroup', 'node_level = :node_level'], [':id_group' => $id_group, ':id_subgroup' => (int) $id_subgroup, ':node_level' => 2])
				->objectByConds();

		return $this;
	}

	public function id_group() {
		return (int) $this->val('id_group');
	}

	public function id_subgroup() {
		return (int) $this->val('id_subgroup');
	}

	public function getFieldsForLists() {
		return ['id', 'id_group', 'id_subgroup', 'web_name', 'web_many_name', 'node_level', 'name', 'parent_node', 'flag_is_catalog'];
	}

	public function getDefaultTitle($filters) {
		$result = '';

		if (isset($filters['mode']) && $filters['mode'] !== null) {
			if ($filters['mode'] === 'price') {
				$result = $this->name() . app()->metadata()->getFilterString($filters) . ' _Cp_' . ' - каталог предложений, цены';
			} elseif ($filters['mode'] === 'map') {
				$result = $this->name() . app()->metadata()->getFilterString($filters) . ' - компании на карте _Cg_';
			}
		} else {
			$result = $this->name() . app()->metadata()->getFilterString($filters) . ' _Cp_' . ' - обзор компаний, адреса, сайты';
			if (count(array_filter($filters)) === 0) {
				$result = $this->val('metadata_title') ? $this->val('metadata_title') : $result;
			}
		}

		return $result;
	}

	public function getDefaultKeywords($filters) {
		$result = '';
		$subgroup = new PriceCatalogTest();
		$subgroup->getSubGroup($this->val('id_group'), $this->val('id_subgroup'));

		if (isset($filters['mode']) && $filters['mode'] !== null) {
			if ($filters['mode'] === 'price') {
				$result = $this->name() . app()->metadata()->getFilterString($filters) . ' _Cp_, ' . $subgroup->name() . ', каталог, адреса, телефоны, цены';
			} elseif ($filters['mode'] === 'map') {
				$result = $this->name() . app()->metadata()->getFilterString($filters) . ', адреса компаний на карте _Cg_';
			}
		} else {
			$result = $this->name() . app()->metadata()->getFilterString($filters) . ' _Cp_, ' . $subgroup->name() . ', компании, поставщики, адреса, телефоны, сайты';
			if (count(array_filter($filters)) === 0) {
				$result = $this->val('metadata_key_words') ? $this->val('metadata_key_words') : $result;
			}
		}

		return $result;
	}

	public function getDefaultDescription($filters) {
		$result = '';
		$subgroup = new PriceCatalogTest();
		$subgroup->getSubGroup($this->val('id_group'), $this->val('id_subgroup'));

		if (isset($filters['mode']) && $filters['mode'] !== null) {
			if ($filters['mode'] === 'price') {
				if ((int) $this->val('id_group') !== 44) {
					$result = 'Каталог предложений о продаже _Cp_ товаров категории ' . $this->name() . app()->metadata()->getFilterString($filters) . ': ассортимент, цены, адреса и телефоны компаний' . ($subgroup->exists() ? '. Смотрите также предложения по сопутствующим товарам в разделе ' . $subgroup->name() : '');
				} else {
					$result = $this->name() . app()->metadata()->getFilterString($filters) . ': цены, адреса, телефоны, сайты компаний' . ($subgroup->exists() ? '. Каталог предложений раздела ' . $subgroup->name() : '');
				}
			} elseif ($filters['mode'] === 'map') {
				$result = $this->name() . app()->metadata()->getFilterString($filters) . '. Компании на карте _Cg_, адреса, телефоны, сайты';
			}
		} else {
			if ((int) $this->val('id_group') !== 44) {
				$result = 'Компании, поставщики, магазины _Cg_, где можно купить или заказать ' . $this->name() . app()->metadata()->getFilterString($filters) . ': адреса, телефоны, сайты' . ($subgroup->exists() ? '. Каталог предложений раздела ' . $subgroup->name() : '');
			} else {
				$result = 'Компании _Cg_, где можно заказать ' . $this->name() . app()->metadata()->getFilterString($filters) . ': адреса, телефоны, сайты' . ($subgroup->exists() ? '. Каталог предложений раздела ' . $subgroup->name() : '');
			}
			if (count(array_filter($filters)) === 0) {
				$result = $this->val('metadata_description') ? $this->val('metadata_description') : $result;
			}
		}

		return $result;
	}

	public function getPromoCatalogData($id_group, $id_subgroup) {
		$pc = new PriceCatalogTest();

		$pc_where = ['AND', '`node_level` = :node_level'];
		$pc_params = [':node_level' => 2];

		if (((int) $id_group === 44 || (int) $id_group === 22 || $id_group !== null) && $id_group !== 0) {
			$pc_where[] = '`id_group` = :id_group';
			$pc_params[':id_group'] = $id_group;
		} elseif ($id_group === 0) {
			$pc_where[] = '`id_group` != :id_group1';
			$pc_where[] = '`id_group` != :id_group2';
			$pc_params[':id_group1'] = 22;
			$pc_params[':id_group2'] = 44;
		}

		if ($id_subgroup !== null) {
			$pc_where[] = '`id_subgroup` = :id_subgroup';
			$pc_params[':id_subgroup'] = $id_subgroup;
		}

		$pc->reader()
				->setWhere($pc_where, $pc_params)
				->objectByConds();

		if ($pc->exists()) {
			$fpc = new FirmPromoCatalog();
			$fp_ids = $fpc->reader()
					->setSelect('firm_promo_id')
					->setWhere(['AND', 'price_catalog_id = :price_catalog_id'], [':price_catalog_id' => $pc->id()])
					->rowsWithKey('firm_promo_id');

			if ($fp_ids) {
				$fp = new FirmPromo();
				$fp_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(array_keys($fp_ids), 'id');
				$fp_city_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');

				$_where = [
					'AND',
					['AND', '`flag_is_active` = :flag', '`timestamp_ending` >= :today'],
					$fp_conds['where'],
					$fp_city_conds['where']
				];

				$_params = array_merge([':flag' => 1, ':today' => \Sky4\Helper\DeprecatedDateTime::now()], $fp_conds['params'], $fp_city_conds['params']);

				$items = $fp->reader()
						->setWhere($_where, $_params)
						->count();

				if ($items > 0) {
					return ['price_catalog_id' => $pc->id(), 'price_catalog_name' => $pc->val('name')];
				}
			}
		}

		return null;
	}

	public function node_level() {
		return (int) $this->val('node_level');
	}

}
