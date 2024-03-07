<?php

namespace App\Model;

class Changelog extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\TimestampActionTrait;

    public function cols() {
        return [
            'title' => ['label' => 'Название'],
            'sites' => ['label' => 'Для сайта'],
            'timestamp_inserting' => ['label' => 'Добавлено', 'style_class' => 'date-time'],
            'flag_is_hidden' => ['label' => 'Только для сотрудников', 'type' => 'flag'],
        ];
    }
    
    public function defaultOrder() {
		return ['timestamp_inserting' => 'DESC'];
	}
    
    public function orderableFieldsNames() {
        return [
            'timestamp_inserting',
            'flag_is_active',
            'flag_is_hidden',
            'title',
            'sites',
        ];
    }

    public function fields() {
        return [
            'title' => [
                'elem' => 'text_field',
                'label' => 'Название'
            ],
            'text' => [
				'col' => [
					'flags' => 'not_null',
					'type' => 'text_2'
				],
				'elem' => 'tiny_mce',
				'label' => 'Текст',
				'params' => [
					'parser' => true
				]
			],
            'sites' => [
                'elem' => 'text_field',
                'label' => 'Для сайта',
				'elem' => 'drop_down_list',
                'options' => $this->sites(),
            ],
            'flag_is_active' => [
                'elem' => 'single_check_box',
                'label' => 'Активна',
                'default_val' => 0
            ],
            'flag_is_hidden' => [
                'elem' => 'single_check_box',
                'label' => 'Видна только сотрудникам',
                'default_val' => 0
            ],
            'likes' => [
                'elem' => 'hidden_field',
                'label' => 'Понравилось'
            ],
            'dislikes' => [
                'elem' => 'hidden_field',
                'label' => 'Не понравилось'
            ],
        ];
    }
    
    public function linkItem() {
		return '/changelog/' . $this->id() . '/';
	}

	public function sites() {
		return [
			'' => '',
			'tovaryplus.ru' => 'www.tovaryplus.ru',
			'727373.ru' => 'www.727373.ru',
			'727373-info.ru' => 'www.727373-info.ru',
		];
	}

}
