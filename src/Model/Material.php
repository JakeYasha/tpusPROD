<?php

namespace App\Model;

use App\Model\Rubric;
use App\Model\Material;
use App\Model\MaterialRubric;
use Sky4\Model\Utils;

class Material extends \Sky4\Model\Composite {

    use Component\IdTrait,
        Component\ActiveTrait,
        Component\NameTrait,
        Component\ImageTrait,
        Component\TimestampActionTrait;

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
            'id_city' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Город',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_city'
            ],
            'id_firm' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'ID фирмы',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'id_firm'
            ],
            'name' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Заголовок',
                    'type' => 'string(255)',
                ],
                'elem' => 'text_field',
                'label' => 'name',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1], 'required']
                ]
            ],
            'preview_link' => [
                'col' => [
                    'flags' => 'not_null',
                    'name' => 'Ссылка предпросмотра',
                    'type' => 'string(255)'
                ],
                'elem' => 'text_field',
                'label' => 'preview_link',
                'params' => [
                    'parser' => true
                ]
            ],
            'mnemonic' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Мнемоника (url)',
                    'type' => 'string(255)',
                ],
                'elem' => 'text_field',
                'label' => 'mnemonic',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1], 'required']
                ]
            ],
            'organization' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Организация',
                    'type' => 'string(255)',
                ],
                'elem' => 'text_field',
                'label' => 'organization',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1]]
                ]
            ],
            'address' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Адрес',
                    'type' => 'string(1024)',
                ],
                'elem' => 'text_field',
                'label' => 'address',
                'params' => [
                    'rules' => ['length' => ['max' => 1024, 'min' => 1]]
                ]
            ],
            'short_text' => [
                'col' => [
                    'flags' => 'not_null',
                    'type' => 'text_2'
                ],
                'elem' => 'tiny_mce',
                'label' => 'Краткое описание',
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
                'label' => 'Текст материала',
                'params' => [
                    'parser' => true
                ]
            ],
            'constructor_data' => [
                'elem' => 'text_field',
                'label' => 'Данные конструктора',
                'params' => [
                    'parser' => true
                ]
            ],
            'material_source_name' => [
                'col' => [
                    'default_val' => '',
                    'name' => 'Наименование источника материала',
                    'type' => 'string(255)',
                ],
                'elem' => 'text_field',
                'label' => 'material_source_name',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1], 'required']
                ]
            ],
            'material_source_url' => [
                'col' => [
                    'default_val' => '',
                    'name' => 'Ссылка на источник материала',
                    'type' => 'string(255)',
                ],
                'elem' => 'text_field',
                'label' => 'material_source_url',
                'params' => [
                    'rules' => ['length' => ['max' => 255, 'min' => 1], 'required']
                ]
            ],
            'enabled_comments' => [
                'elem' => 'single_check_box',
                'label' => 'Комментарии разрешены',
                'default_val' => 1
            ],
            'meta_title' => [
                'col' => \Sky4\Db\ColType::getString(1000),
                'elem' => 'text_field',
                'label' => 'META TITLE'
            ],
            'tags' => [
                'col' => \Sky4\Db\ColType::getString(1000),
                'elem' => 'text_field',
                'label' => 'TAGS'
            ],
            'meta_keywords' => [
                'col' => \Sky4\Db\ColType::getString(1000),
                'elem' => 'text_field',
                'label' => 'META KEYWORDS'
            ],
            'meta_description' => [
                'col' => \Sky4\Db\ColType::getString(1000),
                'elem' => 'text_field',
                'label' => 'META DESCRIPTION'
            ],
            'timestamp_start_show' => [
                'elem' => 'date_time_field',
                'label' => 'Показывать с'
            ],
            'timestamp_end_show' => [
                'elem' => 'date_time_field',
                'label' => 'Показывать по'
            ],
            'flag_is_time_limited' => [
                'elem' => 'single_check_box',
                'label' => 'Показ ограничен по времени',
                'default_val' => 0
            ],
            'flag_is_active' => [
                'elem' => 'single_check_box',
                'label' => 'Активен',
                'default_val' => 0
            ],
            'flag_is_published' => [
                'elem' => 'single_check_box',
                'label' => 'Опубликован',
                'default_val' => 0
            ],
            'is_popular' => [
                'elem' => 'single_check_box',
                'label' => 'Популярный',
                'default_val' => 0
            ],
            'is_recommend' => [
                'elem' => 'single_check_box',
                'label' => 'Рекомендуемый',
                'default_val' => 0
            ],
            'type' => [
                'col' => \Sky4\Db\ColType::getList($this->types()),
                'elem' => 'drop_down_list',
                'label' => 'Тип материала',
                'options' => $this->types(),
                'default_val' => 'material'
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
            'stat_see' => [
                'col' => [
                    'default_val' => '',
                    'flags' => 'not_null',
                    'name' => 'Количество просмотров',
                    'type' => 'int_2',
                ],
                'elem' => 'text_field',
                'label' => 'stat_see'
            ],
        ];
    }
    
    public function isPublished() {
        return $this->val('flag_is_published');
    }
    
    public function link() {
        
//        if (APP_IS_DEV_MODE){
            
            $_material_rubric = new MaterialRubric();
            $material_rubric = $_material_rubric->reader()
                    ->setWhere(['AND', 'id_material = :id_material'], [':id_material' => $this->id()])
                    ->objectByConds();
            $rubric = [];
            if ($material_rubric->exists()) {
                $rubric = new Rubric($material_rubric->val('id_rubric'));
            }
            
            return '/' . $this->val('type') . 's/'.$rubric->val('name_in_url').'/'. $this->val('mnemonic');
//        }else{
//            return '/' . $this->val('type') . 's/'. $this->val('mnemonic');
//        }
	}

    public function types() {
        return [
            'material' => 'Материал',
            'news' => 'Новость',
            'afisha' => 'Афиша',
        ];
    }

    public function getTags($tags = ''){
        if ($tags){
            $_result = '';
            $_tags_arr = explode("#", $tags);
            foreach ($_tags_arr as $tag){
                if ($tag){
                    $tag = mb_ereg_replace('[\s]+', '', $tag);
                    //$_result .= '<a href="' . '/' . $this->val('type') . 's/' . 'tags?='.$tag.'" target="_blank" class="tp-mt-btn-tag">#'.$tag.'</a>';
                    if (strlen($tag)!=0){
                        $_result .= '<a href="javascript:void(0);" class="tp-mt-btn-tag">#'.$tag.'</a>';
                    }
                    
                    
                }
            }
            return $_result;
        }else{
            return 'no tags';
        }
    }
    
    public function get_id_service() {
        return $this->val('id_service') ? $this->val('id_service') : false;
    }
    
    public static function getLastNews($count) {
        $_location = app()->location()->currentId();
        $_news = new Material();
        $where_conds = [
            'where' => [
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',
                    'id_city = :id_city'
                ],
            'params' => [
                    ':type' => 'news',
                    ':flag_is_active' => 1,
                    ':id_city' => $_location
                ]
        ];
        $_last_news = $_news->reader()
                ->setWhere($where_conds['where'], $where_conds['params'])
                ->setLimit($count)
                ->setOrderBy('timestamp_last_published DESC, id DESC')
                ->objects();
        
        $last_news = [];
        foreach($_last_news as $_last_news_item) {
            $last_news []= Material::prepare($_last_news_item);
        }
        
        return $last_news;
    }
    
    public static function getLastMaterials($count = 2, $recommend = null, $popular = null) {
        $_location = app()->location()->currentId();
        $_material = new Material();
        $where_conds = [
            'where' => [
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',
                    'id_city = :id_city'
                ],
            'params' => [
                    ':type' => 'material',
                    ':flag_is_active' => 1,
                    ':id_city' => $_location
                ]
        ];
        
        if ($recommend !== null) {
            $where_conds['where'] = ['AND', 'is_recommend = :is_recommend', $where_conds['where']];
            $where_conds['params'] = [':is_recommend' => $recommend ? 1 : 0] + $where_conds['params'];
        }
        if ($popular !== null) {
            $where_conds['where'] = ['AND', 'is_popular = :is_popular', $where_conds['where']];
            $where_conds['params'] = [':is_popular' => $popular ? 1 : 0] + $where_conds['params'];
        }
        
        $_last_materials = $_material->reader()
                ->setWhere($where_conds['where'], $where_conds['params'])
                ->setLimit($count)
                ->setOrderBy('timestamp_last_published DESC, id DESC')
                ->objects();
        
        $last_materials = [];
        foreach($_last_materials as $_last_material) {
            $last_materials []= Material::prepare($_last_material);
        }
        
        return $last_materials;
    }

    public static function getLastAfisha($count = 2) {
        $_location = app()->location()->currentId();
        $_afisha = new Material();
        $where_conds = [
            'where' => [
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',
                    'id_city = :id_city'
                ],
            'params' => [
                    ':type' => 'afisha',
                    ':flag_is_active' => 1,
                    ':id_city' => $_location
                ]
        ];
        $_last_afisha = $_afisha->reader()
                ->setWhere($where_conds['where'], $where_conds['params'])
                ->setLimit($count)
                ->setOrderBy('timestamp_last_published DESC, id DESC')
                ->objects();
        
        $last_afisha = [];
        foreach($_last_afisha as $_last_afisha_item) {
            $last_afisha []= Material::prepare($_last_afisha_item);
        }
        
        return $last_afisha;
    }

    public function image(){
        $_material_image = Utils::getObjectsByIds($this->val('image'));
        return isset($_material_image[$this->val('image')]) ? $_material_image[$this->val('image')]->embededFileComponent()->setSubDirName('service/' . $this->val('id_service') . '/material/file') : [];
    }
    
    
    public static function prepare(Material $item) {
        // title, short_text, image, text, rubric, material_source_name, material_source_url, link, mnemonic, enabled_comments, timestamp_last_published, timestamp_start_show, is_popular, is_recommend, type
        $_material_rubric = new MaterialRubric();
        $material_rubric = $_material_rubric->reader()
                ->setWhere(['AND', 'id_material = :id_material'], [':id_material' => $item->id()])
                ->objectByConds();
        $rubric = [];
        if ($material_rubric->exists()) {
            $rubric = new Rubric($material_rubric->val('id_rubric'));
        }

		return [
			'id' => $item->id(),
			'name' => $item->val('name'),
            'short_text' => $item->val('short_text'),
            'image' => $item->image(),
            'rubric' => $rubric && $rubric->exists() ? $rubric->name() : 'Газета',
            'material_source_name' => $item->val('material_source_name'),
            'material_source_url' => $item->val('material_source_url'),
            'link' => app()->linkFilter($item->link()),
            'mnemonic' => $item->val('mnemonic'),
            'text' => $item->val('text') ? $item->val('text') : 'Материал готовится',
            'enabled_comments' => $item->val('enabled_comments'),
            'timestamp_last_updating' => \Sky4\Helper\DeprecatedDateTime::day($item->val('timestamp_last_updating')).' '.\Sky4\Helper\DeprecatedDateTime::monthName($item->val('timestamp_last_updating'), 1).' '.\Sky4\Helper\DeprecatedDateTime::year($item->val('timestamp_last_updating')), 
            'timestamp_last_published' => \Sky4\Helper\DeprecatedDateTime::day($item->val('timestamp_last_published')).' '.\Sky4\Helper\DeprecatedDateTime::monthName($item->val('timestamp_last_published'), 1).' '.\Sky4\Helper\DeprecatedDateTime::year($item->val('timestamp_last_published')), 
            'is_popular' => $item->val('is_popular'),
            'is_recommend' => $item->val('is_recommend'),
            'type' => $item->val('type'),
            'tags' => $item->val('tags'),
            'stat_see' => $item->val('stat_see'),
            'advert_restrictions' => $item->val('advert_restrictions'),
            'time_beginning' => \Sky4\Helper\DeprecatedDateTime::day($item->val('timestamp_last_updating')).' '.\Sky4\Helper\DeprecatedDateTime::monthName($item->val('timestamp_last_updating'), 1).' '.\Sky4\Helper\DeprecatedDateTime::year($item->val('timestamp_last_updating')),
                        'time_beginning_short' => date("d.m.Y", \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_start_show'))),
                        'time_ending' => \Sky4\Helper\DeprecatedDateTime::day($item->val('timestamp_end_show')).' '.\Sky4\Helper\DeprecatedDateTime::monthName($item->val('timestamp_end_show'), 1).' '.\Sky4\Helper\DeprecatedDateTime::year($item->val('	timestamp_end_show')),
                        'time_ending_short' => date("d.m.Y", \Sky4\Helper\DeprecatedDateTime::toTimestamp($item->val('timestamp_end_show'))),
                        'flag_is_active' => $item->val('flag_is_active'),
            'flag_is_published' => $item->val('flag_is_published')
		];
	}
}
