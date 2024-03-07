<?=$bread_crumbs?>
<div class="black-block">Статистика 727373.ru</div>
<div class="search_result">
	<div class="search_result_top">
		<?=$tabs?>
	</div>
	<?if($dates_block){?>
	<ul class="date-block">
		<?foreach ($dates_block as $date){?>
		<li<?if($date['active']){?> class="active"<?}?>><a<?if($date['active']){?> class="js-action"<?}?> href="<?=$date['link']?>"><?=$date['name']?></a></li>
		<?}?>
	</ul>
	<?}?>
	<?=$items?>
    <?=$pagination ?? ''?>
	<?=app()->chunk()->render('firmuser.call_support_block')?>
</div>