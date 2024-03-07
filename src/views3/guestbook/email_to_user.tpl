<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>Уважаемый(ая) <?=$item['user_name']?>, ваше сообщение в гостевой книге сайта www.tovaryplus.ru от <?=date("d.m.Y H:i:s", CDateTime::toTimestamp($item['timestamp_inserting']))?>:</p>
	<p><i><?=$item['text']?></i></p>
	<p><strong>Отвечаем на ваше сообщение</strong>:</p>
	<p><?=$item['answer_text']?></p>
	<p><br />С уважением, редактор сайта <a href="http://www.tovaryplus.ru">www.tovaryplus.ru</a>.</p>
</div>