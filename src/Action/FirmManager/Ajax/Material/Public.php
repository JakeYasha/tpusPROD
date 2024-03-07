<?php

namespace App\Action\FirmManager\Ajax\Material;

use App\Model\Material as MaterialModel;
use App\Model\MaterialRubric as MaterialRubricModel;

class Publish extends \App\Action\FirmManager\Ajax\Material {

	public function __construct() {
        parent::__construct();
        $this->setModel(new MaterialModel());
    }

    public function execute() {
        $params = app()->request()->processPostParams([
            'id' => ['type' => 'string'],
            'rubric_id' => ['type' => 'int'],
            'public' => ['type' => 'int']
        ]);

        if (isset($params['id']) && (int) $params['id'] > 0) {
            $this->model()->reader()->object((int) $params['id']);
        }

        if ($this->model()->exists()) {
            if (!app()->firmManager()->isNewsEditor()) {
                return $this->setResultMessage('У вас нехватает прав для редактирования материалов. Обратитесь в поддержку.')
                                ->renderResult();
            }
            var_dump($params['public']); // айайай Яков )))
            $vals = $this->model()->getVals();
            
            $vals['flag_is_published'] = $params['public'];
            $vals['flag_is_active'] = 1;
            $this->model()->update($vals);
            $old_material_rubric = new MaterialRubricModel();
            $old_material_rubric = $old_material_rubric->reader()
                    ->setWhere(
                        ['AND', 'id_material = :id_material'], 
                        [':id_material' => $this->model()->id()]
                    )
                    ->objectByConds();

            if ($old_material_rubric->exists()) {
                $old_material_rubric->delete();
            }
            
            $new_material_rubric = new MaterialRubricModel();
            $new_material_rubric->insert(['id_material' => $this->model()->id(), 'id_rubric' => (int)$params['rubric_id']]);
            
            return $this->setResultData($this->model()->id())->setResultMessage('Материал № ' . $this->model()->id() . ' изменил статус публикации')
                            ->renderResult();
        }else{
            return $this->setResultMessage('Материала не существует! Сначала сохраните, материал, пожалуйста.')
                                ->renderResult();
        }
    }

}
