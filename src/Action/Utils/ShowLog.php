<?php

namespace App\Action\Utils;

class ShowLog extends \App\Action\Utils {

	public function __construct() {
		parent::__construct();
		if (!(new \App\Model\Administrator())->userComponent()->getFromSession()->exists()) {
			exit();
		}
	}

	public function execute() {
		if (isset($_GET['clear'])) {
			file_put_contents(APPS_DIR_PATH.'/logs/tovaryplus.ru_error.log', '');
			app()->response()->redirect('/utils/show-log/');
		}

		$file = array_reverse(explode(PHP_EOL, file_get_contents(APPS_DIR_PATH.'/logs/tovaryplus.ru_error.log')));
		echo '<a href="/utils/show-log/?clear">очистить лог</a>';
		foreach ($file as $string) {
			if (str()->pos($string, '~') !== FALSE) {
				$_strings = explode('~', $string);
				$string = implode('<br/>', $_strings);
			}
			echo $string.'<br/>'.'<br/>';
		}

		exit();
	}

}
