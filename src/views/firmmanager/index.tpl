<?= $bread_crumbs?>
<div class="black-block">Выбор фирмы для управления и просмотра статистики</div>
<div class="cat_description">
	<a href="/page/show/access-personal-account.htm" target="_blank">Инструкция по предоставлению фирмам доступа к личному кабинету</a>
</div>
<div class="search_result in-firm-manager" style="border-top: none;">
<div class="search_price_field">
	<form action="/firm-manager/" method="get">
		<input placeholder="Поиск по названию фирмы..." class="e-text-field" type="text" name="query"<?if($filters['query']){?> value="<?=$filters['query']?>"<?}?> />
		<input type="submit" value="" class="submit">
	</form><?=$sorting?>
</div><br/>
<?=$items?>
<?=$pagination?>
</div>