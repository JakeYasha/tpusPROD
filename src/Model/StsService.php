<?php

namespace App\Model;

class StsService extends \Sky4\Model\Composite {

    public function fields() {
        return [
            'id_service' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null primary_key unsigned',
                    'name' => 'id_service',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_service'
            ],
            'name' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'name',
                    'type' => 'string(128)',
                ],
                'elem' => 'text_field',
                'label' => 'name',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1], 'required']
                ]
            ],
            'address' => [
                'col' => \Sky4\Db\ColType::getString(255),
                'elem' => 'text_field',
                'label' => 'Адрес',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1]]
                ]
            ],
            'mode_work' => [
                'col' => \Sky4\Db\ColType::getString(500),
                'elem' => 'text_field',
                'label' => 'Режим работы',
                'params' => [
                    'rules' => ['length' => array('max' => 500, 'min' => 1)]
                ]
            ],
            'theme_name' => [
                'col' => [
					'default_val' => 'telemagic',
					'flags' => 'not_null',
					'type' => 'string(1000)'
				],
				'elem' => 'radio_buttons',
				'label' => 'Наименование дизайна',
				'options' => $this->getThemes()
            ],
            'logo_img' => [
				'col' => [
                    'flags' => 'not_null',
                    'type' => "list('image','file')"
                ],
				'elem' => 'media_selector',
				'label' => 'Логотип',
			],
            'index_img' => [
				'col' => [
                    'flags' => 'not_null',
                    'type' => "list('image','file')"
                ],
				'elem' => 'media_selector',
				'label' => 'Изображение для шапки',
			],
            'info' => [
                'col' => \Sky4\Db\ColType::getText(2),
                'elem' => 'tiny_mce',
                'label' => 'Расширенная информация',
                'params' => [
                    'rules' => ['length' => array('min' => 1)],
                    'parser' => true
                ]
            ],
            'web' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'web',
                    'type' => 'string(64)',
                ],
                'elem' => 'text_field',
                'label' => 'Сайт'
            ],
            'id_city' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'id_city',
                    'type' => 'int_4',
                ],
                'elem' => 'text_field',
                'label' => 'id_city'
            ],
            'id_country' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'id_country',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_country'
            ],
            'id_region_country' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'id_region_country',
                    'type' => 'int_1',
                ],
                'elem' => 'text_field',
                'label' => 'id_region_country'
            ],
            'exist' => [
                'col' => [
                    'default_val' => '0',
                    'flags' => '',
                    'name' => 'exist',
                    'type' => 'int_1',
                ],
                'elem' => 'text_field',
                'label' => 'exist'
            ],
            'email' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'email',
                    'type' => 'string(64)',
                ],
                'elem' => 'text_field',
                'label' => 'email',
                'params' => [
                    'rules' => ['required', 'email']
                ]
            ],
            'phone' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'phone',
                    'type' => 'string(64)',
                ],
                'elem' => 'text_field',
                'label' => 'Телефон',
                'params' => [
                    'rules' => ['length' => array('max' => 64, 'min' => 1), 'required']
                ]
            ],
            'vk_link' => [
                'col' => [
                    'default_val' => '',
                    'flags' => '',
                    'name' => 'vk_link',
                    'type' => 'string(64)',
                ],
                'elem' => 'text_field',
                'label' => 'Страница в Вконтакте'
            ],
            'fb_link' => [
                'col' => [
                    'default_val' => '',
                    'flags' => '',
                    'name' => 'fb_link',
                    'type' => 'string(64)',
                ],
                'elem' => 'text_field',
                'label' => 'Страница в Facebook'
            ],
            'tw_link' => [
                'col' => [
                    'default_val' => '',
                    'flags' => '',
                    'name' => 'tw_link',
                    'type' => 'string(64)',
                ],
                'elem' => 'text_field',
                'label' => 'Страница в Twitter'
            ],
            'in_link' => [
                'col' => [
                    'default_val' => '',
                    'flags' => '',
                    'name' => 'in_link',
                    'type' => 'string(64)',
                ],
                'elem' => 'text_field',
                'label' => 'Страница в Instagram'
            ],
            'ok_link' => [
                'col' => [
                    'default_val' => '',
                    'flags' => '',
                    'name' => 'ok_link',
                    'type' => 'string(64)',
                ],
                'elem' => 'text_field',
                'label' => 'Страница в Одноклассники'
            ],
            'gp_link' => [
                'col' => [
                    'default_val' => '',
                    'flags' => '',
                    'name' => 'gp_link',
                    'type' => 'string(64)',
                ],
                'elem' => 'text_field',
                'label' => 'Страница в Google+'
            ],
            'yt_link' => [
                'col' => [
                    'default_val' => '',
                    'flags' => '',
                    'name' => 'yt_link',
                    'type' => 'string(64)',
                ],
                'elem' => 'text_field',
                'label' => 'Канал на Youtube'
            ]
        ];
    }
    public function getThemes() {
		$result = [];
        $result["telemagic"] = "telemagic";
        $result["default"] = "default";
		return $result;
	}
    public function getIdByLocation() {
        $result = 10;

        $this->reader()
                ->setWhere(['AND', 'id_city = :id_city'], [':id_city' => app()->location()->currentId()])
                ->objectByConds();

        if ($this->exists()) {
            $result = (int) $this->val('id_service');
        }

        return $result;
    }

    public function getCity() {
        $sts_city = new StsCity();
        $sts_city->reader()->object($this->val('id_city'));

        return $sts_city->name();
    }

    public function hasAddress() {
        return str()->length($this->val('address')) > 5;
    }

    public function hasPhone() {
        return str()->length($this->val('phone')) > 5;
    }

    public function hasModeWork() {
        return str()->length($this->val('mode_work')) > 5;
    }

    public function hasWeb() {
        return str()->length($this->val('web')) > 5;
    }

    public function getWeb() {
        return preg_split('~[;,]~', $this->val('web'));
    }

    public function hasSocialLinks() {
        return $this->hasVKLink() || $this->hasFBLink() || $this->hasTWLink() || $this->hasINLink() || $this->hasOKLink() || $this->hasGPLink() || $this->hasYTLink();
    }

    public function hasVKLink() {
        return str()->length($this->val('vk_link')) > 5;
    }

    public function getVKLink() {
        $url = $this->val('vk_link');
        if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    public function hasFBLink() {
        return str()->length($this->val('fb_link')) > 5;
    }

    public function getFBLink() {
        $url = $this->val('fb_link');
        if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    public function hasTWLink() {
        return str()->length($this->val('tw_link')) > 5;
    }

    public function getTWLink() {
        $url = $this->val('tw_link');
        if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    public function hasINLink() {
        return str()->length($this->val('in_link')) > 5;
    }

    public function getINLink() {
        $url = $this->val('in_link');
        if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    public function hasOKLink() {
        return str()->length($this->val('ok_link')) > 5;
    }

    public function getOKLink() {
        $url = $this->val('ok_link');
        if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    public function hasGPLink() {
        return str()->length($this->val('gp_link')) > 5;
    }

    public function getGPLink() {
        $url = $this->val('gp_link');
        if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    public function hasYTLink() {
        return str()->length($this->val('yt_link')) > 5;
    }

    public function getYTLink() {
        $url = $this->val('yt_link');
        if (str()->pos($url, 'http://') === false && str()->pos($url, 'https://') === false) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    public function idFieldsNames() {
        return ['id_service'];
    }

}
