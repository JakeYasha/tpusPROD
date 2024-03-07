<?
$advert_module = new \App\Model\AdvertModule($item['id_advert_module']);?>
<?/*
 * доступные переменные
 * $item['user_name']
 * $item['user_email']
 * $item['text'] - комментарий к заказу
 * $item['brief_text'] - тема сообщения
 *  */?>
<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>Уважаемый клиент!<p>
	<p>Потенциальный покупатель или заказчик <?= $item['user_name']?> (<?= $item['user_email']?>) интересуется рекламным модулем: <?=$advert_module->header()?><br />
	<strong>Текст сообщения:</strong></p>
	<p><i><?= $item['text']?></i></p>
	<p> </p>
	<p>Для ответа и связи с покупателем или заказчиком используйте его контактные данные: <strong><?= $item['user_name']?> (<?=strlen($item['user_email']) ? "Email: ".$item['user_email'] : ""?> <?=strlen($item['user_phone']) ? "телефон: ".$item['user_phone'] : ""?>)</strong>.</p>
	<p> </p>
	<?=app()->chunk()->setArg($firm)->render('advert_module.footer_adv_text')?>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
</div>