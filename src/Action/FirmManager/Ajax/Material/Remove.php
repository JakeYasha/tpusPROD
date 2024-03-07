<?php

namespace App\Action\FirmManager\Ajax\Material;

use App\Model\Material;
use App\Model\MaterialRubric;

class Remove extends \App\Action\FirmManager\Ajax\Material {

	public function execute() {
		$params = app()->request()->processPostParams([
			'material_id' => ['type' => 'int']
		]);

        if (app()->firmManager()->isNewsEditor()) {
            $material = new Material((int)$params['material_id']);
            if (!$material->exists()) {
                return $this->setResultMessage('Такого материала не существует. Обратитесь в поддержку.')
                        ->renderResult();
            }
            
            if (app()->firmManager()->id_service() != $material->val('id_service')) {
                return $this->setResultMessage('У вас нехватает прав для удаления материала. Обратитесь в поддержку.')
                        ->renderResult();
            }
            
            $_material_rubric = new MaterialRubric();
            $material_rubric = $_material_rubric->reader()
                    ->setWhere(['AND', 'id_material = :id_material'],[':id_material' => $material->id()])
                    ->objectByConds();

            $material_rubric->delete();
            
            $material->delete();
            
            return $this->setResultMessage('ok')
                    ->renderResult();
        }
        return $this->setResultMessage('У вас нехватает прав для редактирования материалов. Обратитесь в поддержку.')
                ->renderResult();
	}

}
