<?
$advert_module = new \App\Model\AdvertModule($item['id_advert_module']);?>
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
        <p>Вы сформировали заказ/сообщение по рекламному модулю: <?=$advert_module->header()?></p>
	<p> </p>
	<p><strong>Текст вашего заказа/сообщения:</strong></p>
	<p><i><?= $item['text']?></i></p>
	<p> </p>
	<p>-------------------------------------------------------</p>
	<p>Служба поддержки сайта tovaryplus.ru, приложит все усилия, чтобы ваше сообщение не осталось без ответа.</p>
	<p> </p>
	<p>Рекламно-информационный сайт <a href="http://www.tovaryplus.ru/page/show/reklamno-informacionnyj-proekt-o-firmah-tovarah-i-uslugah-yaroslavlya-TovaryPlus.htm">TovaryPlus.ru</a> - интернет проект Информационного центра "Товары плюс", Ярославль</p>
	<br/>
	<br/>
	<br/>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
</div>