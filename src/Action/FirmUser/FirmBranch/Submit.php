<?php

namespace App\Action\FirmUser\FirmBranch;

class Submit extends \App\Action\FirmUser\FirmBranch {

	public function execute() {
		$form = new \App\Model\FirmBranch\UserForm();
		$form->setInputVals($_POST);
		if (!$form->errorHandler()->hasErrors()) {
			$date_time = new \Sky4\Helper\DateTime();
			$vals = $form->getVals();
            
            $firm = new \App\Model\Firm();

			if (isset($vals['id_firm']) && (int)$vals['id_firm'] && isset($vals['id_service']) && (int)$vals['id_service']) {
				$firm->reader()
                        ->setWhere(['AND', 'id_firm = :id_firm', 'id_service = :id_service'],[':id_firm' => $vals['id_firm'], ':id_service' => $vals['id_service']])
                        ->objectByConds();
			}
            $firm_branch = new \App\Model\FirmBranch();
            
            if (isset($_POST['id']) && (int)$_POST['id'] > 0) {
                $firm_branch->reader()->object((int)$_POST['id']);
            }
            
            $city = new \App\Model\StsCity((int)$_POST['id_city']);
            if ($city->exists()) {
                $vals['id_region_country'] = $city->val('id_region_country');
                $vals['id_country'] = $city->val('id_country');
            }
            
            $vals['timestamp_last_updating'] = \Sky4\Helper\DeprecatedDateTime::now();
            $vals['random_value'] = rand(1, 1000);
            $vals['priority'] = $firm->val('priority');
            $vals['rating'] = $firm->val('rating');
            $vals['id_firm_user'] = $firm->val('id_firm_user');
            $vals['flag_is_active'] = $firm->val('flag_is_active');   

            $_mode = 'insert';
            if (!$firm_branch->exists()) {
                $firm_branch->insert($vals);
            } else {
                if ($firm_branch->val('id_firm') != $firm->val('id_firm') || $firm_branch->val('id_service') != $firm->val('id_service')) {
                    app()->response()->redirect('/firm-user/firm-branch/');
                }
                $firm_branch->update($vals);
                $_mode = 'update';
            }
            if (true) {//!APP_IS_DEV_MODE) {
                if ($firm->id_service() === 10 && $firm->id_manager() !== null) {
                    $firm_manager = new \App\Model\FirmManager();
                    $firm_manager->getByFirm($firm);

                    if ($firm_manager->exists() && $firm_manager->val('email_default') !== '') {
                        if ($firm_manager->val('email') !== '') {
                            app()->email()
                                    ->setSubject($_mode == 'update' ? 'Обновлена информация о филиале' : 'Добавлена информация о филиале')
                                    ->setTo($firm_manager->val('email'))
                                    ->setModel($firm_branch)
                                    ->setParams(['firm' => $firm,'firm_branch' => $firm_branch,'mode' => $_mode == 'update' ? 'Обновлена' : 'Добавлена'])
                                    ->setTemplate('email_message_to_manager', 'firmbranch')
                                    ->sendToQuery();
                        } else {
                            app()->email()
                                    ->setSubject($_mode == 'update' ? 'Обновлена информация о филиале' : 'Добавлена информация о филиале')
                                    ->setTo($firm_manager->val('email_default'))
                                    ->setModel($firm_branch)
                                    ->setParams(['firm' => $firm,'firm_branch' => $firm_branch,'mode' => $_mode == 'update' ? 'Обновлена' : 'Добавлена'])
                                    ->setTemplate('email_message_to_manager', 'firmbranch')
                                    ->sendToQuery();
                        }
                    }
                } else {
                    $service = new \App\Model\StsService();
                    $service->reader()
                            ->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $firm->id_service()])
                            ->objectByConds();
                    $email = $service->val('email');

                    app()->email()
                            ->setSubject($_mode == 'update' ? 'Обновлена информация о филиале' : 'Добавлена информация о филиале')
                            ->setTo($email)
                            ->setModel($firm_branch)
                            ->setParams(['firm' => $firm,'firm_branch' => $firm_branch,'mode' => $_mode == 'update' ? 'Обновлена' : 'Добавлена'])
                            ->setTemplate('email_message_to_manager', 'firmbranch')
                            ->sendToQuery();
                }
            }
            
			app()->response()->redirect('/firm-user/firm-branch/?success');
		}
	}

}
