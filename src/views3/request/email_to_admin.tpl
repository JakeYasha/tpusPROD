<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>На сайте TovaryPlus.ru появился новый запрос на размещение информации об организации <strong>"<?=$item['company_name']?>"</strong>.</p>
	<p></p>
	<p><strong>ФИО контактного лица:</strong> <?=$item['user_name']?><br/>
	   <strong>Контактный телефон:</strong> <?=$item['company_phone']?><br/>
	   <strong>e-mail:</strong> <?=$item['company_email']?>
	</p>
	<p> </p>
	<p>Город: <?=$item['town']?> <br/>
	   Вид деятельности: <?=isset($item['business']) ? $item['business'] : 'Не указан'?> <br/>
	   Сайт: <?=isset($item['company_web_site_url']) ? $item['company_web_site_url'] : 'Не указан'?> 
	</p>
	<br/>
	<br/>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
	<p>Для перехода в кабинет администратора пройдите по ссылке <a href="http://tovaryplus.ru/cms/">http://tovaryplus.ru/cms/</a></p>
</div>


<!-- <p>Доступные переменные:
	<br/>$object['user_name']
	<br/>$object['user_phone']
	<br/>$object['appointment']
	<br/>$object['company_name']
	<br/>$object['company_city']
	<br/>$object['company_email']
	<br/>$object['company_web_site_url']
	<br/>$object['business']
</p> -->