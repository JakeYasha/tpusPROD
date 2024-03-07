<?php

ini_set('memory_limit', '4G');
require_once rtrim(__DIR__, '/') . '/../../../config/config_app.php';
\Sky4\App::init();
$_firms_objects = [];

if (empty($_GET)) {
    $i = 0;
    if (isset($argv)) {
        foreach ($argv as $val) {
            $i++;
            if ($i == 1)
                continue;

            preg_match("~([a-z0-9]+)=([0-9]+)~", $val, $matches);
            $_GET[$matches[1]] = $matches[2];
        }
    }
}

$string = '';
ini_set('max_execution_time', -1);
ini_set('display_errors', 1);
ini_set("log_errors", 0);

header("Content-type: text/plain; charset=windows-1251\n");
DEFINE('CR', "\n");
DEFINE('SP', "\t");
DEFINE('DEBUG', false);
DEFINE('LOGGING', TRUE);
if (empty($_GET)) {
    die('Add some params');
}
$connect = isset($_GET['connect']) ? $_GET['connect'] : FALSE;
$connect727373 = isset($_GET['connect727373']) ? $_GET['connect727373'] : FALSE;
$service = isset($_GET['service']) ? (int) $_GET['service'] : null;
if ((!$connect || !$connect727373) && (int) $connect !== 0 && (int) $connect727373 !== 0)
    die("usage: /usr/local/php-fpm/bin/php /var/www/sites/tovaryplus.ru/src/Cron/php/get_stat_new.php connect=20140000 connect727373=21516000 service=10");
echo 'start object id = ' . $connect.PHP_EOL;
echo 'start object727373 id = ' . $connect727373.PHP_EOL;
if ($service !== null) {
    echo 'start service id = ' . $service.PHP_EOL;
    DEFINE('STATOUTFILE', APP_DIR_PATH . '/update/service_update/' . $service . '/statistics/webstat.dat');
    DEFINE('TMP_LOGFILE', APP_DIR_PATH . '/update/service_update/' . $service . '/statistics/webstat.tmp.log');
    DEFINE('LOGFILE', APP_DIR_PATH . '/update/service_update/' . $service . '/statistics/webstat.log');
} else {
    DEFINE('STATOUTFILE', APP_DIR_PATH . '/update/webstat_new.dat');
    DEFINE('TMP_LOGFILE', APP_DIR_PATH . '/src/Cron/log/webstat.tmp.log');
    DEFINE('LOGFILE', APP_DIR_PATH . '/src/Cron/log/webstat.log');
}

// Из таблицы stat_object выбираем максимальный id, до которого будем собирать данные
$max_object_id = getMaxStatObjectId();
logger('Получили max_object_id = ' . $max_object_id);

// Из таблицы stat_object727373 выбираем максимальный id, до которого будем собирать данные
$max_object727373_id = getMaxStatObject727373Id();
logger('Получили max_object727373_id = ' . $max_object727373_id);


// Минимальный id из stat_object передается в параметре connect
$min_object_id = ($connect > 0) ? $connect : $max_object_id;
logger('Получили min_object_id = ' . $min_object_id);

// Минимальный id из stat_object передается в параметре connect
$min_object727373_id = ($connect727373 > 0) ? $connect727373 : $max_object727373_id;
logger('Получили min_object727373_id = ' . $min_object727373_id);


// Новых данных по tovaryplus.ru нет, выходим
if ($min_object_id > $max_object_id)
    die('min_object_id(' . $min_object_id . ') > max_object_id(' . $max_object_id . ') == ' . (($min_object_id > $max_object_id) ? 'true' : 'false'));

// Новых данных по 727373 нет, выходим
if ($min_object727373_id > $max_object727373_id)
    die('min_object727373_id(' . $min_object727373_id . ') > max_object727373_id(' . $max_object727373_id . ') == ' . (($min_object727373_id > $max_object727373_id) ? 'true' : 'false'));

//==============================================================================

$data_exists = true;
$step = 50000; // порция обрабатываемых за раз stat_object
$i = 1; // счетчик порций
// Массив уникальных id пользователей
$unique_user_ids = [];
$banned_user_ids = [];

// id_hist_web_connect 	---	user_id
// id_web_site 			---	4 (tovaryplus.ru)
// from_id_service		---	10
// id_service			---	0
// id_firm				---	0
// id_city				---	user_city_id
// datetime_connect		---	user_time_begining
// datetime_disconnect	---	user_time_ending
// remote_host			---	user_ip
// id_application		---	0
// id_web_cookie		---	0 (не user_cookie_hash)
// referer				---	user_referer
// id_web_user			---	0
$string .= 'connect begin' . CR;
logger('Выцепляем stat_userов');
$users_counter = 0;
while ($data_exists) {
    logger('Шаг ' . $i . ': ' . ($min_object_id + (($i - 1) * $step)) . '-' . ($min_object_id + ($i * $step)));
    if (($min_object_id + (($i - 1) * $step)) > $max_object_id) {
        break;
    }

    // Из таблицы stat_object выбираем уникальные id пользователей, для которых будем считать статистику
    $user_ids = getDistinctUserIdsFromStatObjectIdRange($min_object_id + (($i - 1) * $step), $min_object_id + ($i * $step), $service);
    if (count($user_ids) == 0) {
        logger('пропускаем');
        $i++;
        continue;
    }

    // Получаем простой массив id пользователей
    $prepared_user_ids = getSimpleArrayFromDBSelectArray($user_ids, 'id_stat_user');
    // Уберем уже полученных ранее пользователей
    $prepared_user_ids = array_diff($prepared_user_ids, $unique_user_ids);

    // Добавим в общий массив
    $unique_user_ids = array_merge($unique_user_ids, $prepared_user_ids);

    // Если в этой порции нет новых пользователей - пропускаем шаг
    if (count($prepared_user_ids) == 0) {
        $i++;
        continue;
    }
    
    if ($prepared_user_ids) {
        $banned_user_ids = array_unique(array_merge($banned_user_ids, getBannedUserIds($prepared_user_ids)));
    }
    // Уберем забаненных пользователей
    $prepared_user_ids = array_diff($prepared_user_ids, $banned_user_ids);

    // Получаем пользователей
    $users = getUsersByUserIds($prepared_user_ids);
    logger(count($users) . ' новых');
    $j = 0;
    foreach ($users as $user) {
        $j++;
        $string .= $user['user_id'] . SP;
        $string .= '4' . SP;
        $string .= ($service !== null ? $service : '10') . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= $user['user_city_id'] . SP;
        $string .= $user['user_time_begining'] . SP;
        $string .= $user['user_time_ending'] . SP;
        $string .= $user['user_ip'] . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= str_replace("'", "''", @iconv('utf8', 'cp1251', $user['user_referer'])) . SP;
        $string .= '0' . CR;
    }
    $users_counter += $j;
    $i++;
}
logger('Добавлено stat_userов - ' . $users_counter);

$data727373_exists = true;
$step727373 = 50000; // порция обрабатываемых за раз stat_user727373ов
$i727373 = 1; // счетчик порций
// Массив уникальных id пользователей 727373
$unique_user727373_ids = [];
$banned_user727373_ids = [];

// id_hist_web_connect 	---	user_id
// id_web_site 			---	18 (727373.ru)
// from_id_service		---	10
// id_service			---	0
// id_firm				---	0
// id_city				---	user_city_id
// datetime_connect		---	user_time_begining
// datetime_disconnect	---	user_time_ending
// remote_host			---	user_ip
// id_application		---	0
// id_web_cookie		---	0 (не user_cookie_hash)
// referer				---	user_referer
// id_web_user			---	0
logger('Выцепляем stat_user727373ов');
$user727373s_counter = 0;
while ($data727373_exists) {
    logger('Шаг ' . $i727373 . ': ' . ($min_object727373_id + (($i727373 - 1) * $step727373)) . '-' . ($min_object727373_id + ($i727373 * $step727373)));
    if (($min_object727373_id + (($i727373 - 1) * $step727373)) > $max_object727373_id) {
        break;
    }

    // Из таблицы stat_object727373 выбираем уникальные id пользователей 727373, для которых будем считать статистику
    $user727373_ids = getDistinctUser727373IdsFromStatObject727373IdRange($min_object727373_id + (($i727373 - 1) * $step727373), $min_object727373_id + ($i727373 * $step727373), $service);
    if (count($user727373_ids) == 0) {
        logger('пропускаем');
        $i727373++;
        continue;
    }
    
    // Получаем простой массив id пользователей 727373
    $prepared_user727373_ids = getSimpleArrayFromDBSelectArray($user727373_ids, 'id_stat_user');
    // Уберем уже полученных ранее пользователей 727373
    $prepared_user727373_ids = array_diff($prepared_user727373_ids, $unique_user727373_ids);

    // Добавим в общий массив
    $unique_user727373_ids = array_merge($unique_user727373_ids, $prepared_user727373_ids);

    // Если в этой порции нет новых пользователей 727373 - пропускаем шаг
    if (count($prepared_user727373_ids) == 0) {
        $i727373++;
        continue;
    }

    if ($prepared_user727373_ids){
        $banned_user727373_ids = array_unique(array_merge($banned_user727373_ids, getBannedUser727373Ids($prepared_user727373_ids)));
    }
    // Уберем забаненных пользователей
    $prepared_user727373_ids = array_diff($prepared_user727373_ids, $banned_user727373_ids);

    // Получаем пользователей 727373
    $user727373s = getUsersByUser727373Ids($prepared_user727373_ids);
    logger(count($user727373s) . ' новых');
    $j727373 = 0;
    foreach ($user727373s as $user727373) {
        $j727373++;
        $string .= $user727373['user_id'] . SP;
        $string .= '18' . SP;
        $string .= ($service !== null ? $service : '10') . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= $user727373['user_city_id'] . SP;
        $string .= $user727373['user_time_begining'] . SP;
        $string .= $user727373['user_time_ending'] . SP;
        $string .= $user727373['user_ip'] . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= str_replace("'", "''", @iconv('utf8', 'cp1251', $user727373['user_referer'])) . SP;
        $string .= '0' . CR;
    }
    $user727373s_counter += $j727373;
    $i727373++;
}
logger('Добавлено stat_user727373ов - ' . $user727373s_counter);

$string .= 'connect end' . CR;
logger('Закончили stat_userов и stat_user727373ов');

//==============================================================================

$step = 50000; // порция обрабатываемых за раз stat_requestов
$i = 1; // счетчик порций
$data_exists = true;
// Массив уникальных id запросов
$unique_request_ids = [];

// id_hist_request     ---	request_id
// id_web_site         ---	4
// datetime            ---	request_time_inserting
// remote_host         ---	user_ip
// request_text        ---	request_request_text
// id_type_search      ---	0
// remote_city         ---	user_city
// id_hist_web_connect ---	user_id
// search_count        ---	0
// search_time         ---	0
// id_group            ---	0
// id_subgroup         ---	0
// id_country          ---	sts_city.id_country
// id_region_country   ---	sts_city.id_region_country
// id_city             ---	request_city_id
// request_firm        ---	''
// request_good        ---	''
$string .= 'request begin' . CR;
logger('Вычисляем stat_requestов');
echo PHP_EOL;
$request_counter = 0;
while ($data_exists) {
    logger('Шаг ' . $i . ': ' . ($min_object_id + (($i - 1) * $step)) . '-' . ($min_object_id + ($i * $step)));
    if (($min_object_id + (($i - 1) * $step)) > $max_object_id) {
        break;
    }

    // Из таблицы stat_object выбираем уникальные id запросов, для которых будем считать статистику
    $request_ids = getDistinctRequestIdsFromStatObjectIdRange($min_object_id + (($i - 1) * $step), $min_object_id + ($i * $step), $service);
    if (count($request_ids) == 0) {
        logger('пропускаем');
        $i++;
        continue;
    }

    $prepared_request_ids = getSimpleArrayFromDBSelectArray($request_ids, 'id_stat_request');
    // Уберем уже полученных ранее запросов
    $prepared_request_ids = array_diff($prepared_request_ids, $unique_request_ids);

    // Добавим в общий массив
    $unique_request_ids = array_merge($unique_request_ids, $prepared_request_ids);

    // Если в этой порции нет новых запросов - пропускаем шаг
    if (count($prepared_request_ids) == 0) {
        $i++;
        continue;
    }

    // Получаем запросы
    $requests = getrequestsByrequestIds($prepared_request_ids);
    logger(count($requests) . ' новых');
    $j = 0;
    foreach ($requests as $request) {
        if (in_array($request['request_user_id'], $banned_user_ids)) continue;
        $j++;
        $request['request_request_text'] = str_replace("'", "''", $request['request_request_text']);
        $request['request_request_text'] = str_replace(":", "", $request['request_request_text']);
        $string .= $request['request_id'] . SP;
        $string .= '4' . SP;
        $string .= $request['request_time_inserting'] . SP;
        $string .= $request['user_ip'] . SP;
        $string .= str_replace("'", "''", @iconv('utf8', 'cp1251', $request['request_request_text'])) . SP;
        $string .= '0' . SP;
        $string .= str_replace("'", "''", @iconv('utf8', 'cp1251', $request['user_city'])) . SP;
        $string .= $request['request_user_id'] . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= empty($request['city_country_id']) ? '0' . SP : $request['city_country_id'] . SP;
        $string .= empty($request['city_region_id']) ? '0' . SP : $request['city_region_id'] . SP;
        $string .= empty($request['request_city_id']) ? '0' . SP : $request['request_city_id'] . SP;
        $string .= '' . SP;
        $string .= '' . CR;
    }
    $request_counter += $j;
    $i++;
}
logger('Добавлено stat_requestов - ' . $request_counter);

$step727373 = 50000; // порция обрабатываемых за раз stat_request727373ов
$i727373 = 1; // счетчик порций
$data727373_exists = true;
// Массив уникальных id запросов 727373
$unique_request727373_ids = [];

// id_hist_request     ---	request_id
// id_web_site         ---	18
// datetime            ---	request_time_inserting
// remote_host         ---	user_ip
// request_text        ---	request_request_text
// id_type_search      ---	0
// remote_city         ---	user_city
// id_hist_web_connect ---	user_id
// search_count        ---	0
// search_time         ---	0
// id_group            ---	0
// id_subgroup         ---	0
// id_country          ---	sts_city.id_country
// id_region_country   ---	sts_city.id_region_country
// id_city             ---	request_city_id
// request_firm        ---	''
// request_good        ---	''

logger('Вычисляем stat_request727373ов');
echo PHP_EOL;
$request727373_counter = 0;
while ($data727373_exists) {
    logger('Шаг ' . $i727373 . ': ' . ($min_object727373_id + (($i727373 - 1) * $step727373)) . '-' . ($min_object727373_id + ($i727373 * $step727373)));
    if (($min_object727373_id + (($i727373 - 1) * $step727373)) > $max_object727373_id) {
        break;
    }

    // Из таблицы stat_object727373 выбираем уникальные id запросов 727373, для которых будем считать статистику
    $request727373_ids = getDistinctRequest727373IdsFromStatObject727373IdRange($min_object727373_id + (($i727373 - 1) * $step727373), $min_object727373_id + ($i727373 * $step727373), $service);
    if (count($request727373_ids) == 0) {
        logger('пропускаем');
        $i727373++;
        continue;
    }

    $prepared_request727373_ids = getSimpleArrayFromDBSelectArray($request727373_ids, 'id_stat_request');
    // Уберем уже полученных ранее запросов 727373
    $prepared_request727373_ids = array_diff($prepared_request727373_ids, $unique_request727373_ids);

    // Добавим в общий массив
    $unique_request727373_ids = array_merge($unique_request727373_ids, $prepared_request727373_ids);

    // Если в этой порции нет новых запросов 727373 - пропускаем шаг
    if (count($prepared_request727373_ids) == 0) {
        $i727373++;
        continue;
    }

    // Получаем запросы 727373
    $request727373s = getRequestsByRequest727373Ids($prepared_request727373_ids);
    logger(count($request727373s) . ' новых');
    $j727373 = 0;
    foreach ($request727373s as $request727373) {
        if (in_array($request727373['request_user_id'], $banned_user727373_ids)) continue;
        if (isBadStatRequest727373($request727373['request_id'])) continue;
        $j727373++;
        $request727373['request_request_text'] = str_replace("'", "''", $request727373['request_request_text']);
        $request727373['request_request_text'] = str_replace(":", "", $request727373['request_request_text']);
        $string .= $request727373['request_id'] . SP;
        $string .= '18' . SP;
        $string .= $request727373['request_time_inserting'] . SP;
        $string .= $request727373['user_ip'] . SP;
        $string .= str_replace("'", "''", @iconv('utf8', 'cp1251', $request727373['request_request_text'])) . SP;
        $string .= '0' . SP;
        $string .= str_replace("'", "''", @iconv('utf8', 'cp1251', $request727373['user_city'])) . SP;
        $string .= $request727373['request_user_id'] . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= empty($request727373['city_country_id']) ? '0' . SP : $request727373['city_country_id'] . SP;
        $string .= empty($request727373['city_region_id']) ? '0' . SP : $request727373['city_region_id'] . SP;
        $string .= empty($request727373['request_city_id']) ? '0' . SP : $request727373['request_city_id'] . SP;
        $string .= '' . SP;
        $string .= '' . CR;
    }
    $request727373_counter += $j727373;
    $i727373++;
}
logger('Добавлено stat_request727373ов - ' . $request727373_counter);

$string .= 'request end' . CR;
logger('Закончили stat_requestов и stat_request727373ов');

//==============================================================================

$string .= 'internet begin' . CR;
logger('Начали stat_objectов');
$step = 1000; // порция обрабатываемых за раз stat_objectов
$i = 1; // счетчик порций
$data_exists = true;

//id_hist_internet     ---	object_id
//id_hist_request      ---	object_request_id
//id_web_site          ---	4
//datetime             ---	object_time_inserting
//id_service           ---	object_service_id
//id_city              ---	object_city_id
//id_firm              ---	object_firm_id
//id_group             ---	firm => 0 / price => sts_price.id_group | 0
//id_subgroup          ---	firm => 0 / price => sts_price.id_subgroup | 0
//id_producer_country  ---	0
//id_producer_goods    ---	0
//id_price             ---	firm => 0 / price => object_model_id
//name                 ---	firm => firm.company_name | object_name / sts_price.name | object_name
//manufacture          ---	firm =>  / price => sts_price.manufacture
//unit                 ---	firm =>  / price => sts_price.unit
//pack                 ---	firm =>  / price => sts_price.pack
$object_counter = 0;
while ($data_exists) {
    logger('Шаг ' . $i . ': ' . ($min_object_id + (($i - 1) * $step)) . '-' . ($min_object_id + ($i * $step)));
    if (($min_object_id + (($i - 1) * $step)) > $max_object_id) {
        break;
    }

    // Из таблицы stat_object выбираем уникальные id заявок, для которых будем считать статистику
    $extended_objects = getExtendedStatObjectDataFromStatObjectIdRange($min_object_id + (($i - 1) * $step), $min_object_id + ($i * $step), $service);
    if (count($extended_objects) == 0) {
        logger('пропускаем');
        $i++;
        continue;
    }

    $j = 0;
    // Получаем заявки
    foreach ($extended_objects as $extended_object) {
        if (in_array($extended_object['object_user_id'], $banned_user_ids)) continue;
        $j++;

        if (!isset($_firms_objects[$extended_object['object_firm_id']])) {
            $_firms_objects[$extended_object['object_firm_id']] = new \App\Model\Firm($extended_object['object_firm_id']);
        }


        $object_name = preg_replace('~[\t\n\r ]+~', ' ', preg_replace('~\'~', '\'\'', @iconv('utf8', 'cp1251', $extended_object['object_name'])));
        $firm_name = empty($extended_object['firm_name']) ? $object_name : preg_replace('~[\t\n\r ]+~', ' ', preg_replace('~\'~', '\'\'', @iconv('utf8', 'cp1251', $extended_object['firm_name'])));
        $price_name = empty($extended_object['price_name']) ? $object_name : preg_replace('~[\t\n\r ]+~', ' ', preg_replace('~\'~', '\'\'', @iconv('utf8', 'cp1251', $extended_object['price_name'])));
        $price_manufacture = empty($extended_object['price_manufacture']) ? '' : str_replace("\0", "", str_replace("'", "''", @iconv('utf8', 'cp1251', $extended_object['price_manufacture'])));
        $price_unit = empty($extended_object['price_unit']) ? '' : str_replace("\0", "", str_replace("'", "''", @iconv('utf8', 'cp1251', $extended_object['price_unit'])));
        //$price_pack = empty($extended_object['price_pack']) ? '' : str_replace("\0", "", str_replace("'", "''", @iconv('utf8', 'cp1251', $extended_object['price_pack'])));
        $price_group_id = empty($extended_object['price_group_id']) ? '0' : $extended_object['price_group_id'];
        $price_subgroup_id = empty($extended_object['price_subgroup_id']) ? '0' : $extended_object['price_subgroup_id'];
        $string .= $extended_object['object_id'] . SP;
        $string .= $extended_object['object_request_id'] . SP;
        $string .= '4' . SP;
        $string .= $extended_object['object_time_inserting'] . SP;
        $string .= $_firms_objects[$extended_object['object_firm_id']]->id_service() . SP;
        $string .= $extended_object['object_city_id'] . SP;
        $string .= $_firms_objects[$extended_object['object_firm_id']]->id_firm() . SP;
        $string .= $extended_object['object_model_alias'] == 'price' ? $price_group_id . SP : '0' . SP;
        $string .= $extended_object['object_model_alias'] == 'price' ? $price_subgroup_id . SP : '0' . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= $extended_object['object_model_alias'] == 'price' ? $extended_object['object_model_id'] . SP : '0' . SP;
        if ($extended_object['object_model_alias'] == 'price') {
            $string .= str()->crop($price_name, 255) . SP;
        } else if ($extended_object['object_model_alias'] == 'firm') {
            $string .= str()->crop(preg_replace('~[\t\n\r ]+~', ' ', preg_replace('~\'~', '\'\'', @iconv('utf8', 'cp1251', $extended_object['request_response_title']))), 255) . SP;
        } else if ($extended_object['object_model_alias'] == 'firm-promo') {
            $string .= str()->crop($object_name, 255) . SP;
        } else if ($extended_object['object_model_alias'] == 'firm-video') {
            $string .= str()->crop($object_name, 255) . SP;
        } else if ($extended_object['object_model_alias'] == 'price-request') {
            $string .= str()->crop(preg_replace('~[\t\n\r ]+~', ' ', preg_replace('~\'~', '\'\'', @iconv('utf8', 'cp1251', $extended_object['request_response_title']))), 255) . SP;
        } else {
            $string .= '' . SP;
        }
        $string .= $extended_object['object_model_alias'] == 'price' ? $price_manufacture . SP : '' . SP;
        $string .= $extended_object['object_model_alias'] == 'price' ? $price_unit . SP : '' . SP;
        $string .= $extended_object['object_model_alias'] == 'price' ? '' . CR : '' . CR;
    }
    $object_counter += $j;
    $i++;
}
logger('Добавлено stat_objectов - ' . $object_counter);

logger('Начали stat_object727373ов');
$step727373 = 1000; // порция обрабатываемых за раз stat_object727373ов
$i727373 = 1; // счетчик порций
$data727373_exists = true;

//id_hist_internet     ---	object_id
//id_hist_request      ---	object_request_id
//id_web_site          ---	18
//datetime             ---	object_time_inserting
//id_service           ---	object_service_id
//id_city              ---	object_city_id
//id_firm              ---	object_firm_id
//id_group             ---	firm => 0 / price => sts_price.id_group | 0
//id_subgroup          ---	firm => 0 / price => sts_price.id_subgroup | 0
//id_producer_country  ---	0
//id_producer_goods    ---	0
//id_price             ---	firm => 0 / price => object_model_id
//name                 ---	firm => firm.company_name | object_name / sts_price.name | object_name
//manufacture          ---	firm =>  / price => sts_price.manufacture
//unit                 ---	firm =>  / price => sts_price.unit
//pack                 ---	firm =>  / price => sts_price.pack
$object727373_counter = 0;
while ($data727373_exists) {
    logger('Шаг ' . $i727373 . ': ' . ($min_object727373_id + (($i727373 - 1) * $step727373)) . '-' . ($min_object727373_id + ($i727373 * $step727373)));
    if (($min_object727373_id + (($i727373 - 1) * $step727373)) > $max_object727373_id) {
        break;
    }

    // Из таблицы stat_object727373 выбираем уникальные id заявок 727373, для которых будем считать статистику
    $extended_object727373s = getExtendedStatObject727373DataFromStatObject727373IdRange($min_object727373_id + (($i727373 - 1) * $step727373), $min_object727373_id + ($i727373 * $step727373), $service);
    if (count($extended_object727373s) == 0) {
        logger('пропускаем');
        $i727373++;
        continue;
    }

    $j727373 = 0;
    // Получаем заявки 727373
    foreach ($extended_object727373s as $extended_object727373) {
        if (in_array($extended_object727373['object_user_id'], $banned_user727373_ids)) continue;
        $j727373++;
        if (!isset($_firms_objects[$extended_object727373['object_firm_id']])) {
            $_firms_objects[$extended_object727373['object_firm_id']] = new \App\Model\Firm($extended_object727373['object_firm_id']);
        }

        $object_name727373 = preg_replace('~[\t\n\r ]+~', ' ', preg_replace('~\'~', '\'\'', @iconv('utf8', 'cp1251', $extended_object727373['object_name'])));
        $firm_name = empty($extended_object727373['firm_name']) ? $object_name727373 : preg_replace('~[\t\n\r ]+~', ' ', preg_replace('~\'~', '\'\'', @iconv('utf8', 'cp1251', $extended_object727373['firm_name'])));
        $price_name = empty($extended_object727373['price_name']) ? $object_name727373 : preg_replace('~[\t\n\r ]+~', ' ', preg_replace('~\'~', '\'\'', @iconv('utf8', 'cp1251', $extended_object727373['price_name'])));
        $price_manufacture = empty($extended_object727373['price_manufacture']) ? '' : str_replace("\0", "", str_replace("'", "''", @iconv('utf8', 'cp1251', $extended_object727373['price_manufacture'])));
        $price_unit = empty($extended_object727373['price_unit']) ? '' : str_replace("\0", "", str_replace("'", "''", @iconv('utf8', 'cp1251', $extended_object727373['price_unit'])));
        //$price_pack = empty($extended_object727373['price_pack']) ? '' : str_replace("\0", "", str_replace("'", "''", @iconv('utf8', 'cp1251', $extended_object727373['price_pack'])));
        $price_group_id = empty($extended_object727373['price_group_id']) ? '0' : $extended_object727373['price_group_id'];
        $price_subgroup_id = empty($extended_object727373['price_subgroup_id']) ? '0' : $extended_object727373['price_subgroup_id'];
        $string .= $extended_object727373['object_id'] . SP;
        $string .= $extended_object727373['object_request_id'] . SP;
        $string .= '18' . SP;
        $string .= $extended_object727373['object_time_inserting'] . SP;

        $string .= $_firms_objects[$extended_object727373['object_firm_id']]->id_service() . SP;
        $string .= $extended_object727373['object_city_id'] . SP;
        $string .= $_firms_objects[$extended_object727373['object_firm_id']]->id_firm() . SP;
        $string .= $extended_object727373['object_model_alias'] == 'price' ? $price_group_id . SP : '0' . SP;
        $string .= $extended_object727373['object_model_alias'] == 'price' ? $price_subgroup_id . SP : '0' . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= $extended_object727373['object_model_alias'] == 'price' ? $extended_object727373['object_model_id'] . SP : '0' . SP;
        if ($extended_object727373['object_model_alias'] == 'price') {
            $string .= str()->crop($price_name, 255) . SP;
        } else if ($extended_object727373['object_model_alias'] == 'firm') {
            $string .= str()->crop(preg_replace('~[\t\n\r ]+~', ' ', preg_replace('~\'~', '\'\'', @iconv('utf8', 'cp1251', $extended_object727373['request_response_title']))), 255) . SP;
        } else if ($extended_object727373['object_model_alias'] == 'firm-promo') {
            $string .= str()->crop($object_name727373, 255) . SP;
        } else if ($extended_object727373['object_model_alias'] == 'firm-video') {
            $string .= str()->crop($object_name727373, 255) . SP;
        } else if ($extended_object727373['object_model_alias'] == 'price-request') {
            $string .= str()->crop(preg_replace('~[\t\n\r ]+~', ' ', preg_replace('~\'~', '\'\'', @iconv('utf8', 'cp1251', $extended_object727373['request_response_title']))), 255) . SP;
        } else {
            $string .= '' . SP;
        }
        $string .= $extended_object727373['object_model_alias'] == 'price' ? $price_manufacture . SP : '' . SP;
        $string .= $extended_object727373['object_model_alias'] == 'price' ? $price_unit . SP : '' . SP;
        $string .= $extended_object727373['object_model_alias'] == 'price' ? '' . CR : '' . CR;
//		$string .= $extended_object727373['object_model_alias'] == 'price' ? $price_pack.CR : ''.CR;
    }
    $object727373_counter += $j727373;
    $i727373++;
}
logger('Добавлено stat_object727373ов - ' . $object727373_counter);

$string .= 'internet end' . CR;
logger('Закончили stat_objectов и stat_object727373ов');

//==============================================================================

$string .= 'banner clicks begin' . CR;
logger('Начали stat_banner_clicks');
logger('Получаем stat_banner_clicks по unique_user_ids, коих у нас = ' . count($unique_user_ids));
//DEBUG: GET ALL CLICKS
//$banner_clicks=getAllBannerClicks();
// id_hist_web_adversting  ---	banner_click_id
// id_web_firm_adversting  ---	banner_id
// datetime                ---	banner_time_inserting
// url                     ---	banner_url
// id_service              ---	banner_service_id
// id_city                 ---	banner_city_id
// id_firm                 ---	banner_firm_id
// id_web_adversting_pages ---	1
// id_web_banner_place     ---	2
// id_group                ---	0
// id_subgroup             ---	0
// id_hist_web_connect     ---	banner_user_id
// from_id_service         ---	10
// id_web_site             ---	4
$step = 100; // порция обрабатываемых за раз user_ids
$i = 1; // счетчик порций
$data_exists = true;
$banner_clicks_counter = 0;
while ($data_exists) {
    logger('Шаг ' . $i . ': ' . (($i - 1) * $step) . '-' . ($i * $step));
    if ((($i - 1) * $step) > count($unique_user_ids)) {
        break;
    }
    //$banner_clicks=getBannerClicksByStatUser($unique_user_ids);
    $banner_clicks = getBannerClicksByStatUserIdsRange($unique_user_ids, ($i - 1) * $step, $i * $step);
    logger(count($banner_clicks) . ' новых');
    $j = 0;

    foreach ($banner_clicks as $banner_click) {
        if (in_array($banner_click['banner_user_id'], $banned_user_ids)) continue;
        $j++;
        if (!isset($_firms_objects[$banner_click['banner_firm_id']])) {
            $_firms_objects[$banner_click['banner_firm_id']] = new \App\Model\Firm($banner_click['banner_firm_id']);
        }
        $string .= $banner_click['banner_click_id'] . SP;
        $string .= $banner_click['banner_id'] . SP;
        $string .= $banner_click['banner_time_inserting'] . SP;
        $string .= $banner_click['banner_url'] . SP;
        $string .= $_firms_objects[$banner_click['banner_firm_id']]->id_service() . SP;
        $string .= $banner_click['banner_city_id'] . SP;
        $string .= $_firms_objects[$banner_click['banner_firm_id']]->id_firm() . SP;
        $string .= '1' . SP;
        $string .= '2' . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= $banner_click['banner_user_id'] . SP;
        $string .= ($service !== null ? $service : '10') . SP;
        $string .= '4' . CR;
    }
    $banner_clicks_counter += $j;
    $i++;
}
logger('Добавлено stat_banner_clickов - ' . $banner_clicks_counter);

logger('Начали stat_banner727373_clicks');
logger('Получаем stat_banner727373_clicks по unique_user727373_ids, коих у нас = ' . count($unique_user727373_ids));
//DEBUG: GET ALL CLICKS
//$banner_clicks=getAllBannerClicks();
// id_hist_web_adversting  ---	banner_click_id
// id_web_firm_adversting  ---	banner_id
// datetime                ---	banner_time_inserting
// url                     ---	banner_url
// id_service              ---	banner_service_id
// id_city                 ---	banner_city_id
// id_firm                 ---	banner_firm_id
// id_web_adversting_pages ---	1
// id_web_banner_place     ---	2
// id_group                ---	0
// id_subgroup             ---	0
// id_hist_web_connect     ---	banner_user_id
// from_id_service         ---	10
// id_web_site             ---	18
$step727373 = 100; // порция обрабатываемых за раз user_ids
$i727373 = 1; // счетчик порций
$data727373_exists = true;
$banner727373_clicks_counter = 0;
while ($data727373_exists) {
    logger('Шаг ' . $i727373 . ': ' . (($i727373 - 1) * $step727373) . '-' . ($i727373 * $step727373));
    if ((($i727373 - 1) * $step727373) > count($unique_user727373_ids)) {
        break;
    }
    //$banner_clicks=getBannerClicksByStatUser($unique_user_ids);
    $banner727373_clicks = getBannerClicksByStatUser727373IdsRange($unique_user727373_ids, ($i727373 - 1) * $step727373, $i727373 * $step727373);
    logger(count($banner727373_clicks) . ' новых');
    $j727373 = 0;

    foreach ($banner727373_clicks as $banner727373_click) {
        if (in_array($banner727373_click['banner_user_id'], $banned_user727373_ids)) continue;
        $j727373++;
        if (!isset($_firms_objects[$banner727373_click['banner_firm_id']])) {
            $_firms_objects[$banner727373_click['banner_firm_id']] = new \App\Model\Firm($banner727373_click['banner_firm_id']);
        }
        $string .= $banner727373_click['banner_click_id'] . SP;
        $string .= $banner727373_click['banner_id'] . SP;
        $string .= $banner727373_click['banner_time_inserting'] . SP;
        $string .= $banner727373_click['banner_url'] . SP;
        $string .= $_firms_objects[$banner727373_click['banner_firm_id']]->id_service() . SP;
        $string .= $banner727373_click['banner_city_id'] . SP;
        $string .= $_firms_objects[$banner727373_click['banner_firm_id']]->id_firm() . SP;
        $string .= '1' . SP;
        $string .= '2' . SP;
        $string .= '0' . SP;
        $string .= '0' . SP;
        $string .= $banner727373_click['banner_user_id'] . SP;
        $string .= ($service !== null ? $service : '10') . SP;
        $string .= '18' . CR;
    }
    $banner727373_clicks_counter += $j727373;
    $i727373++;
}
logger('Добавлено stat_banner727373_clickов - ' . $banner727373_clicks_counter);

$string .= 'banner clicks end' . CR;
logger('Закончили stat_banner_clicks и stat_banner727373_clicks');

//==============================================================================

logger('Пишем файл статистики', true);
writeStatFile($string);
echo 'DONE'.PHP_EOL;
//==============================================================================
// FUNCTIONS BLOCK START
// Логирование
function logger($data, $finish = FALSE) {
    if (LOGGING) {
        if (DEBUG) {
            print_r(PHP_EOL . $data);
        } else {
            //$data = date("Y-m-d H:i:s", time()).'> '.$data.CR;
            $data = $data . CR;
            file_put_contents(TMP_LOGFILE, $data, FILE_APPEND | LOCK_EX);
            if ($finish) {
                rename(TMP_LOGFILE, LOGFILE);
            }
        }
    }
}

// Запись файла статистики
function writeStatFile($string) {
    $fp = fopen(STATOUTFILE, "w+");
    file_put_contents(STATOUTFILE, $string);
    fclose($fp);
}

// Вывод в консоль
function out($data, $forsed = false) {
    if (DEBUG || $forsed) {
        echo '<<<<<--' . date("Y-m-d H:i:s", time()) . '-->>>>>' . CR;
        print_r($data);
        echo CR;
    }
}

// Получить массив чисел по ключу в строку с разделителем
function getImplodeStringFromArray($array, $row_name) {
    $result = '';
    foreach ($array as $array_item)
        $result .= empty($result) ? $array_item[$row_name] : ', ' . $array_item[$row_name];
    return $result;
}

// Получить массив значений по ключу
function getSimpleArrayFromDBSelectArray($array, $row_name) {
    $result = [];
    foreach ($array as $array_item)
        $result[] = $array_item[$row_name];
    return $result;
}

// Получение максимального id в stat_object
function getMaxStatObjectId() {
    return app()->db()->query()
                    ->setSelect('max(`id`) AS max_object_id')
                    ->setFrom(['stat_object'])
                    ->selectRow()['max_object_id'];
}

// Получение максимального id в stat_object727373
function getMaxStatObject727373Id() {
    return app()->db()->query()
                    ->setSelect('max(`id`) AS max_object_id')
                    ->setFrom(['stat_object727373'])
                    ->selectRow()['max_object_id'];
}

// Получение расширенных stat_object по start stat_object.id и end stat_object.id
function getExtendedStatObjectDataFromStatObjectIdRange($start_object_id, $end_object_id, $id_service = null) {
    $type_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray([1, 2, 3, 4, 5, 6, 7, 16, 17, 18, 19, 20, 21], 'type');

    $conds = ['AND', '`stat_object`.`id` >= :start_object_id', '`stat_object`.`id` < :end_object_id', $type_conds['where']];
    $params = [':start_object_id' => $start_object_id, ':end_object_id' => $end_object_id] + $type_conds['params'];

    $result = app()->db()->query()
            ->setSelect(['`stat_object`.`id`				AS object_id',
                '`stat_object`.`id_city`			AS object_city_id',
                '`stat_object`.`id_firm`			AS object_firm_id',
                '`stat_object`.`id_stat_request`		AS object_request_id',
                '`stat_object`.`id_stat_user`			AS object_user_id',
                '`stat_object`.`model_alias`			AS object_model_alias',
                '`stat_object`.`model_id`			AS object_model_id',
                '`stat_object`.`name`				AS object_name',
                '`stat_object`.`timestamp_inserting`            AS object_time_inserting',
                '`stat_object`.`type`				AS object_type',
                '`price`.`id_group`				AS price_group_id',
                '`price`.`id_subgroup`			AS price_subgroup_id',
                '`price`.`name`				AS price_name',
                '`price`.`vendor`			AS price_manufacture',
                '`price`.`unit`				AS price_unit',
                '`stat_request`.`response_title`		AS request_response_title',
                '`firm`.`company_name`				AS firm_name'])
            ->setFrom(['stat_object'])
            ->setLeftJoin('price', '`stat_object`.`model_alias` = \'price\' AND `price`.`id` = `stat_object`.`model_id`')
            ->setLeftJoin('stat_request', '`stat_request`.`id`=`stat_object`.`id_stat_request`')
            ->setLeftJoin('firm', '`firm`.`id` = `stat_object`.`id_firm`')
            ->setWhere($conds, $params)
            ->setGroupBy(['object_city_id', 'object_firm_id', 'object_request_id', 'object_user_id', 'object_model_alias', 'object_model_id', 'object_name', 'object_time_inserting', 'price_group_id', 'price_subgroup_id', 'price_name', 'price_manufacture', 'price_unit', 'firm_name'])
            ->setOrderBy('`stat_object`.`id` ASC')
            ->select();

    if ($id_service !== null) {
        $firm_ids = app()->location()->getFirmIdsByService($id_service);

        $_result = $result;
        $result = [];
        foreach ($_result as $row) {
            if (in_array($row['object_firm_id'], $firm_ids)) {
                $result [] = $row;
            }
        }
    }

    return $result;
}

// Получение расширенных stat_object727373 по start stat_object727373.id и end stat_object727373.id
function getExtendedStatObject727373DataFromStatObject727373IdRange($start_object_id, $end_object_id, $id_service = null) {
    $type_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray([1, 2, 3, 4, 5, 6, 7, 16, 17, 18, 19, 20, 21], 'type');

    $conds = ['AND', '`stat_object727373`.`id` >= :start_object_id', '`stat_object727373`.`id` < :end_object_id', $type_conds['where']];
    $params = [':start_object_id' => $start_object_id, ':end_object_id' => $end_object_id] + $type_conds['params'];

    $result = app()->db()->query()
            ->setSelect(['`stat_object727373`.`id`				AS object_id',
                '`stat_object727373`.`id_city`			AS object_city_id',
                '`stat_object727373`.`id_firm`			AS object_firm_id',
                '`stat_object727373`.`id_stat_request`		AS object_request_id',
                '`stat_object727373`.`id_stat_user`		AS object_user_id',
                '`stat_object727373`.`model_alias`		AS object_model_alias',
                '`stat_object727373`.`model_id`			AS object_model_id',
                '`stat_object727373`.`name`			AS object_name',
                '`stat_object727373`.`timestamp_inserting`	AS object_time_inserting',
                '`stat_object727373`.`type`			AS object_type',
                '`price`.`id_group`				AS price_group_id',
                '`price`.`id_subgroup`			AS price_subgroup_id',
                '`price`.`name`				AS price_name',
                '`price`.`vendor`			AS price_manufacture',
                '`price`.`unit`				AS price_unit',
                '`stat_request727373`.`response_title`		AS request_response_title',
                '`firm`.`company_name`				AS firm_name'])
            ->setFrom(['stat_object727373'])
            ->setLeftJoin('price', '`stat_object727373`.`model_alias` = \'price\' AND `price`.`id` = `stat_object727373`.`model_id`')
            ->setLeftJoin('stat_request727373', '`stat_request727373`.`id`=`stat_object727373`.`id_stat_request`')
            ->setLeftJoin('firm', '`firm`.`id` = `stat_object727373`.`id_firm`')
            ->setWhere($conds, $params)
            ->setGroupBy(['object_city_id', 'object_firm_id', 'object_request_id', 'object_user_id', 'object_model_alias', 'object_model_id', 'object_name', 'object_time_inserting', 'price_group_id', 'price_subgroup_id', 'price_name', 'price_manufacture', 'price_unit', 'firm_name'])
            ->setOrderBy('`stat_object727373`.`id` ASC')
            ->select();

    if ($id_service !== null) {
        $firm_ids = app()->location()->getFirmIdsByService($id_service);

        $_result = $result;
        $result = [];
        foreach ($_result as $row) {
            if (in_array($row['object_firm_id'], $firm_ids)) {
                $result [] = $row;
            }
        }
    }

    return $result;
}

// Получение уникальных id пользователей из таблицы stat_object по start stat_object.id и end stat_object.id
function getDistinctUserIdsFromStatObjectIdRange($start_object_id, $end_object_id, $id_service = null) {
    $conds = ['AND', '`stat_object`.`id` >= :start_object_id', '`stat_object`.`id` < :end_object_id'];
    $params = [':start_object_id' => $start_object_id, ':end_object_id' => $end_object_id];

    if ($id_service !== null) {
        $firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIdsByService($id_service), 'id_firm');
        if (count($firm_conds) > 0) {
            $conds = ['AND', $firm_conds['where'], '`stat_object`.`id` >= :start_object_id', '`stat_object`.`id` < :end_object_id'];
            $params = [':start_object_id' => $start_object_id, ':end_object_id' => $end_object_id] + $firm_conds['params'];
        }
    }

    return app()->db()->query()
                    ->setSelect('DISTINCT(`stat_object`.`id_stat_user`)')
                    ->setFrom(['stat_object'])
                    ->setWhere($conds, $params)
                    ->setOrderBy('`stat_object`.`id` ASC')
                    ->select();
}

// Получение уникальных id пользователей 727373 из таблицы stat_object727373 по start stat_object727373.id и end stat_object727373.id
function getDistinctUser727373IdsFromStatObject727373IdRange($start_object_id, $end_object_id, $id_service = null) {
    $conds = ['AND', '`stat_object727373`.`id` >= :start_object_id', '`stat_object727373`.`id` < :end_object_id'];
    $params = [':start_object_id' => $start_object_id, ':end_object_id' => $end_object_id];
    if ($id_service !== null) {
        $firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIdsByService($id_service), 'id_firm');
        if (count($firm_conds) > 0) {
            $conds = ['AND', $firm_conds['where'], '`stat_object727373`.`id` >= :start_object_id', '`stat_object727373`.`id` < :end_object_id'];
            $params = [':start_object_id' => $start_object_id, ':end_object_id' => $end_object_id] + $firm_conds['params'];
        }
    }

    return app()->db()->query()
                    ->setSelect('DISTINCT(`stat_object727373`.`id_stat_user`)')
                    ->setFrom(['stat_object727373'])
                    ->setWhere($conds, $params)
                    ->setOrderBy('`stat_object727373`.`id` ASC')
                    ->select();
}

function getBannedUserIds($user_ids) {
    $banned_user_ips = [];
    $user_items = app()->db()->query()
            ->setText("SELECT * FROM ("
                    ." SELECT `id`,`ip_addr`, `timestamp_beginning`, SUBSTRING_INDEX(`timestamp_beginning`, :tstmp, 2) as `without_seconds`, COUNT(`id`) as `users_per_minute` "
                    ." FROM `stat_user` "
                    ." WHERE `id` IN (".implode(',', $user_ids).") "
                    ." GROUP BY `ip_addr`, `without_seconds` ORDER BY `users_per_minute` DESC"
                    .") s WHERE s.`users_per_minute` > :max_users_per_minute")
            ->setParams([':max_users_per_minute' => 20,
                ':tstmp' => ':'])
            ->fetch();

    foreach ($user_items as $item) {
        if (!in_array($item['ip_addr'], $banned_user_ips)) {
            $banned_user_ips [] = $item['ip_addr'];
        }
    }
    // ips from stat_requests
    $request_items = app()->db()->query()
            ->setText("SELECT * FROM ("
                    ." SELECT sr.`id`, SUBSTRING_INDEX(`timestamp_inserting`, :tstmp, 2) as `without_seconds`, COUNT(sr.`id`) as `requests_per_minute`, su.`ip_addr` "
                    ." FROM `stat_request` sr "
                    ." LEFT JOIN `stat_user` su ON su.`id` = sr.`id_stat_user`"
                    ." WHERE sr.`id_stat_user` IN (".implode(',', $user_ids).") "
                    ." GROUP BY su.`ip_addr`, `without_seconds` ORDER BY `requests_per_minute` DESC) s WHERE s.`requests_per_minute` > :max_requests_per_minute")
            ->setParams([':max_requests_per_minute' => 50,
                ':tstmp' => ':'])
            ->fetch();

    foreach ($request_items as $item) {
        if (!in_array($item['ip_addr'], $banned_user_ips)) {
            $banned_user_ips [] = $item['ip_addr'];
        }
    }
    
    $banned_user_ids = [];
    $banned_user_ips = array_unique($banned_user_ips);
    $banned_user_items = app()->db()->query()
            ->setText("SELECT * "
                    ." FROM `stat_user` "
                    ." WHERE `ip_addr` IN ('".implode('\',\'', $banned_user_ips)."') ")
            ->fetch();

    foreach ($banned_user_items as $item) {
        if (!in_array($item['id'], $banned_user_ips)) {
            $banned_user_ids [] = $item['id'];
        }
    }

    return array_unique($banned_user_ids);
}

function getBannedUser727373Ids($user727373_ids) {
    $banned_user727373_ips = [];
    $user727373_items = app()->db()->query()
            ->setText("SELECT * FROM ("
                    ." SELECT `id`,`ip_addr`, `timestamp_beginning`, SUBSTRING_INDEX(`timestamp_beginning`, :tstmp, 2) as `without_seconds`, COUNT(`id`) as `users_per_minute` "
                    ." FROM `stat_user727373` "
                    ." WHERE `id` IN (".implode(',', $user727373_ids).") "
                    ." GROUP BY `ip_addr`, `without_seconds` ORDER BY `users_per_minute` DESC"
                    .") s WHERE s.`users_per_minute` > :max_users_per_minute")
            ->setParams([':max_users_per_minute' => 20,
                ':tstmp' => ':'])
            ->fetch();

    foreach ($user727373_items as $item) {
        if (!in_array($item['ip_addr'], $banned_user727373_ips)) {
            $banned_user727373_ips [] = $item['ip_addr'];
        }
    }
    // ips from stat_requests
    $request727373_items = app()->db()->query()
            ->setText("SELECT * FROM ("
                    ." SELECT sr.`id`, SUBSTRING_INDEX(`timestamp_inserting`, :tstmp, 2) as `without_seconds`, COUNT(sr.`id`) as `requests_per_minute`, su.`ip_addr` "
                    ." FROM `stat_request727373` sr "
                    ." LEFT JOIN `stat_user727373` su ON su.`id` = sr.`id_stat_user`"
                    ." WHERE sr.`id_stat_user` IN (".implode(',', $user727373_ids).") "
                    ." GROUP BY su.`ip_addr`, `without_seconds` ORDER BY `requests_per_minute` DESC) s WHERE s.`requests_per_minute` > :max_requests_per_minute")
            ->setParams([':max_requests_per_minute' => 50,
                ':tstmp' => ':'])
            ->fetch();

    foreach ($request727373_items as $item) {
        if (!in_array($item['ip_addr'], $banned_user727373_ips)) {
            $banned_user727373_ips [] = $item['ip_addr'];
        }
    }
    
    $banned_user727373_ids = [];
    $banned_user727373_items = app()->db()->query()
            ->setText("SELECT * "
                    ." FROM `stat_user727373` "
                    ." WHERE `ip_addr` IN ('".implode('\',\'', array_unique($banned_user727373_ips))."') ")
            ->fetch();

    foreach ($banned_user727373_items as $item) {
        if (!in_array($item['id'], $banned_user727373_ips)) {
            $banned_user727373_ids [] = $item['id'];
        }
    }

    return array_unique($banned_user727373_ids);
}

// Получение пользователей по id
function getUsersByUserIds($user_ids) {
    $conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($user_ids);

    return app()->db()->query()
                    ->setSelect(['`stat_user`.`id`                                  AS user_id',
                        '`stat_user`.`cookie_hash` 			AS user_cookie_hash',
                        '`stat_user`.`id_city` 				AS user_city_id',
                        '`stat_user`.`id_user` 				AS id_user_id',
                        '`stat_user`.`ip_addr` 				AS user_ip',
                        '`stat_user`.`referer`				AS user_referer',
                        '`stat_user`.`timestamp_beginning`              AS user_time_begining',
                        '`stat_user`.`timestamp_ending`                 AS user_time_ending',
                        '`stat_user`.`user_agent`			AS user_agent',
                        '`stat_user`.`user_city_name`                   AS user_city'])
                    ->setFrom(['stat_user'])
                    ->setWhere($conds['where'], $conds['params'])
                    ->setOrderBy('`stat_user`.`id` ASC')
                    ->select();
}

// Получение пользователей 727373 по id
function getUsersByUser727373Ids($user_ids) {
    $conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($user_ids);

    return app()->db()->query()
                    ->setSelect(['`stat_user727373`.`id` 				AS user_id',
                        '`stat_user727373`.`cookie_hash` 		AS user_cookie_hash',
                        '`stat_user727373`.`id_city` 			AS user_city_id',
                        '`stat_user727373`.`id_user` 			AS id_user_id',
                        '`stat_user727373`.`ip_addr` 			AS user_ip',
                        '`stat_user727373`.`referer`			AS user_referer',
                        '`stat_user727373`.`timestamp_beginning`	AS user_time_begining',
                        '`stat_user727373`.`timestamp_ending`		AS user_time_ending',
                        '`stat_user727373`.`user_agent`			AS user_agent',
                        '`stat_user727373`.`user_city_name`		AS user_city'])
                    ->setFrom(['stat_user727373'])
                    ->setWhere($conds['where'], $conds['params'])
                    ->setOrderBy('`stat_user727373`.`id` ASC')
                    ->select();
}

// Получение уникальных id запросов из таблицы stat_object по start stat_object.id и end stat_object.id
function getDistinctRequestIdsFromStatObjectIdRange($start_object_id, $end_object_id, $id_service = null) {
    $conds = ['AND', '`stat_object`.`id` >= :start_object_id', '`stat_object`.`id` < :end_object_id'];
    $params = [':start_object_id' => $start_object_id, ':end_object_id' => $end_object_id];

    if ($id_service !== null) {
        $firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIdsByService($id_service), 'id_firm');
        if (count($firm_conds) > 0) {
            $conds = ['AND', $firm_conds['where'], '`stat_object`.`id` >= :start_object_id', '`stat_object`.`id` < :end_object_id'];
            $params = [':start_object_id' => $start_object_id, ':end_object_id' => $end_object_id] + $firm_conds['params'];
        }
    }

    return app()->db()->query()
                    ->setSelect('DISTINCT(`stat_object`.`id_stat_request`)')
                    ->setFrom('stat_object')
                    ->setWhere($conds, $params)
                    ->setOrderBy('`stat_object`.`id` ASC')
                    ->select();
}

// Получение уникальных id запросов 727373 из таблицы stat_object727373 по start stat_object727373.id и end stat_object727373.id
function getDistinctRequest727373IdsFromStatObject727373IdRange($start_object_id, $end_object_id, $id_service = null) {
    $conds = ['AND', '`stat_object727373`.`id` >= :start_object_id', '`stat_object727373`.`id` < :end_object_id'];
    $params = [':start_object_id' => $start_object_id, ':end_object_id' => $end_object_id];

    if ($id_service !== null) {
        $firm_conds = \Sky4\Model\Utils::prepareWhereCondsFromArray(app()->location()->getFirmIdsByService($id_service), 'id_firm');
        if (count($firm_conds) > 0) {
            $conds = ['AND', $firm_conds['where'], '`stat_object727373`.`id` >= :start_object_id', '`stat_object727373`.`id` < :end_object_id'];
            $params = [':start_object_id' => $start_object_id, ':end_object_id' => $end_object_id] + $firm_conds['params'];
        }
    }

    return app()->db()->query()
                    ->setSelect('DISTINCT(`stat_object727373`.`id_stat_request`)')
                    ->setFrom('stat_object727373')
                    ->setWhere($conds, $params)
                    ->setOrderBy('`stat_object727373`.`id` ASC')
                    ->select();
}

// Получение запросов по id
function getRequestsByRequestIds($request_ids) {
    $conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($request_ids);
    return app()->db()->query()
                    ->setSelect(['`stat_request`.`id`				AS request_id',
                        '`stat_request`.`id_stat_user`				AS request_user_id',
                        '`stat_request`.`request_refferer`			AS request_request_referer',
                        '`stat_request`.`request_text`				AS request_request_text',
                        '`stat_request`.`request_url`				AS request_request_url',
                        '`stat_request`.`response_code`				AS request_response_code',
                        '`stat_request`.`response_id_city`			AS request_city_id',
                        '`stat_request`.`response_title`			AS request_response_title',
                        '`stat_request`.`response_url`				AS request_response_url',
                        '`stat_request`.`timestamp_inserting`                   AS request_time_inserting',
                        '`stat_user`.`ip_addr`					AS user_ip',
                        '`stat_user`.`user_city_name`				AS user_city',
                        '`sts_city`.`id_region_country`				AS city_region_id',
                        '`sts_city`.`id_country`				AS city_country_id'])
                    ->setFrom('stat_request')
                    ->setLeftJoin('stat_user', '`stat_user`.`id`=`stat_request`.`id_stat_user`')
                    ->setLeftJoin('sts_city', '`sts_city`.`id_city`=`stat_request`.`response_id_city`')
                    ->setWhere('`stat_request`.' . $conds['where'], $conds['params'])
                    ->setOrderBy('`stat_request`.`id` ASC')
                    ->select();
}

// Получение запросов 727373 по id
function getRequestsByRequest727373Ids($request_ids) {
    $conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($request_ids);
    return app()->db()->query()
                    ->setSelect(['`stat_request727373`.`id`				AS request_id',
                        '`stat_request727373`.`id_stat_user`			AS request_user_id',
                        '`stat_request727373`.`request_refferer`		AS request_request_referer',
                        '`stat_request727373`.`request_text`			AS request_request_text',
                        '`stat_request727373`.`request_url`			AS request_request_url',
                        '`stat_request727373`.`response_code`			AS request_response_code',
                        '`stat_request727373`.`response_id_city`		AS request_city_id',
                        '`stat_request727373`.`response_title`			AS request_response_title',
                        '`stat_request727373`.`response_url`			AS request_response_url',
                        '`stat_request727373`.`timestamp_inserting`		AS request_time_inserting',
                        '`stat_user727373`.`ip_addr`				AS user_ip',
                        '`stat_user727373`.`user_city_name`			AS user_city',
                        '`sts_city`.`id_region_country`				AS city_region_id',
                        '`sts_city`.`id_country`				AS city_country_id'])
                    ->setFrom('stat_request727373')
                    ->setLeftJoin('stat_user727373', '`stat_user727373`.`id`=`stat_request727373`.`id_stat_user`')
                    ->setLeftJoin('sts_city', '`sts_city`.`id_city`=`stat_request727373`.`response_id_city`')
                    ->setWhere('`stat_request727373`.' . $conds['where'], $conds['params'])
                    ->setOrderBy('`stat_request727373`.`id` ASC')
                    ->select();
}

function getAllBannerClicks() {
    return app()->db()->query()
                    ->setSelect(['`stat_banner_click`.`id`				AS banner_click_id',
                        '`stat_banner_click`.`id_banner`		AS banner_id',
                        '`stat_banner_click`.`id_city` 			AS banner_city_id',
                        '`stat_banner_click`.`id_firm`			AS banner_firm_id',
                        '`stat_banner_click`.`id_stat_user`		AS banner_user_id',
                        '`stat_banner_click`.`timestamp_inserting`	AS banner_time_inserting',
                        '`stat_banner_click`.`timestamp_last_updating` 	AS banner_time_updating',
                        '`banner`.`url` 								AS banner_url'
                    ])
                    ->setFrom('stat_banner_click')
                    ->setLeftJoin('banner', '`banner`.`id`=`stat_banner_click`.`id_banner`')
                    ->setOrderBy('`stat_banner_click`.`id` ASC')
                    ->select();
}

// Получение статистики кликов по баннерам по id stat_user
function getBannerClicksByStatUser($user_ids) {
    $conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($user_ids, 'id_stat_user');
    return app()->db()->query()
                    ->setSelect(['`stat_banner_click`.`id`				AS banner_click_id',
                        '`stat_banner_click`.`id_banner`		AS banner_id',
                        '`stat_banner_click`.`id_city` 			AS banner_city_id',
                        '`stat_banner_click`.`id_firm`			AS banner_firm_id',
                        '`stat_banner_click`.`id_stat_user`		AS banner_user_id',
                        '`stat_banner_click`.`timestamp_inserting`	AS banner_time_inserting',
                        '`stat_banner_click`.`timestamp_last_updating` 	AS banner_time_updating',
                        '`banner`.`url` 								AS banner_url'
                    ])
                    ->setFrom('stat_banner_click')
                    ->setLeftJoin('banner', '`banner`.`id`=`stat_banner_click`.`id_banner`')
                    ->setWhere('`stat_banner_click`.' . $conds['where'], $conds['params'])
                    ->setOrderBy('`stat_banner_click`.`id` ASC')
                    ->select();
}

// Получение статистики кликов по баннерам по id stat_user от start_id до end_id
function getBannerClicksByStatUserIdsRange($user_ids, $start_id, $end_id) {
    if ($end_id >= count($user_ids))
        $end_id = count($user_ids);
    $prepared_user_ids = [];
    for ($i = $start_id; $i < $end_id; $i++) {
        $prepared_user_ids[] = $user_ids[$i];
    }
    $conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($prepared_user_ids, 'id_stat_user');
    return app()->db()->query()
                    ->setSelect(['`stat_banner_click`.`id`				AS banner_click_id',
                        '`stat_banner_click`.`id_banner`		AS banner_id',
                        '`stat_banner_click`.`id_city` 			AS banner_city_id',
                        '`stat_banner_click`.`id_firm`			AS banner_firm_id',
                        '`stat_banner_click`.`id_stat_user`		AS banner_user_id',
                        '`stat_banner_click`.`timestamp_inserting`	AS banner_time_inserting',
                        '`stat_banner_click`.`timestamp_last_updating`  AS banner_time_updating',
                        '`banner`.`url` 				AS banner_url'
                    ])
                    ->setFrom('stat_banner_click')
                    ->setLeftJoin('banner', '`banner`.`id`=`stat_banner_click`.`id_banner`')
                    ->setWhere('`stat_banner_click`.' . $conds['where'], $conds['params'])
                    ->setOrderBy('`stat_banner_click`.`id` ASC')
                    ->select();
}

function getBannerClicksByStatUser727373IdsRange($user727373_ids, $start_id, $end_id) {
    if ($end_id >= count($user727373_ids))
        $end_id = count($user727373_ids);
    $prepared_user727373_ids = [];
    for ($i = $start_id; $i < $end_id; $i++) {
        $prepared_user727373_ids[] = $user727373_ids[$i];
    }
    $conds = \Sky4\Model\Utils::prepareWhereCondsFromArray($prepared_user727373_ids, 'id_stat_user');
    if ($prepared_user727373_ids) {
        return app()->db()->query()
                        ->setSelect(['`stat_banner727373_click`.`id`                AS banner_click_id',
                            '`stat_banner727373_click`.`cml_banner_id`          AS banner_id',
                            '`stat_banner727373_click`.`id_city` 			AS banner_city_id',
                            '`stat_banner727373_click`.`id_firm`			AS banner_firm_id',
                            '`stat_banner727373_click`.`id_stat_user`		AS banner_user_id',
                            '`stat_banner727373_click`.`timestamp_inserting`	AS banner_time_inserting',
                            '`stat_banner727373_click`.`timestamp_last_updating`  AS banner_time_updating',
                            '`banner`.`url` 				AS banner_url'
                        ])
                        ->setFrom('stat_banner727373_click')
                        ->setLeftJoin('banner', '`banner`.`id`=`stat_banner727373_click`.`id_banner`')
                        ->setWhere('`stat_banner727373_click`.' . $conds['where'], $conds['params'])
                        ->setOrderBy('`stat_banner727373_click`.`id` ASC')
                        ->select();
    }

    return [];
}

function isBadStatRequest727373($stat_request_id) {
    $_bad_urls = [ '/user-office/' ];
    
    $conds = [ 'AND', '`id` = :id' ];
    $params = [ ':id' => $stat_request_id ];

    $_r = app()->db()->query()
                    ->setFrom('stat_request727373')
                    ->setWhere($conds, $params)
                    ->selectRow();
    foreach ($_bad_urls as $_bad_url) {
        if (strpos($_r['request_url'], $_bad_url) !== FALSE) {
            return true;
        }
    }

    return false;
}

