<div class="item_info">
	<div class="clearfix">
		<? if ($total_founded === 0) {?>
			<div class="cat_description">
				<p>Нет модулей для отображения.</p>
			</div>
		<? }?>
		<?= $items?>
	</div>
	<div class="search_result">
		<?= $pagination?>
	</div>
</div>
<div class="delimiter-block"></div>
<?=app()->chunk()->render('firmuser.call_support_block')?>
