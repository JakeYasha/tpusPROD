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
            $presenter->page_material_items();

            return $this->setResultData(['html'=>$presenter->find($filters,9)->renderItems()])->setResultMessage('Материал № добавлен')
                ->renderResult();
            
        }
        
}
