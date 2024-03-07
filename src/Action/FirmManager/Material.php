<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Material as MaterialModel;
use App\Model\MaterialRubric;
use App\Model\Rubric;
use Sky4\Model\Utils;
use function app;

class Material extends FirmManager {

	public function execute($id_material = 0) {
		if (app()->firmManager()->isNewsEditor()) {
            app()->metadata()->setJsFile('/js/jquery.simulate.js')
                    ->setJsFile('/js/jquery.minicolors.js')
                    ->setJsFile('/js/sky/plugins/tinymce-5.1.0/tinymce.min.js')
                    ->setJsFile('/js/styler.material.constructor.js?v=' . time())
                    ->setJsFile('/js/material.constructor.js?v=' . time());
            app()->metadata()->setCssFile('/css/jquery.minicolors.css')
                    ->setCssFile('/css/material.constructor.css?v=' . time());
            
            $where = [ 
                'AND', 
                'id_service = :id_service', 
                [
                    'OR', 
                    'type = :type1', 
                    'type = :type2',
                    'type = :type3',
                    'type = :type4'
                ]
            ];
            $params = [
                ':id_service' => app()->firmManager()->id_service(), 
                ':type1' => 'material', 
                ':type2' => 'universal',
                ':type3' => 'news',
                ':type4' => 'afisha'
            ];
            $_rubric = new Rubric();
            $_rubrics = $_rubric->reader()
                    ->setWhere($where,$params)
                    ->setOrderBy('type ASC, name ASC')
                    ->objects();
            
            if ($id_material) {
                $material = new MaterialModel($id_material);
                if ($material->exists()) {
                    app()->metadata()->setTitle('Конструктор материала № ' . $material->id());
                    app()->breadCrumbs()->setElem('Список материалов', self::link('/materials/'));
                    app()->breadCrumbs()->setElem('Материал № ' . $material->id(), self::link('/material/'));
                } else {
                    app()->response()->redirect('/firm-manager/material/');
                }
                
                $_material_rubric = new MaterialRubric();
                $material_rubric = $_material_rubric->reader()
                        ->setWhere(['AND', 'id_material = :id_material'],[':id_material' => $material->id()])
                        ->objectByConds();
                
                $material_rubric_id = $material_rubric->exists() ? $material_rubric->val('id_rubric') : 0;

                app()->frontController()->layout()->setTemplate('material.constructor');
                
                
                
                $this->view()
                        ->set('bread_crumbs', app()->breadCrumbs()->render(true))
                        ->set('material', $material)
                        ->set('advert_restrictions', \Sky4\Container::getList('AdvertRestrictions'))
                        ->set('rubrics', $_rubrics)
                        ->set('rubric', $material_rubric_id)
                        ->setTemplate('material', 'firmmanager')
                        ->save();
            } else {
                app()->metadata()->setTitle('Конструктор нового материала');
                app()->breadCrumbs()->setElem('Список материалов', self::link('/materials/'));
                app()->breadCrumbs()->setElem('Новый материал', self::link('/material/'));
                $material = new MaterialModel();

                app()->frontController()->layout()->setTemplate('material.constructor');

                $this->view()
                        ->set('bread_crumbs', app()->breadCrumbs()->render(true))
                        ->set('material', $material)
                        ->set('advert_restrictions', \Sky4\Container::getList('AdvertRestrictions'))
                        ->set('rubrics', $_rubrics)
                        ->setTemplate('material', 'firmmanager')
                        ->save();
            }
		}

		return $this;
	}

}
