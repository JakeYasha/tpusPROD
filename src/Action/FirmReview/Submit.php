<?php

namespace App\Action\FirmReview;

use App\Action\FirmReview;
use App\Model\Firm;
use function app;

class Submit extends FirmReview {

	public function execute() {
		$params = app()->request()->processPostParams([
			'id_firm' => ['type' => 'int'],
			'user_name' => ['type' => 'string'],
			'user_email' => ['type' => 'string'],
			'text' => ['type' => 'string'],
			'score' => ['type' => 'int']
		]);

		$cap = app()->request()->processPostParams([
			'g-recaptcha-response' => ['type' => 'string']
		]);

		$firm = new Firm($params['id_firm']);

		if ($firm->exists()) {
			$params['id_city'] = $firm->val('id_city');
		} else {
			$params['id_city'] = app()->location()->currentId();
		}

		$result = ['error_code' => 0, 'error_message' => app()->config()->get('forms.firm.review.fail', 'error')];
		// Добавляем простейшую проверку
		if (($params === null) || !is_array($params)) {
			die(json_encode($result));
		}

		if (!app()->capcha()->isValid($cap['g-recaptcha-response'])) {
			$result = ['error_code' => 0, 'error_message' => 'Вы робот?'];
			die(json_encode($result));
		}

		if ($this->model()->insert($params)) {
			$result = ['ok' => true, 'html' => app()->chunk()->setArg(app()->config()->get('forms.firm.review.success', 'ok'))->render('common.default_ajax_form_message')];
		}

		die(json_encode($result));
	}

}
