<?php

namespace App\Action\FirmManager\Ajax\Material;

use App\Model\Material;
use App\Model\MaterialRubric;
use App\Presenter\MaterialItems;

class Search extends \App\Action\FirmManager\Ajax\Material {

	public function execute() {
            $filters = app()->request()->processPostParams([
                'page' => ['type' => 'int'],
                'sorting' => ['type' => 'string'],
                'query' => ['type' => 'string'],
			]);
            
            
            $presenter = new MaterialItems();
            //$presenter->page_material_items();
            $presenter->find($filters);

            
            var_dump($presenter->renderItems());
            //return $this->setResultData('items', $presenter->renderItems())->setResultMessage('Материал № добавлен')
                                //->renderResult();
            
            
            
            //app()->tabs()->setSortOptions(self::materialSortingOptions());
                
            //$view_edit = $this->view();
            //$arg = ['items'=>$presenter->renderItems()];
            //app()->chunk()->setArg($arg)->render('firmmanager.presenter_material_items_list_rubric');
            //$view_edit->set('filters', $filters)
                    //->set('items', $presenter->renderItems())->render('firmmanager.presenter_material_items_list_rubric');
            
            //return $view_edit;

}
}
