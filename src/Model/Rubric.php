<?php

namespace App\Model;

class Rubric extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\ExtendedNameTrait,
        Component\ActiveTrait,
        Component\NameTrait,
        Component\ImageTrait;

    public function beforeInsert(&$vals, $parent_object = null) {
		if (isset($vals['name_in_url']) && !$vals['name_in_url']) {
			$vals['name_in_url'] = str()->translit($vals['name']) . self::urlPostfix();
		}

		return parent::beforeInsert($vals, $parent_object);
	}

	public function beforeUpdate(&$vals) {
		if (isset($vals['name_in_url']) && !$vals['name_in_url']) {
			$vals['name_in_url'] = str()->translit($vals['name']) . self::urlPostfix();
		}

		return parent::beforeUpdate($vals);
	}
    
    private static function urlPostfix() {
		return '.htm';
	}
    
    public function linkItem($alias = 'materials') {
		if ($this->val('name_in_url')) return '/' . $alias . '/' . $this->val('name_in_url');
		return parent::linkItem();
	}
    
    public function fields() {
        return [
            'id_service' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'ID службы',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_service'
            ],
            'type' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Тип рубрики',
                    'type' => 'string(255)',
                ],
                'elem' => 'text_field',
                'label' => 'type'
            ],
            'name' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Заголовок рубрики',
                    'type' => 'string(255)',
                ],
                'elem' => 'text_field',
                'label' => 'module',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1], 'required']
                ]
            ],
            'short_text' => [
                'col' => [
                    'flags' => 'not_null',
                    'type' => 'text_2'
                ],
                'elem' => 'tiny_mce',
                'label' => 'Краткое описание рубрики',
                'params' => [
                    'parser' => true
                ]
            ],
            'text' => [
                'col' => [
                    'flags' => 'not_null',
                    'type' => 'text_2'
                ],
                'elem' => 'tiny_mce',
                'label' => 'Текст рубрики',
                'params' => [
                    'parser' => true
                ]
            ],
        ];
    }

    public function types() {
        return [
            'universal' => 'Универсальная рубрика',
            'material' => 'Рубрика материала',
            'afisha' => 'Рубрика афиши',
            'news' => 'Рубрика новостей',
            'advert_module' => 'Рубрика рекламных блоков',
            'firm_promo' => 'Рубрика промо блоков фирм'
        ];
    }
}
