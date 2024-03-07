<?
$advert_module = new \App\Model\AdvertModule($item['id_advert_module']);
?>
<?/*
 * доступные переменные
 * $advert_module->name()
 * $advert_module->link()
 * ...
 * $item['user_name']
 * $item['user_email']
 * $item['text'] - комментарий к заказу
 * $item['brief_text'] - тема сообщения
 *  */?>
<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>Уважаемый менеджер!<p>
	<p>На сайте tovaryplus.ru создан новый заказ по рекламному модулю: <?=$advert_module->header()?>. Свяжитесь с представителем компании и получите подтверждение, что заказ получен, обработан и исполнен.</p>
	<p></p>
	<p>Потенциальный покупатель или заказчик <strong><?= $item['user_name']?> (<?=strlen($item['user_email']) ? "Email: ".$item['user_email'] : ""?> <?=strlen($item['user_phone']) ? "телефон: ".$item['user_phone'] : ""?>)</strong><br />
	<strong>Текст сообщения:</strong></p>
	<p><i><?= $item['text']?></i></p>
	<br/>
	<br/>
	<br/>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
</div>