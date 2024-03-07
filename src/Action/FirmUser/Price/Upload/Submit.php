<?php

namespace App\Action\FirmUser\Price\Upload;

class Submit extends \App\Action\FirmUser\Price\Upload {

	public function execute() {
		ini_set('max_execution_time', 9000);
		$post = app()->request()->processPostParams([
			'url' => ['type' => 'string'],
			'name_format' => ['type' => 'array'],
			'flag_is_referral' => ['type' => 'int']
		]);

		$url = '/firm-user/price/upload/';
		$yml = new \App\Classes\YmlParser();
		$yml->setNameFormat($post['name_format'])
				->setFlagIsReferral($post['flag_is_referral']);


		if (isset($_FILES['file']) && $_FILES['file']['name'] && $_FILES['file']['type'] === 'text/xml' && (int) $_FILES['file']['error'] === 0) {
			$result = $yml->setXmlData(file_get_contents($_FILES['file']['tmp_name']), app()->firmUser()->id_firm())
					->check();

			if (!$result) {
				$url .= '?error_code=' . $yml->getErrorCode();
			}
		} elseif ($post['url']) {
			$data = @file_get_contents($post['url']);
			$result = $yml->setXmlData($data, app()->firmUser()->id_firm(), 'url', $post['url'])
					->check();

			if (!$result) {
				$url .= '?error_code=' . $yml->getErrorCode();
			}
		} else {
			$url .= '?error_code=1';
		}

		app()->response()->redirect($url);
	}

}
