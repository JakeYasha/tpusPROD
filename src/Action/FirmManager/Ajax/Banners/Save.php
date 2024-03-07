<?php

namespace App\Action\FirmManager\Ajax\Banners;

use App\Model\Banner as Banners;

class Save extends \App\Action\FirmManager\Banners {

	public function __construct() {
        parent::__construct();
        $this->setModel(new Banners());
    }

    public function execute() {
        $params = app()->request()->processPostParams([
            'id' => ['type' => 'string'],
            'timestamp_ending' => ['type' => 'string'],
        ]);

        if (isset($params['id']) && (int) $params['id'] > 0) {
            $this->model()->reader()->object((int) $params['id']);
        }

        if ($this->model()->exists()) {
            // if (!app()->firmManager()->isNewsEditor()) {
            //     return $this->setResultMessage('У вас нехватает прав для редактирования Баннера. Обратитесь в поддержку.')
            //                     ->renderResult();
            // }
            
            $vals = $this->model()->getVals();
            $vals['timestamp_ending'] = date("y.m.d H:i:s", $params['timestamp_ending']);
            $this->model()->update($vals);

            return [true];
        } else {
            // $params['mnemonic'] = str()->translit(trim($params['name']));
            // $service = new \App\Model\StsService(app()->firmManager()->id_service());
            // $params['id_city'] = $service->val('id_city');
            // $params['id_service'] = app()->firmManager()->id_service();
            // $rubric_id = $params['rubric_id'];
            // unset($params['rubric_id']);
            // if ($this->model()->insert($params)) {
            //     $new_material_rubric = new MaterialRubricModel();
            //     $new_material_rubric->insert(['id_material' => $this->model()->id(), 'id_rubric' => (int)$rubric_id]);
                
            //     return $this->setResultData($this->model()->id())->setResultMessage('Материал № ' . $this->model()->id() . ' добавлен')
            //                     ->renderResult();
            // } else {
            //     return $this->setResultMessage('Не удалось добавить новый материал. Пожалуйста обратитесь в поддержку.')
            //                                     ->renderResult();
            // }
            return [false];
        }
    }

}
