<?php

namespace App\Action;

class FirmManager extends \App\Classes\Action {

	public function __construct() {
		parent::__construct();
		$this->setModel(new \App\Model\FirmManager());
		if (!app()->firmManager()->exists()) {
			app()->response()->redirect('/firm-user/login/');
		}
		app()->frontController()->layout()->setTemplate('lk-manager');
        if (strpos(app()->uri(), '/material/') === FALSE) {
            app()->metadata()->setJsFile('/js/sky/plugins/tinymce-4.1.7/tinymce.min.js')
                    ->setJsFile('/js/js-firm-user.js');
        } else {
            app()->metadata()->setJsFile('/js/js-firm-user.js');
        }
		app()->breadCrumbs()
				->removeElem(0)
				->setElem('Личный кабинет', self::link('/'));
	}

	public function execute() {
		$this->params = app()->request()->processGetParams([
			'mode' => ['type' => 'string'],
			'id' => ['type' => 'int'],
			'query' => ['type' => 'string'],
			'sorting' => ['type' => 'string']
		]);

		$this->text()->getByLink('firm-manager/index');
		app()->metadata()->setFromModel($this->text());
		if (!$this->text()->exists()) {
			app()->metadata()->setTitle('Личный кабинет');
		}

		$presenter = new \App\Presenter\FirmItems();
		$presenter->setItemsTemplateSubdirName('firmmanager');
		$presenter->setLimit(20);
		$presenter->findByManager(app()->firmManager()->getManagerUserIds(), $this->params);

		app()->tabs()->setSortOptions(self::firmSortingOptions());
        
		$this->view()
				->set('bread_crumbs', app()->breadCrumbs()->render(true))
				->set('filters', $this->params)
				->set('text', $this->text())
                ->set('ittest',$presenter->getIdService())
				->set('service', app()->stsService())
				->set('items', $presenter->renderItems())
				->set('pagination', $presenter->pagination()->render(true))
				->set('sorting', app()->tabs()
						->setDisplayMode(false)
						->setActiveSortOption($this->params['sorting'])
						->renderSorting(true))
				->setTemplate('index')
				->save();

		return $this;
	}

	public static function firmSortingOptions() {
		return [
			'default' => ['name' => 'по алфавиту &uarr;', 'expression' => 'company_name ASC'],
			'default-desc' => ['name' => 'по алфавиту &darr;', 'expression' => 'company_name DESC'],
			'rating-asc' => ['name' => 'по рейтингу &uarr;', 'expression' => 'rating ASC, company_name ASC'],
			'rating-desc' => ['name' => 'по рейтингу &darr;', 'expression' => 'rating DESC, company_name ASC'],
		];
	}

	public static function link($link, $filters = [], $current = []) {
		return app()->linkFilter('/firm-manager'.$link, $filters, $current);
	}

}
