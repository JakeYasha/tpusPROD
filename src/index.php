<?php

require_once rtrim(__DIR__, '/').'/../config/config_app.php';
try {
	\App\Classes\App::init();
	app()->startTimer();
    //if (APP_IS_DEV_MODE){
        // $p_url = isset($_SERVER['REQUEST_URI']) ? explode('?', $_SERVER['REQUEST_URI'], 2)[0] : '';
        // $p_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        // $p_ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        // $p_itog_url = preg_replace("[`'\",=+]",'',$p_url);
        // $p_itog_str = preg_replace("[`'\",=+]",'',$p_ip.'+i+'.$p_ref);
        
        //временно уберем запись
        //$result = app()->db()->query()->setText('INSERT INTO `global_stat`(`url`, `request`) VALUES ("'.$p_itog_url.'","'.$p_itog_str.'")')->fetch();
        //echo '123123123';
    //}
    
	echo \App\Classes\App::run();
} catch (\Exception $e) {
	if ($e instanceof \Sky4\Exception) {
		$extensions = [
			'.map',
			'.gif',
			'.jpg',
			'.jpeg',
			'.png',
			'.mp3',
			'.wav',
			'.svg',
			'.txt',
			'.eot',
			'.js',
		];
		$skip = false;
		foreach ($extensions as $ext) {
			if (str()->index(str()->toLower(app()->request()->getRequestUri()), $ext)) {
				$skip = true;
			}
		}
		if (!$skip && $e->getMessage() !== \Sky4\Exception::TYPE_BAD_URL) {
			file_put_contents('/var/www/html/logs/tovaryplus.ru_error.log', date('d.m.y H:i:s').'~'.app()->request()->getRequestUri().'~'.$e->getMessage().PHP_EOL, FILE_APPEND);
		}
		$e->render();
	} else {
		echo $e->getMessage();
	}
}

//var_dump($_SESSION['kolvo']);

exit();
