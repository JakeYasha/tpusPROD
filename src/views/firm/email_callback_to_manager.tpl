<?
$firm = new \App\Model\Firm($item['id']);
/*
 * доступные переменные
 * $firm->name()
 * $firm->address()
 * $firm->link()
 * ...
  $params['name'] - имя пользователя, $params['phone'] - телефон, $params['message_text'] - ссылка на страницу с которой заказали звонок
 *  */
?>
<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>Уважаемый менеджер!<p>
	<p>На сайте tovaryplus.ru создан новый заказ на обратный звонок для компании: <?=$firm->name()?>. Свяжитесь с представителем компании и получите подтверждение, что сообщение получено, обработано и исполнено.</p>
	<p>Запрос со страницы: <a href="<?=$params['message_text']?>"><?=$params['message_text']?></a></p>
	<p>Сообщение для компании <a href="http://www.tovaryplus.ru<?=$firm->link()?>"><?=$firm->name()?></a></p>
	<p>Адрес компании: <?=$firm->address()?></p>
	<p> </p>
	<p><strong>Текст отправленного смс сообщения:</strong></p>
	<p><i>Посетитель сайта TovaryPlus.ru <?= $params['name']?> просит вас перезвонить ему на номер <?= $params['phone']?>.</i></p>
	<br/>
	<br/>
	<br/>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
</div>