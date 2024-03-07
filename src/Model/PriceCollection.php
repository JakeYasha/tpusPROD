<?php

namespace App\Model;

class PriceCollection extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\MetadataTrait;

    public function defaultOrder() {
        return ['name' => 'ASC'];
    }

    public function orderableFieldsNames() {
        return ['name', 'name_translit', 'key_1', 'key_2', 'flag_is_actove', 'flag_is_strict'];
    }

    public function cols() {
        $cols = [];
        $cols['id'] = ['label' => 'ID'];
        $cols['name'] = ['label' => 'Название'];
        $cols['name_translit'] = ['label' => 'Название в транслите'];
        $cols['key_1'] = ['label' => 'Ключ 1'];
        $cols['key_2'] = ['label' => 'Ключ 2'];

        $cols['id_group'] = ['label' => 'Группа'];
        $cols['id_subgroup'] = ['label' => 'Подгруппа'];

        return $cols;
    }

    public function formStructure() {
        return [
            ['type' => 'field', 'name' => 'name'],
            ['type' => 'field', 'name' => 'name_translit'],
            ['type' => 'field', 'name' => 'key_1'],
            ['type' => 'field', 'name' => 'key_2'],
            ['type' => 'field', 'name' => 'flag_is_strict'],
            ['type' => 'field', 'name' => 'flag_is_active'],
            ['type' => 'field', 'name' => 'id_city'],
            ['type' => 'field', 'name' => 'id_group'],
            ['type' => 'field', 'name' => 'id_subgroup'],
        ];
    }

    public function fields() {
        return [
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
            'name_translit' => [
                'col' => [
                    'flags' => 'not_null',
                    'name' => 'name_translit',
                    'type' => 'string(100)',
                ],
                'elem' => 'text_field',
                'label' => 'Название в транслите'
            ],
            'key_1' => [
                'col' => [
                    'flags' => 'not_null',
                    'name' => 'key_1',
                    'type' => 'string(100)',
                ],
                'elem' => 'text_field',
                'label' => 'Ключ для поиска 1'
            ],
            'key_2' => [
                'col' => [
                    'flags' => 'not_null',
                    'name' => 'key_2',
                    'type' => 'string(100)',
                ],
                'elem' => 'text_field',
                'label' => 'Ключ для поиска 2'
            ],
            'flag_is_strict' => [
                'elem' => 'single_check_box',
                'label' => 'Строгое соответствие',
                'default_val' => 0
            ]
        ];
    }

    public function title() {
        return $this->exists() ? $this->name() : 'Каталог товаров';
    }

    public static function staticLink($name_translit) {
        return '/collection/' . trim($name_translit) . '.htm';
    }

    public function link($subsystem_name = null) {
        $link = '/collection/' . trim($this->val('name_translit')) . '.htm';

        if (APP_SUB_SYSTEM_NAME === 'CMS' || $subsystem_name === 'FIRM_USER_DASHBOARD') {
            $link = '/76004' . $link;
        }

        return $link;
    }

    public function nameToLower() {
        return str()->toLower($this->name());
    }

    public function nameToFirstCharLower() {
        return str()->firstCharToLower($this->name());
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

    public function id_group() {
        return (int) $this->val('id_group');
    }

    public function id_subgroup() {
        return (int) $this->val('id_subgroup');
    }

    public function getFieldsForLists() {
        return ['id', 'id_group', 'id_subgroup', 'name', 'name_translit', 'key_1', 'key_2', 'flag_is_strict'];
    }

}
