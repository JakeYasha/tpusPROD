<?php

namespace App\Action\FirmFeedback;

use App\Model\FeedbackOptions;
use App\Model\Firm;
use App\Model\FirmManager;
use App\Model\StatObject;
use App\Model\StsService;

class Submit extends \App\Action\FirmFeedback {

	public function execute() {
		$result_type = 'json';
		$params = app()->request()->processPostParams([
			'id_firm' => ['type' => 'int'],
			'id_service' => ['type' => 'int'],
			'request_uri' => ['type' => 'string'],
			'user_name' => ['type' => 'string'],
			'user_email' => ['type' => 'string'],
			'message_subject' => ['type' => 'string'],
			'message_text' => ['type' => 'string'],
			'flag_is_error' => ['type' => 'int'],
			'flag_is_for_call_center' => ['type' => 'int']
		]);

		$request_uri = $params['request_uri'];
		unset($params['request_uri']);

		if ($params['flag_is_error'] === null) {
			$params['flag_is_error'] = 0;
		}

		if ($params['flag_is_for_call_center'] === null) {
			$params['flag_is_for_call_center'] = 0;
		}

		if ($params['flag_is_for_call_center'] != 0) {
			$result_type = 'plain';
		}

		$fo = app()->request()->processPostParams([
			'feedback_option' => ['type' => 'int']
		]);

		$cap = app()->request()->processPostParams([
			'g-recaptcha-response' => ['type' => 'string']
		]);

		$result = ['error_code' => 0, 'error_message' => app()->config()->get('forms.firm.feedback.fail', 'error')];
		// Добавляем простейшую проверку
		if (($params === null) || !is_array($params)) {
			return $this->renderResult($result, $result_type, 'fail');
		}

		if (!app()->capcha()->isValid($cap['g-recaptcha-response'])) {
			$result = ['error_code' => 0, 'error_message' => 'Вы робот?'];
			return $this->renderResult($result, $result_type, 'fail');
		}

        $id_firm = $params['id_firm'];
        $id_service = isset($params['id_service']) && $params['id_service'] ? $params['id_service'] : null;
        
        $firm = new Firm();
		if ($id_service === null) {
			$firm->getByIdFirm((int) $id_firm);
		} else {
			$firm->getByIdFirmAndIdService($id_firm, $id_service);
		}
        
        unset($params['id_service']);
        $params['id_firm'] = $firm->id();
        
		if ($this->model()->insert($params)) {
			app()->stat()->addObject(StatObject::FORM_FEEDBACK_SEND, $this->model())
					->fixResponse(false);
			$result = ['ok' => true, 'html' => app()->chunk()->setArg(app()->config()->get('forms.firm.feedback.success', 'ok'))->render('common.default_ajax_form_message')];

			if ($firm->exists()) {
				if ($params['flag_is_error'] !== null && $params['flag_is_error']) {
					if ($firm->id_service() === 10 && $firm->id_manager() !== null) {
						$firm_manager = new FirmManager();
						$firm_manager->getByFirm($firm);

						if ($firm_manager->exists() && $firm_manager->val('email_default') !== '') {
							if ($firm_manager->val('email') !== '') {
								app()->email()
										->setSubject('Некорректная информация на сайте tovaryplus.ru')
										->setTo($firm_manager->val('email'))
										->setModel($this->model())
										->setParams(['request_uri' => $request_uri])
										->setTemplate('email_message_to_ratiss', 'firm')
										->sendToQuery();
							} else {
								app()->email()
										->setSubject('Некорректная информация на сайте tovaryplus.ru')
										->setTo($firm_manager->val('email_default'))
										->setModel($this->model())
										->setParams(['request_uri' => $request_uri])
										->setTemplate('email_message_to_ratiss', 'firm')
										->sendToQuery();
							}
						}
					} else {
						$service = new StsService();
						$service->reader()
								->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $firm->id_service()])
								->objectByConds();
						$email = $service->val('email');

						app()->email()
								->setSubject('Некорректная информация на сайте tovaryplus.ru')
								->setTo($email)
								->setModel($this->model())
								->setParams(['request_uri' => $request_uri])
								->setTemplate('email_message_to_ratiss', 'firm')
								->sendToQuery();
					}
				} else {
					$fo = new FeedbackOptions($fo['feedback_option']);
					if ($fo->exists()) {
						app()->email()
								->setSubject('Tovaryplus.ru: '.$fo->name())
								->setTo($fo->val('email'))
								->setModel($this->model())
								->setParams(['request_uri' => $request_uri])
								->setTemplate('email_message_to_feedback', 'firm')
								->sendToQuery();
					} else {
						if ($firm->hasEmail()) {
							app()->email()
									->setSubject('Новое сообщение с сайта TovaryPlus.ru для фирмы '.$firm->name())
									->setTo($firm->firstEmail())
									->setModel($this->model())
									->setTemplate('email_message_to_firm', 'firm')
									->sendToQuery();
						}

						if ($firm->id_service() === 10 && $firm->id_manager() !== null) {
							$firm_manager = new FirmManager();
							$firm_manager->getByFirm($firm);

							if ($firm_manager->exists() && $firm_manager->val('email_default') !== '') {
								if ($firm_manager->val('email') !== '') {
									app()->email()
											->setSubject('Новое сообщение для фирмы '.$firm->name())
											->setTo($firm_manager->val('email'))
											->setModel($this->model())
											->setParams(['request_uri' => $request_uri])
											->setTemplate('email_message_to_manager', 'firm')
											->sendToQuery();
								} else {
									app()->email()
											->setSubject('Новое сообщение для фирмы '.$firm->name())
											->setTo($firm_manager->val('email_default'))
											->setModel($this->model())
											->setParams(['request_uri' => $request_uri])
											->setTemplate('email_message_to_manager', 'firm')
											->sendToQuery();
								}
							}
						} elseif ($firm->id_manager() == null) {
							$service = new StsService();
							$service->reader()
									->setWhere(['AND', 'id_service = :id_service'], [':id_service' => $firm->id_service()])
									->objectByConds();

							$email = $service->val('email');
							app()->email()
									->setSubject('Новое сообщение для фирмы '.$firm->name())
									->setTo($email)
									->setModel($this->model())
									->setParams(['request_uri' => $request_uri])
									->setTemplate('email_message_to_manager', 'firm')
									->sendToQuery();
						}
					}
				}
			}
		}

		return $this->renderResult($result, $result_type, 'success');
	}

}
