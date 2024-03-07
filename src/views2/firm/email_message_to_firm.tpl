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
	<p>Уважаемый клиент!<p>
	<p>На сайте tovaryplus.ru в адрес компании <a href="http://www.tovaryplus.ru<?=$firm->link()?>"><?=$firm->name()?></a> поступило новое сообщение от вашего потенциального покупателя или заказчика:</p>
	<p> </p>
    <p><strong>Отправитель сообщения:</strong> <?= $item['user_name']?> (<?= $item['user_email']?>)<br />
	<strong>Тема сообщения:</strong> <?= $item['message_subject']?><br />
	<strong>Текст сообщения:</strong></p>
	<p><i><?= $item['message_text']?></i></p>
    <p> </p>
	<p>Для ответа и связи с посетителем используйте его контактные данные: <strong><?= $item['user_email']?></strong>.</p>
	<p> </p>
	<?=app()->chunk()->setArg($firm)->render('advert_module.footer_adv_text')?>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
</div>
