<?php

namespace App\Action\FirmManager;

use App\Action\FirmManager;
use App\Model\Rubric;
use App\Model\MaterialRubric;
use App\Model\Material;

use function app;

class MaterialRubrics extends FirmManager {

	public function execute($mode = '') {
		if (app()->firmManager()->isNewsEditor()) {
			if ($mode === 'success') {
				$this->view()
						->set('form', app()->chunk()->set('message', 'Данные успешно сохранены!')->render('forms.common_form_success'))
						->setTemplate('default')
						->save();
			} else {
				app()->breadCrumbs()->setElem('Рубрики газеты', self::link('/material-rubrics/'));
                
                $where = [ 'AND', 'id_service = :id_service', 'type = :type' ];
                $params = [':id_service' => app()->firmManager()->id_service(), ':type' => 'material'];
                $_rubric = new Rubric();
                $_rubrics = $_rubric->reader()
                        ->setWhere($where,$params)
                        ->rowsWithKey('id');
                
                $_material_rubrics = app()->db()->query()
                        ->setText("SELECT 
                                r.`id` AS `rubric_id`, 
                                COUNT(mr.`id_material`) AS `count`, 
                                MAX(mr.`id_material`) AS `last_added_material_id`, 
                                MAX(m.`timestamp_last_updating`),
                                m.`id` AS `last_updated_material_id`
                            FROM `rubric` r
                            JOIN `material_rubric` mr ON r.`id` = mr.`id_rubric` 
                            JOIN `material` m ON m.`id` = mr.`id_material` 
                            WHERE r.`id_service` = :id_service AND r.`type` = :type
                            GROUP BY mr.`id_rubric`")
                        ->setParams([':id_service' => app()->firmManager()->id_service(), ':type' => 'material'])
                        ->fetch();

                $rubrics = [];
                
                $exclude_rubric_ids = [];
                foreach ($_material_rubrics as $rubric) {
                    $last_added_material = '';
                    $last_updated_material = '';
                    if ($rubric['last_added_material_id']) {
                        $last_added_material = new Material($rubric['last_added_material_id']);
                        if (!$last_added_material->exists()) {
                            $last_added_material = '';
                        }
                    }
                    if ($rubric['last_updated_material_id']) {
                        $last_updated_material = new Material($rubric['last_updated_material_id']);
                        if (!$last_updated_material->exists()) {
                            $last_updated_material = '';
                        }
                    }
                    $exclude_rubric_ids []= $rubric['rubric_id'];
                    $_rubric = $_rubrics[$rubric['rubric_id']];
                    $rubrics[$rubric['rubric_id']] = [
                        'is_active' => $_rubric['flag_is_active'],
                        'id' => $_rubric['id'],
                        'name' => $_rubric['name'],
                        'materials_count' => $rubric['count'],
                        'last_added' => $last_added_material,
                        'last_updated' => $last_updated_material
                    ];
                }
                
                foreach($_rubrics as $_rubric_id => $_rubric) {
                    if (!in_array($_rubric_id, $exclude_rubric_ids)) {
                        $rubrics[$_rubric_id] = [
                            'is_active' => $_rubric['flag_is_active'],
                            'id' => $_rubric['id'],
                            'name' => $_rubric['name'],
                            'materials_count' => 0,
                            'last_added' => '',
                            'last_updated' => '',
                        ];
                    }
                }
                
				$this->view()
						->set('bread_crumbs', app()->breadCrumbs()->render(true))
						->set('items', $rubrics)
						->setTemplate('material_rubrics', 'firmmanager')
						->save();
			}
		}

		return $this;
	}

}
