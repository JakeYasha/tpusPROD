<?php

if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], ['93.158.228.86','89.22.238.130','95.87.65.28', '158.181.144.63'])) {
	define('APP_IS_DEV_MODE', true);
} else {
	if (!isset($_SERVER['REMOTE_ADDR'])) {
		define('APP_IS_DEV_MODE', false);
	} else {
		define('APP_IS_DEV_MODE', false);
	}
}

define('STAT_ADD_COUNT', 1);
define('APP_SUB_SYSTEM_NAME', 'APP');

require_once rtrim(dirname(__FILE__), '/').'/config.php';
require_once rtrim(dirname(__FILE__), '/').'/autoload.php';
require_once rtrim(dirname(__FILE__), '/').'/special_consts.php';
require_once rtrim(dirname(__FILE__), '/').'/consts.php';

define('RESOURCE_UPDATE_TIME', filemtime(APP_DIR_PATH.'/public/css/style.css'));


/*require_once($_SERVER['DOCUMENT_ROOT'].'/goDB/autoload.php'); // path to goDB
\go\DB\autoloadRegister();
$bdconf = [
    'host'     => 'localhost',
    'username' => 'root',
    'password' => 'XaquRmC3mWksnAA6vDWX',
    'dbname'   => 'tovaryplus_new',
    'charset'  => 'utf8',
    '_debug'   => false,
    '_prefix'  => '',
];
$db = go\DB\DB::create($bdconf, 'mysql');

global $db;


function fetchall_array($db, $pattern, $data = null) {
    if (!is_null($data))
    {
        $res = $db->query($pattern,$data);

    }else{
        $res = $db->query($pattern);
    }
    $result = array();
    foreach ($res as $znac) {
        $result[] = $znac;
    }

    return $result;
    unset($res);
    unset($znac);

}

function send_query($db, $pattern, $data = null) {
    if (!is_null($data))
    {
        $res = $db->query($pattern,$data);

    }else{
        $res = $db->query($pattern);
    }


    return $res;
    unset($res);

}*/