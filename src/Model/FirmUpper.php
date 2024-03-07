<?php
/*
Короче...это можель для вывода на главной(или где-то ещё, таблички с продвигаемыми фирмами. 30.07.2021
 *  */
namespace App\Model;

class FirmUpper extends \Sky4\Model\Composite {

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
            'href' => [
                'col' => [
                    'default_val' => 'published',
                    'flags' => 'not_null',
                    'name' => 'href',
                    'type' => 'string(128)',
                ],
                'elem' => 'text_field',
                'label' => 'href',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1], 'required']
                ]
            ],
            'type' => [
                'col' => [
                    'default_val' => 'firm',
                    'flags' => 'not_null',
                    'name' => 'type',
                    'type' => 'string(128)',
                ],
                'elem' => 'text_field',
                'label' => 'type',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1], 'required']
                ]
            ],
            'state' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'state',
                    'type' => 'string(128)',
                ],
                'elem' => 'text_field',
                'label' => 'state',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1], 'required']
                ]
            ]
//            'css' => [
//                'col' => \Sky4\Db\ColType::getString(5000),
//                'elem' => 'text_area',
//                'label' => 'Стили CSS',
//                'params' => [
//                    'rules' => ['length' => array('max' => 5000)]
//                ]
//            ],
//            'text' => [
//                'col' => \Sky4\Db\ColType::getText(2),
//                'elem' => 'tiny_mce',
//                'label' => 'Текст',
//                'params' => [
//                    'rules' => ['length' => array('min' => 1)],
//                    'parser' => true
//                ]
//            ],
//            'position' => [
//                'col' => [
//                    'default_val' => 'top',
//                    'flags' => 'not_null',
//                    'type' => 'string(1000)'
//                ],
//                'elem' => 'radio_buttons',
//                'label' => 'Позиция',
//                'options' => $this->getPosition(),
//            ],
//            'url' => [
//                'col' => [
//                    'default_val' => '',
//                    'flags' => 'not_null',
//                    'name' => 'name',
//                    'type' => 'string(128)',
//                ],
//                'elem' => 'text_field',
//                'label' => 'url',
//                'params' => [
//                    'rules' => ['length' => ['max' => 255, 'min' => 1,'class'=>'choose-url-advtext'], 'required']
//                ]
//            ],
//            'timestamp_ending' => [
//                'col' => [
//                    'flags' => 'not_null'
//                ],
//                'elem' => 'date_time_field',
//                'label' => 'Время окончания публикации',
//                
//            ]
		
        ];
    }
    
    public function addFirmUpper($dataFirm){
        $id_service  = \App\Classes\App::stsService()->val('id_service');
        if ($this->getFirmUpperByFirmId($dataFirm['firm_id'])!=0){
            return false;
        }
        $href = '/firm/show/'.$dataFirm['firm_id'].'/'.$id_service.'/';
        $query = "INSERT INTO `firm_upper` (`id_service`, `type`, `name`, `href`, `state`) VALUES ('".$id_service."','firm', '".htmlspecialchars($dataFirm['name'])."', '".$href."', 'published');";
        app()->db()->query()
                ->setText($query)->fetch();
        return true;
    }
    
    /*
    Возвращает количество элементов с данным id
     *      */
    public function getFirmUpperByFirmId($firmId){
        $id_service  = \App\Classes\App::stsService()->val('id_service');
        $query = "SELECT COUNT(`id`) FROM `firm_upper` WHERE `href` LIKE '%firm/show/".$firmId."/".$id_service."/%' LIMIT 1";
        $result = app()->db()->query()
                ->setText($query)->fetch();
        
        return (int)$result[0]['COUNT(`id`)'];
    }
    
    public function getByCity($id_service, $type = 'all', $state = 'published') {
        if ($type=='all'){
            $_where = [
                'AND',
                '`id_service` = :service',
                '`state` = :state'
            ];
            $_params = [
                ':service' => $id_service,
                ':state' => $state
            ];
        }else{
            $_where = [
                'AND',
                '`id_service` = :service',
                '`state` = :state',
                '`type` = :type'
            ];
            $_params = [
                ':service' => $id_service,
                ':state' => $state,
                ':type' => $type
            ];
        }
        
        
        //stat published and disable
        //type firm material
        
        
        $result = [];
        $firms_uppers = $this->reader()
                ->setWhere($_where, $_params)
                //->setOrderBy('RAND()')
                ->objects();
        
        foreach ($firms_uppers as $item) {
            
            if (!isset($result[$item->val('type')])) {
                $result[$item->val('type')] = [];
            }
            $result[$item->val('type')] []= [
                'name' => $item->val('name'),
                'href' => $item->val('href')
            ];
            // записываем в массив css и text для вывода из таблицы
        }  
        return $result;
        
        
        
        
    }
    

}
