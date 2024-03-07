<?
$firm = new \App\Model\Firm($item['id_firm']);
?>
<? /*
 * доступные переменные
 * $firm->name()
 * $firm->address()
 * $firm->link()
 * ...
 * $item['user_name']
 * $item['user_email']
 * $item['message_subject'] - тема сообщения
 * $item['message_text'] - само сообщение
 *  */?>
<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>Уважаемый менеджер!<p>
	<p>На сайте tovaryplus.ru создано новое сообщение для компании: <?=$firm->name()?>. Свяжитесь с представителем компании и получите подтверждение, что сообщение получено, обработано и исполнено.</p>
	<p></p>
	<p>Сообщение для компании <a href="http://www.tovaryplus.ru<?=$firm->link()?>"><?=$firm->name()?></a><br />Адрес компании: <?=$firm->address()?></p>
	<p> </p>
    <p><strong>Отправитель сообщения:</strong> <?= $item['user_name']?> (<?= $item['user_email']?>)<br />
	<strong>Тема сообщения:</strong> <?= $item['message_subject']?><br />
	<strong>Текст сообщения:</strong></p>
	<p><i><?= $item['message_text']?></i></p>
	<br/>
	<br/>
	<br/>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
        <br/>
	<br/>
	<p>Отправлено со страницы: <?=$params['request_uri']?></p>
</div>