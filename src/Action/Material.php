<?php

namespace App\Action;

use \App\Model\Material as MaterialModel;
use Sky4\Exception;

class Material extends \App\Classes\Action {

    public function execute($mnemonic = '') {
        if (!APP_IS_DEV_MODE) throw new Exception(Exception::TYPE_BAD_URL);
        app()->frontController()->layout()->setTemplate('material');
        
        $params = app()->request()->processGetParams(['preview_key' => ['type' => 'string']]);
        if ($mnemonic) {
            $_material = new MaterialModel();
            $material = $_material->reader()
                    ->setWhere([
                            'AND', 
                            'mnemonic = :mnemonic',
                            'flag_is_active = :flag_is_active',
                        ], [
                            ':mnemonic' => str_replace('.htm','',$mnemonic),
                            ':flag_is_active' => 1
                        ]
                    )
                    ->objectByConds();
            
            app()->sidebar()
				->setParam('last_news', MaterialModel::getLastNews(4))
				->setParam('last_afisha', MaterialModel::getLastAfisha(1))
				->setTemplate('sidebar_news')
				->setTemplateDir('common');
            if (!$material->exists()) {
                throw new Exception(Exception::TYPE_BAD_URL);
            } else if (!$material->val('flag_is_published') && $material->val('preview_link') !== $params['preview_key']) {
                throw new Exception(Exception::TYPE_BAD_URL);
            } else if (!$material->val('flag_is_published') && $material->val('preview_link') === $params['preview_key']) {
                $this->view()
                        ->set('bread_crumbs', app()->breadCrumbs()->render())
                        ->set('item', $material)
                        ->set('material_image',$_material->image())
                        ->setTemplate('preview')
                        ->save();
            } else if ($material->val('flag_is_published')) {
                $this->view()
                        ->set('bread_crumbs', app()->breadCrumbs()->render())
                        ->set('item', MaterialModel::prepare($material))
                        ->set('material_image',$_material->image())
                        ->set('last_materials', MaterialModel::getLastMaterials(3))
                        ->set('tags', $_material->getTags($material->val('tags')))
                        ->setTemplate('index')
                        ->save();
            }
        }
    }

}
