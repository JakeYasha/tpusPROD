<?php

namespace App\Action\FirmUser\FirmBranch;

use \App\Model\FirmBranch;

class Delete extends \App\Action\FirmUser\FirmBranch {

	public function execute() {
		$params = app()->request()->processGetParams([
			'id' => ['type' => 'array']
		]);

		if (!$params['id']) {
			$params = app()->request()->processGetParams([
				'id' => ['type' => 'int']
			]);
			$params['id'] = [$params['id']];
		}

		if (is_array($params['id'])) {
			$ids = $params['id'];
			foreach ($ids as $id) {
				$firm_branch = new FirmBranch($id);
                if ($firm_branch->exists() && $firm_branch->val('firm_id') == $firm_branch->firm()->id()) {
                    $firm = $firm_branch->firm();
                    
                    if (true) {//!APP_IS_DEV_MODE) {
                        if ($firm->id_service() === 10 && $firm->id_manager() !== null) {
                            $firm_manager = new \App\Model\FirmManager();
                            $firm_manager->getByFirm($firm);

                            if ($firm_manager->exists() && $firm_manager->val('email_default') !== '') {
                                if ($firm_manager->val('email') !== '') {
                                    app()->email()
                                            ->setSubject('Удалена информация о филиале')
                                            ->setTo($firm_manager->val('email'))
                                            ->setModel($firm_branch)
                                            ->setParams(['firm' => $firm,'firm_branch' => $firm_branch,'mode' => 'Удалена'])
                                            ->setTemplate('email_message_to_manager', 'firmbranch')
                                            ->sendToQuery();
                                } else {
                                    app()->email()
                                            ->setSubject('Удалена информация о филиале')
                                            ->setTo($firm_manager->val('email_default'))
                                            ->setModel($firm_branch)
                                            ->setParams(['firm' => $firm,'firm_branch' => $firm_branch,'mode' => 'Удалена'])
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
                                    ->setSubject('Удалена информация о филиале')
                                    ->setTo($email)
                                    ->setModel($firm_branch)
                                    ->setParams(['firm' => $firm,'firm_branch' => $firm_branch,'mode' => 'Удалена'])
                                    ->setTemplate('email_message_to_manager', 'firmbranch')
                                    ->sendToQuery();
                        }
                    }

                    $firm_branch->delete();
				}
			}
		}
		app()->response()->redirect('/firm-user/firm-branch/');
	}

}
