<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Kit as KitModel;
use App\Classes\Pagination;

use App\Presenter\KitItems;

use function app;

class Kits extends FirmManager {

	public function execute() {
		if (app()->firmManager()->isNewsEditor()) {
            app()->metadata()->setTitle('Список подборок');
            app()->breadCrumbs()->setElem('Список подборок', self::link('/firm-manager/kits/'));

            $filters = app()->request()->processGetParams([
                'page' => ['type' => 'int'],
                'sorting' => ['type' => 'string'],
                'query' => ['type' => 'string'],
			]);
            
            $presenter = new KitItems();
            $presenter->setPage($this->getPage())
                    ->find($filters);
            
            app()->tabs()->setSortOptions(self::kitSortingOptions());

            $this->view()
                    ->set('bread_crumbs', app()->breadCrumbs()->render(true))
                    ->set('filters', $filters)
                    ->set('items', $presenter->renderItems())
                    ->set('items_count', $presenter->pagination()->getTotalRecords())
                    ->set('pagination', $presenter->pagination()->render(true))
                    ->set('sorting', app()->tabs()
						->setDisplayMode(false)
						->setActiveSortOption($filters['sorting'])
						->renderSorting(true))
                    ->setTemplate('kits', 'firmmanager')
                    ->save();
		}

		return $this;
	}
    
	public static function kitSortingOptions() {
		return [
			'default' => ['name' => 'по дате &darr;', 'expression' => 'timestamp_inserting DESC'],
			'name-desc' => ['name' => 'по алфавиту &darr;', 'expression' => 'name DESC'],
			'default-asc' => ['name' => 'по дате &uarr;', 'expression' => 'timestamp_inserting ASC'],
			'name-asc' => ['name' => 'по алфавиту &uarr;', 'expression' => 'name ASC'],
		];
	}

}
