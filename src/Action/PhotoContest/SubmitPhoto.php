<?php

namespace App\Action\PhotoContest;

class SubmitPhoto extends \App\Action\PhotoContest {

	public function execute() {
		$data = app()->request()->processPostParams([
			'user_name' => ['type' => 'string'],
			'user_phone' => ['type' => 'string'],
			'id_nomination' => ['type' => 'int'],
			'id_contest' => ['type' => 'int']
		]);

		$this->model()->reader()->object($data['id_contest']);
		$nomination = new \App\Model\PhotoContestNomination($data['id_nomination']);
		$image_id = isset($_SESSION['photo-contest']['photos']) ? end($_SESSION['photo-contest']['photos']) : null;
		$image = new \App\Model\Image($image_id);

		$error_code = 0;
		if ($this->model()->exists() && $nomination->exists() && $this->model()->isWorking()) {
			if ($image->exists()) {
				$where = [
					'AND',
					'user_phone = :user_phone',
					'photo_contest_id = :contest',
					'nomination_id = :nomination'
				];
				$params = [
					':user_phone' => $data['user_phone'],
					':contest' => $this->model()->id(),
					':nomination' => $nomination->id()
				];

				$photo = new \App\Model\PhotoContestItem();
				$photo->reader()->setWhere($where, $params)->objectByConds();

				if ($photo->exists()) {
					$error_code = 1;
				} else {
					$photo->insert([
						'name' => $data['user_name'],
						'user_name' => $data['user_name'],
						'user_phone' => $data['user_phone'],
						'nomination_id' => $nomination->id(),
						'photo_contest_id' => $this->model()->id(),
						'user_agent' => app()->request()->getUserAgent(),
						'image' => 'image~' . $image->id(),
						'flag_is_new' => 1,
						'flag_is_active' => 0
					]);
					$image->update(['source' => 'user']);
					unset($_SESSION['photo-contest']['photos']);
				}
			} else {
				$error_code = 2;
			}
		} else {
			$error_code = 3;
		}

		if ($error_code !== 0) {
			app()->response()->redirect(app()->linkFilter('/photo-contest/add-photo/' . $data['id_contest'] . '/', ['nomination' => $data['id_nomination'], 'error' => $error_code]));
		} else {
			app()->response()->redirect(app()->linkFilter($this->model()->link(), ['nomination' => $nomination->id(), 'success_add' => 1]));
		}
	}

}
