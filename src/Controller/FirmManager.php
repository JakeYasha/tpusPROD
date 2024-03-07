<?php

namespace App\Controller;

class FirmManager extends \App\Classes\Controller {

	/**
	 * @var Firm $firm 
	 */
	public $firm = null;

	public function renderSidebar() {
        if (app()->firmManager()->id_service() == 10) {
            $menu = [
                ['link' => self::link('/sts-service/'), 'name' => 'О представительстве', 'count' => null, 'first' => true],
                ['link' => self::link('/index/'), 'name' => 'Список фирм', 'count' => null],
                ['link' => self::link('/banners/'), 'name' => 'Баннеры', 'count' => null],
                ['link' => self::link('/advert-modules/'), 'name' => 'Рекламные модули', 'count' => null],
                ['link' => self::link('/calls/'), 'name' => 'Звонки', 'count' => null],
                ['link' => self::link('/statistics/'), 'name' => 'Запросы tovaryplus.ru', 'count' => null],
                ['link' => self::link('/statistics727373/'), 'name' => 'Запросы 727373.ru', 'count' => null],
                ['link' => self::link('/yml/categories/'), 'name' => 'Модерация категорий YML', 'count' => null],
                ['link' => self::link('/yml/offers/'), 'name' => 'Модерация предложений YML', 'count' => null, 'last' => true],
            ];
        } else {
            $menu = [
                ['link' => self::link('/sts-service/'), 'name' => 'О представительстве', 'count' => null, 'first' => true],
                ['link' => self::link('/index/'), 'name' => 'Список фирм', 'count' => null],
                ['link' => self::link('/banners/'), 'name' => 'Баннеры', 'count' => null],
                ['link' => self::link('/advert-modules/'), 'name' => 'Рекламные модули', 'count' => null],
                ['link' => self::link('/yml/categories/'), 'name' => 'Модерация категорий YML', 'count' => null],
                ['link' => self::link('/yml/offers/'), 'name' => 'Модерация предложений YML', 'count' => null, 'last' => true],
            ];
        }
        
        if (app()->firmManager()->isNewsEditor()) {
            $menu []= ['link' => self::link('/materials/'), 'name' => 'Материалы', 'count' => null, 'first' => true];
            $menu []= ['link' => self::link('/issues/'), 'name' => 'Выпуски', 'count' => null, 'last' => true];
            
                }
        if (app()->firmManager()->id_service() == 10) {
            $menu [] = ['link' => self::link('/firmtest/'), 'name' => 'SEO фирмы', 'count' => null, 'first' => true];
            $menu [] = ['link' => self::link('/instatable/'), 'name' => 'INSTAGRAM таблица', 'count' => null];
            $menu [] = ['link' => self::link('/parsetable/'), 'name' => 'Таблица парсинга', 'count' => null, 'last' => true];
        }
		$uri = app()->request()->getRequestUri();
		foreach ($menu as $k => $v) {
			if (str()->pos($uri, $v['link']) !== false) {
				$menu[$k]['active'] = true;
			} else {
				$menu[$k]['active'] = false;
			}
		}

		return $this->view()
						->set('menu', $menu)
						->set('firm', $this->firm)
						->setTemplate('sidebar')
						->render();
	}

	protected function setFirm($id_firm, $id_service, $replace = false) {
		if ($this->firm === null || $replace) {
			$firm = new Firm();
			$firm->getByIdFirm($id_firm);
			if ($firm->exists()) {
				if (app()->firmManager()->hasAccess($firm)) {
					//app()->firmUser()->userComponent()->removeFromSession();
					$this->firm = $firm;
				} else {
					throw new CException(CException::TYPE_BAD_URL);
				}
			}
		}

		return $this;
	}

	//

	public function __construct() {
		parent::__construct();
		$this->setModelName('FirmManager');
		app()->frontController()->layout()->setTemplate('lk-manager');
		app()->breadCrumbs()
				->removeElem(0)
				->setElem('Личный кабинет', self::link('/'));

		return true;
	}

	public static function link($link, $filters = [], $current = []) {
		return \App\Action\FirmManager::link($link, $filters, $current);
	}

	/**
	 * @return \App\Model\FirmManager
	 */
	public function model() {
		return parent::model();
	}

}
