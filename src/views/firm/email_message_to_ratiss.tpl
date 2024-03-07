<?
$firm = new \App\Model\Firm($item['id_firm']);
?>
<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>Здравствуйте!<p>
	<p>На сайте tovaryplus.ru создано новое сообщение о некорректности данных для компании: <?=$firm->name()?>. Просим проверить информацию о компании и внести при необходимости изменения в базе данных.</p>
	<p>После проведения очередного глобального обмена внесенные вами изменения будут показаны и на сайте tovaryplus.ru</p>
	<p></p>
	<p>Сообщение о компании <a href="http://www.tovaryplus.ru<?=$firm->link()?>"><?=$firm->name()?></a><br />Адрес компании: <?=$firm->address()?></p>
	<p> </p>
    <p><strong>Отправитель сообщения:</strong> <?= $item['user_name']?> (<?= $item['user_email']?>)<br />
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