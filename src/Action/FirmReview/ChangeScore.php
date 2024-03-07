<?php

namespace App\Action\FirmReview;

use App\Action\FirmReview;
use Sky4\Exception;
use function app;

class ChangeScore extends FirmReview {

	public function execute() {
		$this->model()->get($id);
		if ($this->model()->exists()) {
			$hash = app()->request()->processGetParams(['hash' => 'string'])['hash'];
			if ($hash !== null) {
				if ($this->model()->getChangeScoreHash($this->model()->id(), $this->model()->val('user_email'), $score) === $hash) {
					$this->model()->update(['score' => $score]);
					app()->metadata()
							->noIndex()
							->setTitle('Оценка изменена!');

					$this->view()
							->setTemplate('score_changed')
							->save();
				} else {
					throw new Exception(Exception::TYPE_BAD_URL);
				}
			} else {
				throw new Exception(Exception::TYPE_BAD_URL);
			}
		} else {
			throw new Exception(Exception::TYPE_BAD_URL);
		}
	}

}
