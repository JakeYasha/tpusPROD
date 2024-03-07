<?php

namespace App\Action\Price;

use App\Model\Firm;
use App\Model\FirmManager;
use App\Model\PriceRequest;
use App\Model\PriceRequest\FormAdd;
use App\Model\Price;
use App\Model\StsService;
use function app;

class Request extends \App\Action\Price {

	public function execute($mode) {
        $dev_data = [];
		if ($mode === 'submit') {
			$params = app()->request()->processPostParams([
				'g-recaptcha-response' => ['type' => 'string']
			]);

			$form = new FormAdd(new PriceRequest());

			$form->setEditableFieldsNames(['user_name', 'user_email', 'user_phone', 'brief_text', 'text', 'id_firm', 'id_service', 'id_price'])
					->setInputVals($_POST);

			if ($form->validate()) {
				// Добавляем простейшую проверку
				if (($form->getVals() === null) || !is_array($form->getVals())) {
					$form->errorHandler()->setError('', 'Форма не отправлена, свяжитесь с администратором.');
					die(json_encode([
						'error_message' => '<ul><li>' . $form->errorHandler()->getLastErrorMessage() . '</li></ul>'
					]));
				}

				$hidden_data = app()->request()->processPostParams([
					'id_firm' => ['type' => 'int'],
					'id_service' => ['type' => 'int'],
					'id_price' => ['type' => 'int'],
				]);

                $id_firm = $hidden_data['id_firm'];
                $id_service = isset($hidden_data['id_service']) && $hidden_data['id_service'] ? $hidden_data['id_service'] : null;
                $id_price = isset($hidden_data['id_price']) && $hidden_data['id_price'] ? $hidden_data['id_price'] : null;

                $firm = new Firm();
                if ($id_service === null) {
                    $firm->getByIdFirm((int) $id_firm);
                } else {
                    $firm->getByIdFirmAndIdService($id_firm, $id_service);
                }

                unset($hidden_data['id_service']);
                $hidden_data['id_firm'] = $firm->id();
                
                $dev_data []= 'firm.id = ' . $hidden_data['id_firm'];

				$insert_array = $form->getVals();
				foreach ($hidden_data as $k => $v) {
					$insert_array[$k] = $v;
				}

				if ($form->getVal('user_email') || $form->getVal('user_phone')) {
					
				} else {
					$form->errorHandler()->setError('', 'Необходимо указать контактные данные - телефон или адрес электронной почты!');
					die(json_encode([
						'error_message' => '<ul><li>' . $form->errorHandler()->getLastErrorMessage() . '</li></ul>'
					]));
				}

				if (!app()->capcha()->isValid($params['g-recaptcha-response'])) {
					$form->errorHandler()->setError('', 'Вы робот?');
					$form->errorHandler()->saveErrorsInSession()
							->saveValsInSession($form->getVals());

					die(json_encode([
						'error_message' => '<ul><li>' . $form->errorHandler()->getLastErrorMessage() . '</li></ul>'
					]));
				}

				if (!$form->model()->insert($insert_array)) {
					$form->errorHandler()->setError('', 'Форма не отправлена, свяжитесь с администратором.');
				} else {
					//app()->stat()->addObject(StatObject::FORM_PRICE_REQUEST_SEND, $form->model())
					//		->fixResponse(false);
				}
			} else {
				$form->errorHandler()->setError('', 'Форма не отправлена, свяжитесь с администратором.');
			}

			if ($form->errorHandler()->hasErrors()) {
				$form->errorHandler()->saveErrorsInSession()
						->saveValsInSession($form->getVals());

				die(json_encode([
					'error_message' => '<ul><li>' . $form->errorHandler()->getLastErrorMessage() . '</li></ul>'
				]));
			}

            //отправляем заказ админу
            app()->email()
                    ->setSubject('Новый заказ товара на сайте')
                    ->setTo(app()->config()->get('app.email.administrator'))
                    ->setModel($form->model())
                    ->setTemplate('email_to_admin', 'price')
                    ->sendToQuery();

			if ($firm->exists()) {
				//если у фирмы есть email отправляем заказ на этот email
				if ($firm->exists() && $firm->hasEmail()) {
					app()->email()
							->setSubject('Поступил новый заказ')
							->setTo($firm->firstEmail())
							->setModel($form->model())
							->setTemplate('email_to_client', 'price')
							->sendToQuery();
				} else {
					$price = new Price();
                    $price->reader()->setWhere(['AND', '`id_firm` = :id_firm', 'legacy_id_price = :legacy_id_price'], [':id_firm' => $id_firm, ':legacy_id_price' => $id_price])->objectByConds();
					//$price->reader()->object($hidden_data['id']);
					if ($price->exists()) {
						app()->email()
								->setParams(['name' => $form->getVal('user_name'), 'phone' => $form->getVal('user_phone'), 'price_name' => $price->name()])
								->setSubject('!SMS заказ товара')
								->setTemplate('sms_price_to_firm', 'firm')
								->sendSmsToQuery($firm->val('company_cell_phone'));
					}
				}

				//если у фирмы есть менеджер, отправляем заказ менеджеру фирмы
				$firm_manager = new FirmManager();
				$firm_manager->getByFirm($firm);
				if ($firm_manager->exists() && $firm_manager->val('email_default') !== '') {
					if ($firm->id_service() === 10 && $firm_manager->val('email') !== '') {
						app()->email()
								->setSubject('Создан новый заказ для компании: ' . $firm->name())
								->setTo($firm_manager->val('email'))
								->setModel($form->model())
								->setTemplate('email_to_admin', 'price')
								->sendToQuery();
					} else {
						app()->email()
								->setSubject('Создан новый заказ для компании: ' . $firm->name())
								->setTo($firm_manager->val('email_default'))
								->setModel($form->model())
								->setTemplate('email_to_admin', 'price')
								->sendToQuery();
					}
				} else {
					$service = new StsService();
					$service->reader()
							->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $firm->id_service()])
							->objectByConds();
					$email = $service->val('email');
					app()->email()
							->setSubject('Создан новый заказ для компании: ' . $firm->name())
							->setTo($email)
							->setModel($form->model())
							->setTemplate('email_to_admin', 'price')
							->sendToQuery();
				}

				//отправляем заказ пользователю
				app()->email()
						->setSubject('Оформлен заказ на сайте tovaryplus.ru')
						->setTo($form->getVal('user_email'))
						->setModel($form->model())
						->setTemplate('email_to_user', 'price')
						->sendToQuery();
			}

			die(json_encode(['ok' => true, 'html' => app()->chunk()->render('price.form_success_send')]));
		}
	}

}
