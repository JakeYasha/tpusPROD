<?php

require_once rtrim(__DIR__, '/') . '/../config/config_cms.php';

try {
	\Sky4\App::init();
	echo \Sky4\App::run();
} catch (\Exception $e) {
	if ($e instanceof \Sky4\Exception) {
		$e->render();
	} else {
		echo $e->getMessage();
	}
}

exit();
