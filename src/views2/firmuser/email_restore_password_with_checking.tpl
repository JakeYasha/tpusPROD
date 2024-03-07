<p>Здравствуйте!</p>

<p>Для Вашей компании '<?=$item['company_name']?>' был запрошен сброс пароля.</p>
<p>Для начала работы с личным кабинетом перейдите по ссылке <a href="http://www.tovaryplus.ru/#login">http://www.tovaryplus.ru/#login</a></p>

<p>Используйте следующие данные для авторизации:</p>
<ul>
	<li>Логин: <?=$params['email']?></li>
	<li>Пароль: <?=$params['new_password']?></li>
</ul>

<p>Для активации нового пароля, пожалуйста перейдите по ссылке:</p>
<p><a href="<?=APP_URL?>/firm-user/activate-new-password/?model_alias=<?=$params['model_alias']?>&id=<?=$params['id']?>&control_number=<?=$params['control_number']?>"><?=APP_URL?>/firm-user/activate-new-password/?id=<?=$params['id']?>&control_number=<?=$params['control_number']?></a></p>