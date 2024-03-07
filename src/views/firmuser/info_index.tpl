<?= $bread_crumbs?><a title="Выгрузить в pdf" target="_blank" class="download-btn" href="/firm-user/info-pdf/"></a>
<div class="black-block">Информация о фирме</div>
<div class="search_result">
	<?= $tabs?>
	<?= $content?>
	<div class="delimiter-block"></div>
	<?= app()->chunk()->render('firmuser.call_support_block')?>
</div>