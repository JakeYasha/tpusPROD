<?php

namespace App\Action\FirmUser\Ajax;

use App\Action\FirmUser\Ajax;
use App\Model\Price;
use function app;

class DeletePrice extends Ajax {

	public function execute() {
		$get = app()->request()->processGetParams([
			'id_price' => ['type' => 'int'],
		]);

		$result = ['success' => false];
		
		try {
			$price = new Price();
			$price->delete()
				->from('price')
				->where('id_firm', '=', $get['id_price'])
				->execute();
			$result = [
				'success' => true,
				'message' => 'Success!'
			];
		} catch (\Exception $e) {
			$result['message'] = 'Eroor delete price!';
		}
		
		die(json_encode($result));

		
	}

}
