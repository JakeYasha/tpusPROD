<?php

namespace App\Model;

class Banner extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\ActiveTrait,
        Component\NameTrait,
        Component\IdFirmTrait,
        Component\ImageTrait,
        Component\TimestampActionTrait,
        Component\TimestampIntervalTrait;

    public $_temp_type = null;

    public function afterInsert(&$vals, $parent_object = null) {
        $this->saveRels($vals)
                ->updateRtIndex();
        return parent::afterInsert($vals, $parent_object);
    }

    public function afterUpdate(&$vals) {
        $this->saveRels($vals)
                ->updateRtIndex();
        return parent::afterUpdate($vals);
    }

    public function cols() {
        return [
            'name' => ['label' => 'Название'],
            'id_firm' => ['label' => 'Фирма'],
            'type' => ['label' => 'Тип'],
            'timestamp_beginning' => ['label' => 'Время начала', 'style_class' => 'date-time'],
            'timestamp_ending' => ['label' => 'Время окончания', 'style_class' => 'date-time'],
            'flag_is_active' => ['label' => 'На сайте?', 'type' => 'flag'],
            'site' => ['label' => 'Сайт']
        ];
    }

    public function defaultOrder() {
        return ['id' => 'DESC'];
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
            'type' => [
                'col' => \Sky4\Db\ColType::getList($this->types()),
                'elem' => 'drop_down_list',
                'label' => 'Тип баннера',
                'options' => $this->types(),
                'default_val' => ''
            ],
            'header' => [
                'col' => \Sky4\Db\ColType::getString(100),
                'elem' => 'text_field',
                'label' => 'Заголовок'
            ],
            'about_string' => [
                'col' => \Sky4\Db\ColType::getString(255),
                'elem' => 'text_field',
                'label' => 'Подпись'
            ],
            'advertising_copy_text' => [
                'col' => \Sky4\Db\ColType::getText(2),
                'elem' => 'text_area',
                'label' => 'Юридический текст'
            ],
            'keywords_string' => [
                'col' => \Sky4\Db\ColType::getString(1000),
                'elem' => 'text_field',
                'label' => 'Ключевые слова'
            ],
            'adv_text' => [
                'col' => \Sky4\Db\ColType::getText(2),
                'elem' => 'text_area',
                'label' => 'Рекламный текст',
                'params' => [
                    'parser' => true
                ]
            ],
            'url' => [
                'col' => \Sky4\Db\ColType::getString(500),
                'elem' => 'text_field',
                'label' => 'Ссылка'
            ],
            'id_city' => [
                'col' => [
                    'flags' => 'not_null unsigned',
                    'type' => 'int_4'
                ],
                'elem' => 'hidden_field',
                'label' => 'ID города'
            ],
            'width' => [
                'col' => [
                    'flags' => 'not_null unsigned',
                    'type' => 'int_2'
                ],
                'elem' => 'text_field',
                'label' => 'Ширина',
                'params' => [
                    'rules' => ['int']
                ]
            ],
            'height' => [
                'col' => [
                    'flags' => 'not_null unsigned',
                    'type' => 'int_2'
                ],
                'elem' => 'text_field',
                'label' => 'Высота',
                'params' => [
                    'rules' => ['int']
                ]
            ],
            'max_count' => [
                'col' => \Sky4\Db\ColType::getInt(8),
                'elem' => 'text_field',
                'label' => 'Максимальное количество показов'
            ],
            'current_count' => [
                'col' => \Sky4\Db\ColType::getInt(8),
                'elem' => 'text_field',
                'label' => 'Текущее количество показов'
            ],
            'flag_is_commercial' => [
                'elem' => 'single_check_box',
                'label' => 'Платный баннер?',
                'default_val' => 1
            ],
            'flag_is_everywhere' => [
                'elem' => 'single_check_box',
                'label' => 'Показывать везде?',
                'default_val' => 0
            ],
            'subgroup_ids' => [
                'col' => [
                    'default_val' => '76',
                    'flags' => 'not_null',
                    'type' => 'string(1000)'
                ],
                'elem' => 'model_autocomplete',
                'label' => 'Выбор подгрупп',
                'params' => [
                    'model_alias' => 'price-catalog',
                    'field_name' => 'web_many_name_for_subgroups',
                    'rel_model_alias' => 'banner-group',
                    'rel_field_name_1' => 'id_banner',
                    'rel_field_name_2' => 'id_subgroup',
                    'rel_model_field_id' => 'id_subgroup'
                ]
            ],
            'catalog_ids' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'type' => 'string(255)'
                ],
                'elem' => 'model_autocomplete',
                'label' => 'Рубрикатор',
                'params' => [
                    'model_alias' => 'price-catalog',
                    //'field_name' => 'web_many_name',
                    'field_name' => 'web_many_name_for_catalogs',
                    'rel_model_alias' => 'banner-catalog',
                    'rel_field_name_1' => 'id_banner',
                    'rel_field_name_2' => 'id_catalog',
                    'rel_model_field_id' => 'id'
                ],
                'attrs' => []
            ],
            'region_ids' => [
                'col' => [
                    'default_val' => '76',
                    'flags' => 'not_null',
                    'type' => 'string(1000)'
                ],
                'elem' => 'multiple_drop_down_list',
                'label' => 'Геотаргетинг',
                'options' => $this->getRegions(),
                'params' => [
                    'multiple' => 'multiple'
                ]
            ],
            'site' => [
                'col' => \Sky4\Db\ColType::getList($this->sites()),
                'elem' => 'drop_down_list',
                'label' => 'Сайт баннера',
                'options' => $this->sites(),
                'default_val' => 'tovaryplus'
            ]
        ];
    }

    public function filterFields() {
        return [
            'id_firm' => [
                'elem' => 'drop_down_list',
                'label' => 'Фирма',
                'options' => $this->idFirmComponent()->getFirmNamesForFilter(),
                'field_name' => 'id_firm'
            ],
            'max_count' => [
                'elem' => 'single_check_box',
                'label' => 'Баннеры по показам',
                'cond' => '>',
                'field_name' => 'max_count'
            ],
            'flag_is_active' => [
                'elem' => 'single_check_box',
                'label' => 'Активные',
                'cond' => 'flag',
                'field_name' => 'flag_is_active',
                'assembler' => [
                    'class_name' => '\\App\\Model\\Banner',
                    'method_name' => 'assembleFilterActiveConds'
                ]
            ]/* ,
                  'timestamp_beginning' => [
                  'label' => 'Начало показа',
                  'field_name' => 'timestamp_beginning',
                  'cond' => '>=',
                  ],
                  'timestamp_ending' => [
                  'label' => 'Окончание показа',
                  'field_name' => 'timestamp_ending',
                  'cond' => '<=',
                  ] */
        ];
    }

    public function filterFormStructure() {
        return [
            ['type' => 'field', 'name' => 'id_firm'],
            ['type' => 'field', 'name' => 'max_count'],
            ['type' => 'field', 'name' => 'flag_is_active'],
                //['type' => 'component', 'name' => 'TimestampInterval'],
                //['type' => 'field', 'name' => 'timestamp_ending']
        ];
    }

    public static function assembleFilterActiveConds($field_props, \Sky4\Model\Filter $filter = null, $field_name = null) {
        $_conds['params'] = [];
        $_conds['where'] = [];
        if ($field_name === 'flag_is_active') {
            if (isset($field_props['val']) && (string) $field_props['val'] === 'on') {
                $_conds['params'][':now'] = \Sky4\Helper\DeprecatedDateTime::now();
                $_conds['params'][':flag'] = 1;
                $_conds['where'] = ['timestamp_beginning <= :now', 'timestamp_ending >= :now', 'flag_is_active = :flag'];
            }
        }

        return $_conds;
    }

    public function formStructure() {
        $firm = new Firm();
        $firm->getByIdFirm($this->id_firm());
        return [
            ['type' => 'component', 'name' => 'Name'],
            ['type' => 'field', 'name' => 'id_firm'],
            ['type' => 'field', 'name' => 'id_city'],
            ['type' => 'component', 'name' => 'TimestampInterval'],
            ['type' => 'component', 'name' => 'Active'],
            ['type' => 'field', 'name' => 'max_count'],
            ['type' => 'field', 'name' => 'current_count'],
            //
            ['type' => 'tab', 'name' => 'content', 'label' => 'Контент'],
            ['type' => 'field', 'name' => 'type', 'tab_name' => 'content'],
            ['type' => 'field', 'name' => 'site', 'tab_name' => 'content'],
            ['type' => 'field', 'name' => 'header', 'tab_name' => 'content'],
            ['type' => 'field', 'name' => 'url', 'tab_name' => 'content'],
            ['type' => 'field', 'name' => 'adv_text', 'tab_name' => 'content'],
            ['type' => 'field', 'name' => 'about_string', 'tab_name' => 'content'],
            ['type' => 'field', 'name' => 'advertising_copy_text', 'tab_name' => 'content'],
            ['type' => 'field', 'name' => 'advert_restrictions', 'tab_name' => 'content'],
            ['type' => 'field', 'name' => 'image', 'tab_name' => 'content'],
            ['type' => 'label', 'text' => 'Размеры', 'tab_name' => 'content'],
            /* ['type' => 'field', 'name' => 'width', 'tab_name' => 'content'],
              ['type' => 'field', 'name' => 'height', 'tab_name' => 'content'], */
            //
            /* ['type' => 'tab', 'name' => 'texts', 'label' => 'Тексты'],
              ['type' => 'field', 'name' => 'brief_text', 'tab_name' => 'texts'],
              ['type' => 'field', 'name' => 'text', 'tab_name' => 'texts'], */
            //
            ['type' => 'tab', 'name' => 'rubrics', 'label' => 'Контекст'],
            ['type' => 'field', 'name' => 'keywords_string', 'tab_name' => 'rubrics'],
            ['type' => 'field', 'name' => 'subgroup_ids', 'tab_name' => 'rubrics'],
            ['type' => 'field', 'name' => 'catalog_ids', 'tab_name' => 'rubrics'],
            ['type' => 'field', 'name' => 'flag_is_everywhere', 'tab_name' => 'rubrics'],
            ['type' => 'field', 'name' => 'region_ids', 'tab_name' => 'rubrics'],
        ];
    }

    public function getImage() {
        $file = new File();

        if ($this->hasImage()) {
            $file->get(explode('~', \Sky4\Model\Utils::getFirstCompositeId($this->val('image')))[1]);
        }

        return $file;
    }

    public function hasImage() {
        return $this->val('image') != '';
    }

    private function getRegions() {
        $sr = new StsRegionCountry();
        return $sr->reader()
                        ->setWhere(['AND', 'id_region_country != :nil'], [':nil' => 0])
                        ->setOrderBy('name ASC')
                        ->getList();
    }

    public function isForCurrentRegion() {
        $br = new BannerRegion();
        $br->reader()
                ->setWhere(['AND', 'id_banner = :id_banner', 'id_region = :id_region'], [':id_banner' => $this->id(), ':id_region' => app()->location()->getRegionId()])
                ->objectByConds();
        return $br->exists();
    }

    public function link() {
        return '/adv/item/' . $this->id() . '/';
    }

    public function orderableFieldsNames() {
        return [
            'timestamp_beginning',
            'timestamp_ending',
            'flag_is_active',
            'name',
            'site',
        ];
    }

    private function saveRels(&$vals) {
        $fields = $this->getFields();
        if (isset($fields['subgroup_ids']) && isset($fields['subgroup_ids']['elem']) && ($fields['subgroup_ids']['elem'] === 'model_autocomplete')) {
            $elem = \Sky4\Utils::getElemClass($fields['subgroup_ids']['elem']);
            $elem->setModel($this)
                    ->setParams(isset($fields['subgroup_ids']['params']) ? $fields['subgroup_ids']['params'] : [])
                    ->saveRels('subgroup_ids', $fields['subgroup_ids'], $vals);
        }

        if (isset($fields['catalog_ids']) && isset($fields['catalog_ids']['elem']) && ($fields['catalog_ids']['elem'] === 'model_autocomplete')) {
            $elem = \Sky4\Utils::getElemClass($fields['catalog_ids']['elem']);
            $elem->setModel($this)
                    ->setParams(isset($fields['catalog_ids']['params']) ? $fields['catalog_ids']['params'] : [])
                    ->saveRels('catalog_ids', $fields['catalog_ids'], $vals);
        }

        if (isset($fields['region_ids']) && isset($vals['region_ids'])) {
            $br = new BannerRegion();
            $br->deleteAll(['AND', 'id_banner = :id_banner'], null, null, null, [':id_banner' => $this->id()]);

            $exploaded_vals = explode(',', $vals['region_ids']);
            foreach ($exploaded_vals as $id_region) {
                $br = new BannerRegion();
                $br->insert(['id_banner' => $this->id(), 'id_region' => $id_region]);
            }
        }

        return $this;
    }

    public function title() {
        return $this->exists() ? $this->name() : 'Баннеры';
    }

    public function types() {
        /* return [
          '' => 'Контекстный блок (230x100)',
          'wide_banner' => 'Баннер 760(+)x100',
          'default_banner' => 'Баннер 200x270',
          'slider_banner' => 'Баннер 600(+)x270 (слайдер)',
          'rubrics_big_banner' => 'Большой баннер популярных рубрик 750(+)x360',
          'rubrics_small_banner' => 'Малый баннер популярных рубрик 320x180',
          'header_banner' => 'Верхний баннер 500x130',
          'normal_banner' => 'Баннер 500x130',
          ]; */
        return [
            '' => 'Рекламное место 4, Контекстный текстовый блок (500x130)',
            'wide_banner' => 'Баннер (760x100)',
            'default_banner' => 'Баннер (200x270)',
            'slider_banner' => 'Баннер (600x270) (слайдер)',
            'rubrics_big_banner' => 'Рекламное место 1, Слайдер популярных рубрик (750x360)',
            'rubrics_small_banner' => 'Малый баннер популярных рубрик (320x180)',
            'header_banner' => 'Рекламное место 2, Верхний баннер (500x130)',
            'normal_banner' => 'Рекламное место 3, Контекстный баннер (500x130)',
            'right_direct_240' => 'Каталог, директ справа (при ширине 1280+)',
            'header_727373_banner' => 'Верхний баннер 727373 (500x130)',
            'left_727373_banner' => 'Левый баннер 727373 (240x400)',
            'bottom_727373_banner' => 'Нижний баннер 727373 (500x130)'
        ];
    }

    public function sites() {
        return [
            'tovaryplus' => 'tovaryplus.ru',
            '727373' => '727373.ru'
        ];
    }

    public function isEverywhere() {
        return (int) $this->val('flag_is_everywhere') === 1 ? true : false;
    }

    public function getAdvertRestrictions() {
        $result = false;

        if ($this->val('advert_restrictions')) {
            $list = \Sky4\Container::getList('AdvertRestrictions');
            if (isset($list[$this->val('advert_restrictions')])) {
                $result = $list[$this->val('advert_restrictions')];
            }
        }

        return $result;
    }

    public function updateRtIndex($sphinx = null) {
        if ($sphinx === null) {
            $sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
        }

        $row = [
            'id' => $this->id(),
            'keywords_string' => $this->val('keywords_string'),
            'id_city' => $this->val('id_city'),
            'site' => $this->val('site')
        ];

        $sphinx->replace()
                ->into(SPHINX_BANNER_INDEX)
                ->set($row)
                ->execute();

        return $this;
    }

    public function delete($sphinx = null) {
        $this->deleteRtIndex($sphinx);
        return parent::delete();
    }

    public function deleteRtIndex($sphinx = null) {
        if ($sphinx === null) {
            $sphinx = \Foolz\SphinxQL\SphinxQL::create(app()->getSphinxConnection());
        }

        $sphinx->delete()
                ->from(SPHINX_BANNER_INDEX)
                ->where('id', '=', intval($this->id()))
                ->execute();

        return $this;
    }

    public function isActive() {
        return \Sky4\Helper\DeprecatedDateTime::toTimestamp($this->val('timestamp_ending')) > time() ? true : false;
    }

}
