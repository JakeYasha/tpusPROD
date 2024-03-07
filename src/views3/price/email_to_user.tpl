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
	<p>Уважаемый(ая) <?= $item['user_name']?>!<p>
	<p>Вы сформировали заказ/сообщение в адрес компании <a href="http://www.tovaryplus.ru<?=$firm->link()?>"><?=$firm->name()?></a> по товару/услуге <a href="http://www.tovaryplus.ru<?=$price->link()?>"><?=$price->name()?></a></p>
	<p> </p>
	<p>Ваш заказ передан представителю компании <?=$firm->name()?> для дальнейшей обработки.</p>
	<p><strong>Текст вашего заказа/сообщения:</strong></p>
	<p><i><?= $item['text']?></i></p>
	<p> </p>
	<p>-------------------------------------------------------</p>
	<p>Служба поддержки сайта tovaryplus.ru, приложит все усилия, чтобы ваше сообщение не осталось без ответа. О всех положительных и отрицательных моментах взаимодействия с компанией <?=$firm->name()?> вы можете написать в дальнейшем в своем отзыве о компании.</p>
	<p> </p>
	<p>Рекламно-информационный сайт <a href="http://www.tovaryplus.ru/page/show/reklamno-informacionnyj-proekt-o-firmah-tovarah-i-uslugah-yaroslavlya-TovaryPlus.htm">TovaryPlus.ru</a> - интернет проект Информационного центра "Товары плюс", Ярославль</p>
	<br/>
	<br/>
	<br/>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
</div>