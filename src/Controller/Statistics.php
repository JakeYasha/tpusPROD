<?php

namespace App\Controller;

class Statistics extends \App\Classes\Controller {

	public function actionEmpty() {
		/* naming fix */
		$action = new \App\Action\Statistics\EmptyAction();
		return $action->execute();
	}

}
