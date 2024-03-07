<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Material as MaterialModel;
use App\Classes\Pagination;

use App\Presenter\MaterialItems;

use function app;

class Materials extends FirmManager {

	public function execute() {
		if (app()->firmManager()->isNewsEditor()) {
            app()->metadata()->setTitle('Список материалов');
            app()->breadCrumbs()->setElem('Список материалов', self::link('/firm-manager/materials/'));

            $filters = app()->request()->processGetParams([
                'page' => ['type' => 'int'],
                'sorting' => ['type' => 'string'],
                'query' => ['type' => 'string'],
			]);
            
            $presenter = new MaterialItems();
            $presenter->setPage($this->getPage())
                    ->find($filters);
            
            app()->tabs()->setSortOptions(self::materialSortingOptions());
            
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
                    ->setTemplate('materials', 'firmmanager')
                    ->save();
		}

		return $this;
	}
    
	public static function materialSortingOptions() {
		return [
			'default' => ['name' => 'по дате &darr;', 'expression' => 'timestamp_inserting DESC'],
			'name-desc' => ['name' => 'по алфавиту &darr;', 'expression' => 'name DESC'],
			'default-asc' => ['name' => 'по дате &uarr;', 'expression' => 'timestamp_inserting ASC'],
			'name-asc' => ['name' => 'по алфавиту &uarr;', 'expression' => 'name ASC'],
		];
	}

}
