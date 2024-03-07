<?= $bread_crumbs?>
<div class="black-block">Сообщения</div>
<div class="search_result" style="border-top: none;">
	<div class="search_price_field">
		<form action="/firm-user/feedback/" method="get">
			<input placeholder="Поиск по сообщениям, email, имени..." class="e-text-field" type="text" name="query"<?if($filters['query']){?> value="<?=$filters['query']?>"<?}?> />
		</form>
	</div>
	<?=$content?>
	<div class="attention-info">
		<p>На данной странице отображается информация о поступивших со страниц сайта сообщениях адресованных на email Вашей компании. При входе в личный кабинет в меню у пункта "Сообщения" цифра на красном фоне показывает сколько новых сообщений поступило с последнего момента просмотра страницы со списком сообщений. А на самой странице списка сообщений дополнительно выделены зеленым цветов те сообщения, что поступили с последнего входа представителя компании в личный кабинет.</p>
	</div>
	<div class="delimiter-block"></div>
	<?=app()->chunk()->render('firmuser.call_support_block')?>
</div>