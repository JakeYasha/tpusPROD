<?php

namespace App\Classes;

use App\Model\StsCity;
use \Sky4\Widget\InterfaceElem\Autocomplete as CAutocomplete;
use Sky4\App;

class Sidebar {

	protected $params = [];
	protected $link = '';
	protected $template = 'sidebar';
	protected $template_dir = 'common';
	protected $view = null;

	public function renderDefault() {
		return $this
						->setCityBlock()
						->render();
	}

	public function render() {
		$this->setCityBlock()
				->view()
				->set('url', $this->getLink())
				->setTemplate($this->getTemplate());

		foreach ($this->params as $k => $v) {
			$this->view()->set($k, $v);
		}

		return $this->view()->render();
	}

	private function setCityBlock() {
		$sc = new StsCity();
		$autocomplete = new CAutocomplete();
		$autocomplete
				->setName('code')
				->setAttrs([
					'id' => 'sidebar-town-autocomplete',
					'placeholder' => 'Введите название...'
				])
				->setParams([
					'model_alias' => 'sts-city',
					'val_mode' => 'id',
					'field_name' => 'name'
		]);

		$main_top_menu_items = array();
		if (app()->location()->stats('count_goods') > 0) {
			$main_top_menu_items [] = array(
				'title' => 'Каталог товаров и услуг',
				'active' => str()->pos(app()->uri(), '/catalog/') !== false && str()->pos(app()->uri(), '/firm/') === false,
				'link' => app()->link('/catalog/'),
				'count' => str()->addSpaces(app()->location()->stats('count_goods')));
		}

		$main_top_menu_items [] = array(
			'title' => 'Каталог фирм',
			'active' => str()->pos(app()->uri(), '/firm/catalog/') !== false || str()->pos(app()->uri(), '/firm/bytype/') !== false,
			'link' => app()->link('/firm/catalog/'),
			'count' => str()->addSpaces(app()->location()->stats('count_firms')));
		
		$main_top_menu_items [] = array(
			'title' => 'Скидки и акции',
			'active' => str()->pos(app()->uri(), '/advert-module/') !== false,
			'link' => '/' . app()->location()->currentId() . '/advert-module/');		
			
			

		$main_top_menu_items = $this->_setMenuItemsClasses($main_top_menu_items);

		$main_middle_menu_items = array();
					
		$main_middle_menu_items []= array(
            'title'     => 'Размещение рекламы на сайте',
            'active'    => str()->pos(app()->uri(), '/service/') !== false,
            'link'      => '/service/');	
			

		$is_yar = app()->location()->currentId() == '76004';
		/*$has_firm_videos = app()->location()->stats('count_videos') > 0;
		if ($is_yar) {
			$main_middle_menu_items [] = array(
				'title' => 'Конкурсы',
				'active' => str()->pos(app()->uri(), '/photo-contest/') !== false,
				'link' => '/photo-contest/');
		}
		if ($has_firm_videos) {
			$main_middle_menu_items [] = array(
				'title' => 'Видео о фирмах',
				'active' => str()->pos(app()->uri(), '/firm-video/') !== false,
				'link' => '/' . app()->location()->currentId() . '/firm-video/',
				'count' => str()->addSpaces(app()->location()->stats('count_videos')));
		}
		if ($is_yar) {
			$main_middle_menu_items [] = array(
				'title' => 'Защита прав потребителей',
				'active' => str()->pos(app()->uri(), '/consumer/') !== false,
				'link' => '/consumer/');
			$main_middle_menu_items [] = array(
				'title' => 'Рекламные издания',
				'active' => str()->pos(app()->uri(), '/page/show/commercialissues.htm') !== false,
				'link' => '/page/show/commercialissues.htm');
		}*/

		$main_middle_menu_items = $this->_setMenuItemsClasses($main_middle_menu_items);

		$this->view()
				->set('topCities', $sc->reader()->setOrderBy('`position_weight` DESC')->setLimit(10)->objects())
				->set('main_top_menu_items', $main_top_menu_items)
				->set('main_middle_menu_items', $main_middle_menu_items)
				->set('autocomplete', $autocomplete->render())
				->setTemplate('sidebar');

		return $this;
	}

	private function _setMenuItemsClasses($menu_items) {
		for ($i = 0; $i < count($menu_items); $i++) {
			if ($i == 0) {
				if (!isset($menu_items[$i]['class'])) $menu_items[$i]['class'] = array();
				$menu_items[$i]['class'] [] = 'cms_tree_first';
			}
			if ($i == count($menu_items) - 1) {
				if (!isset($menu_items[$i]['class'])) $menu_items[$i]['class'] = array();
				$menu_items[$i]['class'] [] = 'cms_tree_last';
			}
			if (isset($menu_items[$i]['active']) && $menu_items[$i]['active']) {
				if (!isset($menu_items[$i]['class'])) $menu_items[$i]['class'] = array();
				$menu_items[$i]['class'] [] = 'active';
			}

			if (isset($menu_items[$i]['class'])) $menu_items[$i]['class'] = implode(' ', array_filter($menu_items[$i]['class']));
		}

		return $menu_items;
	}

	/**
	 * 
	 * @param string $param_name
	 * @param string $param_value
	 * @return ASidebar
	 */
	public function setParam($param_name, $param_value) {
		$this->params[$param_name] = $param_value;

		return $this;
	}

	public function getParams() {
		return $this->params;
	}

	public function getParam($param_name) {
		return isset($this->params[$param_name]) ? $this->params[$param_name] : null;
	}

	public function getLink() {
		return $this->link;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function setLink($url) {
		$this->link = $url;
		return $this;
	}

	public function setTemplate($template) {
		$this->template = $template;
		return $this;
	}

	public function setTemplateDir($template_dir) {
		$this->template_dir = $template_dir;
		return $this;
	}

	public function view() {
		if ($this->view === null) {
			$this->view = new \Sky4\View();
			$this->view->setBasicSubdirName($this->template_dir);
		}
		return $this->view;
	}

}
