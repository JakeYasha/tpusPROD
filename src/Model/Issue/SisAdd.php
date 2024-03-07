<?php

namespace App\Model\Issue;

use App\Model\Issue;
use CDate;
use Sky4\Helper\DeprecatedDateTime;
use Sky4\Model;
use Sky4\Model\Utils;


use App\Action\FirmManager;
use App\Model\MaterialRubric;
use App\Model\Rubric;
use App\Model\Material as MaterialModel;
use App\Classes\Pagination;

use App\Presenter\MaterialItems;



class SisAdd extends \Sky4\Model\Form {
	public function __construct(Model $model = null, $params = null) {
		if (!($this->model() instanceof Issue)) {
			$this->setModel(new Issue());
		}
		parent::__construct($model, $params);
	}
    
        
        
        public function fields() {
            
            return [
                'id_issue' => [
                    'col' => [
                        'default_val' => '',
                        'flags' => 'not_null',
                        'name' => 'id_issue',
                        'type' => 'int_2',
                    ],
                    'elem' => 'text_field',
                    'label' => 'id_issue'
                ],
                'id_material' => [
                    'col' => [
                        'default_val' => '',
                        'flags' => 'not_null',
                        'name' => 'ID материала',
                        'type' => 'int_2',
                    ],
                    'elem' => 'text_field',
                    'label' => 'id_material'
                ],
            ];
                    
        }
        
}
