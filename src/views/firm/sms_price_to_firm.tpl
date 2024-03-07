<?
/*
 * доступные переменные
 * ...
  $params['name'] - имя пользователя, $params['phone'] - телефон, $params['price_name']
 *  */
?>TovaryPlus.ru: посетитель <?=$params['name']?> (<?=$params['phone']?>) заинтересовался предложением: <?=  str()->crop($params['price_name'], 30)?>, перезвоните ему