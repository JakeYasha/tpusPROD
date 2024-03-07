<p>Здравствуйте, <?=$item['name']?>!</p>

<p>Для Вашего аккаунта на сайте TovaryPlus.ru открыт доступ в личный кабинет клиента.</p>
<p>Для начала работы с личным кабинетом перейдите по ссылке <a href="http://www.tovaryplus.ru/#login">http://www.tovaryplus.ru/#login</a></p>

<p>Используйте следующие данные для авторизации:</p>
<ul>
	<li>Логин: <?=$params['email']?></li>
	<li>Пароль: <?=$params['new_password']?></li>
</ul>

<p>Для активации нового пароля, пожалуйста перейдите по ссылке:</p>
<p><a href="<?=APP_URL?>/firm-user/activate-new-password/?model_alias=<?=$params['model_alias']?>&id=<?=$params['id']?>&control_number=<?=$params['control_number']?>"><?=APP_URL?>/firm-user/activate-new-password/?id=<?=$params['id']?>&control_number=<?=$params['control_number']?></a></p>

<p>И теперь Вы можете:
<ul>
<li>Добавлять или изменять данные о Вашей организации, добавлять фото и видео материалы, отвечать на поступающиие отзывы о Вашей фирме.</li>
<li>Просматривать историю заказов и сообщений для Вашей организации от посетителей сайта TovaryPlus.ru</li>
<li>Просматривать статистику показов информации о Вашей фирме</li>
<li>Просматривать статистику показов и кликов по Вашим банерам, размещенным на сайте TovaryPlus.ru</li>
</ul></p>