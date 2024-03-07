<?= $bread_crumbs?>
<div class="black-block">Заказы</div>
<div class="search_result" style="border-top: none;">
	<div class="search_price_field">
		<form action="/firm-user/request/" method="get">
			<input placeholder="Поиск по заказу, email, имени..." class="e-text-field" type="text" name="query"<?if($filters['query']){?> value="<?=$filters['query']?>"<?}?> />
		</form>
	</div>
	<?=$content?>
	<div class="attention-info">
		<p>На данной странице отображается информация о поступивших со страниц сайта заявках на заказ товаров или услуг Вашей компании. При входе в личный кабинет в меню у пункта "Заказы" цифра на красном фоне показывает сколько новых заказов поступило с последнего момента просмотра страницы со списком заказов. А на самой странице списка заказов дополнительно выделены зеленым цветов позиции заказов поступивших с последнего входа представителя компании в личный кабинет.</p>
	</div>
	<div class="delimiter-block"></div>
	<?=app()->chunk()->render('firmuser.call_support_block')?>
</div>