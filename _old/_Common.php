<?php

namespace App\Controller;

class Common extends \App\Classes\Controller {

	public function renderCssFiles() {
		$version = '1';
		if (defined('APP_IS_DEV_MODE') && APP_IS_DEV_MODE) {
			$version .= '-' . ceil(time() / 1000);
		}
		/* \app()->metadata()
		  ->setCssFile('/css/css.css?' . $version);
		  \app()->metadata()
		  ->setCssFile('/plugins/fancybox/jquery.fancybox.css?' . $version); */
		return \app()->metadata()->renderCssFiles();
	}

	public function renderFoot() {
		return $this->view()
						->setTemplate('foot')
						->render();
	}

	public function renderFooter() {
		return $this->view()
						->set('copyright', \app()->config()->get('footer.copyright', ''))
						->setTemplate('footer')
						->render();
	}

	public function renderHead() {
		return $this->view()
						->setTemplate('head')
						->render();
	}

	public function renderHeader() {
		return $this->view()
						->setTemplate('header')
						->render();
	}

	public function renderJsFiles() {
		$version = '1';
		if (defined('APP_IS_DEV_MODE') && APP_IS_DEV_MODE) {
			$version .= '-' . ceil(time() / 1000);
		}
		/*
		  // vendors
		  \app()->metadata()
		  // jquery
		  ->setJsFile('/plugins/jquery-1.12.3.min.js?' . $version)
		  // jquery-plugins
		  ->setJsFile('/plugins/fancybox/jquery.fancybox.pack.js?' . $version);

		  // libs
		  \app()->metadata()
		  ->setJsFile(STATIC_FILES_URL . '/common/js/libs-4.1.js?' . $version)
		  ->setJsFile(STATIC_FILES_URL . '/common/js/libs-app-utils-4.1.js?' . $version);

		  // classes
		  \app()->metadata()->setJsFile(STATIC_FILES_URL . '/common/js/classes-validator-4.1.js?' . $version);

		  // plugins
		  \app()->metadata()->setJsFile(STATIC_FILES_URL . '/common/js/plugin-form-4.1.js?' . $version);

		  // app
		  \app()->metadata()
		  ->setJsFile('/js/app.js?' . $version)
		  ->setJsFile('/js/app-common.js?' . $version);
		 */
		return \app()->metadata()->renderJsFiles();
	}

	public function renderTopMenu() {
		$menu_item = new \App\Model\MenuItem();
		$menu_item->getByAlias('top_menu');
		return $this->view()
						->set('items', $menu_item->getItems(\app()->request()->getRequestUri('')))
						->setTemplate('top_menu')
						->render();
	}

}
