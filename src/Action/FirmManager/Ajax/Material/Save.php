<?php

namespace App\Action\FirmManager\Ajax\Material;

use App\Model\Material as MaterialModel;
use App\Model\MaterialRubric as MaterialRubricModel;

class Save extends \App\Action\FirmManager\Ajax\Material {

	public function __construct() {
        parent::__construct();
        $this->setModel(new MaterialModel());
    }

    public function execute() {
        $params = app()->request()->processPostParams([
            'id' => ['type' => 'string'],
            'name' => ['type' => 'string'],
            'preview_link' => ['type' => 'string'],
            'short_text' => ['type' => 'string'],
            'organization' => ['type' => 'string'],
            'address' => ['type' => 'string'],
            'image' => ['type' => 'string'],
            'is_popular' => ['type' => 'string'],
            'is_recommend' => ['type' => 'string'],
            'type' => ['type' => 'string'],
            'material_source_name' => ['type' => 'string'],
            'material_source_url' => ['type' => 'string'],
            'advert_restrictions' => ['type' => 'string'],
            'meta_title' => ['type' => 'string'],
            'meta_keywords' => ['type' => 'string'],
            'meta_description' => ['type' => 'string'],
            'tags' => ['type' => 'string'],
            'constructor_data' => ['type' => 'string'],
            'text' => ['type' => 'string'],
            'rubric_id' => ['type' => 'int'],
        ]);

        if (isset($params['id']) && (int) $params['id'] > 0) {
            $this->model()->reader()->object((int) $params['id']);
        }

        if ($this->model()->exists()) {
            if (!app()->firmManager()->isNewsEditor()) {
                return $this->setResultMessage('У вас нехватает прав для редактирования материалов. Обратитесь в поддержку.')
                                ->renderResult();
            }
            
            $vals = $this->model()->getVals();
            $vals['name'] = $params['name'];
            $vals['preview_link'] = $params['preview_link'];
            $vals['mnemonic'] = str()->translit(trim($params['name']));
            $vals['short_text'] = $params['short_text'];
            $vals['organization'] = $params['organization'];
            $vals['address'] = $params['address'];
            $vals['image'] = $params['image'];
            $vals['is_popular'] = $params['is_popular'];
            $vals['is_recommend'] = $params['is_recommend'];
            $vals['type'] = $params['type'];
            $vals['material_source_name'] = $params['material_source_name'];
            $vals['material_source_url'] = $params['material_source_url'];
            $vals['advert_restrictions'] = $params['advert_restrictions'];
            $vals['meta_title'] = $params['meta_title'];
            $vals['meta_keywords'] = $params['meta_keywords'];
            $vals['meta_description'] = $params['meta_description'];
            $vals['tags'] = $params['tags'];
            $vals['constructor_data'] = $params['constructor_data'];
            $vals['text'] = $params['text'];
            $vals['flag_is_active'] = 1;
            $this->model()->update($vals);
            $old_material_rubric = new MaterialRubricModel();
            $old_material_rubric = $old_material_rubric->reader()->setWhere(['AND', 'id_material = :id_material'], [':id_material' => $this->model()->id()])
                    ->objectByConds();

            if ($old_material_rubric->exists()) {
                $old_material_rubric->delete();
            }
            
            $new_material_rubric = new MaterialRubricModel();
            $new_material_rubric->insert(['id_material' => $this->model()->id(), 'id_rubric' => (int)$params['rubric_id']]);
            
            return $this->setResultData($this->model()->id())->setResultMessage('Материал № ' . $this->model()->id() . ' сохранен')
                            ->renderResult();
        } else {
            $params['mnemonic'] = str()->translit(trim($params['name']));
            $service = new \App\Model\StsService(app()->firmManager()->id_service());
            $params['id_city'] = $service->val('id_city');
            $params['id_service'] = app()->firmManager()->id_service();
            $rubric_id = $params['rubric_id'];
            unset($params['rubric_id']);
            if ($this->model()->insert($params)) {
                $new_material_rubric = new MaterialRubricModel();
                $new_material_rubric->insert(['id_material' => $this->model()->id(), 'id_rubric' => (int)$rubric_id]);
                
                return $this->setResultData($this->model()->id())->setResultMessage('Материал № ' . $this->model()->id() . ' добавлен')
                                ->renderResult();
            } else {
                return $this->setResultMessage('Не удалось добавить новый материал. Пожалуйста обратитесь в поддержку.')
                                                ->renderResult();
            }
        }
    }

}
