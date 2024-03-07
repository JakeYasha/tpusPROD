<?php

namespace App\Action\AdvertModule;

use App\Action\AdvertModule;
use App\Model\AdvertModule\RequestFormAdd;
use App\Model\AdvertModuleRequest;
use function app;

class Request extends AdvertModule {

	public function execute($mode) {
		if ($mode === 'submit') {
			$params = app()->request()->processPostParams([
				'g-recaptcha-response' => ['type' => 'string']
			]);

			$form = new RequestFormAdd(new AdvertModuleRequest());

			$form->setEditableFieldsNames(['user_name', 'user_email', 'user_phone', 'brief_text', 'text', 'id_advert_module'])
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
					'id_advert_module' => ['type' => 'int'],
				]);

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

			$this->model()->reader()->object($form->model()->val('id_advert_module'));
			if ($this->model()->exists()) {
				//отправляем заказ админу
				app()->email()
						->setSubject($form->model()->val('callback_btn_name') == 'order' ? 'Новый заказ по рекламному модулю на сайте' : 'Новая запись по рекламному модулю на сайте')
						->setTo(app()->config()->get('app.email.administrator'))
						->setModel($form->model())
						->setTemplate('email_to_admin', 'advertmodule')
						->sendToQuery();

				//если у рекламного модуля указан email отправляем заказ на этот email
				if ($this->model()->hasEmail()) {
					app()->email()
							->setSubject($form->model()->val('callback_btn_name') == 'order' ? 'Поступил новый заказ по рекламному модулю' : 'Паступила запись по рекламному модулю')
							->setTo($this->model()->email())
							->setModel($form->model())
							->setTemplate('email_to_client', 'advertmodule')
							->sendToQuery();
				}
				//если у рекламного модуля указан phone отправляем заказ по смс
				if ($this->model()->hasPhone()) {
					app()->email()
							->setParams(['name' => $form->model()->val('user_name'), 'phone' => $form->model()->val('user_phone'), 'advert_module_name' => $this->model()->header()])
							->setSubject($form->model()->val('callback_btn_name') == 'order' ? '!SMS заказ по рекламному модулю' : '!SMS запись по рекламному модулю')
							->setTemplate('sms_callback', 'advertmodule')
							->sendSmsToQuery($this->model()->phone());
				}

				//отправляем заказ пользователю
				app()->email()
						->setSubject($form->model()->val('callback_btn_name') == 'order' ? 'Оформлен заказ на сайте tovaryplus.ru' : 'Оформлена запись на сайте tovaryplus.ru')
						->setTo($form->getVal('user_email'))
						->setModel($form->model())
						->setTemplate('email_to_user', 'advertmodule')
						->sendToQuery();
			}

			die(json_encode(['ok' => true, 'html' => app()->chunk()->render('advertmodule.form_success_send')]));
		}
	}

}
