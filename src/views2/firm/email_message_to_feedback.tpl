<?
$firm = new \App\Model\Firm($item['id_firm']);
?>
<? /*
 * доступные переменные
 * $item['user_name']
 * $item['user_email']
 * $item['message_subject'] - тема сообщения
 * $item['message_text'] - само сообщение
 *  */?>
<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>Для ответственного лица!<p>
	<p>На сайте tovaryplus.ru сформировано новое сообщение по теме:<br />
	<strong><?= $item['message_subject']?></strong></p>
	<p> </p>
    <p><strong>Отправитель сообщения:</strong> <?= $item['user_name']?> (<?= $item['user_email']?>)<br />
	<strong>Текст сообщения:</strong></p>
	<p><i><?= $item['message_text']?></i></p>
    <p> </p>
	<p>Для ответа и связи с посетителем используйте его контактные данные: <strong><?= $item['user_email']?></strong>.</p>
	<br/>
	<br/>
	<br/>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
        <br/>
	<br/>
	<p>Отправлено со страницы: <?=$params['request_uri']?></p>
</div>