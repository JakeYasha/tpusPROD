<?php

namespace App\Controller;

use App\Classes\Controller;
use App\Classes\Traits\ControllerExtension;
use App\Model\Firm;
use App\Model\FirmReview;
use App\Model\Text;
use App\Model\FirmUpper;
use CDateTime;
use CException;
use \Sky4\Model\Utils;
use \Sky4\Model\Composite;

class Index extends Controller {

	use ControllerExtension;

	public function actionIndex($no_params_expected = true) {
		if ($no_params_expected !== true) {
			throw new CException(CException::TYPE_BAD_URL);
		}

		$this->text()->getByLink('index/index');
		app()->metadata()->setIndexMeta();

		$text_bottom = new Text();
		$text_bottom->getByLink('index/index_bottom');
		$text_bottom->setVal('text', app()->metadata()->replaceLocationTemplates($text_bottom->val('text')));
		
		$location = (int)app()->location()->currentId();
		$text_bottom_by_location = new Text();
		$text_bottom_by_location->getByLink('index/index_bottom/'.$location);
		if ($text_bottom_by_location->exists()) {
			$text_bottom_by_location->setVal('text', app()->metadata()->replaceLocationTemplates($text_bottom_by_location->val('text')));
		}
        
        $_header = app()->metadata()->replaceLocationTemplates('TovaryPlus.ru – товары, услуги, цены _Cp_');

		$cc = new \App\Controller\Catalog();

		$theme_img = $this->getThemeIndexImage();
        $table_upper = '123';
        
        $table_upper = new FirmUpper();
		
		if ((in_array($_SERVER['REMOTE_ADDR'], ['93.158.228.86','89.22.238.130','93.158.228.86'])) && isset($_GET['debug_mode'])) {
            
            //$table_upper->addFirmUpper(['name'=>'neko-lab','firm_id'=>5364]);
            
			$this->view()
				->set('item', $this->text())
				->set('title', $this->text()->val('title'))
				->set('text_bottom', $text_bottom)
				->set('text_bottom_by_location', $text_bottom_by_location)
				->set('big_mama_banner', $this->text()->getByLink('index/promo')->val('text'))
				->set('promo', $this->getPromo())
				->set('index_promo', app()->isNewTheme() ? $this->getIndexPromo() : '')
				->set('advert_modules', app()->isNewTheme() ? $this->getIndexAdvertModules() : '')
				->set('index_advert_modules', $this->getIndexAdvertModules())
				->set('header', $_header)
				->set('rubrics', $cc->renderRubrics(null, true, 'index'))
				->set('last_reviews', $this->getLastReviews())
				->set('new_companies', $this->getNewCompanies())
                ->set('table_upper',$table_upper->getByCity(\App\Classes\App::stsService()->val('id_service')))
                //->set('table_upper',$table_upper->getFirmUpperByFirmId(5364))
				->set('index_img', $theme_img['index_img'] ? '/public/uploaded/'.$theme_img['index_img'] : '/img3/hero-service.jpg')
				->setTemplate('index')
				->save();
		} else {
			$this->view()
				->set('item', $this->text())
				->set('title', $this->text()->val('title'))
				->set('text_bottom', $text_bottom)
				->set('text_bottom_by_location', $text_bottom_by_location)
				->set('big_mama_banner', $this->text()->getByLink('index/promo')->val('text'))
				->set('promo', $this->getPromo())
				->set('index_promo', app()->isNewTheme() ? $this->getIndexPromo() : '')
				->set('advert_modules', app()->isNewTheme() ? $this->getIndexAdvertModules() : '')
				//->set('advert_modules', $this->getAdvertModules())
				//->set('index_advert_modules', $this->getAdvertIndexModules())
				->set('header', $_header)
				->set('rubrics', $cc->renderRubrics(null, true, 'index'))
				->set('last_reviews', $this->getLastReviews())
				->set('new_companies', $this->getNewCompanies())
                ->set('table_upper',$table_upper->getByCity(\App\Classes\App::stsService()->val('id_service')))
				->set('index_img', $theme_img['index_img'] ? '/public/uploaded/'.$theme_img['index_img'] : '/img3/hero-service.jpg')
				->setTemplate('index')
				->save();
		}
		
		return true;
	}

	/**
	 * @return array
	 */
	public function getThemeIndexImage(){
		$file_index_img = '';
		if (\App\Classes\App::stsService()->val('index_img')){
			$file_index_img = new \App\Model\File();
			$file_index_img->get(explode('~', \Sky4\Model\Utils::getFirstCompositeId(\App\Classes\App::stsService()->val('index_img')))[1]);
			$file_index_img = $file_index_img->getVal('file_subdir_name').'/'.$file_index_img->getVal('file_name').'.'.$file_index_img->getVal('file_extension');
		}
		/*$file_logo_img = '';
		if (\App\Classes\App::stsService()->val('logo_img')){
			$file_logo_img = new \App\Model\File();
			$file_logo_img->get(explode('~', \Sky4\Model\Utils::getFirstCompositeId(\App\Classes\App::stsService()->val('logo_img')))[1]);
			$file_logo_img = $file_logo_img->getVal('file_subdir_name').'/'.$file_logo_img->getVal('file_name').'.'.$file_logo_img->getVal('file_extension');
		}*/
		return [
			'index_img'=>$file_index_img,
			//'logo_img'=>$file_logo_img
			];
	}

	public function getPromo() {
		$fpc = new \App\Controller\FirmPromo();
		return $fpc->renderPromoBlock();
	}
    
    public function getIndexPromo() {
		$fpc = new \App\Controller\FirmPromo();
		return $fpc->renderIndexPromoBlock();
	}

	public function getLastReviews() {
		if (count(app()->location()->getCityIds()) < 1) {
			throw new CException(CException::TYPE_BAD_URL);
		}
        $_city_ids = Utils::prepareWhereCondsFromArray(app()->location()->getCityIds(), 'id_city');
        
        $_where = [
			'AND',
			'`flag_is_active` = :1',
			$_city_ids['where']
		];

		$_params = array_merge([':1' => 1], $_city_ids['params']);
        
        $_fr = new FirmReview();
        $_firm_reviews = $_fr->reader()
                ->setWhere($_where, $_params)
                ->setOrderBy('timestamp_inserting DESC')
                ->objects();

        $limit = 3;
        $i = 0;
        $items = [];
        foreach ($_firm_reviews as $_firm_review) {
            $_firm = new Firm($_firm_review->id_firm());
            if ($_firm->isBlocked()) continue;

            $i++;
            $date = $_firm_review->val('timestamp_inserting');
            $items[] = [
                'firm' => $_firm,
                'user' => $_firm_review->val('user_name'),
                'date' => \Sky4\Helper\DeprecatedDateTime::day($date).' '.\Sky4\Helper\DeprecatedDateTime::monthName($date, 1).' '.\Sky4\Helper\DeprecatedDateTime::year($date),
                'score' => $_firm_review->val('score'),
                'text' => $_firm_review->val('text')
            ];
            if ($i == $limit) break;
        }

		return $this->view()
						->setTemplate('last_reviews')
						->set('items', $items)
						->render();
	}

	public function getNewCompanies() {
		$firm_location_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIds(), 'id');
		$_where = [
			'AND',
			'`flag_is_active` = :1',
            ['OR', '`priority` > :priority', '`id_city` <> :id_city'],
			$firm_location_conds['where'],
		];

		$_params = [':1' => 1, ':priority' => 0, ':id_city' => 76004];
		$_params = array_merge($_params, $firm_location_conds['params']);

		$firm = new Firm();
		$items = $firm->reader()
				->setWhere($_where, $_params)
				->setLimit(10)
				->setOrderBy("`timestamp_inserting` DESC")
				->objects();

		return $this->view()
						->setTemplate('new_companies')
						->set('items', $items)
						->render();
	}

	public function getAdvertModules() {
		$amc = new \App\Controller\AdvertModule();
		return $amc->renderAdvertModuleBlock();
	}

    public function getIndexAdvertModules() {
		$amc = new \App\Controller\AdvertModule();
		return $amc->renderIndexAdvertModuleBlock();
	}

	public function getRSSNews() {
		if (app()->location()->currentId() == '44000') {
			$rss = new \App\Classes\RssFeed(
					"http://novosti44.ru/novosti/totalnews?format=feed", "/var/www/sites/tovaryplus.ru/public/uploaded/rss/novosti44_ru.xml");

			$rss->getRss();
			return $rss->render();
		} else {
			return "";
		}
	}

}
