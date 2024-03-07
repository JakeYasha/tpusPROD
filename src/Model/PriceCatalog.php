<?php

namespace App\Model;

class PriceCatalog extends \Sky4\Model\Composite {

    private $name_changed = false;
    private $strict_changed = false;

    use Component\IdTrait,
        Component\AdjacencyListTrait,
        Component\ImageTrait,
        Component\MetadataTrait,
        Component\MetadataPriceModeTrait;

    private $path = null;
    private $path_string = null;

    public function defaultOrder() {
        return ['name' => 'ASC'];
    }

    public function defaultCopyPasteEnabled() {
        return false;
    }

    public function orderableFieldsNames() {
        return ['name', 'web_name', 'web_many_name', 'flag_is_strict'];
    }

    public function parent_node() {
        return (int) $this->val('parent_node');
    }

    public function cols() {
        $cols = [];
        $cols['id'] = ['label' => 'ID'];
        if ((int) $this->val('node_level') > 1) {
            $cols['web_many_name'] = ['label' => 'Мн.ч'];
            //$cols['web_name'] = ['label' => 'Ед.ч', 'type' => 'text_field'];
            $cols['name'] = ['label' => 'Ключ'];
            $cols['flag_is_catalog'] = ['label' => 'Показывать в каталоге?', 'type' => 'flag'];
            $cols['flag_is_strict'] = ['label' => 'Строгое соответствие?', 'type' => 'flag'];
        } else {
            $cols['web_many_name'] = ['label' => 'Название'];
        }

        $cols['image'] = ['label' => 'Картинка по-умолчанию'];
        $cols['node_level'] = ['label' => 'Уровень'];
        $cols['id_subgroup'] = ['label' => 'Подгруппа'];

        return $cols;
    }

    public function afterDelete() {
        $this->deleteRtIndex();
        (new \App\Classes\Catalog(true))->onCatalogDelete($this);
        return parent::beforeDelete();
    }

    public function beforeInsert(&$vals, $parent_object = null) {
        if (is_array($vals) && $parent_object instanceof PriceCatalog) {
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

        if ($this->val('name') !== $vals['name']) {
            $this->name_changed = true;
        }

        if ($this->val('flag_is_strict') !== $vals['flag_is_strict']) {
            $this->strict_changed = true;
        }

        return parent::beforeUpdate($vals);
    }

    public function formStructure() {
        if ($this->id() && $this->val('node_level') < 3) {
            return [
                ['type' => 'field', 'name' => 'web_many_name'],
                ['type' => 'field', 'name' => 'advert_restrictions'],
                ['type' => 'field', 'name' => 'agelimit'],
                ['type' => 'field', 'name' => 'image'],
                //
                ['type' => 'tab', 'name' => 'text_tab', 'label' => 'Тексты'],
                ['type' => 'field', 'name' => 'text1', 'tab_name' => 'text_tab'],
                ['type' => 'field', 'name' => 'text2', 'tab_name' => 'text_tab'],
                //
                ['type' => 'tab', 'name' => 'metadata_tab', 'label' => 'Метаданные'],
                ['type' => 'component', 'name' => 'Metadata', 'tab_name' => 'metadata_tab'],
                //
                ['type' => 'tab', 'name' => 'metadata_tab_2', 'label' => 'Метаданные для товаров'],
                ['type' => 'component', 'name' => 'MetadataPriceMode', 'tab_name' => 'metadata_tab_2'],
                ['type' => 'field', 'name' => 'path'],
            ];
        } else {
            return [
                ['type' => 'label', 'text' => 'Названия'],
                ['type' => 'field', 'name' => 'name'],
                ['type' => 'field', 'name' => 'web_name'],
                ['type' => 'field', 'name' => 'web_many_name'],
                ['type' => 'field', 'name' => 'advert_restrictions'],
                ['type' => 'field', 'name' => 'agelimit'],
                ['type' => 'field', 'name' => 'image'],
                ['type' => 'field', 'name' => 'flag_is_catalog'],
                ['type' => 'field', 'name' => 'flag_is_strict'],
                //
                ['type' => 'tab', 'name' => 'text_tab', 'label' => 'Тексты'],
                ['type' => 'field', 'name' => 'text1', 'tab_name' => 'text_tab'],
                ['type' => 'field', 'name' => 'text2', 'tab_name' => 'text_tab'],
                //
                ['type' => 'tab', 'name' => 'metadata_tab', 'label' => 'Метаданные'],
                ['type' => 'component', 'name' => 'Metadata', 'tab_name' => 'metadata_tab'],
                //
                ['type' => 'tab', 'name' => 'metadata_tab_2', 'label' => 'Метаданные для товаров'],
                ['type' => 'component', 'name' => 'MetadataPriceMode', 'tab_name' => 'metadata_tab_2'],
                ['type' => 'field', 'name' => 'path'],
            ];
        }
    }
    
    public function fields() {
        return [
            'id' => [
                'col' => [
                    'flags' => 'not_null',
                    'name' => 'id',
                    'type' => 'int_4',
                ],
                'elem' => 'text_field',
                'label' => 'id'
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
                'label' => 'Ключ для поиска'
            ],
            'web_name' => [
                'col' => [
                    'flags' => 'not_null',
                    'name' => 'web_name',
                    'type' => 'string(100)',
                ],
                'elem' => 'text_field',
                'label' => 'Название в блоке внутр. рубрик'
            ],
            'web_many_name' => [
                'col' => [
                    'flags' => 'not_null',
                    'name' => 'web_many_name',
                    'type' => 'string(100)',
                ],
                'elem' => 'text_field',
                'label' => 'Название, h1, title'
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
            ],
            'flag_is_strict' => [
                'elem' => 'single_check_box',
                'label' => 'Строгое соответствие',
                'default_val' => 0
            ],
            'path' => $this->fieldPropCreator()->stringField('Путь', 100)
        ];
    }

    
    public function getId(){
        if ((int) $this->val('node_level') === 1) {
            return $this->val('id_group');
        } elseif ((int) $this->val('node_level') === 2) {
            return $this->val('id_subgroup');
        } else {
            return $this->id();
        }
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

    public function link($subsystem_name = null) {
        if ((int) $this->val('node_level') === 1) {
            $link = '/catalog/' . $this->val('id_group') . '/';
        } elseif ((int) $this->val('node_level') === 2) {
            $link = '/catalog/' . $this->val('id_group') . '/' . $this->val('id_subgroup') . '/';
        } else {
            $link = '/catalog/' . $this->val('id_group') . '/' . $this->val('id_subgroup') . '/' . $this->id() . '/' . str()->translit(trim($this->val('web_many_name'))) . '.htm';
        }

        if (APP_SUB_SYSTEM_NAME === 'CMS' || $subsystem_name === 'FIRM_USER_DASHBOARD') {
            $link = '/76004' . $link;
        }

        return $link;
    }

    public function linkPriceList(Firm $firm) {
        if ($firm->isBranch()) {
            return '/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/' . $firm->branch_id . '/?id_catalog=' . $this->id() . '&mode=price';
        } else {
            return '/firm/show/' . $firm->id_firm() . '/' . $firm->id_service() . '/?id_catalog=' . $this->id() . '&mode=price';
        }
    }

    public function name($mode = 'classic') {
        if ($mode === 'original') {
            $name = $this->val('web_many_name') ? $this->val('web_many_name') : $this->val('web_name');
        } elseif ($mode === 'tags') {
            $name = str()->firstCharToUpper($this->val('web_many_name'));
        } elseif ($mode === 'top') {
            $name = str()->firstCharToUpper($this->val('web_name'));
        } elseif ($mode === 'bottom') {
            $name = str()->firstCharToUpper($this->val('web_many_name'));
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
        if ($this->val('text1')) {
            $text1 = app()->metadata()->replaceLocationTemplates($this->val('text1'));
            $text = $text1;
        }

        return $text;
    }

    public function getAnnotationText($id_group = null, $id_subgroup = null, $filters = []) {
        $subgroups_with_limited_top_text = [386];
        $mode = $id_group == 44 ? 'services' : 'goods';
        $text = '';
        if (!in_array($id_subgroup, $subgroups_with_limited_top_text))
            $text = 'Детальную информацию о предложениях в рубрике &quot;' . (app()->metadata()->getHeader() . '' . app()->metadata()->getFilterString($filters)) . '&quot;, текущие цены, ' . ($mode == 'goods' ? 'наличие товара, подробности заказа и доставки' : 'подробности заказа') . ', пожалуйста, уточняйте в фирмах, предлагающих ' . ($mode == 'goods' ? 'заинтересовавший вас товар' : 'заинтересовавшую вас услугу') . '.';

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
            $g_where = ['AND', $group_conds['where'], 'node_level = :node_level', '`id_subgroup` = :id_subgroup'];
            $g_params = [];
            $g_params[':node_level'] = 1;
            $g_params[':id_subgroup'] = 0;
            $g_params += $group_conds['params'];

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

    public function suggestCatalogs($q) {
        $q = str()->trim($q);

        $field_name = 'web_many_name';
        $_select = ['id', 'id_group', 'id_subgroup', 'web_many_name', 'node_level'];
        $_where = ['AND', '`' . $field_name . '` LIKE :' . $field_name, '`node_level` = :node_level'];
        $_params = [':' . $field_name => '%' . $q . '%', ':node_level' => 3];

        $_items = $this->reader()
                ->setSelect($_select)
                ->setWhere($_where, $_params)
                ->setLimit(20)
                ->rows();

        $items = [];
        if ($_items) {
            $_subgroups = [];
            foreach ($_items as $it) {
                if (!isset($_subgroups[$it['id_subgroup']])) {
                    $_subgroups[$it['id_subgroup']] = 1;
                }
            }

            $subgroup_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(array_keys($_subgroups), 'id_subgroup');
            $sg_where = ['AND', $subgroup_conds['where'], 'node_level = :node_level'];
            $sg_params = [];
            $sg_params[':node_level'] = 2;
            $sg_params += $subgroup_conds['params'];

            $subgroups = $this->reader()
                    ->setSelect($_select)
                    ->setWhere($sg_where, $sg_params)
                    ->rowsWithKey('id_subgroup');

            $items = [];
            foreach ($_items as $it) {
                $items[] = ['key' => $it['id'], 'val' => $it['web_many_name'] . '<span style="color:#ccc;display:block;">' . $subgroups[$it['id_subgroup']]['web_many_name'] . '</span>'];
            }
        }

        return $items;
    }

    public function suggestSubgroupsForPriceAdd($q) {
        $q = str()->trim($q);

        $field_name = 'web_many_name';
        $field_name2 = 'name';
        $_select = ['id', 'id_group', 'id_subgroup', 'web_many_name', 'node_level', 'parent_node'];
        $_where = ['AND', ['OR', '`' . $field_name . '` LIKE :' . $field_name, '`' . $field_name2 . '` LIKE :' . $field_name], '`node_level` > :node_level', ['OR', 'flag_is_catalog = :flag_is_catalog', ['AND', 'flag_is_catalog = :not_flag_is_catalog', 'node_level = :node2']]];
        $_params = [':' . $field_name => '%' . $q . '%', ':node_level' => 1, ':flag_is_catalog' => 1, ':not_flag_is_catalog' => 0, ':string' => $q, ':node2' => 2];

        $_items = $this->reader()
                ->setSelect($_select)
                ->setWhere($_where, $_params)
                ->setOrderBy('`node_level` ASC, LOCATE (:string, `web_many_name`)')
                ->setLimit(20)
                ->objects();

        $paths = [];
        foreach ($_items as $ob) {
            $path = $ob->structure()->reader()->setSelect(['web_many_name'])->pathObjects();
            foreach ($path as $str) {
                $paths[$ob->id()][] = $str->name();
            }
            array_pop($paths[$ob->id()]);
        }

        $items = [];
        if ($_items) {
            $items = [];
            foreach ($_items as $it) {
                $items[] = [
                    'id' => $it->id(),
                    'name' => $it->name(), 'label' => $it->name(),
                    'sub_label' => implode('&nbsp;&rarr;&nbsp;', $paths[$it->id()])
                ];
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

    public function isNameChanged() {
        return $this->name_changed === true || $this->strict_changed === true;
    }

    public function id_group() {
        return (int) $this->val('id_group');
    }

    public function id_subgroup() {
        return (int) $this->val('id_subgroup');
    }

    public function imageResolutions() {
        return [
            'image' => [
                ['width' => 260, 'height' => 260]
            ]
        ];
    }

    public function getFieldsForLists() {
        return ['id', 'id_group', 'id_subgroup', 'web_name', 'web_many_name', 'node_level', 'name', 'parent_node', 'flag_is_catalog', 'flag_is_strict'];
    }

    public function getDefaultTitle($filters) {
        $result = '';

        if (isset($filters['mode']) && $filters['mode'] !== null) {
            if ($filters['mode'] === 'price') {
                $result = $this->name() . app()->metadata()->getFilterString($filters) . ' _Cp_' . ' - каталог предложений, цены';
                $result = $this->val('metadata_price_mode_title') ? $this->val('metadata_price_mode_title') : $result;
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
        $subgroup = new PriceCatalog();
        $subgroup->getSubGroup($this->val('id_group'), $this->val('id_subgroup'));

        if (isset($filters['mode']) && $filters['mode'] !== null) {
            if ($filters['mode'] === 'price') {
                $result = $this->name() . app()->metadata()->getFilterString($filters) . ' _Cp_, ' . $subgroup->name() . ', товары и услуги, цены, скидки';
                $result = $this->val('metadata_price_mode_key_words') ? $this->val('metadata_price_mode_key_words') : $result;
            } elseif ($filters['mode'] === 'map') {
                $result = $this->name() . app()->metadata()->getFilterString($filters) . ', компании на карте _Cg_, адреса, телефоны, сайты';
            }
        } else {
            $result = $this->name() . app()->metadata()->getFilterString($filters) . ' _Cp_, ' . $subgroup->name() . ', компании, адреса, телефоны, сайты';
            if (count(array_filter($filters)) === 0) {
                $result = $this->val('metadata_key_words') ? $this->val('metadata_key_words') : $result;
            }
        }

        return $result;
    }

    public function getDefaultDescription($filters) {
        $result = '';
        $subgroup = new PriceCatalog();
        $subgroup->getSubGroup($this->val('id_group'), $this->val('id_subgroup'));

        if (isset($filters['mode']) && $filters['mode'] !== null) {
            if ($filters['mode'] === 'price') {
                if ((int) $this->val('id_group') !== 44) {
                    $result = 'Каталог предложений _Cp_ категории ' . $this->name() . app()->metadata()->getFilterString($filters) . ($subgroup->exists() ? ' раздела ' . $subgroup->name() : '') . ': ассортимент, цены, адреса и телефоны компаний';
                } else {
                    $result = $this->name() . app()->metadata()->getFilterString($filters) . ' - предложения от компаний _Cg_, цены, адреса, телефоны, сайты' . ($subgroup->exists() ? '. Сопутствующие предложения в разделе ' . $subgroup->name() : '');
                }
                $result = $this->val('metadata_price_mode_description') ? $this->val('metadata_price_mode_description') : $result;
            } elseif ($filters['mode'] === 'map') {
                $result = $this->name() . app()->metadata()->getFilterString($filters) . '. Компании на карте _Cg_, адреса, телефоны, сайты';
            }
        } else {
            if ((int) $this->val('id_group') !== 44) {
                $result = 'Компании, поставщики, магазины _Cg_, где можно заказать или купить ' . $this->name() . app()->metadata()->getFilterString($filters) . ': адреса, телефоны, сайты' . ($subgroup->exists() ? '. Каталог предложений раздела ' . $subgroup->name() : '');
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
        $pc = new PriceCatalog();

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

        $has_firm_promo = false;
        $has_advert_modules = false;

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

                $has_firm_promo = $items > 0;
            }

            $amg = new AdvertModuleGroup();
            $advert_ids = $amg->reader()
                    ->setSelect(['id_advert_module'])
                    ->setWhere(['AND', '`id_group` = :id_group', '`id_subgroup` = :id_subgroup'], [':id_group' => (int) $id_group, ':id_subgroup' => (int) $id_subgroup])
                    ->rowsWithKey('id_advert_module');

            if ($advert_ids) {
                $am_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(array_keys($advert_ids), 'id');
                $am = new AdvertModule();
                $_where = [
                    'AND',
                    ['AND', '`flag_is_active` = :flag', '`timestamp_ending` >= :today'],
                    $am_conds['where']
                ];

                $_params = array_merge([':flag' => 1, ':today' => \Sky4\Helper\DeprecatedDateTime::now()], $am_conds['params']);

                $items = $am->reader()
                        ->setWhere($_where, $_params)
                        ->count();

                $has_advert_modules = $items > 0;
            }
        }

        return ($has_advert_modules || $has_firm_promo) ? ['price_catalog_id' => $pc->id(), 'price_catalog_name' => $pc->val('name')] : null;
    }

    public function node_level() {
        return (int) $this->val('node_level');
    }

    public function afterUpdate(&$vals) {
        $result = parent::afterUpdate($vals);
        $this->updateRtIndex();
        $cat = new \App\Classes\Catalog(true);
        $cat->onCatalogUpdate($this);
        return $result;
    }

    public function afterInsert(&$vals, $parent_object = null) {
        $result = parent::afterInsert($vals, $parent_object);
        if (is_array($vals) && (!isset($vals['path']) || !$vals['path'])) {
            $vals['path'] = $this->makePathString();
            app()->db()->query()->setText('UPDATE `price_catalog` SET `path` = :path WHERE id = :id')->execute([':path' => $vals['path'], ':id' => $this->id()]);
            $this->setVal('path', $vals['path']);
        }
        $this->updateRtIndex();
        (new \App\Classes\Catalog(true))->onCatalogInsert($this);

        return $result;
    }

    public function updateRtIndex($sphinx = null) {
        if ($sphinx === null) {
            $sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
        }

        $grand_parent = new PriceCatalog();
        $grand_parent->reader()->setWhere(['AND', 'node_level = :node_level', 'id_subgroup = :id_subgroup'], [
            ':node_level' => 2,
            ':id_subgroup' => $this->val('id_subgroup')
        ])->objectByConds();

        $subgroup_name = array_filter([
            $grand_parent->val('name'),
            $grand_parent->val('web_name'),
            $grand_parent->val('web_many_name')
        ]);

        $row = [
            'id' => $this->id(),
            'id_group' => $this->val('id_group'),
            'id_catalog' => $this->id(),
            'id_parent' => $this->val('parent_node'),
            'node_level' => $this->val('node_level'),
            'flag_is_strict' => $this->val('flag_is_strict'),
            'flag_is_catalog' => $this->val('flag_is_catalog'),
            //
            'name' => $this->val('name'),
            'subgroup_name' => implode(' ', $subgroup_name),
            'web_name' => $this->val('web_name'),
            'web_many_name' => $this->val('web_many_name')
        ];

        $sphinx->replace()
                ->into(SPHINX_PRICE_CATALOG_INDEX)
                ->set($row)
                ->execute();

        return $this;
    }

    public function deleteRtIndex($sphinx = null) {
        if ($sphinx === null) {
            $sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
        }

        $sphinx->delete()
                ->from(SPHINX_PRICE_CATALOG_INDEX)
                ->where('id', '=', intval($this->id()))
                ->execute();

        return $this;
    }

    public function getPathString() {
        return $this->val('path');
    }

    public function makePathString() {
        if ($this->path_string === null) {
            $result = [];
            $path = $this->getPath();
            foreach ($path as $p) {
                $result[] = $p->id();
            }

            $this->path_string = '[' . implode('][', $result) . ']';
            $this->path_string = preg_replace('~([^0-9\]\[]+)|(\[{2,})|(\]{2,})~', '', $this->path_string);
        }

        return $this->path_string;
    }

    public function getPath() {
        if ($this->path === null) {
            $this->path = $this->structure()->reader()->pathObjects();
        }

        return $this->path;
    }
    
    public function getPriceCatalogSubgroups($catalog_ids) {
        $conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($catalog_ids);
        $pc = new PriceCatalog();
        $subgroup_ids = array_keys($pc->reader()
                ->setSelect('id_subgroup')
                ->setWhere($conds['where'], $conds['params'])
                ->setGroupBy('id_subgroup')
                ->rowsWithKey('id_subgroup'));
        
        return $subgroup_ids;
    }

}
