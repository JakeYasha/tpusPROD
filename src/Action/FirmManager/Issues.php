<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Issue as IssueModel;
use App\Classes\Pagination;

use App\Presenter\IssueItems;

use function app;

class Issues extends FirmManager {

	public function execute() {
		if (app()->firmManager()->isNewsEditor()) {
            app()->metadata()->setTitle('Список выпусков');
            app()->breadCrumbs()->setElem('Список выпусков', self::link('/firm-manager/issues/'));

            $filters = app()->request()->processGetParams([
                'page' => ['type' => 'int'],
                'sorting' => ['type' => 'string'],
                'query' => ['type' => 'string'],
			]);
            
            $presenter = new IssueItems();
            $presenter->setPage($this->getPage())
                    ->find($filters);
            
            app()->tabs()->setSortOptions(self::issueSortingOptions());

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
                    ->setTemplate('issues', 'firmmanager')
                    ->save();
		}

		return $this;
	}
    
	public static function issueSortingOptions() {
		return [
			'default' => ['name' => 'по дате &darr;', 'expression' => 'timestamp_inserting DESC'],
			'name-desc' => ['name' => 'по алфавиту &darr;', 'expression' => 'name DESC'],
			'default-asc' => ['name' => 'по дате &uarr;', 'expression' => 'timestamp_inserting ASC'],
			'name-asc' => ['name' => 'по алфавиту &uarr;', 'expression' => 'name ASC'],
		];
	}

}
