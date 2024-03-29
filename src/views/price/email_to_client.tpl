<?
$firm = new \App\Model\Firm($item['id_firm']);
$price = new \App\Model\Price(); $price->reader()->setWhere(['AND','legacy_id_price = :id_price', 'legacy_id_service = :id_service'], [':id_price' => $item['id_price'], ':id_service' => $firm->id_service()])->objectByConds();
?>
<?/*
 * доступные переменные
 * $firm->name()
 * $firm->address()
 * $firm->link()
 * $price->name()
 * $price->link()
 * ...
 * $item['user_name']
 * $item['user_email']
 * $item['text'] - комментарий к заказу
 * $item['brief_text'] - тема сообщения
 *  */?>
<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>Уважаемый клиент!<p>
	<p>На сайте tovaryplus.ru в адрес компании <a href="http://www.tovaryplus.ru<?=$firm->link()?>"><?=$firm->name()?></a> поступил новый заказ (сообщение):</p>
	<p> </p>
	<p>Потенциальный покупатель или заказчик <?= $item['user_name']?> (<?= $item['user_email']?>) интересуется позицей прайс-листа компании: <a href="http://www.tovaryplus.ru<?=$price->link()?>"><?=$price->name()?></a><br />
	<strong>Текст сообщения:</strong></p>
	<p><i><?= $item['text']?></i></p>
	<p> </p>
	<p>Для ответа и связи с покупателем или заказчиком используйте его контактные данные: <strong><?= $item['user_name']?> (<?=strlen($item['user_email']) ? "Email: ".$item['user_email'] : ""?> <?=strlen($item['user_phone']) ? "телефон: ".$item['user_phone'] : ""?>)</strong>.</p>
	<p> </p>
	<?=app()->chunk()->setArg($firm)->render('advert_module.footer_adv_text')?>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
</div>