<?php

namespace App\Action\Issue;

use App\Model\Issue\ManagerForm;

class Submit extends \App\Action\Issue {

	public function execute() {
        if (app()->firmManager()->exists()) {
            $form = new ManagerForm($this->model());
            $vals = $_POST;
            $vals['id_service'] = app()->firmManager()->id_service();
            $sts_service = new \App\Model\StsService(app()->firmManager()->id_service());
            $vals['id_city'] = $sts_service->exists() ? $sts_service->val('id_city') : '76004';
            $form->setInputVals($vals);
            
            $errors = $form->errorHandler()->getErrors();

            if (!$errors) {
                $form->model()->setVals($form->getVals());
                if (!$form->validate()) {
                    $form->errorHandler()->setError('', 'Пожалуйста проверьте правильность ввода данных в форму.');
                } else if ($form->model()->exists()) {
                    $form->model()->update($form->getVals());
                } else if (!$form->model()->insert($form->getVals())) {
                    $form->errorHandler()->setError('', 'Форма не отправлена, свяжитесь с администратором.');
                }
            }

            if ($form->errorHandler()->hasErrors()) {
                $form->errorHandler()->saveErrorsInSession()
                        ->saveValsInSession($form->getVals());
                //app()->response()->redirect('/firm-manager/issues/');
            }
        }
        
		//app()->response()->redirect('/firm-manager/issues/');
	}

}
