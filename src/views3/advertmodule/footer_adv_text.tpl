<? if ((int) $service->id() === 10) {?>
	<p>-------------------------------------------------------</p>
	<p>Если у Вас возникли вопросы, позвоните нашим менеджерам по рекламе по телефонам + 7(4852) 25-97-93, 42-97-76 в будни с 8.30 до 17.30 МСК<br />
		или напишите в службу поддержки сайта на почту: mng@tovaryplus.ru.</p>
	<p> </p>
	<p>Рекламно-информационный сайт <a href="http://www.tovaryplus.ru">TovaryPlus.ru</a> - интернет проект Информационного центра "Товары плюс", Ярославль</p>
	<p>Какие дополнительные услуги Вы можете получить в рамках сотрудничества с центром, <a href="http://www.tovaryplus.ru/service/">смотрите в нашем коммерческом предложении</a>.</p> 
	<p>-------------------------------------------------------</p>
<? } else {?>
	<p>-------------------------------------------------------</p>
	<p>Если у Вас возникли вопросы, позвоните нашим менеджерам по рекламе по телефону <?= $service->val('phone')?><br />
		или напишите в службу поддержки на почту: <?= $service->val('email')?>.</p>
	<p> </p>
	<p><?= $service->val('name')?>, <?= $service->getCity()?></p>
	<p> </p>
	<p>Какие дополнительные услуги Вы можете получить в рамках сотрудничества с рекламно-информационным сайтом TovaryPlus.ru, <a href="http://www.tovaryplus.ru/service/">смотрите в нашем коммерческом предложении</a></p>
	<p>-------------------------------------------------------</p>
<?
}?>