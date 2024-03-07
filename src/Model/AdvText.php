<?php

namespace App\Model;

class AdvText extends \Sky4\Model\Composite {

    use Component\IdTrait, // подключаем чтобы было рабочее определение для id
        Component\StateTrait; // подключаем для статуса публикации

    public function fields() {
        return [
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
            'css' => [
                'col' => \Sky4\Db\ColType::getString(5000),
                'elem' => 'text_area',
                'label' => 'Стили CSS',
                'params' => [
                    'rules' => ['length' => array('max' => 5000)]
                ]
            ],
            'text' => [
                'col' => \Sky4\Db\ColType::getText(2),
                'elem' => 'tiny_mce',
                'label' => 'Текст',
                'params' => [
                    'rules' => ['length' => array('min' => 1)],
                    'parser' => true
                ]
            ],
            'position' => [
                'col' => [
                    'default_val' => 'top',
                    'flags' => 'not_null',
                    'type' => 'string(1000)'
                ],
                'elem' => 'radio_buttons',
                'label' => 'Позиция',
                'options' => $this->getPosition(),
            ],
            'url' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'name',
                    'type' => 'string(128)',
                ],
                'elem' => 'text_field',
                'label' => 'url',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1,'class'=>'choose-url-advtext'], 'required']
                ]
            ],
            'timestamp_ending' => [
                'col' => [
                    'flags' => 'not_null'
                ],
                'elem' => 'date_time_field',
                'label' => 'Время окончания публикации',
                
            ]
		
        ];
    }

    /**
     * 
     * @return array
     */
    public function getByUrl($request_uri) {
        $request_uri = explode('?', $request_uri);
        $request_uri = $request_uri[0];
        $_where = [
            'AND',
            '`state` = :state',
            '`url` = :url OR `url` = :urldop',
            '`timestamp_ending`>=CURRENT_TIMESTAMP()'
        ];
        $_params = [
            ':state' => 'published',
            ':url' => $request_uri,
            ':urldop' => $request_uri
        ];
//        /// сделать проверку на вхождение ячейки в url 
//        if (APP_IS_DEV_MODE) {
//            $_where = [
//                'AND',
//                '`state` != :state',
//                '`url` = :url OR `url` = :urldop',
//                '`timestamp_ending`>=CURRENT_TIMESTAMP()'
//            ];
//            $_params = [
//                ':state' => 'deleted',
//                ':url' => $request_uri,
//                ':urldop' => $request_uri.'/'
//            ];
//        }
        
        $result = [];
        $adv_texts = $this->reader()
                ->setWhere($_where, $_params)
                ->setOrderBy('RAND()')
                ->objects();
        
        foreach ($adv_texts as $item) {
            if (!isset($result[$item->val('position')])) {
                $result[$item->val('position')] = [];
            }
            $result[$item->val('position')] []= [
                'css'   =>  $item->val('css'),
                'text'  =>  $item->val('text')
            ];
            // записываем в массив css и text для вывода из таблицы
        }  
        return $result;
    }

    /**
     * 
     * @return array
     */
    public function getPosition() {
        return [
            'top' => 'Верх(top)',
            //'middle' => 'middle',
            'bottom' => 'Низ(bottom)'
        ];
    }

}
