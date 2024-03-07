<?php

namespace App\Action;

use App\Model\Rubric;
use App\Model\Material;
use App\Model\MaterialRubric;
use Sky4\Model\Utils;
use Sky4\Exception;
use App\Model\Material as MaterialModel;

class Materials extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new Material());
	}

	public function execute($mnemonick = null) {
        
        //if (!APP_IS_DEV_MODE) throw new Exception(Exception::TYPE_BAD_URL);
        
        if (APP_IS_DEV_MODE){
            //var_dump(app()->request()->getParam('tag')); получение тегов
            //var_dump(app()->getPathUrl());
            //
//            $p = new Material();
//            $items = app()->db()->query()
//                ->setText("SELECT `type`, `mnemonic` FROM `material` WHERE `flag_is_published`='1'")
//                ->fetch();
//            
//            $pages_tts = '';
//            
//            foreach ($items as $it) {
//                $pages_tts .='/' . $it['type'] . 's/' . $it['mnemonic'] .'/'. PHP_EOL.'<br>';
//            }
//            
//            var_dump($pages_tts);
        } 
        
        
        $description = 'Онлайн-газета Товары+ Новости, афиша, акции и публикации компаний города'
                . app()->location()->currentName('genitive') 
                . ' и области.';
        
        app()->metadata()->setMetatag('description', $description);
        app()->metadata()->setMetatag('robots', 'index, follow');
        
        
        $alert_last = 'Мы нашли для Вас что-то интересное.';
        $path_url = app()->getPathUrl();
        $mnemonick = $path_url[0];
//        if (count($path_url)>1){
//        /*
//            Если количество параметров в урле больше 1, то значит, нужно проверить на материал сначала. Если нет, то раздел.
//                 */    
        $_material = new MaterialModel();
        $material = $_material->reader()
                ->setWhere([
                        'AND', 
                        'mnemonic = :mnemonic',
                        'flag_is_active = :flag_is_active',
                    ], [
                        ':mnemonic' => end($path_url),//str_replace('.htm','',end($path_url)),
                        ':flag_is_active' => 1
                    ]
                )
                ->objectByConds();
            
        //}
        
        //app()->frontController()->layout()->setTemplate('material');
        //$_location = app()->location()->currentId();
        $_location='76004';
        
        $_rubric = new Rubric();
        $rubrics = $_rubric->reader()
                ->setWhere([
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',        
                    'id_city = :id_city'
                ], [
                    ':type' => 'material',
                    ':flag_is_active' => 1,
                    ':id_city' => $_location
                ])
                ->objects();
        
        /*
            Здесь и далее - ведём подсчёт статей в рубриках, ранится в одельном ассоциативном массиве,
         * где ключ = id рубрики, а значение = количество опубликованных(активных статей)
         * ключ 'all' = общее количество статей
         *          */
        
        $rubrics_count = [];
        $rubrics_count['all'] = 0;
        foreach($rubrics as $rubric){
            $where_conds_r = [
                'params' => [
                        ':id_rubric' => $rubric->id()
                    ]
            ];
            $count_r = app()->db()->query()
                ->setText('SELECT COUNT(*) FROM `material_rubric` WHERE `id_rubric`=:id_rubric AND `id_material` IN (SELECT `id` FROM `material` WHERE `id_material`=`material_rubric`.`id_material` AND `flag_is_published`=1 AND `flag_is_active`=1)')
                ->setParams($where_conds_r['params'])
                ->fetch();
            $rubrics_count[$rubric->id()] = $count_r[0]['COUNT(*)'];
            $rubrics_count['all'] = $rubrics_count['all']+$count_r[0]['COUNT(*)'];
        }
        
        
        $current_rubric = '';
        $material_ids_conds = '';
        
        if ($material->exists()){
            if (strlen($material->val('name'))>10){
                $title = 'Газета Товары+, статья: '.$material->val('name').' - Читайте на сайте tovaryplus.ru';
            }else{
                $title = 'Газета Товары+. Читайте на сайте tovaryplus.ru';
            }
            app()->metadata()->setTitle($material->val('name').' - Читайте на сайте  онлайн-газеты Товары+. tovaryplus.ru');
            
            app()->metadata()->setMetatag('title', $material->val('name').' - Читайте на сайте  онлайн-газеты Товары+. tovaryplus.ru');
            app()->metadata()->setMetatag('description', $material->val('short_text').' - Онлайн-газета Товары+. tovaryplus.ru');
            $parent_rubric_material = app()->db()->query()
                ->setText('SELECT `name_in_url` FROM `rubric` WHERE `id` IN (SELECT `id_rubric` FROM `material_rubric` WHERE `id_material`='.$material->val('id').') LIMIT 1')
				->fetch();// получаем ссылку на корневую рубрику
            
            app()->db()->query()
                ->setText('UPDATE `material` SET `stat_see`=`stat_see`+1 WHERE `id`='.$material->val('id').'')
				->fetch();//увеличиваем количество просмотров статьи
            
            app()->frontController()->layout()->setTemplate('material');
                app()->sidebar()
				->setParam('last_news', MaterialModel::getLastNews(4))
				->setParam('last_afisha', MaterialModel::getLastAfisha(1))
				->setTemplate('sidebar_news')
				->setTemplateDir('common');
                if (!isset($params['preview_key'])){$params['preview_key']=false;}
                if (!$material->val('flag_is_published') && $material->val('preview_link') !== $params['preview_key']) {
                    throw new Exception(Exception::TYPE_BAD_URL);
                } else if (!$material->val('flag_is_published') && $material->val('preview_link') === $params['preview_key']) {
                    $this->view()
                            ->set('bread_crumbs', app()->breadCrumbs()->render())
                            ->set('item', $material)
                            ->set('material_image',$_material->image())
                            ->setTemplate('preview')
                            ->save();
                } else if ($material->val('flag_is_published')) {
                    $this->view()
                            ->set('type','material')
                            ->set('bread_crumbs', app()->breadCrumbs()->render())
                            ->set('item', MaterialModel::prepare($material))
                            ->set('rubric_hrefname', $parent_rubric_material[0]['name_in_url'])
                            ->set('material_image',$_material->image())
                            ->set('last_materials', MaterialModel::getLastMaterials(3))
                            ->set('tags', $_material->getTags($material->val('tags')))
                            ->setTemplate('index_material')
                            ->save();
                }
                return true;
            }
        
        
        // определяем рубрика ли это?
        $rubric_id = '%'; // если нет материалов в рубрике, то просто выводим кучу всего
        //if ($_location==''){$_location='76004';}
        
            
        if (count($path_url)>1){
            $mnemonick = end($path_url);
        
            //echo '$mnemonick = '.$mnemonick;

            $rubrics_this = $_rubric->reader()
                    ->setWhere([
                        'AND', 
                        'type = :type',
                        'flag_is_active = :flag_is_active',        
                        'id_city = :id_city',
                        'name_in_url = :mnemonic'

                    ], [
                        ':type' => 'material',
                        ':flag_is_active' => 1,
                        ':id_city' => $_location,
                        ':mnemonic' => $mnemonick
                    ])
                    ->rows();
            //var_dump($rubrics_this);//какой id рубрики???
            if (count($rubrics_this)==0){
                //die('РУБРИКИ НЕТ!!'); // вывод текста в шаблоне!!!
                $rubric_id = '%';
                $alert_last = 'Рурика или материал отстутствуют =(, но мы нашли тоже кое-что интересное:';
            }else{
                $rubric_id = $rubrics_this[0]['id'];
            }
            //echo $rubrics_this[0]['name'];
            
        }
        
        
        
        
        /*$_material_rubric = new MaterialRubric();
        $material_rubric = $_material_rubric->reader()
                        ->setWhere(['AND', 'id_material = :id_material'],[':id_material' => $material->id()])
                        ->objectByConds();*/
        
        
        app()->frontController()->layout()->setTemplate('materials');
        /*
        if ($mnemonick) {
            foreach ($rubrics as $rubric) {
                if($mnemonick === $rubric->val('name_in_url')) {
                    $current_rubric = $rubric;
                    break;
                }
            }
            
            $last_year_materials = new Material();
            $_last_year_material_ids = $last_year_materials->reader()
                    ->setSelect('id')
                    ->setWhere([
                        'AND', 
                        'type = :type',
                        'flag_is_active = :flag_is_active',                    'flag_is_published = :flag_is_published',
                        'id_city = :id_city',
                        'timestamp_last_published < :timestamp_last_published'
                    ], [
                        ':type' => 'material',
                        ':flag_is_active' => 1,                    ':flag_is_published' => 1,
                        ':id_city' => $_location,
                        ':timestamp_last_published' => \Sky4\Helper\DeprecatedDateTime::shiftYears(1)
                    ])
                    ->setLimit(1000)
                    ->setOrderBy('timestamp_last_published DESC, id DESC')
                    ->rowsWithKey('id');
            
            if ($_last_year_material_ids) {
                $_material_ids_conds = Utils::prepareWhereCondsFromArray(array_keys($_last_year_material_ids), 'id_material');

                if ($current_rubric) {
                    $material_rubric = new MaterialRubric();
                    
                    $__material_ids_conds = [
                        'where' => ['AND', 'id_rubric = :id_rubric', $_material_ids_conds['where']],
                        'params' => [':id_rubric' => $current_rubric->id()] + $_material_ids_conds['params']
                    ];
                    
                    $last_year_material_ids = $material_rubric->reader()
                            ->setSelect('id_material')
                            ->setWhere($__material_ids_conds['where'], $__material_ids_conds['params'])
                            ->rowsWithKey('id_material');

                    if ($last_year_material_ids) {
                        $material_ids_conds = Utils::prepareWhereCondsFromArray(array_keys($last_year_material_ids), 'id');
                    }
                }
            }
        }
        
        */
        
        /// МАТЕРИАЛЫ НОВЫЕ = В РУБРИКЕ
        
        
        
        
        $_material = new Material();
        $where_conds = [
            'where' => [
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',
                    'flag_is_published = :flag_is_published',
                    'id_city = :id_city',
                    
                ],
            'params' => [
                    ':type' => 'material',
                    ':flag_is_active' => 1,
                    ':flag_is_published' => 1,
                    ':id_city' => $_location
                ]
        ];
        if ($material_ids_conds) {
            $where_conds['where'] []= $material_ids_conds['where'];
            $where_conds['params'] += $material_ids_conds['params'];
        }
//        $_last_materials = $_material->reader()
//                ->setWhere($where_conds['where'], $where_conds['params'])
//                ->setLimit(20)
//                ->setOrderBy('timestamp_last_published DESC, id DESC')
//                ->objects();
//        
//        $last_materials = [];
//        foreach($_last_materials as $_last_material) {
//            $last_materials []= Material::prepare($_last_material);
//        }
        
        $last_materials = $this->last_materials($_location, $rubric_id);
        
        if (empty($last_materials)){
            $alert_last = 'К сожалению, материалов в этой рубрике нет...но быть может, Вам будет интересно что-то из этого:';
            $last_materials = $this->last_materials($_location, '%', 19);
        }
        
        
        
        
        
        $_news = new Material();
        $where_conds = [
            'where' => [
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',                    'flag_is_published = :flag_is_published',
                    'id_city = :id_city'
                ],
            'params' => [
                    ':type' => 'news',
                    ':flag_is_active' => 1,                    ':flag_is_published' => 1,
                    ':id_city' => $_location
                ]
        ];
        if ($material_ids_conds) {
            $where_conds['where'] []= $material_ids_conds['where'];
            $where_conds['params'] += $material_ids_conds['params'];
        }
        $_last_news = $_news->reader()
                ->setWhere($where_conds['where'], $where_conds['params'])
                ->setOrderBy('timestamp_last_published DESC, id DESC')
                ->setLimit(4)
                ->objects();
        
        $last_news = [];
        foreach($_last_news as $_last_news_item) {
            $last_news []= Material::prepare($_last_news_item);
        }
        
        $_afisha = new Material();
        $where_conds = [
            'where' => [
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',                    'flag_is_published = :flag_is_published',
                    'id_city = :id_city'
                ],
            'params' => [
                    ':type' => 'afisha',
                    ':flag_is_active' => 1,                    ':flag_is_published' => 1,
                    ':id_city' => $_location
                ]
        ];
        if ($material_ids_conds) {
            $where_conds['where'] []= $material_ids_conds['where'];
            $where_conds['params'] += $material_ids_conds['params'];
        }
        $_last_afisha = $_afisha->reader()
                ->setWhere($where_conds['where'], $where_conds['params'])
                ->setOrderBy('timestamp_last_published DESC, id DESC')
                ->setLimit(2)
                ->objects();
        $last_afisha = [];
        foreach($_last_afisha as $_last_afisha_item) {
            $last_afisha []= Material::prepare($_last_afisha_item);
        }
        
        $_recomend_material = new Material();
        $where_conds = [
            'where' => [
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',                    'flag_is_published = :flag_is_published',
                    'is_recommend = :is_recommend',
                    'id_city = :id_city'
                ],
            'params' => [
                    ':type' => 'material',
                    ':flag_is_active' => 1,                    ':flag_is_published' => 1,
                    ':is_recommend' => 1,
                    ':id_city' => $_location
                ]
        ];
        if ($material_ids_conds) {
            $where_conds['where'] []= $material_ids_conds['where'];
            $where_conds['params'] += $material_ids_conds['params'];
        }
        $_recomend_materials = $_recomend_material->reader()
                ->setWhere($where_conds['where'], $where_conds['params'])
                ->setOrderBy('timestamp_last_published DESC, id DESC')
                ->setLimit(8)
                ->objects();
        
        $recomend_materials = [];
        foreach($_recomend_materials as $_recomend_material) {
            $recomend_materials []= Material::prepare($_recomend_material);
        }
        
        $_popular_material = new Material();
        $where_conds = [
            'where' => [
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',                    'flag_is_published = :flag_is_published',
                    'is_popular = :is_popular',
                    'id_city = :id_city'
                ],
            'params' => [
                    ':type' => 'material',
                    ':flag_is_active' => 1,                    ':flag_is_published' => 1,
                    ':is_popular' => 1,
                    ':id_city' => $_location
                ]
        ];
        if ($material_ids_conds) {
            $where_conds['where'] []= $material_ids_conds['where'];
            $where_conds['params'] += $material_ids_conds['params'];
        }
        $_popular_materials = $_popular_material->reader()
                ->setWhere($where_conds['where'], $where_conds['params'])
                ->setOrderBy('timestamp_last_published DESC, id DESC')
                ->setLimit(4)
                ->objects();
        
        $popular_materials = [];
        foreach($_popular_materials as $_popular_materialz) {
            $popular_materials []= Material::prepare($_popular_materialz);
        }
        
        $title = 'Новости и публикации компаний ' 
                . app()->location()->currentName('genitive') 
                . ' и области' . ($current_rubric ? ' в рубрике ' . $current_rubric->name() : '');
                
        app()->metadata()->setTitle($title);
        app()->metadata()->setMetatag('title', $title);
        $temp = $this->getCityTemp();
        //var_dump($temp);
        $temp["result"]["temp"] = $temp[0]["temp"];
        $temp["result"]["description"] = $temp[0]["description"];
        
        //var_dump($temp["result"]);
        
		$this->view()
                ->setTemplate('index')
                ->set('type','materials')
                ->set('title', $title)
                ->set('current_rubric', $current_rubric)
                ->set('temp', $temp)
                ->set('rubrics', $rubrics)
                ->set('rubrics_count', $rubrics_count)
                ->set('mobile_rubrics', $rubrics)
                ->set('alert_last',$alert_last)
                ->set('last_materials', $last_materials)
                ->set('recomend_materials', $recomend_materials)
                ->set('popular_materials', $popular_materials)
                ->set('last_news', $last_news)
                ->set('last_afisha', [])//$last_afisha)
                ->set('index_img', '/img3/hero-main.jpg')
				->save();
		return true;
	}

    public function getCityTemp() {
                
        $xml_doc = app()->db()->query()->setText("SELECT `temp`, `description` FROM `city_weather` WHERE `id_city`=76004 ORDER BY `timestamp` DESC LIMIT 1")
            ->fetch();
        return $xml_doc;
    }
    
    
	/**
	 * 
	 * @return \App\Model\Material
	 */
	public function model() {
		return parent::model();
	}

    public function last_materials($_location, $rubric_id, $limit = False) {
        $_material = new Material();
        $where_conds = [
            'where' => [
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',
                    'flag_is_published = :flag_is_published',
                    'id_city = :id_city'
                    
                ],
            'params' => [
                    ':type' => 'material',
                    ':flag_is_active' => 1,
                    ':flag_is_published' => 1,
                    ':id_city' => $_location,
                    ':id_rubric' => $rubric_id
                ]
        ];
//        'SELECT * FROM `material` WHERE `type` = :type AND `flag_is_active` = :flag_is_active AND `flag_is_published` = :flag_is_published AND `id_city` = :id_city AND `id` IN (SELECT `id_material` FROM `material_rubric` WHERE `id_rubric`= :id_rubric)'
        // left outer join `tableid` t on t.`id` = R.`number` 
//        if ($material_ids_conds) {
//            $where_conds['where'] []= $material_ids_conds['where'];
//            $where_conds['params'] += $material_ids_conds['params'];
//        }
        if (!$limit){
            // LIMIT 3
            $limit = '';
            if ($rubric_id=='%'){
                $limit = ' LIMIT 16';
            }
        }else{
            $limit = ' LIMIT '.$limit;
        }
        
        $_last_materials = app()->db()->query()
                ->setText('SELECT * FROM `material` WHERE `type` = :type AND `flag_is_active` = :flag_is_active AND `flag_is_published` = :flag_is_published AND `id_city` = :id_city AND `id` IN (SELECT `id_material` FROM `material_rubric` WHERE `id_rubric` LIKE :id_rubric) ORDER BY `timestamp_last_updating` DESC, `priority` DESC'.$limit )
                ->setParams($where_conds['params'])
                //->setLimit(2)
				->fetch();
        
        $_last_materials = $_material->reader()->model()->processObjectsFromArray($_last_materials);
        
        /*
            Так как в результате запроса через setText мы получаем fetch - массив, нам необходимо его преобразовать в объект. Делаем это через reader-модели. 
         * Честно, не знаю как точно это работает, но работает...потанцевать бы с бубном и упростить систему до пресетов, но низзя хех. 23.12.2020
         *          */
        
        
        //var_dump($_last_materials);
        $last_materials = [];
        foreach($_last_materials as $_last_material) {
            $last_materials []= Material::prepare($_last_material);
        }
        return $last_materials;
    }
    
}
