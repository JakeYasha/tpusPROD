<div class="item_info">
	<div class="clearfix">
		<? if ($total_founded === 0) {?>
			<div class="cat_description">
				<p>Пока нет сообщений</p>
			</div>
		<? }?>
		<?= $items?>
	</div>
	<div class="search_result">
		<?= $pagination?>
	</div>
</div>