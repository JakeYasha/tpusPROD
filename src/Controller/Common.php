<?php

namespace App\Controller;

use App\Classes\Controller;
use App\Model\MenuItem;
use App\Model\StatSite;
use App\Model\StsService;
use App\Model\StsCity;
use App\Model\Cart as CartModel;
use Sky4\Widget\InterfaceElem\Autocomplete as CAutocomplete;
use Sky4\Session as CSession;
use Sky4\Helper\DeprecatedDateTime as DeprecatedDateTime;
use App\Model\Rubric;

use function app;
use function encode;

class Common extends Controller {

	public static function getCssFiles() {
		return [
			'/css/client.main.css',
			'/css/client.extended.css',
			'/css/mobile.css',
			'/css/jquery.fancybox.css',
			'/css/slick.css',
			'/css/jquery-ui-1.10.4.custom.min.css',
			'/css/style.css'
		];
	}
    
    public static function getCss3Files() {
		return [
			'/css3/client.main.css',
			'/css3/client.extended.css',
			'/css3/mobile.css',
			'/css3/jquery.fancybox.css',
			'/css3/slick.css',
			'/css3/jquery-ui-1.10.4.custom.min.css',
			'/css3/style.css'
		];
	}

	public static function getJsFiles() {
		return [
			'/js/jquery-1.11.0.js',
			'/js/jquery.slides.min.js',
			'/js/jquery.formstyler.min.js',
			'/js/jquery.qtip.min.js',
			'/js/jquery.autosuggest.js',
			'/js/imagesloaded.pkgd.min.js',
			'/js/masonry.pkgd.min.js',
			'/js/js.js',
			'/js/common.js',
			'/js/cart.js',
			'/js/sky/plugins/jquery-ui-1.10.4.custom/jquery-ui-1.10.4.custom.min.js',
			'/js/sky/plugins/jquery-ui-1.10.4.custom/jquery-ui-datepicker-ru.js',
			'/js/jquery.jcarousel.min.js',
			'/js/jquery.fancybox.pack.js',
			'/js/jquery.maskedinput.min.js',
			'/js/sky/common/js/utils.js',
			'/js/sky/common/js/form.js',
			'/js/sky/common/js/validator.js',
			'/js/jquery.autosize.min.js',
			'/jsall/jsall.js'
		];
	}
    
    public static function getJs3Files() {
		return [
			'/js3/jquery-1.11.0.js',
			'/js3/jquery.slides.min.js',
			'/js3/jquery.formstyler.min.js',
			'/js3/jquery.qtip.min.js',
			'/js3/jquery.autosuggest.js',
			'/js3/imagesloaded.pkgd.min.js',
			'/js3/masonry.pkgd.min.js',
			'/js3/js.js',
			'/js3/common.js',
			'/js3/cart.js',
			'/js3/sky/plugins/jquery-ui-1.10.4.custom/jquery-ui-1.10.4.custom.min.js',
			'/js3/sky/plugins/jquery-ui-1.10.4.custom/jquery-ui-datepicker-ru.js',
			'/js3/jquery.jcarousel.min.js',
			'/js3/jquery.fancybox.pack.js',
			'/js3/jquery.maskedinput.min.js',
			'/js3/sky/common/js/utils.js',
			'/js3/sky/common/js/form.js',
			'/js3/sky/common/js/validator.js',
			'/js3/jquery.autosize.min.js',
			'/jsall/jsall.js'
		];
	}

	public static function getFirmUserJsFiles() {
		return [
			'/js/sky/plugins/tinymce-4.1.7/tinymce.min.js',
			'/js/js-firm-user.js',
		];
	}

	public function renderHeader($template = 'header_new', $rubrics = null, $mobile_rubrics = null) {
		$autocomplete = new CAutocomplete();
		$query = app()->request()-> processGetParams(['query' => 'string'])['query'];

        $autocomplete
				->setName('code')
				->setAttrs([
					'id' => 'main-search-autocomplete',
					'placeholder' => 'Введите название товара или услуги',
					'data-location' => app()->location()->currentId(),
					'name' => 'query',
                    'class' => 'form__control form__control--search-input'
				])
				->setParams([
					'model_alias' => 'search',
					'val_mode' => 'id',
					'field_name' => 'name'
		]);

		if ($query !== null && $query) {
			$autocomplete->setAttrs(['value' => $query]);
		}

		$session = new CSession();

		if (!isset($_SESSION['search_settings'])) {
			$_SESSION['search_settings'] = [
				'action' => '/search/price/',
				'mode' => 'price',
				'city' => 'index'
			];
		}
		$_SESSION['search_settings']['city'] = app()->location()->currentId();
		$_SESSION['search_settings']['action'] = app()->link('/search/'.$_SESSION['search_settings']['mode'].'/');
		
        $_rubrics = '';
        $_mobile_rubrics = '';

        if (app()->isNewTheme()){
            $cc = new \App\Controller\Catalog();
            $_rubrics = $rubrics ? $rubrics : $cc->renderRubrics(null, null, 'default');
            $_mobile_rubrics = $mobile_rubrics ? $mobile_rubrics : $cc->renderRubrics(null, null, 'default', 'mobile_rubrics');
        }
        
        $sc = new StsCity();
        
		return $this->view()
                        ->set('topCities', $sc->reader()->setOrderBy('`position_weight` DESC')->setLimit(11)->objects())
						->set('autocomplete', $autocomplete->render())
						->set('search_settings', $_SESSION['search_settings'])
						->set('search_modes', self::getSearchModes())
                        ->set('rubrics', $_rubrics)
                        ->set('mobile_rubrics', $_mobile_rubrics)
						->setTemplate($template)
						->render();
	}
    
    public function renderCommonHeader() {
        $cc = new Catalog();
        $rubrics = $cc->renderRubrics(null, null, 'index');
        $mobile_rubrics = $cc->renderRubrics(null, null, 'index', 'mobile_rubrics');
        return $this->renderHeader('header_new', $rubrics, $mobile_rubrics);
    }
    
    public function renderMaterialsHeader() {
//        $_rubric = new Rubric();
        $_location = app()->location()->currentId();
//        $_rubrics = $_rubric->reader()
//                ->setWhere([
//                    'AND', 
//                    'type = :type',
//                    'flag_is_active = :flag_is_active',
//                    'id_city = :id_city'
//                ], [
//                    ':type' => 'material',
//                    ':flag_is_active' => 1,
//                    ':id_city' => $_location
//                ])
//                ->objects();
        
        
        $_rubric = new Rubric();
        $_rubrics = $_rubric->reader()
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
        foreach($_rubrics as $rubric){
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
        
        
        
        
        $rubrics = $this->view()
                ->set('rubrics', $_rubrics)
                ->set('items', [])
                ->set('rubrics_count',$rubrics_count)
                ->setTemplate('rubrics', 'materials')
                ->render();
        $mobile_rubrics = $this->view()
                ->set('rubrics', $_rubrics)
                ->set('items', [])
                ->set('rubrics_count',$rubrics_count)
                ->setTemplate('mobile_rubrics', 'materials')
                ->render();

        return $this->renderHeader('materials_header_new', $rubrics, $mobile_rubrics);
    }
    
    public function renderMaterialsPreFooter() {
        return $this->view()
                        ->set('last_popular_materials', \App\Model\Material::getLastMaterials(4, null, true))
                        ->set('last_recommend_materials', \App\Model\Material::getLastMaterials(3, true, null))
						->setTemplate('materials_pre_footer')
						->render();
    }
    
    public function renderNewsHeader() {
        $_rubric = new Rubric();
        $_location = app()->location()->currentId();
        $_rubrics = $_rubric->reader()
                ->setWhere([
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',
                    'id_city = :id_city'
                ], [
                    ':type' => 'news',
                    ':flag_is_active' => 1,
                    ':id_city' => $_location
                ])
                ->objects();
        
        /*foreach($_rubrics as $_rubric) {
            $_vals = $_rubric->getVals();
            $_vals['name_in_url'] = str()->translit($_vals['name']) . '.htm';
            $_rubric->update($_vals);
        }*/
        $rubrics = $this->view()
                ->set('rubrics', $_rubrics)
                ->set('items', [])
                ->setTemplate('rubrics', 'news')
                ->render();
        $mobile_rubrics = $this->view()
                ->set('rubrics', $_rubrics)
                ->set('items', [])
                ->setTemplate('mobile_rubrics', 'news')
                ->render();

        return $this->renderHeader('news_header_new', $rubrics, $mobile_rubrics);
    }

    public function renderAfishaHeader() {
        $_rubric = new Rubric();
        $_location = app()->location()->currentId();
        $_rubrics = $_rubric->reader()
                ->setWhere([
                    'AND', 
                    'type = :type',
                    'flag_is_active = :flag_is_active',
                    'id_city = :id_city'
                ], [
                    ':type' => 'afisha',
                    ':flag_is_active' => 1,
                    ':id_city' => $_location
                ])
                ->objects();
        
        /*foreach($_rubrics as $_rubric) {
            $_vals = $_rubric->getVals();
            $_vals['name_in_url'] = str()->translit($_vals['name']) . '.htm';
            $_rubric->update($_vals);
        }*/
        $rubrics = $this->view()
                ->set('rubrics', $_rubrics)
                ->set('items', [])
                ->setTemplate('rubrics', 'afisha')
                ->render();
        $mobile_rubrics = $this->view()
                ->set('rubrics', $_rubrics)
                ->set('items', [])
                ->setTemplate('mobile_rubrics', 'afisha')
                ->render();

        return $this->renderHeader('afisha_header_new', $rubrics, $mobile_rubrics);
    }

    public function renderHeaderShort() {
        return $this->renderHeader('header_short');
    }

	public function renderFooter() {
		$ss = new StatSite();
		$ss->reader()
				->setOrderBy('timestamp_last_updating DESC')
				->objectByConds();

		$service = app()->stsService();

		if (!$service->exists()) {
			$service = new StsService(10);
		}

        $view = $this->view()
						->set('date_update', date('d.m.Y в H:i:s', DeprecatedDateTime::toTimestamp($ss->val('timestamp_last_updating'))))
						->set('service', $service);
        if ((defined('APP_IS_LK_MANAGER') && APP_IS_LK_MANAGER) 
        || (defined('APP_IS_LK_FIRM_USER') && APP_IS_LK_FIRM_USER)) {
            $view->setDirPath(APP_DIR_PATH.'/src/views_');
        }
        return $view->setTemplate('footer')
						->render();
	}

	public function renderSidebar() {
		return app()->sidebar()->render();
	}

	public function renderFooterMenu() {
		$menu_item = new MenuItem();
		$menu_item->findByAlias('footer_menu');

		$all_items = $menu_item->getItems(app()->request()->getRequestUri());
		$filtered_by_region_items = [];
		$current_region_id = app()->location()->getRegionId();

		foreach ($all_items as $item) {
			if (!isset($item['alias']) || !$item['alias'] || strpos(strrev($item['alias']), strrev('_region-'.$current_region_id)) === 0) {
				$filtered_by_region_items[] = $item;
			} else if (strpos(strrev($item['alias']), strrev('_region-76')) === 0) {
				list($alias, $region_id) = explode("_region-", $item['alias']);
				//$regional_item_exists = array_filter($all_items,function($tmp_item,$alias,$current_region_id){
				//        return $tmp_item['alias'] == $alias."_region-".$current_region_id;
				//});
				$regional_item_exists = false;
				foreach ($all_items as $tmp_item) {
					if ($tmp_item['alias'] == $alias."_region-".$current_region_id) {
						$regional_item_exists = true;
						break;
					}
				}
				if (!$regional_item_exists) {
					$filtered_by_region_items[] = $item;
				}
			}
		}

		$view = $this->view()
						->set('items', $filtered_by_region_items);
        if ((defined('APP_IS_LK_MANAGER') && APP_IS_LK_MANAGER) 
        || (defined('APP_IS_LK_FIRM_USER') && APP_IS_LK_FIRM_USER)) {
            $view->setDirPath(APP_DIR_PATH.'/src/views_');
        }
        return $view->setTemplate('footer_menu')
						->render();
	}

	public function renderLeftMenuDefault() {
		$menu_item = new MenuItem();
		$menu_item->findByAlias('left_menu_default');
		return $this->view()
						->set('items', $menu_item->getItems(app()->request()->getRequestUri()))
						->setTemplate('left_menu_default')
						->render();
	}

	public function renderLeftMenuDefaultByRegion() {
		$menu_item = new MenuItem();
		$menu_item->findByAlias('left_menu_default_city-'.app()->location()->currentId());
		if (!$menu_item->exists()) {
			$menu_item->findByAlias('left_menu_default_region-'.app()->location()->getRegionId());
		}

		if (!$menu_item->exists()) {
			$menu_item->findByAlias('left_menu_default');
		}
		return $this->view()
						->set('items', $menu_item->getItems(app()->request()->getRequestUri()))
						->setTemplate('left_menu_default')
						->render();
	}

	public function renderDefaultAjaxFormMessage($message) {
		return $this->view()
						->set('message', $message)
						->setTemplate('defaul_ajax_form_message')
						->render();
	}

	public function renderAgreementBlock() {
		return $this->view()
						->setTemplate('agreement_block')
						->render();
	}

	public function renderPerosnalData() {
		return $this->view()
						->setTemplate('personal_data')
						->render();
	}

    public static function getSearchModes() {
		return [
			'price' => 'Товары и услуги',
			'firms' => 'Компании'
		];
	}

}
