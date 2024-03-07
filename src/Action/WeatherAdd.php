<?php 

require_once '/var/www/sites/tovaryplus.ru/config/config_app.php';
ini_set('display_errors', 1);
function getCityWeather() {
    $url = 'http://api.openweathermap.org/data/2.5/find';
    $headers = [
        'Content-Type: application/json',
        //'X-Gismeteo-Token: 56b30cb255.3443075'
    ];

    $params = array(
        'q' => 'Yaroslavl',
        'lang' => 'ru',
        'type' => 'like',
        'APPID' => '910e85295bde2ebc3182f3b098167c98'

    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    curl_close($ch);

    $xml_doc = json_decode($data, true);
    





    if ($xml_doc) {
        //return $xml_doc;
        if (isset($xml_doc["list"][0]["main"]["temp"])){
            $temp = $xml_doc["list"][0]["main"]["temp"]-273.15;
        }else{
            $temp = '?';
        }

        if (isset($xml_doc["list"][0]["weather"][0]["description"])){
            $description = $xml_doc["list"][0]["weather"][0]["description"];
        }else{
            $description = 'Неизвестно';
        }
//        echo '\n\n\n';
//        var_dump($description);
//        echo '\n\n\n';
        $rezult = app()->db()->query()
            ->setText('INSERT INTO `city_weather`(`temp`, `description`) VALUES (:temp, :description)')
            ->execute([
                ':temp' => $temp,
                ':description' => $description
            ]);
//        echo '\n\n\n';
//        var_dump($rezult);
//        echo '\n\n\n';
    }
    //echo 'Add temp';
    //return true;
}  

//echo '123';

getCityWeather();//получаем температуру